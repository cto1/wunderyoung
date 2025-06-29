<?php

require_once 'conf.php';
require_once 'WorksheetGeneratorAPI.php';

class DownloadAPI {
    private $generator;
    
    public function __construct() {
        $this->generator = new WorksheetGeneratorAPI();
    }
    
    // Handle PDF download by worksheet ID
    public function downloadPDF($worksheetId, $token = null) {
        try {
            // TODO: Validate token when implementing token system
            
            // Generate and stream PDF
            $this->generator->downloadWorksheetPDF($worksheetId, $token);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    // Handle download by token (when we implement magic links)
    public function downloadByToken($token) {
        try {
            // TODO: Implement token-based downloads
            // 1. Validate token
            // 2. Get worksheet ID from token
            // 3. Call downloadPDF with worksheet ID
            
            throw new Exception('Token-based downloads not yet implemented');
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new DownloadAPI();
    
    if (isset($_GET['worksheet_id'])) {
        $worksheetId = intval($_GET['worksheet_id']);
        $token = $_GET['token'] ?? null;
        $api->downloadPDF($worksheetId, $token);
    } 
    elseif (isset($_GET['token'])) {
        $token = $_GET['token'];
        $api->downloadByToken($token);
    }
    else {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing worksheet_id or token parameter'
        ]);
    }
}
else {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
} 