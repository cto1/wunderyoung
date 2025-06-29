<?php
// verify.php - This page is where the email link redirects to

// Get query parameters
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

// If missing parameters, show error
if (empty($token) || empty($email)) {
    echo "Invalid verification link. Missing parameters.";
    exit;
}

$page_title = 'Verifying Login - Yes Homework';
$page_description = 'Verifying your login link';
include 'include/header.html';
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body text-center">
            <div id="loading-state">
                <span class="loading loading-spinner loading-lg text-primary mb-4"></span>
                <h2 class="card-title text-xl font-bold mb-2">Verifying Login...</h2>
                <p class="text-gray-600">Please wait while we verify your login link.</p>
            </div>

            <!-- Success State -->
            <div id="success-state" class="hidden">
                <div class="w-16 h-16 mx-auto mb-4 bg-success rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h2 class="card-title text-xl font-bold text-success mb-2 justify-center">Login Successful!</h2>
                <p class="text-gray-600 mb-4">Redirecting you to your worksheets...</p>
                <div class="loading loading-spinner loading-md text-success"></div>
            </div>

            <!-- Error State -->
            <div id="error-state" class="hidden">
                <div class="w-16 h-16 mx-auto mb-4 bg-error rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-white text-2xl"></i>
                </div>
                <h2 class="card-title text-xl font-bold text-error mb-2 justify-center">Verification Failed</h2>
                <p id="error-message" class="text-gray-600 mb-6 text-center"></p>
                
                <div class="space-y-2">
                    <a href="login.php" class="btn btn-primary btn-block">Request New Login Link</a>
                    <a href="signup.php" class="btn btn-outline btn-block">Create New Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Get email and token from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');
    const token = urlParams.get('token');

    if (!email || !token) {
        showError('Invalid verification link. Email and token are required.');
        return;
    }

    try {
        // Verify the login token
        const response = await fetch(`../api/auth/verify?email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}`);
        const result = await response.json();

        if (result.status === 'success') {
            // Store user data and redirect
            localStorage.setItem('authToken', 'authenticated');
            localStorage.setItem('user', JSON.stringify({
                id: result.id,
                email: result.email,
                plan: result.plan
            }));

            showSuccess();

            // Redirect after a short delay to show success state
            setTimeout(() => {
                window.location.href = 'worksheets.php';
            }, 2000);

        } else {
            showError(result.message || 'Verification failed');
        }

    } catch (error) {
        console.error('Verification error:', error);
        showError('Network error. Please try again or request a new login link.');
    }
});

function showSuccess() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('success-state').classList.remove('hidden');
}

function showError(message) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('success-state').classList.add('hidden');
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-state').classList.remove('hidden');
}
</script>

    <!-- DaisyUI & TailwindCSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TCCYXNZR2B"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-TCCYXNZR2B');
    </script>
    
</head>
<body>
</body>
</html>