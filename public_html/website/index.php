<?php include 'include/header.html'; ?>

<body>
  
  <!-- Navbar -->
  <?php include 'include/navbar.html'; ?>
  

  <main>

    <!-- Home Hero Banner -->
    <?php include 'include/home-hero-banner.html'; ?>

    <!-- Home Social Proof -->
    <?php include 'include/home-social-proof.html'; ?>
    
    <!-- Home Features -->
    <?php include 'include/home-features.html'; ?>

    <!-- Home How It Works -->
    <?php include 'include/home-how-it-works.html'; ?>

    <!-- Home Worksheet Previews -->
    <?php include 'include/home-worksheet-previews.html'; ?>

    <!-- Home Pricing -->
    <?php include 'include/home-pricing.html'; ?>

    <!-- Home CTA Section -->
    <?php include 'include/home-cta.html'; ?>

  </main>
  
  <!-- Footer -->
  <?php include 'include/footer.html'; ?>
  
  <!-- ---------- JS Scripts ---------- -->
  <script src="script.js"></script>
  <script>
    // Handle email capture and signup
    function handleEmailCapture(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;
        if (email) {
            window.location.href = `/app/signup.php?email=${encodeURIComponent(email)}`;
        }
    }

    function handleSignup(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;
        if (email) {
            window.location.href = `/app/signup.php?email=${encodeURIComponent(email)}`;
        }
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
  </script>

</body>

</html> 