<?php
// Disable displaying errors to output (important for JSON responses)
ini_set('display_errors', 0);
// But still log errors to the error log
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

// if (!$jwt_token) {
//     http_response_code(401);
//     echo json_encode(["status" => "error", "message" => "Missing JWT token in request headers."]);
//     exit();
// }

// --- API Endpoints Mapping ---
$apiEndpoints = [

    // ----- [Login Flow] -----
    "request_login" => "$base_url/auth/login",
    "verify_login" => "$base_url/auth/verify?email={email}&token={token}",
    "JWT_token" => "$base_url/auth/token",

    
    // ----- [Organization] -----
    "get_organization" => "$base_url/organizations/{org_id}",
    "update_organization" => "$base_url/organizations/{org_id}",


    // ----- [Users] -----
    "signup" => "$base_url/auth/signup",


    
   
    
    // ----- [Yes Homework - Authentication Routes] -----
    "auth_signup" => "$base_url/auth/signup",
    "auth_login" => "$base_url/auth/login", 
    "auth_verify" => "$base_url/auth/verify?email={email}&token={token}",
    "auth_token" => "$base_url/auth/token",
    "auth_password_login" => "$base_url/auth/password-login",
    "auth_refresh_token" => "$base_url/auth/refresh-token",
    
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
$orgId = $_GET['org_id'] ?? null;
$token = $_GET['token'] ?? null;
$email = $_GET['email'] ?? null;
$userId = $_GET['user_id'] ?? null;
$projectId = $_GET['project_id'] ?? null;
$fileId = $_GET['file_id'] ?? null;
$companyNumber = $_GET['company_number'] ?? null;
$ideaId = $_GET['idea_id'] ?? null;
$filter = $_GET['filter'] ?? null;
$childId = $_GET['child_id'] ?? null;
$worksheetId = $_GET['worksheet_id'] ?? null; 


if (!$apiKey || !isset($apiEndpoints[$apiKey])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or missing API key."]);
    exit();
}



// Validate Required Parameters
if (strpos($apiEndpoints[$apiKey], '{org_id}') !== false && !$orgId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing org_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{token}') !== false && !$token) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing token parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{user_id}') !== false && !$userId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing user_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{project_id}') !== false && !$projectId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing project_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{file_id}') !== false && !$fileId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing file_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{company_number}') !== false && !$companyNumber) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing company_number parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{idea_id}') !== false && !$ideaId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing idea_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{filter}') !== false && !$filter) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing filter parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{email}') !== false && !$email) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing email parameter."]);
    exit();
}
// Skip child_id validation for auth and other routes that don't need it
$routesWithoutChildId = [
    'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
    'signup', 'request_login', 'verify_login', 'JWT_token',
    'get_user_profile', 'update_user_profile', 'get_children', 'add_child',
    'get_user_worksheets', 'create_worksheet', 'get_worksheet_stats',
    'generate_worksheets_bulk', 'send_welcome_email',
    'submit_feedback_v2', 'test_openai', 'debug_env', 'health_check',
    'get_token_info', 'create_download_token', 'submit_feedback', 'download_pdf'
];

if (strpos($apiEndpoints[$apiKey], '{child_id}') !== false && !$childId && !in_array($apiKey, $routesWithoutChildId)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing child_id parameter."]);
    exit();
}
if (strpos($apiEndpoints[$apiKey], '{worksheet_id}') !== false && !$worksheetId) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing worksheet_id parameter."]);
    exit();
}

// Construct API URL
$apiUrl = str_replace(
    ['{org_id}', '{token}', '{email}', '{user_id}', '{project_id}', '{file_id}', '{company_number}', '{idea_id}', '{filter}', '{child_id}', '{worksheet_id}'],
    [$orgId, $token, $email, $userId, $projectId, $fileId, $companyNumber, $ideaId, $filter, $childId, $worksheetId],
    $apiEndpoints[$apiKey]
);

// Append any other GET parameters (like for filtering or pagination)
$otherParams = array_diff_key($_GET, array_flip(['api', 'org_id', 'token', 'email', 'user_id', 'project_id', 'file_id', 'company_number', 'idea_id', 'filter', 'child_id', 'worksheet_id']));
if (!empty($otherParams)) {
    $apiUrl .= (strpos($apiUrl, '?') === false ? '?' : '&') . http_build_query($otherParams);
}

// --- Handle Yes Homework API Calls ---
$yesHomeworkApis = [
    // Authentication
    'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
    // User Profile
    'get_user_profile', 'update_user_profile',
    // Children Management
    'get_children', 'add_child', 'update_child', 'delete_child', 'get_child_worksheets', 'get_child_feedback_summary', 'get_child_completion_streak', 'preview_child_worksheet',
    // Worksheet Management
    'get_user_worksheets', 'create_worksheet', 'get_worksheet', 'update_worksheet', 'delete_worksheet', 'download_worksheet', 'get_worksheet_stats', 'generate_child_worksheet', 'generate_worksheets_bulk', 'send_welcome_email',
    // Download Tokens
    'get_download_token', 'get_token_previous_worksheet', 'generate_from_token',
    // Feedback
    'submit_feedback_v2', 'get_feedback',
    // Download System
    'get_token_info', 'create_download_token', 'submit_feedback', 'download_pdf',
    // Testing/Debug
    'test_openai', 'debug_env', 'health_check'
];

// Check if this is a localhost environment for local API handling
$isLocalhost = in_array($host, ['localhost', '127.0.0.1', 'localhost:8080', '127.0.0.1:8080']) || 
               strpos($host, 'localhost:') === 0 || 
               strpos($host, '127.0.0.1:') === 0;

if (in_array($apiKey, $yesHomeworkApis) && $isLocalhost) {
    // Handle localhost requests by including local API files directly
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // Determine which API file to include
    $yesHomeworkMainApis = [
        'auth_signup', 'auth_login', 'auth_verify', 'auth_token', 'auth_password_login', 'auth_refresh_token',
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
        
        if (file_exists($localApiPath)) {
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
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Local API file not found: $localApiPath"]);
            exit();
        }
    }
}

// --- Handle Vault File Upload Request [POST_BS_FILES] ---
if ($apiKey === 'POST_BS_FILES') {
    if (!isset($_FILES['files'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "No files were uploaded."]);
        exit();
    }

    $orgId = $_POST['org_id'] ?? null;
    $projectId = $_POST['project_id'] ?? null;

    if (!$orgId || !$projectId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing org_id or project_id."]);
        exit();
    }

    $uploadUrl = "$base_url/organizations/$orgId/vault/projects/$projectId/files";

    $multiCurl = [];
    $results = [];

    foreach ($_FILES['files']['tmp_name'] as $i => $tmpName) {
        $postData = [
            'file' => new CURLFile($tmpName, $_FILES['files']['type'][$i], $_FILES['files']['name'][$i])
        ];

        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $jwt_token"
        ]);

        $multiCurl[] = $ch;
    }

    $mh = curl_multi_init();
    foreach ($multiCurl as $ch) {
        curl_multi_add_handle($mh, $ch);
    }

    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    foreach ($multiCurl as $ch) {
        $response = curl_multi_getcontent($ch);
        $results[] = json_decode($response, true);
        curl_multi_remove_handle($mh, $ch);
    }

    curl_multi_close($mh);

    header("Content-Type: application/json");
    echo json_encode([
        "status" => "success",
        "uploaded" => count($results),
        "results" => $results
    ]);
    exit();
}

// --- Handle Other API Requests [OTHER] ---
else {
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
}

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
