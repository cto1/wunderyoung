<?php include 'website/include/header.html'; ?>
  

  <main>

    <!-- Home Hero Banner -->
    <?php include 'website/include/home-hero-banner.html'; ?>

    <!-- Home Social Proof -->
    <?php include 'website/include/home-social-proof.html'; ?>
    
    <!-- Home Features -->
    <?php include 'website/include/home-features.html'; ?>

    <!-- Home How It Works -->
    <?php include 'website/include/home-how-it-works.html'; ?>

    <!-- Home Worksheet Previews -->
    <?php include 'website/include/home-worksheet-previews.html'; ?>

    <!-- Home Pricing -->
    <?php include 'website/include/home-pricing.html'; ?>

  </main>
  
  <!-- Home CTA Section -->
  <?php include 'website/include/home-cta.html'; ?>
  
  <!-- Footer -->
  <?php include 'website/include/footer.html'; ?>
  
  <!-- ---------- JS Scripts ---------- -->
  <script src="website/script.js"></script>
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