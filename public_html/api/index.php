<?php
// Daily Homework API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Basic API response
$response = [
    'status' => 'success',
    'message' => 'Daily Homework API is running',
    'version' => '1.0.0',
    'domain' => 'dailyhome.work'
];

echo json_encode($response, JSON_PRETTY_PRINT);
?> 