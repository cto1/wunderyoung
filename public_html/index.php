<?php
// Daily Homework - Homepage
// Domain: dailyhome.work
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Homework - Printable Worksheets Delivered Daily</title>
    <meta name="description" content="Printable daily worksheets delivered to your inbox. No screens, just 15 minutes of fun learning per day. Age-appropriate activities for children.">
    
    <!-- Tailwind CSS + DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body data-theme="light">
    <!-- Navigation -->
    <div class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li>
                        <details>
                            <summary>Homework</summary>
                            <ul>
                                <li class="menu-title">By Age</li>
                                <li><a href="#age-3">Age 3 (Nursery)</a></li>
                                <li><a href="#age-4">Age 4 (Reception)</a></li>
                                <li><a href="#age-5">Age 5 (Year 1)</a></li>
                                <li><a href="#age-6">Age 6 (Year 2)</a></li>
                                <li><a href="#age-7">Age 7 (Year 3)</a></li>
                                <li><a href="#age-8">Age 8 (Year 4)</a></li>
                                <li><a href="#age-9">Age 9 (Year 5)</a></li>
                                <li><a href="#age-10">Age 10 (Year 6)</a></li>
                                <li><a href="#age-11">Age 11 (Year 6)</a></li>
                                <li class="menu-title">By Subject</li>
                                <li><a href="#english">English</a></li>
                                <li><a href="#maths">Maths</a></li>
                                <li><a href="#spanish">Spanish</a></li>
                            </ul>
                        </details>
                    </li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#samples">Free Sample</a></li>
                    <li><a href="#signup" class="btn btn-primary btn-sm">Start Now</a></li>
                </ul>
            </div>
            <a class="btn btn-ghost text-xl font-bold">Daily Homework</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li>
                    <details>
                        <summary>Homework</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none min-w-max">
                            <li class="menu-title">By Age</li>
                            <li><a href="#age-3">Age 3 (Nursery)</a></li>
                            <li><a href="#age-4">Age 4 (Reception)</a></li>
                            <li><a href="#age-5">Age 5 (Year 1)</a></li>
                            <li><a href="#age-6">Age 6 (Year 2)</a></li>
                            <li><a href="#age-7">Age 7 (Year 3)</a></li>
                            <li><a href="#age-8">Age 8 (Year 4)</a></li>
                            <li><a href="#age-9">Age 9 (Year 5)</a></li>
                            <li><a href="#age-10">Age 10 (Year 6)</a></li>
                            <li><a href="#age-11">Age 11 (Year 6)</a></li>
                            <li class="menu-title">By Subject</li>
                            <li><a href="#english">English</a></li>
                            <li><a href="#maths">Maths</a></li>
                            <li><a href="#spanish">Spanish</a></li>
                        </ul>
                    </details>
                </li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#samples">Free Sample</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <a href="#signup" class="btn btn-primary">Start Now</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero min-h-screen bg-gradient-to-br from-primary to-secondary">
        <div class="hero-content text-center text-primary-content">
            <div class="max-w-4xl">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Printable Daily Worksheets.<br>
                    <span class="text-accent">In Your Inbox.</span><br>
                    No Screens.
                </h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">
                    Fun, age-appropriate learning in just 15 minutes per day.
                </p>
                <button onclick="scrollToSignup()" class="btn btn-accent btn-lg text-xl">
                    👉 Get Started Free
                </button>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-base-100">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">How It Works</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card bg-primary text-primary-content">
                    <div class="card-body items-center text-center">
                        <div class="badge badge-secondary badge-lg text-2xl font-bold w-16 h-16 rounded-full">1</div>
                        <h3 class="card-title">Enter your email</h3>
                        <p>(no password!)</p>
                    </div>
                </div>
                <div class="card bg-secondary text-secondary-content">
                    <div class="card-body items-center text-center">
                        <div class="badge badge-primary badge-lg text-2xl font-bold w-16 h-16 rounded-full">2</div>
                        <h3 class="card-title">Add your child's details</h3>
                        <p>Name and interests</p>
                    </div>
                </div>
                <div class="card bg-accent text-accent-content">
                    <div class="card-body items-center text-center">
                        <div class="badge badge-neutral badge-lg text-2xl font-bold w-16 h-16 rounded-full">3</div>
                        <h3 class="card-title">Get daily worksheets</h3>
                        <p>Printable, bite-sized and fun!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What Makes It Different -->
    <section class="py-16 bg-base-200">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">What Makes It Different</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body items-center text-center">
                        <div class="text-4xl mb-3">🧠</div>
                        <h3 class="card-title">Designed for retention</h3>
                        <p class="text-base-content/70">Not cramming</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body items-center text-center">
                        <div class="text-4xl mb-3">✍️</div>
                        <h3 class="card-title">Pen & paper only</h3>
                        <p class="text-base-content/70">No screen time</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body items-center text-center">
                        <div class="text-4xl mb-3">💌</div>
                        <h3 class="card-title">Delivered to inbox</h3>
                        <p class="text-base-content/70">No searching or apps</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body items-center text-center">
                        <div class="text-4xl mb-3">🎯</div>
                        <h3 class="card-title">Personalised</h3>
                        <p class="text-base-content/70">Name + interests included</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Try It Free -->
    <section id="signup" class="py-16 bg-primary text-primary-content">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Try It Free</h2>
            <p class="text-xl mb-8 opacity-90">Enter your email to get today's free worksheet</p>
            <div class="max-w-md mx-auto">
                <form id="signupForm" class="join w-full">
                    <input 
                        type="email" 
                        id="email" 
                        placeholder="your@email.com" 
                        required
                        class="input input-bordered join-item flex-1"
                    >
                    <button 
                        type="submit"
                        class="btn btn-accent join-item"
                    >
                        Get Free Worksheet →
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Examples Preview Gallery -->
    <section id="samples" class="py-16 bg-base-100">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Sample Worksheets</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card bg-base-100 shadow-xl">
                    <figure class="px-10 pt-10">
                        <div class="bg-pink-200 h-48 w-full rounded-xl flex items-center justify-center">
                            <span class="text-pink-800">Reception Sample</span>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Reception (Ages 4-5)</h3>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm">View Sample →</button>
                        </div>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <figure class="px-10 pt-10">
                        <div class="bg-green-200 h-48 w-full rounded-xl flex items-center justify-center">
                            <span class="text-green-800">Year 2 Sample</span>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Year 2 (Ages 6-7)</h3>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm">View Sample →</button>
                        </div>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <figure class="px-10 pt-10">
                        <div class="bg-blue-200 h-48 w-full rounded-xl flex items-center justify-center">
                            <span class="text-blue-800">Year 5 Sample</span>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Year 5 (Ages 9-10)</h3>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm">View Sample →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-16 bg-base-200">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Simple Pricing</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Free Plan -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-2xl mb-4">Free Plan</h3>
                        <div class="text-3xl font-bold mb-6">€0<span class="text-lg opacity-70">/month</span></div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center"><span class="text-success mr-2">✓</span> 1 child</li>
                            <li class="flex items-center"><span class="text-success mr-2">✓</span> 1 subject</li>
                            <li class="flex items-center"><span class="text-error mr-2">✗</span> No personalisation</li>
                        </ul>
                        <div class="card-actions justify-center">
                            <button class="btn btn-outline btn-wide">Start Free</button>
                        </div>
                    </div>
                </div>
                
                <!-- Premium Plan -->
                <div class="card bg-primary text-primary-content shadow-xl relative">
                    <div class="badge badge-accent absolute top-4 right-4">Popular</div>
                    <div class="card-body">
                        <h3 class="card-title text-2xl mb-4">Premium Plan</h3>
                        <div class="text-3xl font-bold mb-6">€9<span class="text-lg opacity-70">/month</span></div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center"><span class="text-accent mr-2">✓</span> 3 children</li>
                            <li class="flex items-center"><span class="text-accent mr-2">✓</span> All subjects</li>
                            <li class="flex items-center"><span class="text-accent mr-2">✓</span> Personalised content</li>
                            <li class="flex items-center"><span class="text-accent mr-2">✓</span> Daily email + downloads</li>
                        </ul>
                        <div class="card-actions justify-center">
                            <button class="btn btn-accent btn-wide">Start Premium</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQs -->
    <section class="py-16 bg-base-100">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto">
                <div class="collapse collapse-plus bg-base-200 mb-4">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-xl font-medium">
                        What age groups do you cover?
                    </div>
                    <div class="collapse-content"> 
                        <p>We cover Reception through Year 6 (ages 4-11), with content specifically designed for each age group's developmental stage.</p>
                    </div>
                </div>
                <div class="collapse collapse-plus bg-base-200 mb-4">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-xl font-medium">
                        Can I cancel anytime?
                    </div>
                    <div class="collapse-content"> 
                        <p>Yes! You can cancel your subscription at any time. No long-term commitments or cancellation fees.</p>
                    </div>
                </div>
                <div class="collapse collapse-plus bg-base-200 mb-4">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-xl font-medium">
                        Can I add siblings?
                    </div>
                    <div class="collapse-content"> 
                        <p>The Premium plan supports up to 3 children. Each child gets personalised worksheets based on their age and interests.</p>
                    </div>
                </div>
                <div class="collapse collapse-plus bg-base-200 mb-4">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-xl font-medium">
                        Is it curriculum-aligned?
                    </div>
                    <div class="collapse-content"> 
                        <p>Yes! Our worksheets are designed to complement the UK National Curriculum and support key learning objectives for each year group.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-20 bg-gradient-to-br from-primary to-secondary text-primary-content text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6">No screens. Just learning made easy.</h2>
            <button onclick="scrollToSignup()" class="btn btn-accent btn-lg text-xl">
                👉 Get Started Free
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-neutral text-neutral-content">
        <aside>
            <p class="font-bold text-lg">Daily Homework</p>
            <p>&copy; 2024 dailyhome.work</p>
        </aside>
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a href="/app/" class="link link-hover">App</a>
                <a href="/api/" class="link link-hover">API</a>
                <a href="/privacy.php" class="link link-hover">Privacy Policy</a>
                <a href="/terms.php" class="link link-hover">Terms of Service</a>
            </div>
        </nav>
    </footer>

    <!-- JavaScript -->
    <script>
        function scrollToSignup() {
            document.getElementById('signup').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            
            // Here you would integrate with your magic link API
            fetch('/api/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'signup',
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Check your email for your magic link!');
                    document.getElementById('email').value = '';
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
        });
    </script>
</body>
</html>