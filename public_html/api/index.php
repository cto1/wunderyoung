<?php
// index.php
require_once 'env.php';
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
    ini_set('error_log', '/var/www/parsebank/logs/error.log');
}

header('Content-Type: application/json');

require_once 'vendor/autoload.php';

//try {
  //$this->functionFailsForSure();
//} catch (\Throwable $exception) {
  //\Sentry\captureException($exception);
//}

require_once 'conf.php';
require_once 'Router.php';
require_once 'OrgAPI.php';
require_once 'UserAuthAPI.php';
require_once 'SettingsAPI.php';
require_once 'JWTAuth.php';
require_once 'AuthMiddleware.php';

require_once 'VaultAPI.php';
require_once 'VaultOcrAPI.php';
require_once 'BankStatementAI_API.php';
require_once 'UsageTracker.php';

// Initialize APIs and Router
$orgAPI = new OrgAPI();
$userAuthAPI = new UserAuthAPI();
$router = new Router();
$settingsAPI = new SettingsAPI();
$jwtAuth = new JWTAuth();
$vaultAPI = new VaultAPI();
$vaultOcrAPI = new VaultOcrAPI();
$bankStatementAPI = new BankStatementAI_API();
$usageTracker = new UsageTracker();

// Include and register all route files AFTER the router is initialized
require_once 'public_routes.php';
require_once 'vault_routes.php';
require_once 'vault_ocr_routes.php';
require_once 'usage_routes.php';
require_once 'bank_statement_routes.php';
require_once 'company_routes.php';
require_once 'ideas_routes.php';

// Refresh token endpoint (protected)
$router->addRoute('POST', '/auth/refresh-token', function($params, $data, $context) use ($jwtAuth) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    // Generate a new token using current user data
    $newToken = $jwtAuth->generateToken($context['userData']);
    
    return [
        'status' => 'success',
        'token' => $newToken
    ];
});

// Password management endpoints (protected)
$router->addRoute('GET', '/auth/password-status', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    return $userAuthAPI->hasPassword($context['userData']['user_id']);
});

$router->addRoute('POST', '/auth/set-password', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    if (!isset($data['password'])) {
        return [
            'status' => 'error',
            'message' => 'Password is required'
        ];
    }
    
    return $userAuthAPI->setPassword($context['userData']['user_id'], $data['password']);
});

$router->addRoute('POST', '/auth/change-password', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    if (!isset($data['current_password']) || !isset($data['new_password'])) {
        return [
            'status' => 'error',
            'message' => 'Current and new passwords are required'
        ];
    }
    
    return $userAuthAPI->changePassword(
        $context['userData']['user_id'], 
        $data['current_password'], 
        $data['new_password']
    );
});

$router->addRoute('POST', '/auth/remove-password', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    if (!isset($data['current_password'])) {
        return [
            'status' => 'error',
            'message' => 'Current password is required'
        ];
    }
    
    return $userAuthAPI->removePassword($context['userData']['user_id'], $data['current_password']);
});

// Admin reset user password endpoint (admin/owner only)
$router->addRoute('POST', '/auth/admin-reset-password', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    // Only admins and owners can reset passwords
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Only admins and owners can reset user passwords'
        ];
    }
    
    if (!isset($data['target_user_id']) || !isset($data['new_password'])) {
        return [
            'status' => 'error',
            'message' => 'Target user ID and new password are required'
        ];
    }
    
    return $userAuthAPI->adminResetPassword(
        $context['userData']['user_id'], 
        $data['target_user_id'], 
        $data['new_password']
    );
});

// Security monitoring endpoint (admin/owner only)
$router->addRoute('GET', '/auth/login-attempts', function($params, $data, $context) use ($userAuthAPI) {
    if (!isset($context['userData'])) {
        return [
            'status' => 'error',
            'message' => 'Authentication required'
        ];
    }
    
    // Only admins and owners can view login attempts
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Insufficient permissions'
        ];
    }
    
    return $userAuthAPI->getLoginAttempts($context['userData']['org_id']);
});

// Organization Routes (protected)
$router->addRoute('POST', '/organizations', function($params, $data, $context) use ($orgAPI) {
    return $orgAPI->create($data);
});

$router->addRoute('GET', '/organizations/{org_id}', function($params, $data, $context) use ($orgAPI) {
    return $orgAPI->read($params['org_id']);
});

$router->addRoute('PUT', '/organizations/{org_id}', function($params, $data, $context) use ($orgAPI) {
    return $orgAPI->update($params['org_id'], $data);
});

$router->addRoute('DELETE', '/organizations/{org_id}', function($params, $data, $context) use ($orgAPI) {
    // Verify user has owner role
    if ($context['userData']['role'] !== 'owner') {
        return [
            'status' => 'error',
            'message' => 'Only organization owners can delete organizations'
        ];
    }
    return $orgAPI->delete($params['org_id']);
});

// Get all organizations (admin only)
$router->addRoute('GET', '/organizations', function($params, $data, $context) use ($orgAPI) {
    // Verify user has appropriate role
    if ($context['userData']['role'] !== 'owner') {
        return [
            'status' => 'error',
            'message' => 'Unauthorized access'
        ];
    }
    return $orgAPI->getAllOrgs();
});

// User Routes (protected)
$router->addRoute('GET', '/auth/{org_id}/users', function($params, $data, $context) use ($userAuthAPI) {
    return $userAuthAPI->getOrgUsers($params['org_id']);
});

$router->addRoute('PUT', '/users/{user_id}', function($params, $data, $context) use ($userAuthAPI) {
    // Users can only update their own profile, unless they're an admin/owner
    if ($params['user_id'] !== $context['userData']['user_id'] && 
        !in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'You can only update your own profile'
        ];
    }
    return $userAuthAPI->updateProfile($params['user_id'], $data);
});

$router->addRoute('POST', '/organizations/{org_id}/users', function($params, $data, $context) use ($userAuthAPI) {
    // Verify user data exists
    if (!isset($context['userData']) || !isset($context['userData']['user_id']) || !isset($context['userData']['role'])) {
        return [
            'status' => 'error',
            'message' => 'Invalid authentication data'
        ];
    }
    
    // Only admins and owners can invite users
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Only admins and owners can invite users'
        ];
    }
    
    $inviterId = $context['userData']['user_id'];
    return $userAuthAPI->inviteUser($params['org_id'], $data, $inviterId);
});

$router->addRoute('DELETE', '/organizations/{org_id}/users/{user_id}', function($params, $data, $context) use ($userAuthAPI) {
    // Only admins and owners can delete users
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Only admins and owners can delete users'
        ];
    }
    
    $deleterId = $context['userData']['user_id'];
    return $userAuthAPI->deleteUser($params['org_id'], $params['user_id'], $deleterId);
});

$router->addRoute('PUT', '/organizations/{org_id}/users/{user_id}/role', function($params, $data, $context) use ($userAuthAPI) {
    // Verify user is authenticated and has provided a new role
    if (!isset($context['userData']) || !isset($data['role'])) {
        return [
            'status' => 'error',
            'message' => 'Missing required data'
        ];
    }
    
    return $userAuthAPI->changeUserRole(
        $params['org_id'],
        $params['user_id'],
        $data['role'],
        $context['userData']['user_id']
    );
});

// Settings Routes (protected)
$router->addRoute('GET', '/organizations/{org_id}/settings', function($params, $data, $context) use ($settingsAPI) {
    return $settingsAPI->getSettings($params['org_id']);
});

$router->addRoute('PUT', '/organizations/{org_id}/settings', function($params, $data, $context) use ($settingsAPI) {
    // Only admins and owners can update settings
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Only admins and owners can update organization settings'
        ];
    }
    
    return $settingsAPI->saveSettings($params['org_id'], $data);
});

$router->addRoute('POST', '/organizations/{org_id}/settings/reset', function($params, $data, $context) use ($settingsAPI) {
    // Only admins and owners can reset settings
    if (!in_array($context['userData']['role'], ['owner', 'admin'])) {
        return [
            'status' => 'error',
            'message' => 'Only admins and owners can reset organization settings'
        ];
    }
    
    return $settingsAPI->resetSettings($params['org_id']);
});

// Handle the request
$response = $router->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
echo json_encode($response);