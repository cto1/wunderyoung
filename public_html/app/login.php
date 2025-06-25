<?php 
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include 'include/translations.php'; 
require_once 'config.php';

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Daily Homework</title>
    <meta name="description" content="Sign in to your Daily Homework account and manage your child's learning journey.">

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

    <!--  Favicons -->
    <link rel="icon" type="image/png" href="assets/favicons/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicons/apple-touch-icon.png">
    <link rel="shortcut icon" href="assets/favicons/favicon.ico">

    <!-- Font-Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- DaisyUI & TailwindCSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Include SheetJS CDN / xlsx library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-gradient-to-br from-blue-50 to-purple-50">

    <!-- Navigation -->
    <nav class="navbar bg-white shadow-lg">
        <div class="navbar-start">
            <a href="/website/" class="btn btn-ghost text-xl font-bold text-primary">
                <i class="fas fa-home mr-2"></i>
                DailyHome.Work
            </a>
        </div>
        <div class="navbar-end">
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="min-h-screen flex justify-center items-center p-5">
        <div class="w-full max-w-md">
            
            <!-- Welcome Card -->
            <div class="card bg-white shadow-2xl">
                <div class="card-body p-8">
                    
                    <!-- Logo -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-book-open text-white text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800">Welcome Back!</h1>
                        <p class="text-gray-600 mt-2">Sign in to continue your child's learning journey</p>
                    </div>

                    <!-- Login Form -->
                    <div class="space-y-6">
                        
                        <!-- Email Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email Address</span>
                            </label>
                            <div class="relative">
                                <input type="email" id="loginEmailInput"
                                    class="input input-bordered w-full pr-12" 
                                    placeholder="parent@email.com" 
                                    autocomplete="email">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div id="passwordField" class="form-control">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="loginPasswordInput"
                                    class="input input-bordered w-full pr-12" 
                                    placeholder="Enter your password" 
                                    autocomplete="current-password">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Auth method toggle -->
                        <div class="text-center">
                            <button type="button" id="toggleAuthMethod" class="text-sm text-primary underline hover:text-primary/80">
                                Or use magic link instead
                            </button>
                        </div>

                        <!-- Message Container -->
                        <div id="messageContainer" class="hidden">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span id="messageText"></span>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button id="loginButton" class="btn btn-primary w-full btn-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>

                        <!-- Sign Up Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account?
                                <a href="signup.php" class="font-medium text-primary underline hover:text-primary/80">
                                    Create one for free
                                </a>
                            </p>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Benefits Reminder -->
            <div class="mt-8 text-center">
                <div class="bg-white/80 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Why Daily Homework?</h3>
                    <div class="grid grid-cols-1 gap-3 text-sm text-gray-600">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Daily worksheets delivered to your inbox</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Personalized for your child's interests</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>No apps, no screens - just print and learn</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer footer-center p-4 bg-white/80 backdrop-blur-sm text-base-content">
        <nav class="grid grid-flow-col gap-4">
            <a href="/privacy.php" class="link link-hover">Privacy Policy</a>
            <a href="/terms.php" class="link link-hover">Terms of Service</a>
            <a href="mailto:support@dailyhome.work" class="link link-hover">Contact</a>
        </nav>
    </footer>

    <!-- Authentication Scripts -->
    <script src="js/authentication-scripts/login.js"></script>

</body>

</html>
