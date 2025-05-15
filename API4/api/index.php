<?php
/**
 * Main API Entry Point
 * Routes all API requests to the appropriate controller
 */

// Set proper headers and handle CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-KEY");

// Handle OPTIONS pre-flight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = explode('/', $request_uri);

// Find the endpoint (last part of the URL)
$endpoint = end($path);

// Map endpoints to controllers
switch ($endpoint) {
    case 'patients':
        require_once 'controllers/PatientController.php';
        break;
        
    case 'debug':
        require_once 'debug.php';
        break;
        
    case 'setup':
        require_once 'setup.php';
        break;
        
    default:
        // API information endpoint
        echo json_encode([
            'status' => 'success',
            'message' => 'Patient Records API',
            'version' => '1.0',
            'endpoints' => [
                'patients' => '/api/patients',
                'debug' => '/api/debug',
                'setup' => '/api/setup'
            ]
        ]);
        break;
}
?>