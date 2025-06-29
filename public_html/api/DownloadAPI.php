<?php

require_once 'conf.php';
require_once 'WorksheetGeneratorAPI.php';
require_once 'DownloadTokenAPI.php';

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
    
    // Handle download by token - generates worksheet on-demand
    public function downloadByToken($token) {
        try {
            error_log("PDF Download: Starting download for token: $token");
            
            // 1. Validate token and get child information
            $tokenAPI = new DownloadTokenAPI();
            $tokenResult = $tokenAPI->getDownloadTokenInfo($token);
            
            if ($tokenResult['status'] !== 'success') {
                error_log("PDF Download: Token validation failed: " . $tokenResult['message']);
                throw new Exception($tokenResult['message']);
            }
            
            $tokenData = $tokenResult['token_data'];
            error_log("PDF Download: Token validated for child: " . $tokenData['child_name']);
            
            // 2. Generate worksheet content on-demand using AI
            $childData = [
                'id' => $tokenData['child_id'],
                'name' => $tokenData['child_name'],
                'age_group' => $tokenData['age_group'],
                'interest1' => $tokenData['interest1'],
                'interest2' => $tokenData['interest2']
            ];
            
            error_log("PDF Download: Generating worksheet content for: " . $childData['name']);
            $worksheetContent = $this->generator->generateWorksheetContentForDownload($childData, $tokenData['date']);
            error_log("PDF Download: Worksheet content generated successfully, length: " . strlen($worksheetContent));
            
            // 3. Generate and stream PDF directly
            error_log("PDF Download: Starting PDF generation");
            $this->generator->streamPDFToBrowserFromContent($worksheetContent, $tokenData['child_name'], $tokenData['date']);
            error_log("PDF Download: PDF streamed successfully");
            
            // 4. Mark token as used
            $tokenAPI->markTokenAsUsed($token);
            error_log("PDF Download: Token marked as used");
            
        } catch (Exception $e) {
            error_log("PDF Download Error: " . $e->getMessage());
            error_log("PDF Download Error Trace: " . $e->getTraceAsString());
            
            // Clear any output buffers to prevent corruption
            if (ob_get_level()) {
                ob_clean();
            }
            
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'token' => $token,
                    'error_line' => $e->getLine(),
                    'error_file' => basename($e->getFile())
                ]
            ]);
        }
    }
}

// Handle API requests - only execute if this file is being accessed directly
if (basename($_SERVER['SCRIPT_NAME']) === 'DownloadAPI.php') {
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
} 