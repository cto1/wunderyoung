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
?>

<!DOCTYPE html>
<html data-theme="winter">
<head>
    <title>Verifying Login - Yes Homework</title>
    
    <?php
    // Add Hotjar tracking code only for production environment (exactsum.com)
    $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    if (strpos($domain, 'exactsum.com') !== false && strpos($domain, 'demo.') !== 0) :
    ?>
    <!-- Hotjar Tracking Code for https://exactsum.com/app/ -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:6426506,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ERN0YG8LEM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-ERN0YG8LEM');
    </script>
    <?php endif; ?>

    <script>
        // Function to get JWT token using email verification token
        async function getJwtToken() {
            const email = "<?php echo htmlspecialchars($email); ?>";
            const token = "<?php echo htmlspecialchars($token); ?>";
            
            console.log("Email:", email);
            console.log("Token:", token);

            try {
                // Step 1: Verify the email token
                console.log("Verifying email token...");
                const verifyResponse = await fetch(`/api/auth/verify?email=${encodeURIComponent(email)}&token=${token}`);
                const verifyData = await verifyResponse.json();
                console.log("Verification response:", verifyData);
                
                if (verifyData.status !== 'success') {
                    throw new Error(verifyData.message || 'Verification failed');
                }
                
                // Step 2: Get a JWT token using the verified user data
                console.log("Getting JWT token...");
                const tokenResponse = await fetch('/api/auth/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        id: verifyData.id,
                        email: verifyData.email 
                    }),
                });
                
                const tokenData = await tokenResponse.json();
                console.log("Token response:", tokenData);
                
                if (tokenData.status !== 'success' || !tokenData.token) {
                    throw new Error(tokenData.message || 'Failed to get token');
                }
                
                // Save JWT token and user info to localStorage
                localStorage.setItem('jwt_token', tokenData.token);
                localStorage.setItem('user_id', verifyData.id);
                localStorage.setItem('user_email', verifyData.email);
                localStorage.setItem('user_plan', verifyData.plan);
                
                console.log("Authentication successful!");
                
                // Show success message instead of auto-redirect
                document.getElementById('loading-container').style.display = 'none';
                document.getElementById('success-container').style.display = 'block';
                
            } catch (error) {
                console.error('Authentication error:', error);
                const errorMessage = error.message;
                const errorContainer = document.getElementById('error-container');
                const loadingContainer = document.getElementById('loading-container');
                
                // Show appropriate error message and actions
                document.getElementById('error-message').textContent = errorMessage;
                
                // Show different actions based on error type
                const errorActions = document.getElementById('error-actions');
                if (errorMessage.includes('already been used') || errorMessage.includes('expired')) {
                    errorActions.innerHTML = `
                        <div class="text-center space-y-3">
                            <a href="/app/login.php" class="btn btn-modern btn-wide">
                                <i class="fas fa-magic mr-2"></i>
                                Request New Login Link
                            </a>
                            <p class="text-sm text-gray-500">Or try logging in with your password if you have one</p>
                            <a href="/app/login.php?tab=password" class="btn btn-outline btn-sm">
                                <i class="fas fa-key mr-2"></i>
                                Use Password Instead
                            </a>
                        </div>
                    `;
                } else {
                    errorActions.innerHTML = `
                        <div class="text-center space-y-3">
                            <a href="/app/login.php" class="btn btn-modern btn-wide">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Try Logging In Again
                            </a>
                        </div>
                    `;
                }
                
                errorContainer.style.display = 'block';
                loadingContainer.style.display = 'none';
            }
        }
        
        // Manual verification for debugging
        window.onload = function() {
            // Show manual verification option
            document.getElementById('loading-container').style.display = 'none';
            document.getElementById('manual-verify-container').style.display = 'block';
        };
        
        function manualVerify() {
            document.getElementById('manual-verify-container').style.display = 'none';
            document.getElementById('loading-container').style.display = 'block';
            getJwtToken();
        }
    </script>
    <style>
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
        }
        
        #loading-container {
            text-align: center;
        }
        
        #error-container {
            display: none;
        }
        
        .loader {
            width: 85px;
            height: 50px;
            background-repeat: no-repeat;
            background-image: linear-gradient(#463AA2 50px, transparent 0),
                              linear-gradient(#463AA2 50px, transparent 0),
                              linear-gradient(#463AA2 50px, transparent 0),
                              linear-gradient(#463AA2 50px, transparent 0),
                              linear-gradient(#463AA2 50px, transparent 0),
                              linear-gradient(#463AA2 50px, transparent 0);
            background-position: 0px center, 15px center, 30px center, 45px center, 60px center, 75px center;
            animation: rikSpikeRoll 0.65s linear infinite alternate;
            margin: 0 auto 2rem auto;
        }
        
        @keyframes rikSpikeRoll {
            0% { background-size: 10px 3px; }
            16% { background-size: 10px 50px, 10px 3px, 10px 3px, 10px 3px, 10px 3px, 10px 3px; }
            33% { background-size: 10px 30px, 10px 50px, 10px 3px, 10px 3px, 10px 3px, 10px 3px; }
            50% { background-size: 10px 10px, 10px 30px, 10px 50px, 10px 3px, 10px 3px, 10px 3px; }
            66% { background-size: 10px 3px, 10px 10px, 10px 30px, 10px 50px, 10px 3px, 10px 3px; }
            83% { background-size: 10px 3px, 10px 3px, 10px 10px, 10px 30px, 10px 50px, 10px 3px; }
            100% { background-size: 10px 3px, 10px 3px, 10px 3px, 10px 10px, 10px 30px, 10px 50px; }
        }
        
        .verification-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #51cf66, #40c057);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 30px rgba(81, 207, 102, 0.3);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #463AA2, #5b4fc7);
            border: none;
            color: white;
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(70, 58, 162, 0.3);
        }
        
        .btn-modern:hover {
            background: linear-gradient(135deg, #5b4fc7, #463AA2);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(70, 58, 162, 0.4);
            color: white;
        }
    </style>

    
    <!-- DaisyUI & TailwindCSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
</head>
<body>
    <div class="main-container">
        <!-- Manual Verification State -->
        <div id="manual-verify-container">
            <div class="card verification-card shadow-2xl p-8 w-full max-w-md">
                <div class="success-icon">
                    <i class="fas fa-bug text-white text-2xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">
                    Debug Verification
                </h2>
                
                <div class="alert alert-info mb-6">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <div class="font-bold">Link Details:</div>
                        <div class="text-sm">
                            <div><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
                            <div><strong>Token:</strong> <?php echo htmlspecialchars(substr($token, 0, 16) . '...'); ?></div>
                        </div>
                    </div>
                </div>
                
                <button onclick="manualVerify()" class="btn btn-modern btn-wide mb-4">
                    <i class="fas fa-play mr-2"></i>
                    Start Verification
                </button>
                
                <div class="text-center">
                    <a href="/app/login.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Loading State -->
        <div id="loading-container" style="display: none;">
            <div class="card verification-card shadow-2xl p-8 w-full max-w-md">
                <div class="success-icon pulse-animation">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                
                <div class="loader mb-6"></div>
                
                <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">
                    Verifying Your Login
                </h2>
                
                <p class="text-center text-gray-600 text-lg mb-6">
                    Please wait while we securely verify your login link...
                </p>
                
                <div class="flex justify-center">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Success State -->
        <div id="success-container" style="display: none;">
            <div class="card verification-card shadow-2xl p-8 w-full max-w-md">
                <div class="success-icon">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">
                    Verification Successful!
                </h2>
                
                <div class="alert alert-success mb-6">
                    <i class="fas fa-check-circle"></i>
                    <span>Your account has been verified and you're now logged in.</span>
                </div>
                
                <div class="text-center space-y-3">
                    <a href="/app/worksheets.php" class="btn btn-modern btn-wide">
                        <i class="fas fa-file-alt mr-2"></i>
                        Go to Worksheets
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Error State -->  
        <div id="error-container">
            <div class="card verification-card shadow-2xl p-8 w-full max-w-md">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">
                    Verification Failed
                </h2>
                
                <div class="alert alert-error mb-6">
                    <i class="fas fa-times-circle"></i>
                    <span id="error-message">An error occurred during verification.</span>
                </div>
                
                <div id="error-actions" class="text-center space-y-3">
                    <a href="/app/login.php" class="btn btn-modern btn-wide">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Try Logging In Again
                    </a>
                </div>
                
                <div class="divider mt-6"></div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-3">Need help?</p>
                    <div class="flex justify-center space-x-4 text-sm">
                        <a href="/website/" class="link link-primary">
                            <i class="fas fa-home mr-1"></i>
                            Home
                        </a>
                        <a href="mailto:support@yeshomework.com" class="link link-primary">
                            <i class="fas fa-envelope mr-1"></i>
                            Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>