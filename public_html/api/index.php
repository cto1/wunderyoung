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
require_once 'DownloadTokenAPI.php';
require_once 'FeedbackAPI.php';
require_once 'JWTAuth.php';
require_once 'AuthMiddleware.php';

// Initialize APIs and Router
$userAuthAPI = new UserAuthAPI();
$worksheetAPI = new WorksheetAPI();
$worksheetGeneratorAPI = new WorksheetGeneratorAPI();
$downloadTokenAPI = new DownloadTokenAPI();
$feedbackAPI = new FeedbackAPI();
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
$router->addRoute('GET', '/worksheets/stats', function($params, $data, $context) use ($worksheetAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $worksheetAPI->getWorksheetStats($context['userData']['id']);
});

// Send welcome email for new child (protected)
$router->addRoute('POST', '/send-welcome-email', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    try {
        if (!isset($data['child_id']) || !isset($data['child_name']) || !isset($data['parent_email'])) {
            throw new Exception('Missing required fields');
        }
        
        // Verify child belongs to user
        $db = Database::getInstance();
        $pdo = $db->getPDO();
        $stmt = $pdo->prepare("SELECT id FROM children WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['child_id'], $context['userData']['id']]);
        
        if (!$stmt->fetch()) {
            throw new Exception('Child not found or unauthorized');
        }
        
        // Send welcome email
        $subject = "Welcome to Yes Homework! {$data['child_name']}'s First Worksheet is Ready";
        $textContent = "Hi there!\n\nGreat news! We've created {$data['child_name']}'s first personalized worksheet and it's waiting in your inbox.\n\nThis worksheet has been tailored based on your child's interests and age group to make learning fun and engaging.\n\nWhat's next?\n1. Check your email for the worksheet PDF\n2. Print it out for your child\n3. Watch them enjoy their personalized learning activities!\n\nWe'll continue sending daily worksheets to help {$data['child_name']} learn and grow.\n\nHappy learning!\nThe Yes Homework Team";
        
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #3b82f6;'>Welcome to Yes Homework!</h2>
            <p>Great news! We've created <strong>{$data['child_name']}'s</strong> first personalized worksheet and it's waiting in your inbox.</p>
            
            <div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0;'>This worksheet has been tailored based on your child's interests and age group to make learning fun and engaging.</p>
            </div>
            
            <h3 style='color: #1f2937;'>What's next?</h3>
            <ol style='color: #4b5563;'>
                <li>Check your email for the worksheet PDF</li>
                <li>Print it out for your child</li>
                <li>Watch them enjoy their personalized learning activities!</li>
            </ol>
            
            <p>We'll continue sending daily worksheets to help <strong>{$data['child_name']}</strong> learn and grow.</p>
            
            <p style='color: #059669; font-weight: bold;'>Happy learning!<br>The Yes Homework Team</p>
        </div>";
        
        // Use the existing email sending functionality from UserAuthAPI
        $reflection = new ReflectionClass($userAuthAPI);
        $sendEmailMethod = $reflection->getMethod('sendEmail');
        $sendEmailMethod->setAccessible(true);
        $sendEmailMethod->invoke($userAuthAPI, $data['parent_email'], $subject, $textContent, $htmlContent);
        
        return [
            'status' => 'success',
            'message' => 'Welcome email sent successfully'
        ];
        
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
});

// ==================== AI WORKSHEET GENERATION ROUTES ====================

// Create download link for worksheet (protected) - NEW FLOW
$router->addRoute('POST', '/children/{child_id}/generate-worksheet', function($params, $data, $context) use ($downloadTokenAPI, $userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify child belongs to user
    $childrenResult = $userAuthAPI->getChildren($context['userData']['id']);
    if ($childrenResult['status'] !== 'success') {
        return $childrenResult;
    }
    
    $childExists = false;
    $childData = null;
    foreach ($childrenResult['children'] as $child) {
        if ($child['id'] === $params['child_id']) {
            $childExists = true;
            $childData = $child;
            break;
        }
    }
    
    if (!$childExists) {
        return ['status' => 'error', 'message' => 'Child not found or unauthorized'];
    }
    
    $date = $data['date'] ?? date('Y-m-d');
    $isWelcome = $data['is_welcome'] ?? false;
    
    // Create download token instead of generating worksheet immediately
    $tokenResult = $downloadTokenAPI->createDownloadToken($params['child_id'], $date, $isWelcome);
    
    if ($tokenResult['status'] === 'success') {
        // Send email with download link
        $downloadUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                      '://' . $_SERVER['HTTP_HOST'] . '/download.php?token=' . $tokenResult['token'];
        
        try {
            // Get user email
            $userProfile = $userAuthAPI->getProfile($context['userData']['id']);
            if ($userProfile['status'] === 'success') {
                $userEmail = $userProfile['user']['email'];
                
                // Send email with download link
                $subject = $isWelcome ? 
                    "Welcome! {$childData['name']}'s First Worksheet is Ready" :
                    "{$childData['name']}'s Daily Worksheet for " . date('F j, Y', strtotime($date));
                
                $textContent = $isWelcome ?
                    "Hi there!\n\nGreat news! We've created {$childData['name']}'s first personalized worksheet.\n\nClick the link below to download it:\n{$downloadUrl}\n\nThis worksheet has been tailored based on your child's interests and age group to make learning fun and engaging.\n\nHappy learning!\nThe Yes Homework Team" :
                    "Hi there!\n\n{$childData['name']}'s daily worksheet for " . date('F j, Y', strtotime($date)) . " is ready!\n\nClick the link below to download it:\n{$downloadUrl}\n\nThis worksheet has been personalized based on your child's interests and previous feedback.\n\nHappy learning!\nThe Yes Homework Team";
                
                $htmlContent = $isWelcome ?
                    "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: #3b82f6;'>Welcome to Yes Homework!</h2>
                        <p>Great news! We've created <strong>{$childData['name']}'s</strong> first personalized worksheet.</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$downloadUrl}' style='background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;'>Download Worksheet</a>
                        </div>
                        <p>This worksheet has been tailored based on your child's interests and age group to make learning fun and engaging.</p>
                        <p style='color: #059669; font-weight: bold;'>Happy learning!<br>The Yes Homework Team</p>
                    </div>" :
                    "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: #3b82f6;'>{$childData['name']}'s Daily Worksheet</h2>
                        <p><strong>{$childData['name']}'s</strong> daily worksheet for " . date('F j, Y', strtotime($date)) . " is ready!</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$downloadUrl}' style='background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;'>Download Worksheet</a>
                        </div>
                        <p>This worksheet has been personalized based on your child's interests and previous feedback.</p>
                        <p style='color: #059669; font-weight: bold;'>Happy learning!<br>The Yes Homework Team</p>
                    </div>";
                
                // Use the existing email sending functionality
                $reflection = new ReflectionClass($userAuthAPI);
                $sendEmailMethod = $reflection->getMethod('sendEmail');
                $sendEmailMethod->setAccessible(true);
                $sendEmailMethod->invoke($userAuthAPI, $userEmail, $subject, $textContent, $htmlContent);
            }
            
        } catch (Exception $e) {
            error_log("Failed to send worksheet email: " . $e->getMessage());
            // Don't fail the request if email fails
        }
        
        return [
            'status' => 'success',
            'token' => $tokenResult['token'],
            'download_url' => $downloadUrl,
            'message' => 'Download link created and sent to your email'
        ];
    }
    
    return $tokenResult;
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

// Debug endpoint to check environment (unprotected for testing)
$router->addRoute('GET', '/debug/env', function($params, $data, $context) {
    return [
        'status' => 'success',
        'env' => [
            'openai_key_exists' => isset($_ENV['OPENAI_API_KEY']),
            'openai_key_length' => isset($_ENV['OPENAI_API_KEY']) ? strlen($_ENV['OPENAI_API_KEY']) : 0,
            'openai_key_prefix' => isset($_ENV['OPENAI_API_KEY']) ? substr($_ENV['OPENAI_API_KEY'], 0, 10) . '...' : 'not set',
            'debug_mode' => $_ENV['DEBUG'] ?? 'not set',
            'php_version' => PHP_VERSION
        ]
    ];
});

// ==================== DOWNLOAD TOKEN ROUTES ====================

// Get download token info (unprotected - public access via token)
$router->addRoute('GET', '/download-tokens/{token}', function($params, $data, $context) use ($downloadTokenAPI) {
    return $downloadTokenAPI->getDownloadTokenInfo($params['token']);
});

// Get previous worksheet for feedback (unprotected - public access via token)
$router->addRoute('GET', '/download-tokens/{token}/previous-worksheet', function($params, $data, $context) use ($downloadTokenAPI) {
    // First validate the token
    $tokenResult = $downloadTokenAPI->getDownloadTokenInfo($params['token']);
    if ($tokenResult['status'] !== 'success') {
        return $tokenResult;
    }
    
    $tokenData = $tokenResult['token_data'];
    return $downloadTokenAPI->getPreviousWorksheetForFeedback($tokenData['child_id'], $tokenData['date']);
});

// Generate worksheet from download token (unprotected - public access via token)
$router->addRoute('POST', '/download-tokens/{token}/generate', function($params, $data, $context) use ($downloadTokenAPI, $worksheetGeneratorAPI) {
    // First validate the token
    $tokenResult = $downloadTokenAPI->getDownloadTokenInfo($params['token']);
    if ($tokenResult['status'] !== 'success') {
        return $tokenResult;
    }
    
    $tokenData = $tokenResult['token_data'];
    
    // Generate the worksheet
    $worksheetResult = $worksheetGeneratorAPI->generateWorksheet(
        $tokenData['user_id'], 
        $tokenData['child_id'], 
        $tokenData['date']
    );
    
    if ($worksheetResult['status'] === 'success') {
        // Mark token as used
        $downloadTokenAPI->markTokenAsUsed($params['token']);
        
        return [
            'status' => 'success',
            'worksheet_id' => $worksheetResult['worksheet_id'],
            'download_url' => $worksheetResult['download_url'] ?? null,
            'message' => 'Worksheet generated successfully'
        ];
    }
    
    return $worksheetResult;
});

// ==================== FEEDBACK ROUTES ====================

// Submit worksheet feedback (unprotected - can be accessed via email links)
$router->addRoute('POST', '/feedback', function($params, $data, $context) use ($feedbackAPI) {
    return $feedbackAPI->submitFeedback($data);
});

// Get feedback for a worksheet (protected)
$router->addRoute('GET', '/feedback/{worksheet_id}', function($params, $data, $context) use ($feedbackAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    return $feedbackAPI->getFeedback($params['worksheet_id']);
});

// Get child feedback summary (protected)
$router->addRoute('GET', '/children/{child_id}/feedback-summary', function($params, $data, $context) use ($feedbackAPI, $userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify child belongs to user
    $childrenResult = $userAuthAPI->getChildren($context['userData']['id']);
    if ($childrenResult['status'] !== 'success') {
        return $childrenResult;
    }
    
    $childExists = false;
    foreach ($childrenResult['children'] as $child) {
        if ($child['id'] === $params['child_id']) {
            $childExists = true;
            break;
        }
    }
    
    if (!$childExists) {
        return ['status' => 'error', 'message' => 'Child not found or unauthorized'];
    }
    
    return $feedbackAPI->getChildFeedbackSummary($params['child_id']);
});

// Get completion streak for child (protected)
$router->addRoute('GET', '/children/{child_id}/completion-streak', function($params, $data, $context) use ($feedbackAPI, $userAuthAPI) {
    if (!isset($context['userData'])) {
        return ['status' => 'error', 'message' => 'Authentication required'];
    }
    
    // Verify child belongs to user
    $childrenResult = $userAuthAPI->getChildren($context['userData']['id']);
    if ($childrenResult['status'] !== 'success') {
        return $childrenResult;
    }
    
    $childExists = false;
    foreach ($childrenResult['children'] as $child) {
        if ($child['id'] === $params['child_id']) {
            $childExists = true;
            break;
        }
    }
    
    if (!$childExists) {
        return ['status' => 'error', 'message' => 'Child not found or unauthorized'];
    }
    
    return $feedbackAPI->getCompletionStreak($params['child_id']);
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
