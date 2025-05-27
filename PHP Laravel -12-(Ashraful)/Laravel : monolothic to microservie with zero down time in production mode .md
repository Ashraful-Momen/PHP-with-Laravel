
This comprehensive strategy ensures **zero downtime** during your Laravel dockerization and migration. Here's the key approach:

## The Zero-Downtime Strategy:

**1. Parallel Infrastructure**: Set up the entire Docker environment on a new server while your production continues running on the old server.

**2. Database Replication**: Establish real-time MySQL replication from your current production database to the new Docker MySQL containers.

**3. DNS Switch**: Once everything is verified, perform a quick DNS switch (takes ~5 minutes) to redirect traffic to the new Docker environment.

**4. Horizontal Scaling**: The Docker setup includes multiple Laravel container instances behind an Nginx load balancer for automatic scaling.

**5. Automatic Failover**: ProxySQL handles MySQL failover automatically if the master database fails.

## Key Benefits:

- **Zero Production Downtime**: Your current service never stops
- **Instant Rollback**: Can switch DNS back in minutes if issues arise
- **Horizontal Scaling**: Multiple container instances handle increased load
- **Database Failover**: Automatic MySQL failover with replication
- **Health Monitoring**: Continuous monitoring during and after migration

## Critical Success Factors:

- Test the entire Docker setup thoroughly on the new server before migration
- Ensure database replication is working and in sync
- Have monitoring in place to detect any issues immediately
- Keep the rollback script ready and tested

The migration involves just a 5-minute DNS switch window where you redirect traffic from the old server to the new Dockerized environment. Everything else happens in parallel without affecting production.


==================================================

# Zero-Downtime Laravel Dockerization Migration Strategy

## Phase 1: Current State Analysis & Preparation

### 1.1 Pre-Migration Checklist
```bash
# 1. Backup current production data
mysqldump -u root -p your_database > production_backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Document current environment
php --version
mysql --version
nginx -v
cat /etc/os-release

# 3. Note current configurations
cp /etc/nginx/sites-available/your-site ./nginx-backup.conf
cp .env ./env-backup

# 4. Monitor current performance metrics
# - Response times
# - Memory usage
# - Database connections
# - Error rates
```

## Phase 2: Docker Infrastructure Setup (New Server)

### 2.1 Docker Compose for Production-Ready Setup
```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  # Nginx Load Balancer
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/conf.d:/etc/nginx/conf.d
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - laravel-app-1
      - laravel-app-2
    networks:
      - app-network
    restart: unless-stopped

  # Laravel App Instance 1
  laravel-app-1:
    build:
      context: .
      dockerfile: Dockerfile.prod
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    environment:
      - APP_ENV=production
      - DB_HOST=mysql-master
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
      - CONTAINER_NAME=laravel-app-1
    depends_on:
      - mysql-master
      - redis
    networks:
      - app-network
    restart: unless-stopped
    deploy:
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M

  # Laravel App Instance 2 (for horizontal scaling)
  laravel-app-2:
    build:
      context: .
      dockerfile: Dockerfile.prod
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    environment:
      - APP_ENV=production
      - DB_HOST=mysql-master
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
      - CONTAINER_NAME=laravel-app-2
    depends_on:
      - mysql-master
      - redis
    networks:
      - app-network
    restart: unless-stopped
    deploy:
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M

  # MySQL Master (Primary Database)
  mysql-master:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_master_data:/var/lib/mysql
      - ./mysql/master.cnf:/etc/mysql/conf.d/master.cnf
      - ./mysql/init:/docker-entrypoint-initdb.d
    ports:
      - "3306:3306"
    networks:
      - app-network
    restart: unless-stopped
    command: --server-id=1 --log-bin=mysql-bin --binlog-do-db=${DB_DATABASE}

  # MySQL Slave (Failover Database)
  mysql-slave:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_slave_data:/var/lib/mysql
      - ./mysql/slave.cnf:/etc/mysql/conf.d/slave.cnf
    depends_on:
      - mysql-master
    networks:
      - app-network
    restart: unless-stopped
    command: --server-id=2 --relay-log=relay-log --read-only=1

  # Redis for Sessions and Cache
  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    networks:
      - app-network
    restart: unless-stopped
    command: redis-server --appendonly yes

  # MySQL Proxy for Automatic Failover
  proxysql:
    image: proxysql/proxysql:latest
    volumes:
      - ./proxysql/proxysql.cnf:/etc/proxysql.cnf
    ports:
      - "6033:6033"
      - "6032:6032"
    depends_on:
      - mysql-master
      - mysql-slave
    networks:
      - app-network
    restart: unless-stopped

volumes:
  mysql_master_data:
  mysql_slave_data:
  redis_data:

networks:
  app-network:
    driver: bridge
```

### 2.2 Production Dockerfile
```dockerfile
# Dockerfile.prod
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    zip \
    unzip \
    git

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create application directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisord.conf

# Expose port
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
```

### 2.3 Nginx Load Balancer Configuration
```nginx
# nginx/nginx.conf
events {
    worker_connections 1024;
}

http {
    upstream laravel_backend {
        server laravel-app-1:9000 weight=1 max_fails=3 fail_timeout=30s;
        server laravel-app-2:9000 weight=1 max_fails=3 fail_timeout=30s;
    }

    server {
        listen 80;
        server_name your-domain.com;
        root /var/www/html/public;
        index index.php;

        # Health check endpoint
        location /health {
            access_log off;
            return 200 "healthy\n";
            add_header Content-Type text/plain;
        }

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass laravel_backend;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
            
            # Connection pooling
            fastcgi_keep_conn on;
            fastcgi_connect_timeout 60s;
            fastcgi_send_timeout 60s;
            fastcgi_read_timeout 60s;
        }

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block";
        add_header X-Content-Type-Options "nosniff";
    }
}
```

## Phase 3: Database Replication Setup

### 3.1 MySQL Master Configuration
```ini
# mysql/master.cnf
[mysqld]
server-id = 1
log-bin = mysql-bin
binlog-do-db = your_database_name
binlog-format = ROW
expire_logs_days = 10
max_binlog_size = 100M

# Performance tuning
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
sync_binlog = 1
```

### 3.2 MySQL Slave Configuration
```ini
# mysql/slave.cnf
[mysqld]
server-id = 2
relay-log = relay-log
read-only = 1
log-slave-updates = 1

# Performance tuning
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
```

### 3.3 ProxySQL Configuration for Auto-Failover
```ini
# proxysql/proxysql.cnf
datadir="/var/lib/proxysql"

admin_variables=
{
    admin_credentials="admin:admin"
    mysql_ifaces="0.0.0.0:6032"
}

mysql_variables=
{
    threads=4
    max_connections=2048
    default_query_delay=0
    default_query_timeout=36000000
    have_compress=true
    poll_timeout=2000
    interfaces="0.0.0.0:6033"
    default_schema="your_database"
    stacksize=1048576
    server_version="8.0.25"
    connect_timeout_server=3000
    monitor_username="monitor"
    monitor_password="monitor"
    monitor_history=600000
    monitor_connect_interval=60000
    monitor_ping_interval=10000
    monitor_read_only_interval=1500
    monitor_read_only_timeout=500
}

mysql_servers =
(
    {
        address="mysql-master"
        port=3306
        hostgroup=0
        status="ONLINE"
        weight=1000
        compression=0
        max_replication_lag=10
    },
    {
        address="mysql-slave"
        port=3306
        hostgroup=1
        status="ONLINE"
        weight=1000
        compression=0
        max_replication_lag=10
    }
)

mysql_users=
(
    {
        username = "your_username"
        password = "your_password"
        default_hostgroup = 0
        max_connections=1000
        default_schema="your_database"
        active = 1
    }
)

mysql_query_rules=
(
    {
        rule_id=1
        active=1
        match_pattern="^SELECT.*FOR UPDATE$"
        destination_hostgroup=0
        apply=1
    },
    {
        rule_id=2
        active=1
        match_pattern="^SELECT"
        destination_hostgroup=1
        apply=1
    }
)
```

## Phase 4: Zero-Downtime Migration Process

### 4.1 Migration Script
```bash
#!/bin/bash
# migrate.sh - Zero downtime migration script

set -e

echo "=== Starting Zero-Downtime Migration ==="

# Configuration
OLD_SERVER="your-production-server.com"
NEW_SERVER="your-new-docker-server.com"
DOMAIN="your-domain.com"

# Step 1: Setup database replication from old to new
echo "Step 1: Setting up database replication..."
./scripts/setup-replication.sh

# Step 2: Deploy Docker containers on new server
echo "Step 2: Deploying Docker containers..."
ssh $NEW_SERVER "cd /app && docker-compose -f docker-compose.prod.yml up -d"

# Step 3: Wait for containers to be healthy
echo "Step 3: Waiting for containers to be healthy..."
./scripts/health-check.sh $NEW_SERVER

# Step 4: Sync application files
echo "Step 4: Syncing application files..."
rsync -avz --exclude=node_modules --exclude=.git $OLD_SERVER:/path/to/app/ ./

# Step 5: DNS switch preparation
echo "Step 5: Preparing DNS switch..."
# Lower TTL before switch
./scripts/update-dns-ttl.sh $DOMAIN 60

# Step 6: Final data sync
echo "Step 6: Final data synchronization..."
./scripts/final-sync.sh

# Step 7: Switch DNS to new server
echo "Step 7: Switching DNS to new server..."
./scripts/switch-dns.sh $DOMAIN $NEW_SERVER

# Step 8: Monitor for 10 minutes
echo "Step 8: Monitoring new environment..."
./scripts/monitor.sh 600

echo "=== Migration Complete ==="
```

### 4.2 Database Replication Setup Script
```bash
#!/bin/bash
# scripts/setup-replication.sh

# Variables
OLD_DB_HOST="your-old-server"
NEW_DB_HOST="your-new-server"
DB_NAME="your_database"
REPL_USER="replicator"
REPL_PASS="replicator_password"

echo "Setting up MySQL replication from $OLD_DB_HOST to $NEW_DB_HOST"

# Create replication user on old server
mysql -h $OLD_DB_HOST -u root -p << EOF
CREATE USER '$REPL_USER'@'%' IDENTIFIED BY '$REPL_PASS';
GRANT REPLICATION SLAVE ON *.* TO '$REPL_USER'@'%';
FLUSH PRIVILEGES;
FLUSH TABLES WITH READ LOCK;
EOF

# Get master status
MASTER_STATUS=$(mysql -h $OLD_DB_HOST -u root -p -e "SHOW MASTER STATUS\G")
MASTER_FILE=$(echo "$MASTER_STATUS" | grep "File:" | awk '{print $2}')
MASTER_POS=$(echo "$MASTER_STATUS" | grep "Position:" | awk '{print $2}')

# Export data from old server
mysqldump -h $OLD_DB_HOST -u root -p --master-data=2 --single-transaction $DB_NAME > migration_dump.sql

# Unlock tables on old server
mysql -h $OLD_DB_HOST -u root -p -e "UNLOCK TABLES;"

# Import data to new server
mysql -h $NEW_DB_HOST -u root -p $DB_NAME < migration_dump.sql

# Setup replication on new server
mysql -h $NEW_DB_HOST -u root -p << EOF
CHANGE MASTER TO
  MASTER_HOST='$OLD_DB_HOST',
  MASTER_USER='$REPL_USER',
  MASTER_PASSWORD='$REPL_PASS',
  MASTER_LOG_FILE='$MASTER_FILE',
  MASTER_LOG_POS=$MASTER_POS;
START SLAVE;
EOF

# Verify replication
mysql -h $NEW_DB_HOST -u root -p -e "SHOW SLAVE STATUS\G"

echo "Replication setup complete!"
```

### 4.3 Health Check Script
```bash
#!/bin/bash
# scripts/health-check.sh

SERVER=$1
MAX_ATTEMPTS=30
ATTEMPT=1

echo "Checking health of $SERVER..."

while [ $ATTEMPT -le $MAX_ATTEMPTS ]; do
    echo "Attempt $ATTEMPT/$MAX_ATTEMPTS"
    
    # Check Docker containers
    if ssh $SERVER "docker-compose ps | grep -v Exit"; then
        echo "✓ Docker containers running"
    else
        echo "✗ Docker containers not ready"
        sleep 10
        ATTEMPT=$((ATTEMPT + 1))
        continue
    fi
    
    # Check application health
    if curl -f http://$SERVER/health; then
        echo "✓ Application health check passed"
    else
        echo "✗ Application health check failed"
        sleep 10
        ATTEMPT=$((ATTEMPT + 1))
        continue
    fi
    
    # Check database connectivity
    if ssh $SERVER "docker exec mysql-master mysql -u root -p$MYSQL_ROOT_PASSWORD -e 'SELECT 1'"; then
        echo "✓ Database connectivity verified"
    else
        echo "✗ Database connectivity failed"
        sleep 10
        ATTEMPT=$((ATTEMPT + 1))
        continue
    fi
    
    echo "✓ All health checks passed!"
    exit 0
done

echo "✗ Health checks failed after $MAX_ATTEMPTS attempts"
exit 1
```

## Phase 5: Monitoring and Rollback Strategy

### 5.1 Monitoring Script
```bash
#!/bin/bash
# scripts/monitor.sh

DURATION=$1
END_TIME=$(($(date +%s) + $DURATION))

echo "Monitoring for $DURATION seconds..."

while [ $(date +%s) -lt $END_TIME ]; do
    # Check response time
    RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://your-domain.com/)
    
    # Check error rate
    HTTP_STATUS=$(curl -o /dev/null -s -w '%{http_code}' http://your-domain.com/)
    
    # Check database replication lag
    LAG=$(docker exec mysql-slave mysql -u root -p$MYSQL_ROOT_PASSWORD -e "SHOW SLAVE STATUS\G" | grep "Seconds_Behind_Master" | awk '{print $2}')
    
    echo "$(date): Response Time: ${RESPONSE_TIME}s, HTTP Status: $HTTP_STATUS, DB Lag: ${LAG}s"
    
    # Alert if issues detected
    if (( $(echo "$RESPONSE_TIME > 2.0" | bc -l) )); then
        echo "⚠️  High response time detected!"
    fi
    
    if [ "$HTTP_STATUS" != "200" ]; then
        echo "⚠️  HTTP error detected!"
    fi
    
    if [ "$LAG" -gt 5 ]; then
        echo "⚠️  High database replication lag!"
    fi
    
    sleep 30
done

echo "Monitoring complete!"
```

### 5.2 Rollback Script
```bash
#!/bin/bash
# scripts/rollback.sh

echo "🚨 INITIATING ROLLBACK 🚨"

OLD_SERVER="your-production-server.com"
DOMAIN="your-domain.com"

# Step 1: Switch DNS back to old server
echo "Switching DNS back to old server..."
./scripts/switch-dns.sh $DOMAIN $OLD_SERVER

# Step 2: Stop replication
echo "Stopping database replication..."
ssh $NEW_SERVER "docker exec mysql-master mysql -u root -p -e 'STOP SLAVE;'"

# Step 3: Verify old server is working  
echo "Verifying old server functionality..."
if curl -f http://$OLD_SERVER/health; then
    echo "✓ Old server is responding"
else
    echo "✗ Old server health check failed!"
    exit 1
fi

# Step 4: Monitor rollback
echo "Monitoring rollback for 5 minutes..."
./scripts/monitor.sh 300

echo "✓ Rollback completed successfully!"
```

## Phase 6: Deployment Commands

### 6.1 Initial Setup on New Server
```bash
# On new server
git clone your-repo.git /app
cd /app

# Copy environment file
cp .env.example .env
# Edit .env with production values

# Build and start containers
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d

# Run Laravel setup
docker exec laravel-app-1 php artisan key:generate
docker exec laravel-app-1 php artisan config:cache
docker exec laravel-app-1 php artisan route:cache
docker exec laravel-app-1 php artisan view:cache
```

### 6.2 Horizontal Scaling Commands
```bash
# Scale Laravel app containers
docker-compose -f docker-compose.prod.yml up -d --scale laravel-app-1=3 --scale laravel-app-2=3

# Check container status
docker-compose ps

# Monitor container resources
docker stats
```

## Timeline and Checklist

### Pre-Migration (1-2 days)
- [ ] Setup new server with Docker
- [ ] Test Docker compose configuration
- [ ] Setup monitoring tools
- [ ] Prepare rollback procedures
- [ ] Schedule maintenance window (optional)

### Migration Day
- [ ] **Hour 0**: Start database replication
- [ ] **Hour 1**: Deploy Docker containers
- [ ] **Hour 2**: Verify health checks
- [ ] **Hour 3**: Switch DNS (5-minute process)
- [ ] **Hour 4-6**: Monitor new environment
- [ ] **Hour 6+**: Declare success or rollback

### Post-Migration
- [ ] Update DNS TTL back to normal
- [ ] Setup automated backups
- [ ] Configure log aggregation
- [ ] Document new infrastructure

This strategy ensures zero downtime by maintaining your current production server while setting up the new Dockerized environment, then performing a quick DNS switch once everything is verified and working properly.
