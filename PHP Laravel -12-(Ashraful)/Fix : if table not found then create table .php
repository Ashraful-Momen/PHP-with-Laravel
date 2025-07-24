private function storeBkashTokenInDatabase($response, $account = null)
    {
        try {
            // Create bkash_tokens table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('bkash_tokens')) {
                DB::statement("
                    CREATE TABLE bkash_tokens (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        account_key VARCHAR(50) DEFAULT NULL,
                        id_token TEXT NOT NULL,
                        token_type VARCHAR(50) NOT NULL,
                        refresh_token TEXT NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
            }

            $account_key = $account ? "account_$account" : 'default';

            // Insert or update token record
            DB::table('bkash_tokens')->updateOrInsert(
                ['account_key' => $account_key],
                [
                    'id_token' => $response['id_token'],
                    'token_type' => $response['token_type'],
                    'refresh_token' => $response['refresh_token'],
                    'expires_at' => now()->addHour(),
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to store bKash token in database: ' . $e->getMessage());
        }
    }
