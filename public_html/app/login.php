<?php 
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include 'include/translations.php'; 
require_once 'config.php';

?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?=__("website_title"); ?>
    </title>

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

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="css/styles.css"> -->

</head>

<body>

    <section class="w-full h-full min-h-screen flex justify-center items-center p-5 bg-base-300">

        <div class="w-full max-w-md bg-base-100 p-5 sm:p-10 shadow-2xl h-full text-center flex flex-col justify-between">

            <!-- Logo -->
            <div class="mb-6">
                <img src="./assets/logos/exactsum.png"
                     class="w-12 mx-auto dynamic_main_logo"
                     style="filter: drop-shadow(0 0.5px 1px white);">
            </div>


            <!-- Center componets -->
            <div>

                <!-- Login content -->
                <div>

                    <!-- Welcome text -->
                    <h1 class="text-4xl font-semibold mb-4"><?= __("welcome_text"); ?></h1>

                    <!-- Email input -->
                    <div class="relative pb-4 mb-1">

                        <!-- Input field -->
                        <input type="email" id="loginEmailInput"
                            class=" w-full mt-2 rounded-md p-4 text-sm sm:text-base bg-transparent border border-secondary/50 focus:border-secondary"
                            placeholder="<?= __('email_label') ?>" autocomplete="">

                        <!-- Message container -->
                        <p id="messageContainer" class="">
                        </p>

                    </div>

                    <!-- Password input (now visible by default) -->
                    <div id="passwordField" class="relative pb-4">
                        <input type="password" id="loginPasswordInput"
                            class="w-full mt-2 rounded-md p-4 text-sm sm:text-base bg-transparent border border-secondary/50 focus:border-secondary"
                            placeholder="Password (at least 8 characters)" autocomplete="current-password">
                    </div>

                    <!-- Auth method toggle -->
                    <div class="text-center mb-4">
                        <button type="button" id="toggleAuthMethod" class="text-sm text-secondary underline hover:text-secondary/80">
                            Or use magic link instead
                        </button>
                    </div>

                    <!-- Login button -->
                    <button id="loginButton" class="w-full bg-secondary text-base-100 font-semibold py-3 rounded-md">
                        <?= __("login_link_text"); ?>
                    </button>

                </div>

            </div>

            <!-- Footer links -->
            <div class="mt-6 text-sm text-secondary">

                <a href="#" class="underline">
                    <?= __("footer_terms_text"); ?>
                </a>
                <span class="text-secondary mx-1">|</span>
                <a href="#" class="underline">
                    <?= __("footer_privacy_text"); ?>
                </a>

            </div>

        </div>

    </section>


    <!-- ---------- JS Scripts ---------- -->

    <!-- Authentication Scripts > Login Page -->
    <script src="js/authentication-scripts/login.js"></script>

</body>

</html>
