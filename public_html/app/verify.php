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
    <title>Verifying Login - ExactSum</title>
    
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
            
            console.log(email);
            console.log(token);

    
            try {
                // Step 1: Verify the email token
                console.log("Verifying email token...");
                const verifyResponse = await fetch(`/api/auth/verify?email=${encodeURIComponent(email)}&token=${token}`);
                const verifyData = await verifyResponse.json();
                console.log("Verification response:", verifyData);
                
                if (verifyData.status !== 'success') {
                    throw new Error(verifyData.message || 'Verification failed');
                }
                
                // Check if this is an email client crawler accessing the link
                if (verifyData.crawler_access === true) {
                    console.log("Email client crawler detected - showing success page");
                    document.getElementById('loading-container').innerHTML = `
                        <h2 class="var-head-content text-3xl">Link Verified</h2>
                        <p class="var-text-content text-lg">Your invitation link is valid and ready to use.</p>
                        <p class="text-sm text-gray-500 mt-4">Please click the link in your email from your browser to complete the login process.</p>
                    `;
                    return; // Don't proceed with authentication for crawler access
                }
                
                // Get the user_id from the verification response
                const userId = verifyData.user_id;
                
                if (!userId) {
                    throw new Error('User ID not returned from verification');
                }
                
                // Step 2: Get a JWT token using the user_id
                console.log("Getting JWT token with user_id:", userId);
                const tokenResponse = await fetch('/api/auth/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        user_id: userId,
                        email: email 
                    }),
                });
                
                const tokenData = await tokenResponse.json();
                console.log("Token response:", tokenData);
                
                if (tokenData.status !== 'success' || !tokenData.token) {
                    throw new Error(tokenData.message || 'Failed to get token');
                }
                
                // Save JWT token and user info to localStorage
                localStorage.setItem('jwt_token', tokenData.token);
                
                if (tokenData.user) {
                    localStorage.setItem('user_id', tokenData.user.user_id);
                    localStorage.setItem('org_id', tokenData.user.org_id);
                    localStorage.setItem('user_email', tokenData.user.email);
                    localStorage.setItem('user_role', tokenData.user.role);
                }
                
                console.log("Authentication successful, redirecting to dashboard");
                // Redirect to dashboard
                window.location.href = '/app/vault.php';
                
            } catch (error) {
                console.error('Authentication error:', error);
                document.getElementById('error-message').textContent = error.message;
                document.getElementById('error-container').style.display = 'block';
                document.getElementById('loading-container').style.display = 'none';
            }
        }
        
        // Run authentication on page load
        window.onload = getJwtToken;
    </script>
    <style>
        body,h2,p{
            margin: 0 !important;
            padding: 0 !important;
        }
        .main-cont {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        #error-container {
            display: none;
            color: red;
        }
        .loader-container {
            display: flex;
            justify-content: center; /* Center horizontally */
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
            background-position: 0px center, 15px center, 30px center, 45px center, 60px center, 75px center, 90px center;
            animation: rikSpikeRoll 0.65s linear infinite alternate;
          }
        @keyframes rikSpikeRoll {
          0% { background-size: 10px 3px;}
          16% { background-size: 10px 50px, 10px 3px, 10px 3px, 10px 3px, 10px 3px, 10px 3px}
          33% { background-size: 10px 30px, 10px 50px, 10px 3px, 10px 3px, 10px 3px, 10px 3px}
          50% { background-size: 10px 10px, 10px 30px, 10px 50px, 10px 3px, 10px 3px, 10px 3px}
          66% { background-size: 10px 3px, 10px 10px, 10px 30px, 10px 50px, 10px 3px, 10px 3px}
          83% { background-size: 10px 3px, 10px 3px,  10px 10px, 10px 30px, 10px 50px, 10px 3px}
          100% { background-size: 10px 3px, 10px 3px, 10px 3px,  10px 10px, 10px 30px, 10px 50px}
        }
        .var-head-content{
            font-size: 2rem;
            margin-bottom: 1rem !important;
        }
        .var-text-content{
            font-size: 1.2rem;
            margin-bottom: 2.5rem !important;
        }
        .x_icon{
            width: 5rem;
            margin-bottom: 1rem !important;
        }
        .err-head-content{
            font-size: 2rem;
            margin-bottom: 1rem !important;
        }
        .err-text-content{
            font-size: 1.2rem;
            color: #FF4C4C;
            text-decoration: underline;
            margin-bottom: 1.8rem !important;
        }
        /* Back button */
        .back-button {
          --b: 3px;   
          --s: .45em; 
          --color: #463AA2 ;
        
          padding: calc(.5em + var(--s)) calc(.9em + var(--s));
          color: var(--color);
          --_p: var(--s);
          background:
            conic-gradient(from 90deg at var(--b) var(--b),#0000 90deg,var(--color) 0)
            var(--_p) var(--_p)/calc(100% - var(--b) - 2*var(--_p)) calc(100% - var(--b) - 2*var(--_p));
          transition: .3s linear, color 0s, background-color 0s;
          outline: var(--b) solid #0000;
          outline-offset: .6em;
          font-size: 20px;
          font-weight: 600;
          border: 0;
        
          user-select: none;
          -webkit-user-select: none;
          touch-action: manipulation;
          cursor: pointer;
        }

        .back-button:hover,
        .back-button:focus-visible{
          --_p: 0px;
          outline-color: var(--color);
          outline-offset: .05em;
        }

        .back-button:active {
          background: var(--color);
          color: #fff;
        }
    </style>

    
    <!-- DaisyUI & TailwindCSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body>
    <div class="main-cont bg-base-300">

        <div id="loading-container" class="m-5 p-5 sm:p-10 rounded-md space-y-2 border border-gray-400/50 bg-base-100 shadow-2xl">
            <h2 class="var-head-content text-3xl">Verifying your login</h2>
            <p class="var-text-content text-lg">Please wait while we complete the authentication process.</p>
            <div class="loader-container">
                <div class="loader"></div>
            </div>
        </div>
        
        <div id="error-container" class="m-5 p-5 sm:p-10 rounded-md space-y-2 border border-gray-400/50 bg-base-100 shadow-2xl">
            <img src="./assets/icons/x_icon.png" alt="x-icon" class="x_icon mx-auto">
            <h2 class="err-head-content text-3xl">Authentication Error!</h2>
            <p id="error-message" class="err-text-content"></p>
            <a href="./login.php">
                <button class="back-button" role="button">Return to Login</button>
            </a>
        </div>
        
    </div>
</body>
</html>