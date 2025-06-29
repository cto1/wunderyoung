<?php
require_once 'conf.php';
require_once 'env.php';
loadEnv();

class FeedbackAPI {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
    }
    
    /**
     * Submit feedback for a worksheet
     * POST /api/FeedbackAPI.php?action=submit
     */
    public function submitFeedback($token, $parentName, $parentEmail, $difficulty, $engagement, $completion, $favoritePart, $challengingPart, $suggestions, $wouldRecommend) {
        try {
            // Extract worksheet ID from token
            $parts = explode('_', $token);
            if (count($parts) < 3) {
                throw new Exception('Invalid token format');
            }
            
            $worksheetId = end($parts);
            
            // Verify worksheet exists
            $stmt = $this->pdo->prepare("SELECT id FROM worksheets WHERE id = ?");
            $stmt->execute([$worksheetId]);
            if (!$stmt->fetch()) {
                throw new Exception('Worksheet not found');
            }
            
            // Save feedback
            $feedbackId = Database::generateFeedbackId();
            $stmt = $this->pdo->prepare("
                INSERT INTO feedback (
                    id, worksheet_id, parent_name, parent_email, difficulty, engagement, 
                    completion, favorite_part, challenging_part, suggestions, would_recommend, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                $feedbackId, $worksheetId, $parentName, $parentEmail, $difficulty, $engagement,
                $completion, $favoritePart, $challengingPart, $suggestions, $wouldRecommend
            ]);
            
            return [
                'status' => 'success',
                'feedback_id' => $feedbackId,
                'message' => 'Feedback submitted successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get feedback for a worksheet (authenticated)
     * GET /api/FeedbackAPI.php?action=get&worksheet_id=ws_123
     */
    public function getFeedback($worksheetId, $user) {
        try {
            // Verify user owns the worksheet
            $stmt = $this->pdo->prepare("
                SELECT w.id FROM worksheets w 
                JOIN children c ON w.child_id = c.id 
                WHERE w.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$worksheetId, $user['id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Worksheet not found');
            }
            
            // Get feedback
            $stmt = $this->pdo->prepare("
                SELECT * FROM feedback 
                WHERE worksheet_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$worksheetId]);
            $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'feedback' => $feedback
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get all feedback for user's children
     * GET /api/FeedbackAPI.php?action=list
     */
    public function getFeedbackList($user) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT f.*, w.date, c.name as child_name 
                FROM feedback f 
                JOIN worksheets w ON f.worksheet_id = w.id 
                JOIN children c ON w.child_id = c.id 
                WHERE c.user_id = ? 
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$user['id']]);
            $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'feedback' => $feedback
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

// Handle direct API calls
if (basename($_SERVER['SCRIPT_NAME']) === 'FeedbackAPI.php') {
    $api = new FeedbackAPI();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action']) && $_GET['action'] === 'submit') {
            $token = $input['token'] ?? null;
            $parentName = $input['parentName'] ?? null;
            $parentEmail = $input['parentEmail'] ?? null;
            $difficulty = $input['difficulty'] ?? null;
            $engagement = $input['engagement'] ?? null;
            $completion = $input['completion'] ?? null;
            $favoritePart = $input['favoritePart'] ?? '';
            $challengingPart = $input['challengingPart'] ?? '';
            $suggestions = $input['suggestions'] ?? '';
            $wouldRecommend = $input['wouldRecommend'] ?? null;
            
            if (!$token || !$parentName || !$parentEmail || !$difficulty || !$engagement || !$completion || !$wouldRecommend) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'All required fields must be provided']);
                exit;
            }
            
            $result = $api->submitFeedback($token, $parentName, $parentEmail, $difficulty, $engagement, $completion, $favoritePart, $challengingPart, $suggestions, $wouldRecommend);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // For authenticated endpoints, we need to verify the user
        require_once 'UserAuthAPI.php';
        $authAPI = new UserAuthAPI();
        
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Authorization required']);
            exit;
        }
        
        $token = substr($authHeader, 7);
        $user = $authAPI->getUserFromToken($token);
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token']);
            exit;
        }
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'get':
                    $worksheetId = $_GET['worksheet_id'] ?? null;
                    if (!$worksheetId) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'worksheet_id is required']);
                        exit;
                    }
                    $result = $api->getFeedback($worksheetId, $user);
                    break;
                    
                case 'list':
                    $result = $api->getFeedbackList($user);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                    exit;
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Action parameter required']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
}
?> 