<?php
// Disable displaying errors to output (important for JSON responses)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Wrap the entire script in a try-catch to ensure proper JSON responses
try {

// --- Allowed Origins ---
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Detect Environment and Set Base URL ---
$host = $_SERVER['HTTP_HOST'] ?? '';
if (strpos($host, 'demo.yeshomework.com') !== false) {
    $base_url = "https://demo.yeshomework.com/api";
} else {
    $base_url = "https://yeshomework.com/api";
}

// --- Extract JWT Token from Request Headers ---
$headers = getallheaders();
$jwt_token = $headers['Authorization'] ?? null;

// --- API Endpoints Mapping ---
$apiEndpoints = [
    // ----- [Yes Homework - Authentication Routes] -----
    "auth_signup" => "$base_url/auth/signup",
    "auth_login" => "$base_url/auth/login", 
    "auth_verify" => "$base_url/auth/verify?email={email}&token={token}",
    "auth_token" => "$base_url/auth/token",
    "auth_password_login" => "$base_url/auth/password-login",
    "auth_refresh_token" => "$base_url/auth/refresh-token",
    "request_login" => "$base_url/auth/login",
    "verify_login" => "$base_url/auth/verify?email={email}&token={token}",
    "JWT_token" => "$base_url/auth/token",
    "signup" => "$base_url/auth/signup",
    
    // ----- [Yes Homework - User Profile Routes] -----
    "get_user_profile" => "$base_url/users/profile",
    "update_user_profile" => "$base_url/users/profile",
    
    // ----- [Yes Homework - Children Routes] -----
    "get_children" => "$base_url/children",
    "add_child" => "$base_url/children", 
    "update_child" => "$base_url/children/{child_id}",
    "delete_child" => "$base_url/children/{child_id}",
    "get_child_worksheets" => "$base_url/children/{child_id}/worksheets",
    "get_child_feedback_summary" => "$base_url/children/{child_id}/feedback-summary",
    "get_child_completion_streak" => "$base_url/children/{child_id}/completion-streak",
    "preview_child_worksheet" => "$base_url/children/{child_id}/preview-worksheet",
    
    // ----- [Yes Homework - Worksheet Routes] -----
    "get_user_worksheets" => "$base_url/worksheets",
    "create_worksheet" => "$base_url/worksheets",
    "get_worksheet" => "$base_url/worksheets/{worksheet_id}",
    "update_worksheet" => "$base_url/worksheets/{worksheet_id}",
    "delete_worksheet" => "$base_url/worksheets/{worksheet_id}",
    "download_worksheet" => "$base_url/worksheets/{worksheet_id}/download",
    "get_worksheet_stats" => "$base_url/worksheets/stats",
    "generate_child_worksheet" => "$base_url/children/{child_id}/generate-worksheet",
    "generate_worksheets_bulk" => "$base_url/generate-worksheets-bulk",
    "send_welcome_email" => "$base_url/send-welcome-email",
    
    // ----- [Yes Homework - Download Token Routes] -----
    "get_download_token" => "$base_url/download-tokens/{token}",
    "get_token_previous_worksheet" => "$base_url/download-tokens/{token}/previous-worksheet",
    "generate_from_token" => "$base_url/download-tokens/{token}/generate",
    
    // ----- [Yes Homework - Feedback Routes] -----
    "submit_feedback_v2" => "$base_url/feedback",
    "get_feedback" => "$base_url/feedback/{worksheet_id}",
    
    // ----- [Yes Homework - Testing/Debug Routes] -----
    "test_openai" => "$base_url/test-openai",
    "debug_env" => "$base_url/debug/env",
    "health_check" => "$base_url/health",
    
    // ----- [Yes Homework - Download System] -----
    "get_token_info" => "$base_url/DownloadTokenAPI.php?action=get_info&token={token}",
    "create_download_token" => "$base_url/DownloadTokenAPI.php",
    "submit_feedback" => "$base_url/FeedbackAPI.php",
    "download_pdf" => "$base_url/DownloadAPI.php?token={token}",
];

// --- Extract Query Parameters ---
$apiKey = $_GET['api'] ?? null;
$token = $_GET['token'] ?? null;
$email = $_GET['email'] ?? null;
$childId = $_GET['child_id'] ?? null;
$worksheetId = $_GET['worksheet_id'] ?? null; 

if (!$apiKey || !isset($apiEndpoints[$apiKey])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or missing API key."]);
    exit();
}

// --- Parameter Validation ---
// Skip ALL validation for authentication routes
$authRoutes = [
    'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
    'request_login', 'verify_login', 'JWT_token', 'signup'
];

if (!in_array($apiKey, $authRoutes)) {
    // Only validate parameters for non-auth routes
    if (strpos($apiEndpoints[$apiKey], '{child_id}') !== false && !$childId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing child_id parameter."]);
        exit();
    }
    if (strpos($apiEndpoints[$apiKey], '{token}') !== false && !$token) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing token parameter."]);
        exit();
    }
    if (strpos($apiEndpoints[$apiKey], '{email}') !== false && !$email) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing email parameter."]);
        exit();
    }
    if (strpos($apiEndpoints[$apiKey], '{worksheet_id}') !== false && !$worksheetId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing worksheet_id parameter."]);
        exit();
    }
}

// Construct API URL
$apiUrl = str_replace(
    ['{token}', '{email}', '{child_id}', '{worksheet_id}'],
    [$token, $email, $childId, $worksheetId],
    $apiEndpoints[$apiKey]
);

// Append any other GET parameters
$otherParams = array_diff_key($_GET, array_flip(['api', 'token', 'email', 'child_id', 'worksheet_id']));
if (!empty($otherParams)) {
    $apiUrl .= (strpos($apiUrl, '?') === false ? '?' : '&') . http_build_query($otherParams);
}

// Debug logging for auth_login
if ($apiKey === 'auth_login') {
    error_log("DEBUG: Final apiUrl for auth_login = $apiUrl");
    error_log("DEBUG: Request method = " . $_SERVER['REQUEST_METHOD']);
    error_log("DEBUG: Input data = " . file_get_contents("php://input"));
}

// --- Handle Yes Homework API Calls ---
$yesHomeworkApis = [
    'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
    'request_login', 'verify_login', 'JWT_token', 'signup',
    'get_user_profile', 'update_user_profile',
    'get_children', 'add_child', 'update_child', 'delete_child', 'get_child_worksheets', 'get_child_feedback_summary', 'get_child_completion_streak', 'preview_child_worksheet',
    'get_user_worksheets', 'create_worksheet', 'get_worksheet', 'update_worksheet', 'delete_worksheet', 'download_worksheet', 'get_worksheet_stats', 'generate_child_worksheet', 'generate_worksheets_bulk', 'send_welcome_email',
    'get_download_token', 'get_token_previous_worksheet', 'generate_from_token',
    'submit_feedback_v2', 'get_feedback',
    'get_token_info', 'create_download_token', 'submit_feedback', 'download_pdf',
    'test_openai', 'debug_env', 'health_check'
];

// Check if this is a localhost environment for local API handling
$isLocalhost = in_array($host, ['localhost', '127.0.0.1', 'localhost:8080', '127.0.0.1:8080']) || 
               strpos($host, 'localhost:') === 0 || 
               strpos($host, '127.0.0.1:') === 0;

// Debug logging for auth_login
if ($apiKey === 'auth_login') {
    error_log("DEBUG: host = $host");
    error_log("DEBUG: isLocalhost = " . ($isLocalhost ? 'true' : 'false'));
    error_log("DEBUG: Will use " . ($isLocalhost ? 'local API' : 'remote API'));
}

if (in_array($apiKey, $yesHomeworkApis) && $isLocalhost) {
    // Handle localhost requests by including local API files directly
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // Determine which API file to include
    $yesHomeworkMainApis = [
        'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
        'request_login', 'verify_login', 'JWT_token', 'signup',
        'get_user_profile', 'update_user_profile',
        'get_children', 'add_child', 'update_child', 'delete_child', 'get_child_worksheets', 'get_child_feedback_summary', 'get_child_completion_streak', 'preview_child_worksheet',
        'get_user_worksheets', 'create_worksheet', 'get_worksheet', 'update_worksheet', 'delete_worksheet', 'download_worksheet', 'get_worksheet_stats', 'generate_child_worksheet', 'generate_worksheets_bulk', 'send_welcome_email',
        'get_download_token', 'get_token_previous_worksheet', 'generate_from_token',
        'submit_feedback_v2', 'get_feedback',
        'test_openai', 'debug_env', 'health_check'
    ];
    
    if (in_array($apiKey, $yesHomeworkMainApis)) {
        // Routes that go through /api/index.php
        $localApiPath = $documentRoot . '/api/index.php';
        
        if (file_exists($localApiPath)) {
            // Extract the route path from the full URL
            $parsedUrl = parse_url($apiUrl);
            $routePath = $parsedUrl['path'];
            
            // Set up environment for the API router
            $_SERVER['REQUEST_URI'] = $routePath . (!empty($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
            $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            
            // For GET requests with special parameters (like auth_verify)
            if ($apiKey === 'auth_verify' && $email && $token) {
                $_GET['email'] = $email;
                $_GET['token'] = $token;
            }
            
            // Temporarily change working directory and include the API
            $oldCwd = getcwd();
            chdir(dirname($localApiPath));
            
            // Capture output
            ob_start();
            include $localApiPath;
            $response = ob_get_contents();
            ob_end_clean();
            
            // Restore working directory
            chdir($oldCwd);
            
            // Output the response
            header("Content-Type: application/json");
            echo $response;
            exit();
        }
    } else {
        // Direct API files (DownloadAPI, etc.)
        $parsedUrl = parse_url($apiUrl);
        $localApiPath = $documentRoot . $parsedUrl['path'];
        
        // Debug logging for all direct API calls
        error_log("Direct API Debug: API Key = $apiKey");
        error_log("Direct API Debug: API URL = $apiUrl");
        error_log("Direct API Debug: Parsed URL path = " . $parsedUrl['path']);
        error_log("Direct API Debug: Document Root = $documentRoot");
        error_log("Direct API Debug: Local API Path = $localApiPath");
        error_log("Direct API Debug: File exists = " . (file_exists($localApiPath) ? 'yes' : 'no'));
        
        if (file_exists($localApiPath)) {
            // Temporarily change working directory and include the API
            $oldCwd = getcwd();
            chdir(dirname($localApiPath));
            
            // Special handling for PDF downloads - don't buffer output
            if ($apiKey === 'download_pdf') {
                // Debug logging for PDF downloads
                error_log("PDF Download Debug: API Key = $apiKey");
                error_log("PDF Download Debug: Local API Path = $localApiPath");
                error_log("PDF Download Debug: Token = $token");
                error_log("PDF Download Debug: File exists = " . (file_exists($localApiPath) ? 'yes' : 'no'));
                
                // Stream PDF directly without buffering
                include $localApiPath;
            } else {
                // Capture output for JSON APIs
                ob_start();
                include $localApiPath;
                $response = ob_get_contents();
                ob_end_clean();
                
                // Output the response
                header("Content-Type: application/json");
                echo $response;
            }
            
            // Restore working directory
            chdir($oldCwd);
            exit();
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Local API file not found: $localApiPath"]);
            exit();
        }
    }
}

// --- Handle Remote API Requests ---
$inputData = file_get_contents("php://input");
$method = $_SERVER['REQUEST_METHOD'];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: $jwt_token"
]);

switch ($method) {
    case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
        break;
    case 'PUT':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
        break;
    case 'DELETE':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inputData);
        break;
    case 'GET':
        // No extra setup needed for GET
        break;
    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed."]);
        exit();
}

// Execute API Request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    error_log("cURL Error: " . curl_error($ch));
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "cURL Error: " . curl_error($ch)]);
    curl_close($ch);
    exit();
}

curl_close($ch);

// Forward API Response
header("Content-Type: application/json");
http_response_code($httpCode);
echo $response;

} catch (Exception $e) {
    // Log the error server-side
    error_log("Proxy server exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Return a proper JSON error response
    header("Content-Type: application/json");
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error occurred: " . $e->getMessage()
    ]);
}
?>
