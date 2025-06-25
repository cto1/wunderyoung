<?php
// Load translations
$lang = 'en_US'; // Default language
if (file_exists(__DIR__ . "/languages/{$lang}.php")) {
    $translations = include __DIR__ . "/languages/{$lang}.php";
}

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Homework - Printable worksheets delivered to your inbox</title>
    <meta name="description" content="Daily printable worksheets personalised to your child. No apps, no screens. Just 15 minutes a day of screen-free learning delivered to your inbox.">
    
    <!-- Tailwind CSS + DaisyUI for modern styling -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body data-theme="light" class="bg-gradient-to-br from-blue-50 to-purple-50">
    
    <!-- Navigation -->
    <nav class="navbar bg-white shadow-lg sticky top-0 z-50">
        <div class="navbar-start">
            <a href="#" class="btn btn-ghost text-xl font-bold text-primary">
                <i class="fas fa-home mr-2"></i>
                DailyHome.Work
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#samples">Samples</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <a href="/app/login.php" class="btn btn-ghost mr-2">Sign In</a>
            <a href="#get-started" class="btn btn-primary">Get Started Free</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero min-h-screen bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="hero-content text-center">
            <div class="max-w-4xl">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                    Homework that arrives in your inbox. <br>
                    <span class="text-yellow-300">No apps, no screens.</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-white max-w-3xl mx-auto">
                    Daily printable worksheets personalised to your child — in just 15 minutes a day.
                </p>
                
                <div class="space-y-4">
                    <a href="#get-started" class="btn btn-warning btn-lg text-lg px-8">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Get Started Free
                    </a>
                    <p class="text-sm text-white">
                        No downloads. No login needed. Just enter your email.
                    </p>
                </div>
                
                <!-- Quick demo -->
                <div class="mt-12 max-w-md mx-auto bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Try it now - Enter your email:</h3>
                    <div class="join w-full">
                        <input type="email" placeholder="parent@email.com" class="input input-bordered join-item flex-1 text-black" />
                        <button class="btn btn-warning join-item">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <p class="text-xs mt-2 text-white">Get your first worksheet in 2 minutes</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Trusted by hundreds of parents</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-paper-plane text-4xl"></i>
                    </div>
                    <div class="stat-title">Worksheets Delivered</div>
                    <div class="stat-value text-primary">10,000+</div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <i class="fas fa-child text-4xl"></i>
                    </div>
                    <div class="stat-title">Children Improving Daily</div>
                    <div class="stat-value text-secondary">1,500+</div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fas fa-ban text-4xl"></i>
                    </div>
                    <div class="stat-title">Screen-Free Learning</div>
                    <div class="stat-value text-accent">100%</div>
                </div>
            </div>
            
            <!-- Testimonials -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="rating rating-sm mb-4">
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                        </div>
                        <p class="text-gray-600 italic">
                            "Finally, homework that doesn't involve screens! My 6-year-old looks forward to her daily worksheet every morning."
                        </p>
                        <div class="mt-4">
                            <p class="font-semibold">Sarah M.</p>
                            <p class="text-sm text-gray-500">Parent of 2, London</p>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="rating rating-sm mb-4">
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                        </div>
                        <p class="text-gray-600 italic">
                            "The personalization is amazing. The worksheets include my son's favorite dinosaurs and really keep him engaged."
                        </p>
                        <div class="mt-4">
                            <p class="font-semibold">James T.</p>
                            <p class="text-sm text-gray-500">Parent of 1, Manchester</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-gradient-to-r from-blue-50 to-purple-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">Why DailyHome.Work?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-green-500 mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">Daily worksheets</h3>
                        <p class="text-gray-600">No searching, no printing chaos. Your child's worksheet arrives every morning like clockwork.</p>
                    </div>
                </div>
                
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-blue-500 mb-4">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">Age-appropriate content</h3>
                        <p class="text-gray-600">Bite-sized, curriculum-aligned activities perfect for your child's learning level.</p>
                    </div>
                </div>
                
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-purple-500 mb-4">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">Personalised learning</h3>
                        <p class="text-gray-600">Worksheets include your child's name and favorite topics to keep them engaged.</p>
                    </div>
                </div>
                
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-red-500 mb-4">
                            <i class="fas fa-mobile-alt"></i>
                            <i class="fas fa-slash text-2xl absolute"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">No apps or screen time</h3>
                        <p class="text-gray-600">Pure pencil-and-paper learning. No downloads, no logins, no digital distractions.</p>
                    </div>
                </div>
                
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-yellow-500 mb-4">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">AI-created, educator-reviewed</h3>
                        <p class="text-gray-600">Smart technology meets educational expertise for quality learning materials.</p>
                    </div>
                </div>
                
                <div class="card bg-white shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body text-center">
                        <div class="text-4xl text-green-500 mb-4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="card-title justify-center mb-4">Just 15 minutes daily</h3>
                        <p class="text-gray-600">Quick, focused learning sessions that fit into any family routine.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">How It Works</h2>
            
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-white text-2xl font-bold">1</span>
                            </div>
                            <div class="hidden md:block absolute top-10 left-20 w-full h-0.5 bg-gray-300"></div>
                        </div>
                        <div class="card bg-base-100 shadow-lg">
                            <div class="card-body">
                                <h3 class="card-title justify-center mb-4">
                                    <i class="fas fa-envelope text-primary mr-2"></i>
                                    Enter your email
                                </h3>
                                <p class="text-gray-600">Passwordless login. No app required. Get started in seconds.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-white text-2xl font-bold">2</span>
                            </div>
                            <div class="hidden md:block absolute top-10 left-20 w-full h-0.5 bg-gray-300"></div>
                        </div>
                        <div class="card bg-base-100 shadow-lg">
                            <div class="card-body">
                                <h3 class="card-title justify-center mb-4">
                                    <i class="fas fa-user-plus text-secondary mr-2"></i>
                                    Add your child's details
                                </h3>
                                <p class="text-gray-600">Age, name, and favorite topics — we'll personalise everything just for them.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-accent rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-white text-2xl font-bold">3</span>
                        </div>
                        <div class="card bg-base-100 shadow-lg">
                            <div class="card-body">
                                <h3 class="card-title justify-center mb-4">
                                    <i class="fas fa-print text-accent mr-2"></i>
                                    Get daily worksheets
                                </h3>
                                <p class="text-gray-600">Fresh worksheet in your inbox every morning. Just print and go!</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-12">
                    <a href="#get-started" class="btn btn-primary btn-lg">
                        Start Your Free Trial
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Worksheet Previews Section -->
    <section id="samples" class="py-16 bg-gradient-to-r from-green-50 to-blue-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">See What Your Child Will Get</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Reception Sample -->
                <div class="card bg-white shadow-xl">
                    <figure class="px-6 pt-6">
                        <div class="bg-gradient-to-br from-yellow-100 to-orange-100 rounded-lg p-8 w-full aspect-[3/4] flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-shapes text-6xl text-orange-500 mb-4"></i>
                                <h4 class="font-bold text-xl">Reception</h4>
                                <p class="text-sm text-gray-600">Ages 4-5</p>
                            </div>
                        </div>
                    </figure>
                    <div class="card-body">
                        <h3 class="card-title">Shape Recognition & Counting</h3>
                        <p class="text-gray-600">Fun activities with colors, shapes, and basic counting. Perfect for little hands!</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download mr-2"></i>
                                Download Sample
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Year 2 Sample -->
                <div class="card bg-white shadow-xl">
                    <figure class="px-6 pt-6">
                        <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg p-8 w-full aspect-[3/4] flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-calculator text-6xl text-blue-500 mb-4"></i>
                                <h4 class="font-bold text-xl">Year 2</h4>
                                <p class="text-sm text-gray-600">Ages 6-7</p>
                            </div>
                        </div>
                    </figure>
                    <div class="card-body">
                        <h3 class="card-title">Maths & English Mix</h3>
                        <p class="text-gray-600">Addition, subtraction, spelling, and reading comprehension activities.</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download mr-2"></i>
                                Download Sample
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Year 6 Sample -->
                <div class="card bg-white shadow-xl">
                    <figure class="px-6 pt-6">
                        <div class="bg-gradient-to-br from-green-100 to-teal-100 rounded-lg p-8 w-full aspect-[3/4] flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-book-open text-6xl text-green-500 mb-4"></i>
                                <h4 class="font-bold text-xl">Year 6</h4>
                                <p class="text-sm text-gray-600">Ages 10-11</p>
                            </div>
                        </div>
                    </figure>
                    <div class="card-body">
                        <h3 class="card-title">Advanced Learning</h3>
                        <p class="text-gray-600">Complex maths problems, creative writing, and SATs preparation.</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download mr-2"></i>
                                Download Sample
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <div class="badge badge-secondary badge-lg p-4">
                    <i class="fas fa-magic mr-2"></i>
                    All worksheets are personalized with your child's name and interests!
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">Choose Your Plan</h2>
            
            <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Free Plan -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-2xl justify-center mb-4">Free Plan</h3>
                        <div class="text-center mb-6">
                            <span class="text-4xl font-bold">£0</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>1 child</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>1 subject (rotating weekly)</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-3"></i>
                                <span>Basic worksheets only</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-3"></i>
                                <span>No personalization</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span>Daily email delivery</span>
                            </li>
                        </ul>
                        
                        <div class="card-actions justify-center">
                            <a href="#get-started" class="btn btn-outline btn-primary btn-wide">
                                Get Started Free
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Premium Plan -->
                <div class="card bg-gradient-to-br from-purple-500 to-blue-600 text-white shadow-xl relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <div class="badge badge-warning badge-lg">Most Popular</div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title text-2xl justify-center mb-4">Premium Plan</h3>
                        <div class="text-center mb-6">
                            <span class="text-4xl font-bold">€9</span>
                            <span class="opacity-75">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>Up to 3 children</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>All subjects (Maths, English, Spanish)</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>Fully personalized content</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>Name + interests included</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>Access to worksheet backlog</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-yellow-300 mr-3"></i>
                                <span>Daily email delivery</span>
                            </li>
                        </ul>
                        
                        <div class="card-actions justify-center">
                            <a href="#get-started" class="btn btn-warning btn-wide">
                                Upgrade to Premium
                            </a>
                        </div>
                        
                        <p class="text-center text-sm opacity-75 mt-4">
                            Cancel anytime. No commitments.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <div class="max-w-2xl mx-auto">
                    <h3 class="text-xl font-semibold mb-4">30-Day Money-Back Guarantee</h3>
                    <p class="text-gray-600">Try Premium risk-free. If you're not completely satisfied, we'll refund your money, no questions asked.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="get-started" class="py-16 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-8">Ready to Transform Your Child's Learning?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join hundreds of parents who've already discovered the joy of stress-free, screen-free homework.
            </p>
            
            <div class="max-w-md mx-auto bg-white/10 backdrop-blur-sm rounded-lg p-8">
                <h3 class="text-2xl font-semibold mb-6">Start Your Free Trial Today</h3>
                <form class="space-y-4">
                    <input type="email" placeholder="Enter your email address" class="input input-bordered w-full text-black text-lg" required />
                    <button type="submit" class="btn btn-warning btn-lg w-full">
                        <i class="fas fa-rocket mr-2"></i>
                        Get My First Worksheet
                    </button>
                </form>
                <p class="text-sm mt-4 opacity-75">
                    Free forever. Upgrade anytime. No credit card required.
                </p>
            </div>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="flex items-center justify-center space-x-3">
                    <i class="fas fa-shield-alt text-2xl text-yellow-300"></i>
                    <span>100% Secure</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i class="fas fa-paper-plane text-2xl text-yellow-300"></i>
                    <span>Instant Setup</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i class="fas fa-heart text-2xl text-yellow-300"></i>
                    <span>Parent Approved</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-neutral text-neutral-content">
        <nav class="grid grid-flow-col gap-4">
            <a href="/privacy.php" class="link link-hover">Privacy Policy</a>
            <a href="/terms.php" class="link link-hover">Terms of Service</a>
            <a href="mailto:support@dailyhome.work" class="link link-hover">Contact</a>
        </nav>
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a href="#" class="text-2xl hover:text-primary"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-2xl hover:text-primary"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-2xl hover:text-primary"><i class="fab fa-instagram"></i></a>
            </div>
        </nav>
        <aside>
            <p>&copy; <?php echo date('Y'); ?> DailyHome.Work - Making homework happy, one worksheet at a time.</p>
        </aside>
    </footer>

    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
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

        // Email form handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                if (email) {
                    // Redirect to signup page with email pre-filled
                    window.location.href = `/app/signup.php?email=${encodeURIComponent(email)}`;
                }
            });
        });
    </script>
</body>
</html> 