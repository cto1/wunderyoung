<?php include 'include/header.html'; ?>

<body class="overflow-x-hidden">

      <!-- Main Content -->
      <main class="flex-1 transition-all duration-300">

        <!-- Main Header -->
        <?php include 'include/headers/main-header.html'; ?>
        
        <!-- Settings Page Section -->
        <?php include 'include/settings-page-components/settings-page-section.html'; ?>

      </main>
      
      <!-- Popups -->
      <?php include 'include/settings-page-components/settings-page-popups/edit-password-popup.html'; ?>
      <?php include 'include/settings-page-components/settings-page-popups/edit-name-popup.html'; ?>
      <?php include 'include/settings-page-components/settings-page-popups/invite-user-popup.html'; ?>
      <?php include 'include/settings-page-components/settings-page-popups/edit-user-popup.html'; ?>
      <?php include 'include/settings-page-components/settings-page-popups/delete-user-popup.html'; ?>

      <!-- File Preview Components -->

      
  <!-- ---------- JS Scripts ---------- -->

  <!-- [Global JS] > API Utilities -->
  <script src="js/api-utils.js"></script>
  
  <!-- [Global JS] > Localstorage Data  -->
  <script src="js/localstorage-data.js"></script>

  <!-- [Global JS] > Global Theme  -->
  <script src="js/global-theme.js"></script>
  
  <!-- [Headers Scripts] -->
  <script src="js/headers-scripts/header-script.js"></script>
  
  <!-- [Settings Page Scripts] > (Settings Page All)  -->
  <script src="js/settings-page-scripts/settings-page-all.js"></script>

  <!-- [Common Scripts] -->
  <script src="js/common-scripts/toast-notifications.js"></script>
  
  
</body>

</html>