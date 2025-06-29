<?php
require_once 'conf.php';
require_once 'UserAuthAPI.php';
require_once 'env.php';
loadEnv();

class ChildAPI {
    private $pdo;
    private $authAPI;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPDO();
        $this->authAPI = new UserAuthAPI();
    }
    
    /**
     * Get user from Authorization header
     */
    private function getAuthenticatedUser() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new Exception('Authorization header required');
        }
        
        $token = substr($authHeader, 7);
        $user = $this->authAPI->getUserFromToken($token);
        
        if (!$user) {
            throw new Exception('Invalid or expired token');
        }
        
        return $user;
    }
    
    /**
     * Add a new child
     * POST /api/ChildAPI.php?action=add
     */
    public function addChild($name, $ageGroup, $interest1, $interest2 = '') {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Validate input
            if (empty($name) || empty($ageGroup)) {
                throw new Exception('Name and age group are required');
            }
            
            if (!is_numeric($ageGroup) || $ageGroup < 3 || $ageGroup > 12) {
                throw new Exception('Age group must be between 3 and 12');
            }
            
            // Create child
            $childId = Database::generateChildId();
            $stmt = $this->pdo->prepare("
                INSERT INTO children (id, user_id, name, age_group, interest1, interest2, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$childId, $user['id'], $name, $ageGroup, $interest1, $interest2]);
            
            return [
                'status' => 'success',
                'child_id' => $childId,
                'message' => 'Child added successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get all children for user
     * GET /api/ChildAPI.php?action=list
     */
    public function getChildren() {
        try {
            $user = $this->getAuthenticatedUser();
            
            $stmt = $this->pdo->prepare("
                SELECT id, name, age_group, interest1, interest2, created_at 
                FROM children 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$user['id']]);
            $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'children' => $children
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get specific child
     * GET /api/ChildAPI.php?action=get&child_id=child_123
     */
    public function getChild($childId) {
        try {
            $user = $this->getAuthenticatedUser();
            
            $stmt = $this->pdo->prepare("
                SELECT id, name, age_group, interest1, interest2, created_at 
                FROM children 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$childId, $user['id']]);
            $child = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$child) {
                throw new Exception('Child not found');
            }
            
            return [
                'status' => 'success',
                'child' => $child
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Update child
     * PUT /api/ChildAPI.php?action=update
     */
    public function updateChild($childId, $name, $ageGroup, $interest1, $interest2 = '') {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Validate input
            if (empty($name) || empty($ageGroup)) {
                throw new Exception('Name and age group are required');
            }
            
            if (!is_numeric($ageGroup) || $ageGroup < 3 || $ageGroup > 12) {
                throw new Exception('Age group must be between 3 and 12');
            }
            
            // Check if child belongs to user
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $user['id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }
            
            // Update child
            $stmt = $this->pdo->prepare("
                UPDATE children 
                SET name = ?, age_group = ?, interest1 = ?, interest2 = ? 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$name, $ageGroup, $interest1, $interest2, $childId, $user['id']]);
            
            return [
                'status' => 'success',
                'message' => 'Child updated successfully'
            ];
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Delete child
     * DELETE /api/ChildAPI.php?action=delete
     */
    public function deleteChild($childId) {
        try {
            $user = $this->getAuthenticatedUser();
            
            // Check if child belongs to user
            $stmt = $this->pdo->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $user['id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Child not found');
            }
            
            // Delete child (and related worksheets)
            $this->pdo->beginTransaction();
            
            // Delete worksheets
            $stmt = $this->pdo->prepare("DELETE FROM worksheets WHERE child_id = ?");
            $stmt->execute([$childId]);
            
            // Delete child
            $stmt = $this->pdo->prepare("DELETE FROM children WHERE id = ? AND user_id = ?");
            $stmt->execute([$childId, $user['id']]);
            
            $this->pdo->commit();
            
            return [
                'status' => 'success',
                'message' => 'Child deleted successfully'
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

// Handle direct API calls
if (basename($_SERVER['SCRIPT_NAME']) === 'ChildAPI.php') {
    $api = new ChildAPI();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            $name = $input['name'] ?? null;
            $ageGroup = $input['age_group'] ?? null;
            $interest1 = $input['interest1'] ?? null;
            $interest2 = $input['interest2'] ?? '';
            
            if (!$name || !$ageGroup || !$interest1) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'name, age_group, and interest1 are required']);
                exit;
            }
            
            $result = $api->addChild($name, $ageGroup, $interest1, $interest2);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'list':
                    $result = $api->getChildren();
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                case 'get':
                    $childId = $_GET['child_id'] ?? null;
                    if (!$childId) {
                        http_response_code(400);
                        echo json_encode(['status' => 'error', 'message' => 'child_id is required']);
                        exit;
                    }
                    $result = $api->getChild($childId);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Action parameter required']);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action']) && $_GET['action'] === 'update') {
            $childId = $input['child_id'] ?? null;
            $name = $input['name'] ?? null;
            $ageGroup = $input['age_group'] ?? null;
            $interest1 = $input['interest1'] ?? null;
            $interest2 = $input['interest2'] ?? '';
            
            if (!$childId || !$name || !$ageGroup || !$interest1) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'child_id, name, age_group, and interest1 are required']);
                exit;
            }
            
            $result = $api->updateChild($childId, $name, $ageGroup, $interest1, $interest2);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action']) && $_GET['action'] === 'delete') {
            $childId = $input['child_id'] ?? null;
            
            if (!$childId) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'child_id is required']);
                exit;
            }
            
            $result = $api->deleteChild($childId);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
}
?> 