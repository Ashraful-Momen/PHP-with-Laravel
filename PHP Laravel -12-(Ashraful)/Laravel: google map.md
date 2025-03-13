# Complete Guide: Building Laravel Projects with Google Maps

This comprehensive guide will walk you through integrating Google Maps into your Laravel projects, from basic setup to advanced implementations.

## Table of Contents

1. [Setting Up Google Maps API](#setting-up-google-maps-api)
2. [Installing Required Packages](#installing-required-packages)
3. [Basic Map Integration](#basic-map-integration)
4. [Working with Map Markers](#working-with-map-markers)
5. [Storing and Retrieving Locations](#storing-and-retrieving-locations)
6. [Dynamic Maps with AJAX](#dynamic-maps-with-ajax)
7. [Geocoding and Reverse Geocoding](#geocoding-and-reverse-geocoding)
8. [Advanced Features](#advanced-features)
9. [Real-World Project Examples](#real-world-project-examples)
10. [Performance Optimization](#performance-optimization)
11. [Troubleshooting](#troubleshooting)

## Setting Up Google Maps API

### Creating a Google Cloud Project

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - Maps JavaScript API
   - Geocoding API
   - Places API
   - Directions API (if needed)
   - Distance Matrix API (if needed)

### Generating an API Key

1. In the Google Cloud Console, navigate to "Credentials"
2. Click "Create credentials" and select "API key"
3. Restrict your API key:
   - Set HTTP referrers to your application's domain
   - Limit API usage to only the APIs you need

### Adding API Key to Laravel Environment

Add your API key to your `.env` file:

```
GOOGLE_MAPS_API_KEY=your_api_key_here
```

And update your `config/services.php` file:

```php
'google' => [
    'maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],
],
```

## Installing Required Packages

### Laravel-specific Google Maps Package

For a Laravel-friendly wrapper, you can use one of these packages:

**Option 1: Laravel Google Maps**

```bash
composer require cornford/googlmapper
```

Publish the configuration:

```bash
php artisan vendor:publish --provider="Cornford\Googlmapper\MapperServiceProvider" --tag="config"
```

**Option 2: Laravel-JS-Routes with Custom Implementation**

```bash
composer require tightenco/ziggy
```

Publish the assets:

```bash
php artisan ziggy:generate
```

### JavaScript Dependencies

For more control, use the Google Maps JavaScript API directly.

In your `resources/js/app.js`:

```javascript
import GoogleMapsLoader from 'google-maps';

GoogleMapsLoader.KEY = process.env.MIX_GOOGLE_MAPS_API_KEY;
```

In your webpack.mix.js, ensure you expose the API key:

```javascript
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .env({
        MIX_GOOGLE_MAPS_API_KEY: process.env.GOOGLE_MAPS_API_KEY
    });
```

## Basic Map Integration

### Using Googlmapper Package

In your controller:

```php
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;

public function showMap()
{
    Mapper::map(34.0522, -118.2437, [
        'zoom' => 12,
        'marker' => true,
        'type' => 'ROADMAP',
        'cluster' => false
    ]);
    
    return view('maps.index');
}
```

In your Blade view:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Google Maps in Laravel</title>
    {!! Mapper::renderJavascript() !!}
</head>
<body>
    <div class="container">
        <h1>Google Map</h1>
        <div style="width: 100%; height: 400px;">
            {!! Mapper::render() !!}
        </div>
    </div>
</body>
</html>
```

### Custom Implementation with JavaScript API

Define your route in `routes/web.php`:

```php
Route::get('/map', 'MapController@index')->name('map.index');
```

In your controller:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('maps.custom', [
            'apiKey' => config('services.google.maps.api_key')
        ]);
    }
}
```

In your Blade view:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Custom Google Map</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Custom Google Map</h1>
        <div id="map"></div>
    </div>

    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 34.0522, lng: -118.2437 },
                zoom: 12,
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap" async defer></script>
</body>
</html>
```

## Working with Map Markers

### Adding Multiple Markers

Using Googlmapper:

```php
public function showMultipleMarkers()
{
    Mapper::map(34.0522, -118.2437, ['zoom' => 12]);
    
    Mapper::marker(34.0522, -118.2437, ['title' => 'Marker 1', 'animation' => 'DROP']);
    Mapper::marker(34.0500, -118.2500, ['title' => 'Marker 2', 'icon' => 'https://example.com/custom-marker.png']);
    
    return view('maps.multiple-markers');
}
```

Using custom JavaScript:

```javascript
function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 34.0522, lng: -118.2437 },
        zoom: 12,
    });
    
    const locations = [
        { lat: 34.0522, lng: -118.2437, title: 'Location 1' },
        { lat: 34.0500, lng: -118.2500, title: 'Location 2' },
        { lat: 34.0550, lng: -118.2600, title: 'Location 3' }
    ];
    
    locations.forEach(location => {
        const marker = new google.maps.Marker({
            position: { lat: location.lat, lng: location.lng },
            map: map,
            title: location.title,
            animation: google.maps.Animation.DROP
        });
        
        marker.addListener('click', () => {
            infowindow.open(map, marker);
        });
    });
}
```

### Custom Marker Icons

```javascript
const marker = new google.maps.Marker({
    position: { lat: 34.0522, lng: -118.2437 },
    map: map,
    icon: {
        url: '/images/custom-marker.png',
        scaledSize: new google.maps.Size(40, 40),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(20, 40)
    }
});
```

### Info Windows for Markers

```javascript
const locations = [
    { lat: 34.0522, lng: -118.2437, title: 'Location 1', content: '<h3>Location 1</h3><p>Description here</p>' },
    { lat: 34.0500, lng: -118.2500, title: 'Location 2', content: '<h3>Location 2</h3><p>Description here</p>' }
];

locations.forEach(location => {
    const marker = new google.maps.Marker({
        position: { lat: location.lat, lng: location.lng },
        map: map,
        title: location.title
    });
    
    const infowindow = new google.maps.InfoWindow({
        content: location.content
    });
    
    marker.addListener('click', () => {
        infowindow.open(map, marker);
    });
});
```

## Storing and Retrieving Locations

### Database Schema

```php
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('address')->nullable();
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### Location Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name', 'address', 'latitude', 'longitude', 'description'
    ];
    
    // Accessor for map coordinates
    public function getCoordinatesAttribute()
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude
        ];
    }
}
```

### Creating Locations

```php
// In your controller
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'description' => 'nullable|string',
    ]);
    
    $location = Location::create($validated);
    
    return redirect()->route('locations.index')
        ->with('success', 'Location created successfully.');
}
```

### Displaying Locations on Map

```php
public function index()
{
    $locations = Location::all();
    
    return view('locations.index', compact('locations'));
}
```

In your Blade view:

```html
<div id="map" style="height: 500px;"></div>

<script>
    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: {{ $locations->first()->latitude ?? 34.0522 }}, lng: {{ $locations->first()->longitude ?? -118.2437 }} },
            zoom: 10,
        });
        
        const locations = @json($locations);
        
        locations.forEach(location => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                map: map,
                title: location.name
            });
            
            const infowindow = new google.maps.InfoWindow({
                content: `
                    <div>
                        <h3>${location.name}</h3>
                        <p>${location.description || ''}</p>
                        <p>${location.address || ''}</p>
                    </div>
                `
            });
            
            marker.addListener('click', () => {
                infowindow.open(map, marker);
            });
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&callback=initMap" async defer></script>
```

## Dynamic Maps with AJAX

### Controller Endpoint

```php
public function getLocations(Request $request)
{
    $query = Location::query();
    
    // Apply filters if needed
    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    
    $locations = $query->get();
    
    return response()->json($locations);
}
```

Add this route:

```php
Route::get('/api/locations', 'LocationController@getLocations')->name('api.locations');
```

### Ajax Implementation

```html
<div id="map" style="height: 500px;"></div>
<input type="text" id="search" placeholder="Search locations...">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let map;
    let markers = [];
    
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 34.0522, lng: -118.2437 },
            zoom: 10,
        });
        
        loadLocations();
        
        // Set up search
        $('#search').on('keyup', function() {
            loadLocations($(this).val());
        });
    }
    
    function loadLocations(search = '') {
        $.ajax({
            url: '{{ route('api.locations') }}',
            method: 'GET',
            data: { search: search },
            success: function(locations) {
                // Clear existing markers
                clearMarkers();
                
                // Add new markers
                locations.forEach(location => {
                    addMarker(location);
                });
                
                // Fit bounds to markers if there are any
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => bounds.extend(marker.getPosition()));
                    map.fitBounds(bounds);
                }
            }
        });
    }
    
    function addMarker(location) {
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
            map: map,
            title: location.name,
            animation: google.maps.Animation.DROP
        });
        
        const infowindow = new google.maps.InfoWindow({
            content: `
                <div>
                    <h3>${location.name}</h3>
                    <p>${location.description || ''}</p>
                    <p>${location.address || ''}</p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infowindow.open(map, marker);
        });
        
        markers.push(marker);
    }
    
    function clearMarkers() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&callback=initMap" async defer></script>
```

## Geocoding and Reverse Geocoding

### Integrating Geocoding Services

Install Geocoder PHP with Guzzle:

```bash
composer require geocoder-php/google-maps-provider php-http/guzzle7-adapter php-http/message
```

Create a GeocodingService:

```php
namespace App\Services;

use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Geocoder\StatefulGeocoder;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Http\Adapter\Guzzle7\Client;

class GeocodingService
{
    protected $geocoder;
    
    public function __construct()
    {
        $httpClient = new Client();
        $provider = new GoogleMaps($httpClient, null, config('services.google.maps.api_key'));
        $this->geocoder = new StatefulGeocoder($provider, 'en');
    }
    
    /**
     * Get coordinates for an address
     */
    public function getCoordinates(string $address)
    {
        try {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));
            
            if (!$result->isEmpty()) {
                $coordinates = $result->first()->getCoordinates();
                
                return [
                    'latitude' => $coordinates->getLatitude(),
                    'longitude' => $coordinates->getLongitude(),
                    'address' => $result->first()->getFormattedAddress(),
                ];
            }
        } catch (\Exception $e) {
            report($e);
        }
        
        return null;
    }
    
    /**
     * Get address for coordinates
     */
    public function getAddress(float $latitude, float $longitude)
    {
        try {
            $result = $this->geocoder->reverseQuery(ReverseQuery::fromCoordinates($latitude, $longitude));
            
            if (!$result->isEmpty()) {
                return [
                    'address' => $result->first()->getFormattedAddress(),
                    'street_number' => $result->first()->getStreetNumber(),
                    'street_name' => $result->first()->getStreetName(),
                    'postal_code' => $result->first()->getPostalCode(),
                    'locality' => $result->first()->getLocality(),
                    'country' => $result->first()->getCountry()->getName(),
                ];
            }
        } catch (\Exception $e) {
            report($e);
        }
        
        return null;
    }
}
```

Register in `AppServiceProvider`:

```php
$this->app->singleton(GeocodingService::class, function ($app) {
    return new GeocodingService();
});
```

### Geocoding in Controller

```php
public function store(Request $request, GeocodingService $geocoder)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'description' => 'nullable|string',
    ]);
    
    // Geocode the address
    $coordinates = $geocoder->getCoordinates($request->address);
    
    if ($coordinates) {
        $location = Location::create([
            'name' => $validated['name'],
            'address' => $coordinates['address'], // Use formatted address
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully.');
    }
    
    return back()->withInput()
        ->with('error', 'Could not geocode the provided address.');
}
```

### Client-Side Geocoding with Places API

Add the Places library to your Google Maps script:

```html
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places&callback=initMap" async defer></script>
```

Implement address autocomplete:

```html
<div class="form-group">
    <label for="address">Address</label>
    <input type="text" id="address" class="form-control" name="address" value="{{ old('address') }}" required>
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
</div>

<script>
    function initMap() {
        const input = document.getElementById('address');
        const autocomplete = new google.maps.places.Autocomplete(input);
        
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            
            if (!place.geometry) {
                console.log("No details available for: " + place.name);
                return;
            }
            
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
</script>
```

## Advanced Features

### Clustered Markers

Include the MarkerClusterer library:

```html
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
```

Implement clustering:

```javascript
function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 34.0522, lng: -118.2437 },
        zoom: 10,
    });
    
    // Fetch locations
    fetch('/api/locations')
        .then(response => response.json())
        .then(locations => {
            const markers = locations.map(location => {
                return new google.maps.Marker({
                    position: { 
                        lat: parseFloat(location.latitude), 
                        lng: parseFloat(location.longitude) 
                    },
                    title: location.name
                });
            });
            
            // Add marker clusterer
            new markerClusterer.MarkerClusterer({ map, markers });
        });
}
```

### Directions and Routes

```html
<div id="map" style="height: 500px;"></div>
<div id="directions-panel" style="width: 100%; height: 200px; overflow: auto;"></div>

<div class="form-group">
    <label for="origin">Starting Point</label>
    <input type="text" id="origin" class="form-control">
</div>

<div class="form-group">
    <label for="destination">Destination</label>
    <input type="text" id="destination" class="form-control">
</div>

<button id="get-directions" class="btn btn-primary">Get Directions</button>

<script>
    let map;
    let directionsService;
    let directionsRenderer;
    
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 34.0522, lng: -118.2437 },
            zoom: 10,
        });
        
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);
        directionsRenderer.setPanel(document.getElementById("directions-panel"));
        
        // Set up autocomplete on inputs
        const originInput = document.getElementById("origin");
        const destinationInput = document.getElementById("destination");
        
        new google.maps.places.Autocomplete(originInput);
        new google.maps.places.Autocomplete(destinationInput);
        
        // Set up directions button
        document.getElementById("get-directions").addEventListener("click", function() {
            calculateAndDisplayRoute(directionsService, directionsRenderer);
        });
    }
    
    function calculateAndDisplayRoute(directionsService, directionsRenderer) {
        directionsService.route(
            {
                origin: document.getElementById("origin").value,
                destination: document.getElementById("destination").value,
                travelMode: google.maps.TravelMode.DRIVING,
            },
            (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                } else {
                    window.alert("Directions request failed due to " + status);
                }
            }
        );
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places&callback=initMap" async defer></script>
```

### Geofencing

```php
// Create migration for geofence table
Schema::create('geofences', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('center_latitude', 10, 7);
    $table->decimal('center_longitude', 10, 7);
    $table->integer('radius')->comment('radius in meters');
    $table->timestamps();
});

// Create Geofence model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    protected $fillable = [
        'name', 'center_latitude', 'center_longitude', 'radius'
    ];
    
    // Check if a point is within the geofence
    public function containsPoint($latitude, $longitude)
    {
        // Calculate distance using Haversine formula
        $earthRadius = 6371000; // meters
        
        $dLat = deg2rad($latitude - $this->center_latitude);
        $dLon = deg2rad($longitude - $this->center_longitude);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($this->center_latitude)) * cos(deg2rad($latitude)) *
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance <= $this->radius;
    }
}
```

JavaScript for drawing geofences:

```javascript
let map;
let drawingManager;
let geofenceCircle;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 34.0522, lng: -118.2437 },
        zoom: 12,
    });
    
    // Initialize drawing manager
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.CIRCLE,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [google.maps.drawing.OverlayType.CIRCLE],
        },
        circleOptions: {
            fillColor: "#FF0000",
            fillOpacity: 0.2,
            strokeWeight: 2,
            strokeColor: "#FF0000",
            clickable: true,
            editable: true,
            zIndex: 1,
        },
    });
    
    drawingManager.setMap(map);
    
    // Handle circle complete event
    google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
        // Only allow one circle
        if (geofenceCircle) {
            geofenceCircle.setMap(null);
        }
        
        geofenceCircle = circle;
        drawingManager.setDrawingMode(null);
        
        // Update form fields with circle data
        const center = circle.getCenter();
        document.getElementById('center_latitude').value = center.lat();
        document.getElementById('center_longitude').value = center.lng();
        document.getElementById('radius').value = circle.getRadius();
        
        // Listen for radius changes
        google.maps.event.addListener(circle, 'radius_changed', function() {
            document.getElementById('radius').value = circle.getRadius();
        });
        
        // Listen for center changes
        google.maps.event.addListener(circle, 'center_changed', function() {
            const newCenter = circle.getCenter();
            document.getElementById('center_latitude').value = newCenter.lat();
            document.getElementById('center_longitude').value = newCenter.lng();
        });
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=drawing,places&callback=initMap" async defer></script>
```

## Real-World Project Examples

### Property Listing Site

```php
// Migration
Schema::create('properties', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('address');
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->decimal('price', 12, 2);
    $table->integer('bedrooms');
    $table->integer('bathrooms');
    $table->text('description');
    $table->timestamps();
});

// Property model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'title', 'address', 'latitude', 'longitude', 
        'price', 'bedrooms', 'bathrooms', 'description'
    ];
}

// Controller
namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Services\GeocodingService;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::all();
        
        return view('properties.index', compact('properties'));
    }
    
    public function search(Request $request)
    {
        $query = Property::query();
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }
        
        $properties = $query->get();
        
        return response()->json($properties);
    }
    
    public function store(Request $request, GeocodingService $geocoder)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'description' => 'required|string',
        ]);
        
        // Geocode the address
        $coordinates = $geocoder->getCoordinates($request->address);
        
        if ($coordinates) {
            $property = Property::create([
                'title' => $validated['title'],
                'address' => $coordinates['address'],
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
                'price' => $validated['price'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'description' => $validated['description'],
            ]);
            
            return redirect()->route('properties.index')
                ->with('success', 'Property created successfully.');
        }
        
        return back()->withInput()
            ->with('error', 'Could not geocode the provided address.');
    }
}
```

The view for the property listing with map filtering:

```html
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Filter Properties</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="min_price">Min Price</label>
                        <input type="number" id="min_price" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="max_price">Max Price</label>
                        <input type="number" id="max_price" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="bedrooms">Minimum Bedrooms</label>
                        <input type="number" id="bedrooms" class="form-control">
                    </div>
                    
                    <button id="search-btn" class="btn btn-primary">Search</button>
                </div>
            </div>
            
            <div id="property-list" class="mt-4">
                <!-- Properties will appear here -->
            </div>
        </div>
        
        <div class="col-md-8">
            <div id="map" style="height: 600px;"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let map;
    let markers = [];
    let infoWindow;
    
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 34.0522, lng: -118.2437 },
            zoom: 10,
        });
        
        infoWindow = new google.maps.InfoWindow();
        
        // Load initial properties
        loadProperties();
        
        // Set up search button
        $('#search-btn').on('click', function() {
            loadProperties();
        });
    }
    
    function loadProperties() {
        const minPrice = $('#min_price').val();
        const maxPrice = $('#max_price').val();
        const bedrooms = $('#bedrooms').val();
        
        $.ajax({
            url: '{{ route('properties.search') }}',
            method: 'GET',
            data: { 
                min_price: minPrice,
                max_price: maxPrice,
                bedrooms: bedrooms
            },
            success: function(properties) {
                // Clear existing markers
                clearMarkers();
                
                // Clear property list
                $('#property-list').empty();
                
                // Add new markers and listings
                properties.forEach(property => {
                    addMarker(property);
                    addPropertyToList(property);
                });
                
                // Fit bounds to markers if there are any
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => bounds.extend(marker.getPosition()));
                    map.fitBounds(bounds);
                }
            }
        });
    }
    
    function addMarker(property) {
        const marker = new google.maps.Marker({
            position: { 
                lat: parseFloat(property.latitude), 
                lng: parseFloat(property.longitude) 
            },
            map: map,
            title: property.title,
            animation: google.maps.Animation.DROP
        });
        
        marker.addListener('click', () => {
            const content = `
                <div>
                    <h5>${property.title}</h5>
                    <p>${property.address}</p>
                    <p><strong>${property.price.toLocaleString()}</strong></p>
                    <p>${property.bedrooms} bed, ${property.bathrooms} bath</p>
                    <a href="/properties/${property.id}" class="btn btn-sm btn-primary">View Details</a>
                </div>
            `;
            
            infoWindow.setContent(content);
            infoWindow.open(map, marker);
        });
        
        markers.push(marker);
    }
    
    function addPropertyToList(property) {
        const propertyElement = `
            <div class="card mb-3 property-item" data-id="${property.id}">
                <div class="card-body">
                    <h5 class="card-title">${property.title}</h5>
                    <p class="card-text">${property.address}</p>
                    <p class="card-text"><strong>${property.price.toLocaleString()}</strong></p>
                    <p class="card-text">${property.bedrooms} bed, ${property.bathrooms} bath</p>
                    <a href="/properties/${property.id}" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        `;
        
        $('#property-list').append(propertyElement);
        
        // Add click event to center map on this property when clicked
        $('.property-item[data-id="' + property.id + '"]').on('click', function() {
            const marker = markers.find((m, i) => {
                return $(this).data('id') == property.id;
            });
            
            if (marker) {
                map.setCenter(marker.getPosition());
                map.setZoom(15);
                google.maps.event.trigger(marker, 'click');
            }
        });
    }
    
    function clearMarkers() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&callback=initMap" async defer></script>
```

### Food Delivery App - Restaurant Locator

```php
// Migration
Schema::create('restaurants', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('address');
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->string('cuisine_type');
    $table->text('description');
    $table->decimal('delivery_radius', 8, 2)->comment('in kilometers');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Restaurant model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name', 'address', 'latitude', 'longitude', 
        'cuisine_type', 'description', 'delivery_radius', 'is_active'
    ];
    
    // Check if delivery is available to a location
    public function deliversTo($latitude, $longitude)
    {
        // Calculate distance using Haversine formula
        $earthRadius = 6371; // kilometers
        
        $dLat = deg2rad($latitude - $this->latitude);
        $dLon = deg2rad($longitude - $this->longitude);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance <= $this->delivery_radius;
    }
}

// Controller
namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Services\GeocodingService;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('is_active', true)->get();
        
        return view('restaurants.index', compact('restaurants'));
    }
    
    public function checkDelivery(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string',
        ]);
        
        $geocoder = app(GeocodingService::class);
        $coordinates = $geocoder->getCoordinates($request->address);
        
        if (!$coordinates) {
            return response()->json([
                'success' => false,
                'message' => 'Could not geocode the provided address.'
            ]);
        }
        
        $availableRestaurants = Restaurant::where('is_active', true)
            ->get()
            ->filter(function($restaurant) use ($coordinates) {
                return $restaurant->deliversTo($coordinates['latitude'], $coordinates['longitude']);
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'address' => $coordinates['address'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'restaurants' => $availableRestaurants
        ]);
    }
}
```

Implementation in the frontend:

```html
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Check Delivery Availability</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="delivery-address">Delivery Address</label>
                        <input type="text" id="delivery-address" class="form-control" placeholder="Enter your address">
                    </div>
                    <button id="check-delivery" class="btn btn-primary">Check Availability</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-5">
            <div id="available-restaurants" class="d-none">
                <h3>Restaurants that deliver to you:</h3>
                <div id="restaurant-list"></div>
            </div>
            <div id="no-restaurants" class="d-none alert alert-warning">
                Sorry, no restaurants deliver to this location.
            </div>
        </div>
        <div class="col-md-7">
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let map;
    let userMarker;
    let restaurantMarkers = [];
    let deliveryCircles = [];
    
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 34.0522, lng: -118.2437 },
            zoom: 10,
        });
        
        // Set up autocomplete for address input
        const input = document.getElementById('delivery-address');
        const autocomplete = new google.maps.places.Autocomplete(input);
        
        // Load all restaurants initially
        loadRestaurants();
        
        // Set up check delivery button
        $('#check-delivery').on('click', checkDeliveryAvailability);
    }
    
    function loadRestaurants() {
        $.get('/api/restaurants', function(restaurants) {
            // Clear existing markers
            clearMap();
            
            // Add restaurant markers
            restaurants.forEach(restaurant => {
                addRestaurantMarker(restaurant, false);
            });
            
            // Fit map bounds
            if (restaurantMarkers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                restaurantMarkers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        });
    }
    
    function checkDeliveryAvailability() {
        const address = $('#delivery-address').val();
        
        if (!address) {
            alert('Please enter an address');
            return;
        }
        
        $.post('/api/restaurants/check-delivery', { address: address }, function(response) {
            if (!response.success) {
                alert(response.message);
                return;
            }
            
            // Clear existing markers and circles
            clearMap();
            
            // Add user marker
            userMarker = new google.maps.Marker({
                position: { 
                    lat: parseFloat(response.latitude), 
                    lng: parseFloat(response.longitude) 
                },
                map: map,
                title: 'Your Location',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });
            
            // Add restaurant markers and delivery zones
            if (response.restaurants.length > 0) {
                $('#available-restaurants').removeClass('d-none');
                $('#no-restaurants').addClass('d-none');
                $('#restaurant-list').empty();
                
                response.restaurants.forEach(restaurant => {
                    addRestaurantMarker(restaurant, true);
                    addRestaurantToList(restaurant);
                });
                
                // Fit map to include user and restaurants
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(userMarker.getPosition());
                restaurantMarkers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            } else {
                $('#available-restaurants').addClass('d-none');
                $('#no-restaurants').removeClass('d-none');
                
                // Center on user location
                map.setCenter({ 
                    lat: parseFloat(response.latitude), 
                    lng: parseFloat(response.longitude) 
                });
                map.setZoom(14);
            }
        });
    }
    
    function addRestaurantMarker(restaurant, showDeliveryZone) {
        const marker = new google.maps.Marker({
            position: { 
                lat: parseFloat(restaurant.latitude), 
                lng: parseFloat(restaurant.longitude) 
            },
            map: map,
            title: restaurant.name
        });
        
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div>
                    <h5>${restaurant.name}</h5>
                    <p>${restaurant.address}</p>
                    <p><strong>Cuisine:</strong> ${restaurant.cuisine_type}</p>
                    <p>${restaurant.description}</p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        restaurantMarkers.push(marker);
        
        // Add delivery zone circle if requested
        if (showDeliveryZone) {
            const deliveryCircle = new google.maps.Circle({
                strokeColor: '#4CAF50',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#4CAF50',
                fillOpacity: 0.1,
                map: map,
                center: { 
                    lat: parseFloat(restaurant.latitude), 
                    lng: parseFloat(restaurant.longitude) 
                },
                radius: restaurant.delivery_radius * 1000 // Convert km to meters
            });
            
            deliveryCircles.push(deliveryCircle);
        }
    }
    
    function addRestaurantToList(restaurant) {
        const restaurantElement = `
            <div class="card mb-3 restaurant-item" data-id="${restaurant.id}">
                <div class="card-body">
                    <h5 class="card-title">${restaurant.name}</h5>
                    <p class="card-text"><strong>Cuisine:</strong> ${restaurant.cuisine_type}</p>
                    <p class="card-text">${restaurant.description}</p>
                    <a href="/restaurants/${restaurant.id}" class="btn btn-sm btn-primary">View Menu</a>
                </div>
            </div>
        `;
        
        $('#restaurant-list').append(restaurantElement);
        
        // Add click event to highlight this restaurant on the map
        $('.restaurant-item[data-id="' + restaurant.id + '"]').on('click', function() {
            const marker = restaurantMarkers.find((m, i) => {
                return $(this).data('id') == restaurant.id;
            });
            
            if (marker) {
                map.setCenter(marker.getPosition());
                map.setZoom(15);
                google.maps.event.trigger(marker, 'click');
            }
        });
    }
    
    function clearMap() {
        // Clear restaurant markers
        restaurantMarkers.forEach(marker => marker.setMap(null));
        restaurantMarkers = [];
        
        // Clear delivery circles
        deliveryCircles.forEach(circle => circle.setMap(null));
        deliveryCircles = [];
        
        // Clear user marker
        if (userMarker) {
            userMarker.setMap(null);
            userMarker = null;
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places&callback=initMap" async defer></script>
```

## Performance Optimization

### Lazy Loading Maps

Instead of loading the Google Maps script on every page, load it only when needed:

```php
// Create a blade directive in AppServiceProvider
Blade::directive('googleMapsScript', function () {
    return "<?php echo '<script src=\"https://maps.googleapis.com/maps/api/js?key=' . config('services.google.maps.api_key') . '&callback=initMap\" async defer></script>'; ?>";
});
```

Use this in your views:

```html
@if($showMap)
    @googleMapsScript
@endif
```

### Caching Geocoding Results

Create a geocoding middleware or service that caches results:

```php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CachedGeocodingService
{
    protected $geocoder;
    protected $cacheTtl;
    
    public function __construct(GeocodingService $geocoder)
    {
        $this->geocoder = $geocoder;
        $this->cacheTtl = config('services.geocoding.cache_ttl', 30 * 24 * 60 * 60); // 30 days by default
    }
    
    public function getCoordinates(string $address)
    {
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($address) {
            return $this->geocoder->getCoordinates($address);
        });
    }
    
    public function getAddress(float $latitude, float $longitude)
    {
        $cacheKey = 'reverse_geocode_' . md5($latitude . '_' . $longitude);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($latitude, $longitude) {
            return $this->geocoder->getAddress($latitude, $longitude);
        });
    }
}
```

### Optimizing Large Datasets

For applications with large numbers of locations:

1. Implement pagination with the Marker Clusterer library
2. Use server-side filtering to only return locations in the current viewport
3. Implement a spatial database index

```php
// Add spatial index to migration
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    // ... other fields
    
    // Add spatial index for better performance
    $table->spatialIndex(['latitude', 'longitude']);
});

// Controller method for retrieving locations within bounds
public function getInBounds(Request $request)
{
    $validated = $request->validate([
        'north' => 'required|numeric',
        'south' => 'required|numeric',
        'east' => 'required|numeric',
        'west' => 'required|numeric',
    ]);
    
    $locations = Location::whereBetween('latitude', [$request->south, $request->north])
        ->whereBetween('longitude', [$request->west, $request->east])
        ->take(100) // Limit to prevent overloading the map
        ->get();
    
    return response()->json($locations);
}
```

Add this to your frontend code:

```javascript
function loadLocationsInViewport() {
    const bounds = map.getBounds();
    
    if (!bounds) return;
    
    const north = bounds.getNorthEast().lat();
    const east = bounds.getNorthEast().lng();
    const south = bounds.getSouthWest().lat();
    const west = bounds.getSouthWest().lng();
    
    $.get('/api/locations/in-bounds', {
        north: north,
        south: south,
        east: east,
        west: west
    }, function(locations) {
        // Clear existing markers
        clearMarkers();
        
        // Add new markers
        locations.forEach(location => {
            addMarker(location);
        });
    });
}

// Listen for map idle event (fired when panning/zooming ends)
google.maps.event.addListener(map, 'idle', loadLocationsInViewport);
```

### Using Static Maps for Read-Only Views

When you don't need interactivity, use static maps to reduce load time:

```php
function getStaticMapUrl($latitude, $longitude, $markers = [], $zoom = 12, $width = 600, $height = 300)
{
    $url = 'https://maps.googleapis.com/maps/api/staticmap?';
    
    // Set center and zoom
    $url .= "center={$latitude},{$longitude}&zoom={$zoom}";
    
    // Set size
    $url .= "&size={$width}x{$height}";
    
    // Add markers
    foreach ($markers as $marker) {
        $url .= "&markers=color:red|{$marker['lat']},{$marker['lng']}";
    }
    
    // Add API key
    $url .= "&key=" . config('services.google.maps.api_key');
    
    return $url;
}
```

Use in blade templates:

```html
<img src="{{ getStaticMapUrl($property->latitude, $property->longitude) }}" alt="Map for {{ $property->address }}">
```

## Troubleshooting

### Common Issues and Solutions

#### 1. API Key Restrictions

**Issue**: Maps not loading due to API key restrictions

**Solution**:
- Check Google Cloud Console for error messages
- Ensure your domain is properly listed in the HTTP referrers
- For development, allow `localhost` and your development domains

#### 2. Billing Issues

**Issue**: "For development purposes only" watermark

**Solution**:
- Set up billing in Google Cloud Console 
- Ensure the API is properly enabled
- Verify API key is correctly configured

#### 3. CORS Issues

**Issue**: API requests failing due to CORS errors

**Solution**:
- Use Laravel's proxy for API requests in development
- Ensure proper headers are set in production

```php
// Create a proxy route
Route::get('maps-proxy', function (Request $request) {
    $url = 'https://maps.googleapis.com/maps/api/' . $request->path;
    $url .= '?' . http_build_query($request->query());
    
    $client = new \GuzzleHttp\Client();
    $response = $client->get($url);
    
    return response($response->getBody())
        ->withHeaders($response->getHeaders());
});
```

#### 4. Geocoding Rate Limits

**Issue**: Hitting rate limits for geocoding services

**Solution**:
- Implement caching as shown earlier
- Use batch geocoding for bulk operations
- Implement exponential backoff for retries

```php
function geocodeWithRetry($address, $maxRetries = 3)
{
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            $result = $this->geocoder->getCoordinates($address);
            
            if ($result) {
                return $result;
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'OVER_QUERY_LIMIT') !== false) {
                $attempt++;
                $sleepTime = pow(2, $attempt) * 1000000; // Exponential backoff
                usleep($sleepTime);
                continue;
            }
            
            throw $e;
        }
        
        $attempt++;
    }
    
    return null;
}
```

### Debugging Tools

1. **Google Cloud Console Logs**:
   - Check API request logs in the Google Cloud Console
   - Look for error codes and failed requests

2. **Browser Developer Tools**:
   - Monitor network requests in Chrome DevTools
   - Check for JavaScript errors in the console

3. **Laravel Debugbar**:
   - Monitor Laravel database queries and performance
   - Track geocoding service calls

```bash
composer require barryvdh/laravel-debugbar --dev
```
