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
    <title>Passwordless Login - Daily Homework</title>

    <!-- Favicons -->
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-gradient-to-br from-primary/10 to-secondary/10 min-h-screen">

    <div class="container mx-auto px-4 py-8 min-h-screen flex items-center">
        <div class="w-full max-w-6xl mx-auto bg-base-100 rounded-2xl shadow-2xl overflow-hidden">
            
            <div class="grid md:grid-cols-2 min-h-[600px]">
                
                <!-- Left Side - Benefits -->
                <div class="bg-gradient-to-br from-primary to-secondary text-primary-content p-8 md:p-12 flex flex-col justify-center">
                    
                    <div class="mb-8">
                        <h2 class="text-3xl md:text-4xl font-bold mb-6">Why Passwordless?</h2>
                        <p class="text-lg opacity-90 mb-8">Experience the future of secure, hassle-free authentication</p>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="flex items-start space-x-4">
                            <div class="bg-accent/20 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-shield-alt text-accent text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-2">Enhanced Security</h3>
                                <p class="text-sm opacity-80">No passwords to remember, steal, or hack. Magic links are more secure than traditional passwords.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-accent/20 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-clock text-accent text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-2">Save Time</h3>
                                <p class="text-sm opacity-80">Skip password creation, memorization, and reset processes. Login instantly with one click.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-accent/20 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-mobile-alt text-accent text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-2">Device Friendly</h3>
                                <p class="text-sm opacity-80">Works seamlessly across all devices. No typing complex passwords on mobile.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-accent/20 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-user-friends text-accent text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-2">Better User Experience</h3>
                                <p class="text-sm opacity-80">Eliminate password frustration. Simple, secure, and user-friendly authentication.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-accent/20 p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-globe text-accent text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-2">Universal Access</h3>
                                <p class="text-sm opacity-80">Access your account from any device with email. No need to sync passwords.</p>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 p-4 bg-accent/10 rounded-lg">
                        <p class="text-sm opacity-90">
                            <i class="fas fa-lightbulb text-accent mr-2"></i>
                            <strong>Did you know?</strong> Passwordless authentication reduces login time by 75% and eliminates 95% of account security issues.
                        </p>
                    </div>

                </div>

                <!-- Right Side - Login Form -->
                <div class="p-8 md:p-12 flex flex-col justify-center">
                    
                    <!-- Logo -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-primary-content text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-primary">Daily Homework</h1>
                        <p class="text-base-content/70 mt-2">Secure Passwordless Login</p>
                    </div>

                    <!-- Login Form -->
                    <div class="space-y-6">
                        
                        <div>
                            <h2 class="text-xl font-semibold mb-4 text-center">Welcome Back!</h2>
                            <p class="text-base-content/70 text-center mb-6">Enter your email to receive a secure magic link</p>
                        </div>

                        <form id="passwordlessLoginForm" class="space-y-4">
                            
                            <!-- Email Input -->
                            <div>
                                <label for="email" class="label">
                                    <span class="label-text font-medium">Email Address</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email"
                                        class="input input-bordered w-full pl-12 focus:input-primary" 
                                        placeholder="Enter your email address"
                                        required
                                        autocomplete="email"
                                    >
                                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-base-content/40"></i>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="loginButton"
                                class="btn btn-primary w-full btn-lg"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Magic Link
                            </button>

                        </form>

                        <!-- Status Messages -->
                        <div id="messageContainer" class="text-center"></div>

                        <!-- How it works -->
                        <div class="mt-8 p-4 bg-base-200 rounded-lg">
                            <h3 class="font-semibold mb-3 flex items-center">
                                <i class="fas fa-info-circle text-info mr-2"></i>
                                How it works
                            </h3>
                            <ol class="text-sm space-y-2 text-base-content/70">
                                <li class="flex items-start">
                                    <span class="badge badge-primary badge-sm mr-2 mt-0.5">1</span>
                                    Enter your email address above
                                </li>
                                <li class="flex items-start">
                                    <span class="badge badge-primary badge-sm mr-2 mt-0.5">2</span>
                                    Check your inbox for a magic link
                                </li>
                                <li class="flex items-start">
                                    <span class="badge badge-primary badge-sm mr-2 mt-0.5">3</span>
                                    Click the link to login securely
                                </li>
                            </ol>
                        </div>

                        <!-- Back to Password Login -->
                        <div class="text-center pt-4">
                            <p class="text-sm text-base-content/60">
                                Prefer traditional login? 
                                <a href="login.php" class="text-primary hover:text-primary-focus underline">
                                    Use password instead
                                </a>
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-6 text-base-content/60">
        <div class="space-x-4 text-sm">
            <a href="/privacy.php" class="hover:text-primary">Privacy Policy</a>
            <span>•</span>
            <a href="/terms.php" class="hover:text-primary">Terms of Service</a>
            <span>•</span>
            <a href="/" class="hover:text-primary">Back to Home</a>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.getElementById('passwordlessLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const loginButton = document.getElementById('loginButton');
            const messageContainer = document.getElementById('messageContainer');
            
            // Update button state
            loginButton.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Sending Magic Link...';
            loginButton.disabled = true;
            
            // Clear previous messages
            messageContainer.innerHTML = '';
            
            // Send magic link request
            fetch('/api/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send_magic_link',
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageContainer.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Magic link sent!</strong><br>
                                Check your inbox and click the link to login.
                            </div>
                        </div>
                    `;
                    document.getElementById('email').value = '';
                } else {
                    messageContainer.innerHTML = `
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Error:</strong> ${data.message || 'Something went wrong. Please try again.'}
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageContainer.innerHTML = `
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Network Error:</strong> Please check your connection and try again.
                        </div>
                    </div>
                `;
            })
            .finally(() => {
                // Reset button state
                loginButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Magic Link';
                loginButton.disabled = false;
            });
        });

        // Auto-focus email input
        document.getElementById('email').focus();
    </script>

</body>

</html>