<?php 
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include 'include/translations.php'; 

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

        <div class="w-full max-w-sm h-full flex flex-col justify-between">

            <!-- logo -->
            <div class="h-10 sm:h-12 flex items-center gap-2 mb-4">
                <img src="./assets/logos/exactsum.png" alt="" class="h-full" style="filter: drop-shadow(0 0.5px 1px white);">
                <span class="text-3xl font-bold tracking-tight">ExactSum</span>
            </div>

            <!-- Center componets -->
            <div class="p-5 rounded-md space-y-2 border border-gray-400/50 bg-base-100 shadow-2xl">
                <h3 class="font-bold text-xl text-red-600">Access Link Expired!</h3>
                <p class="font-[500] text-md text-gray-600">It looks like this link has expired. Please check your most recent email from ExactSum for an active link.</p>
            </div>

        </div>

    </section>

</body>

</html>
