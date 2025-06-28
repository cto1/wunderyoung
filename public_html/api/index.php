<?php
// Yes Homework API - Main entry point
require_once 'env.php';
require_once __DIR__ . '/../../vendor/autoload.php';
loadEnv();

// Configure error handling based on environment
$isDebugMode = filter_var($_ENV['DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($isDebugMode) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', '/var/log/dailyhomework/error.log');
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'conf.php';
require_once 'Router.php';
require_once 'UserAuthAPI.php';
require_once 'WorksheetAPI.php';
require_once 'WorksheetGeneratorAPI.php';
require_once 'JWTAuth.php';
require_once 'AuthMiddleware.php';

// Initialize APIs and Router
$userAuthAPI = new UserAuthAPI();
$worksheetAPI = new WorksheetAPI();
$worksheetGeneratorAPI = new WorksheetGeneratorAPI();
$router = new Router();
$jwtAuth = new JWTAuth();

// Health check endpoint
$router->addRoute('GET', '/health', function($params, $data, $context) {
    return [
        'status' => 'success',
        'message' => 'Yes Homework API is running',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ];
});

// ==================== AUTH ROUTES ====================

// Sign up
$router->addRoute('POST', '/auth/signup', function($params, $data, $context) use ($userAuthAPI) {
    return $userAuthAPI->signup($data);
});

// Request login (magic link)
$router->addRoute('POST', '/auth/login', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($data['email'])) {
        return ['status' => 'error', 'message' => 'Email is required'];
    }
    return $userAuthAPI->requestLogin($data['email']);
});

// Password login
$router->addRoute('POST', '/auth/password-login', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($data['email']) || !isset($data['password'])) {
        return ['status' => 'error', 'message' => 'Email and password are required'];
    }
    return $userAuthAPI->passwordLogin($data['email'], $data['password']);
});

// Verify login token
$router->addRoute('GET', '/auth/verify', function($params, $data, $context) use ($userAuthAPI) {
    $email = $_GET['email'] ?? null;
    $token = $_GET['token'] ?? null;
    
    if (!$email || !$token) {
        return ['status' => 'error', 'message' => 'Email and token are required'];
    }
    
    return $userAuthAPI->verifyLogin($email, $token);
});

// Generate JWT token (after magic link verification)
$router->addRoute('POST', '/auth/token', function($params, $data, $context) use ($jwtAuth, $userAuthAPI) {
    if (!isset($data['id']) || !isset($data['email'])) {
        return ['status' => 'error', 'message' => 'User ID and email are required'];
    }
    
    // Verify user exists in database and email matches
    $profileResult = $userAuthAPI->getProfile($data['id']);
    if ($profileResult['status'] !== 'success') {
        return ['status' => 'error', 'message' => 'User not found'];
    }
    
    // Verify email matches the user in database
    if ($profileResult['user']['email'] !== $data['email']) {
        return ['status' => 'error', 'message' => 'Email mismatch'];
    }
    
    // Use actual user data from database (not from request)
    $userData = [
        'id' => $profileResult['user']['id'],
        'email' => $profileResult['user']['email'],
        'plan' => $profileResult['user']['plan']
    ];
    
    $token = $jwtAuth->generateToken($userData);
    
    return [
        'status' => 'success',
        'token' => $token,
        'user' => $userData
    ];
});

// Refresh token endpoint (protected)
$router->addRoute('POST', '/auth/refresh-token', function($params, $data, $context) use ($jwtAuth) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $newToken = $jwtAuth->generateToken($context['userData']);
    
    return [
        'status' => 'success',
        'token' => $newToken
    ];
});

// ==================== USER ROUTES ====================

// Get user profile (protected)
$router->addRoute('GET', '/users/profile', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->getProfile($context['userData']['id']);
});

// Update user profile (protected)
$router->addRoute('PUT', '/users/profile', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->updateProfile($context['userData']['id'], $data);
});

// ==================== CHILDREN ROUTES ====================

// Get user's children (protected)
$router->addRoute('GET', '/children', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->getChildren($context['userData']['id']);
});

// Add child (protected)
$router->addRoute('POST', '/children', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->addChild($context['userData']['id'], $data);
});

// Update child (protected)
$router->addRoute('PUT', '/children/{child_id}', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->updateChild($context['userData']['id'], $params['child_id'], $data);
});

// Delete child (protected)
$router->addRoute('DELETE', '/children/{child_id}', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $userAuthAPI->deleteChild($context['userData']['id'], $params['child_id']);
});

// ==================== WORKSHEET ROUTES ====================

// Get all worksheets for user (protected)
$router->addRoute('GET', '/worksheets', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $limit = $_GET['limit'] ?? 50;
    return $worksheetAPI->getUserWorksheets($context['userData']['id'], $limit);
});

// Get worksheets for specific child (protected)
$router->addRoute('GET', '/children/{child_id}/worksheets', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $limit = $_GET['limit'] ?? 30;
    return $worksheetAPI->getWorksheets($params['child_id'], $limit);
});

// Create worksheet (protected)
$router->addRoute('POST', '/worksheets', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $worksheetAPI->createWorksheet($data);
});

// Get specific worksheet (protected)
$router->addRoute('GET', '/worksheets/{worksheet_id}', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $result = $worksheetAPI->getWorksheet($params['worksheet_id']);
    
    // Verify worksheet belongs to the user
    if ($result['status'] === 'success') {
        if ($result['worksheet']['user_id'] !== $context['userData']['id']) {
            return ['status' => 'error', 'message' => 'Unauthorized access'];
        }
    }
    
    return $result;
});

// Update worksheet (protected)
$router->addRoute('PUT', '/worksheets/{worksheet_id}', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify worksheet belongs to user before updating
    $worksheetResult = $worksheetAPI->getWorksheet($params['worksheet_id']);
    if ($worksheetResult['status'] !== 'success') {
        return $worksheetResult;
    }
    
    if ($worksheetResult['worksheet']['user_id'] !== $context['userData']['id']) {
        return ['status' => 'error', 'message' => 'Unauthorized access'];
    }
    
    return $worksheetAPI->updateWorksheet($params['worksheet_id'], $data);
});

// Delete worksheet (protected)
$router->addRoute('DELETE', '/worksheets/{worksheet_id}', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify worksheet belongs to user before deleting
    $worksheetResult = $worksheetAPI->getWorksheet($params['worksheet_id']);
    if ($worksheetResult['status'] !== 'success') {
        return $worksheetResult;
    }
    
    if ($worksheetResult['worksheet']['user_id'] !== $context['userData']['id']) {
        return ['status' => 'error', 'message' => 'Unauthorized access'];
    }
    
    return $worksheetAPI->deleteWorksheet($params['worksheet_id']);
});

// Mark worksheet as downloaded (protected)
$router->addRoute('POST', '/worksheets/{worksheet_id}/download', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify worksheet belongs to user
    $worksheetResult = $worksheetAPI->getWorksheet($params['worksheet_id']);
    if ($worksheetResult['status'] !== 'success') {
        return $worksheetResult;
    }
    
    if ($worksheetResult['worksheet']['user_id'] !== $context['userData']['id']) {
        return ['status' => 'error', 'message' => 'Unauthorized access'];
    }
    
    return $worksheetAPI->markAsDownloaded($params['worksheet_id']);
});

// Get worksheet statistics (protected)
$router->addRoute('GET', '/stats/worksheets', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $worksheetAPI->getWorksheetStats($context['userData']['id']);
});

// ==================== AI WORKSHEET GENERATION ROUTES ====================

// Generate personalized worksheet for a specific child (protected)
$router->addRoute('POST', '/children/{child_id}/generate-worksheet', function($params, $data, $context) use ($worksheetGeneratorAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $date = $data['date'] ?? null;
    return $worksheetGeneratorAPI->generateWorksheet($context['userData']['id'], $params['child_id'], $date);
});

// Preview worksheet content without saving (protected)
$router->addRoute('GET', '/children/{child_id}/preview-worksheet', function($params, $data, $context) use ($worksheetGeneratorAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $worksheetGeneratorAPI->previewWorksheet($context['userData']['id'], $params['child_id']);
});

// Generate worksheets for all children (paid users only) (protected)
$router->addRoute('POST', '/generate-worksheets-bulk', function($params, $data, $context) use ($worksheetGeneratorAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    $date = $data['date'] ?? null;
    return $worksheetGeneratorAPI->generateWorksheetForAllChildren($context['userData']['id'], $date);
});

// Test OpenAI connection (protected)
$router->addRoute('GET', '/test-openai', function($params, $data, $context) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    try {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            return ['status' => 'error', 'message' => 'OPENAI_API_KEY not configured'];
        }
        
        $openai = new OpenaiProvider($apiKey, 'gpt-4');
        $result = $openai->callApiWithoutEcho(
            "Say 'Hello from Yes Homework API!' and confirm you can help create educational worksheets.",
            "You are a helpful AI assistant for educational content creation."
        );
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'OpenAI connection successful',
                'response' => $result['content'],
                'tokens_used' => $result['tokens_in'] + $result['tokens_out'],
                'latency_ms' => $result['latency_ms']
            ];
        } else {
            return ['status' => 'error', 'message' => 'Failed to connect to OpenAI'];
        }
        
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
});

// Handle the request
try {
    $response = $router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'details' => $isDebugMode ? $e->getMessage() : null
    ]);
}
