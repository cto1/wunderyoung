<?php 
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include 'include/translations.php'; 

// Get email from URL parameter if provided
$prefilled_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started - Daily Homework</title>
    <meta name="description" content="Start your child's personalized learning journey with Daily Homework. Free worksheets delivered daily to your inbox.">

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
            <a href="login.php" class="btn btn-ghost mr-2">Sign In</a>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="min-h-screen flex justify-center items-center p-5">
        <div class="w-full max-w-md">
            
            <!-- Signup Card -->
            <div class="card bg-white shadow-2xl">
                <div class="card-body p-8">
                    
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800">Get Started Free!</h1>
                        <p class="text-gray-600 mt-2">Create your account and get your first worksheet in 2 minutes</p>
                    </div>

                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <ul class="steps steps-vertical lg:steps-horizontal w-full">
                            <li class="step step-primary">Email</li>
                            <li class="step">Child Info</li>
                            <li class="step">First Worksheet</li>
                        </ul>
                    </div>

                    <!-- Signup Form -->
                    <div class="space-y-6">
                        
                        <!-- Email Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Your Email Address</span>
                            </label>
                            <div class="relative">
                                <input type="email" id="signupEmailInput"
                                    class="input input-bordered w-full pr-12" 
                                    placeholder="parent@email.com" 
                                    value="<?php echo $prefilled_email; ?>"
                                    autocomplete="email">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-gray-500">We'll send your worksheets here</span>
                            </label>
                        </div>

                        <!-- Message Container -->
                        <div id="messageContainer" class="hidden">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span id="messageText"></span>
                            </div>
                        </div>

                        <!-- Signup Button -->
                        <button id="signupButton" class="btn btn-primary w-full btn-lg">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Continue - It's Free!
                        </button>

                        <!-- Benefits List -->
                        <div class="bg-green-50 rounded-lg p-4 mt-6">
                            <h3 class="font-semibold text-green-800 mb-3">What you'll get:</h3>
                            <ul class="space-y-2">
                                <li class="flex items-center text-sm text-green-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Daily personalized worksheets
                                </li>
                                <li class="flex items-center text-sm text-green-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    No apps or screen time required
                                </li>
                                <li class="flex items-center text-sm text-green-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Curriculum-aligned activities
                                </li>
                                <li class="flex items-center text-sm text-green-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Just 15 minutes per day
                                </li>
                            </ul>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account?
                                <a href="login.php" class="font-medium text-primary underline hover:text-primary/80">
                                    Sign in here
                                </a>
                            </p>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Trust Signals -->
            <div class="mt-8 text-center">
                <div class="bg-white/80 backdrop-blur-sm rounded-lg p-6">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-primary">10,000+</div>
                            <div class="text-xs text-gray-600">Worksheets Delivered</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-secondary">1,500+</div>
                            <div class="text-xs text-gray-600">Happy Children</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-accent">100%</div>
                            <div class="text-xs text-gray-600">Screen-Free</div>
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
    <script src="js/authentication-scripts/signup.js"></script>

</body>

</html>