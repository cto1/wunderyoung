<?php
require_once 'env.php';
require_once 'UserAuthAPI.php';

echo "Testing Email Functionality\n";
echo "==========================\n\n";

// Create UserAuthAPI instance
$userAuth = new UserAuthAPI();

// Test 1: First create a user
echo "1. Creating test user...\n";
$signupResult = $userAuth->signup(['email' => 'test@example.com']);
print_r($signupResult);
echo "\n";

// Test 2: Request login (this should send email)
echo "2. Requesting login (this will test email sending)...\n";
$loginResult = $userAuth->requestLogin('test@example.com');
print_r($loginResult);
echo "\n";

echo "Check the error log and/or your email for results!\n"; 