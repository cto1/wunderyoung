<?php

require_once 'conf.php';

class DownloadTokenAPI {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPDO();
    }

    // Create a new download token
    public function createDownloadToken($childId, $date, $isWelcome = false) {
        try {
            // Check if child exists
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ?");
            $stmt->execute([$childId]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }

            // Check if token already exists for this child and date
            $stmt = $this->pdo->prepare("SELECT token, expires_at FROM download_tokens WHERE child_id = ? AND date = ?");
            $stmt->execute([$childId, $date]);
            $existingToken = $stmt->fetch();
            
            if ($existingToken) {
                return [
                    'status' => 'success',
                    'token' => $existingToken['token'],
                    'expires_at' => $existingToken['expires_at'],
                    'message' => 'Download token already exists'
                ];
            }

            // Generate new token
            $token = Database::generateDownloadToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days')); // Token expires in 7 days

            $stmt = $this->pdo->prepare("
                INSERT INTO download_tokens (token, child_id, date, is_welcome, expires_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $token,
                $childId,
                $date,
                $isWelcome ? 1 : 0,
                $expiresAt
            ]);

            return [
                'status' => 'success',
                'token' => $token,
                'expires_at' => $expiresAt,
                'message' => 'Download token created successfully'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Get download token info
    public function getDownloadTokenInfo($token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT dt.*, c.name as child_name, c.age_group, c.interest1, c.interest2, c.user_id
                FROM download_tokens dt
                JOIN children c ON dt.child_id = c.id
                WHERE dt.token = ?
            ");
            $stmt->execute([$token]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tokenData) {
                throw new Exception('Invalid download token');
            }

            // Check if token has expired
            if (strtotime($tokenData['expires_at']) < time()) {
                throw new Exception('Download token has expired');
            }

            // Check if token has already been used
            if ($tokenData['used_at'] !== null) {
                throw new Exception('Download token has already been used');
            }

            return [
                'status' => 'success',
                'token_data' => $tokenData
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Mark download token as used
    public function markTokenAsUsed($token) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE download_tokens 
                SET used_at = CURRENT_TIMESTAMP 
                WHERE token = ?
            ");
            $stmt->execute([$token]);

            return ['status' => 'success', 'message' => 'Token marked as used'];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Get previous worksheet for feedback (if exists)
    public function getPreviousWorksheetForFeedback($childId, $currentDate) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT w.*, wf.completed, wf.math_difficulty, wf.english_difficulty, 
                       wf.science_difficulty, wf.other_difficulty, wf.feedback_notes
                FROM worksheets w
                LEFT JOIN worksheet_feedback wf ON w.id = wf.worksheet_id
                WHERE w.child_id = ? AND w.date < ?
                ORDER BY w.date DESC
                LIMIT 1
            ");
            $stmt->execute([$childId, $currentDate]);
            $worksheet = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'previous_worksheet' => $worksheet
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Clean up expired tokens
    public function cleanupExpiredTokens() {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM download_tokens WHERE expires_at < CURRENT_TIMESTAMP");
            $deletedCount = $stmt->execute();

            return [
                'status' => 'success',
                'deleted_count' => $deletedCount,
                'message' => 'Expired tokens cleaned up'
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

// Handle API requests - only execute if this file is being accessed directly
if (basename($_SERVER['SCRIPT_NAME']) === 'DownloadTokenAPI.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $api = new DownloadTokenAPI();
        $action = $_GET['action'] ?? '';
        
        if ($action === 'get_info') {
            $token = $_GET['token'] ?? '';
            if (empty($token)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing token parameter']);
                exit;
            }
            
            $result = $api->getDownloadTokenInfo($token);
            echo json_encode($result);
        }
        elseif ($action === 'cleanup') {
            $result = $api->cleanupExpiredTokens();
            echo json_encode($result);
        }
        else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $api = new DownloadTokenAPI();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $childId = $input['child_id'] ?? '';
        $date = $input['date'] ?? date('Y-m-d');
        $isWelcome = $input['is_welcome'] ?? false;
        
        if (empty($childId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing child_id parameter']);
            exit;
        }
        
        $result = $api->createDownloadToken($childId, $date, $isWelcome);
        echo json_encode($result);
    }
    else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
}