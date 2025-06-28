<?php 
$page_title = 'Sign In - Daily Homework';
include 'include/auth-header.html'; 
?>

<main class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Success message from signup -->
        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>Account created! Check your email for a login link, or sign in with your password below.</span>
        </div>
        <?php endif; ?>
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-home text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold mb-2">Welcome back</h2>
            <p class="text-gray-600">Sign in to access your child's worksheets</p>
        </div>

        <!-- Login Method Selection -->
        <div class="card card-sophisticated shadow-2xl">
            <div class="card-body p-8">
                
                <!-- Login Method Tabs -->
                <div class="tabs tabs-boxed mb-6">
                    <a class="tab tab-active" onclick="switchLoginMethod('passwordless')" id="passwordlessTab">
                        <i class="fas fa-magic mr-2"></i>
                        Magic Link
                    </a>
                    <a class="tab" onclick="switchLoginMethod('password')" id="passwordTab">
                        <i class="fas fa-key mr-2"></i>
                        Password
                    </a>
                </div>

                <!-- Email Field (Always visible) -->
                <form id="loginForm" class="space-y-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Email Address</span>
                        </label>
                        <input type="email" name="email" placeholder="parent@example.com" class="input input-bordered w-full" required>
                    </div>

                    <!-- Password Field (Hidden by default) -->
                    <div class="form-control" id="passwordField" style="display: none;">
                        <label class="label">
                            <span class="label-text font-semibold">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="Enter your password" class="input input-bordered w-full">
                        <label class="label">
                            <span class="label-text-alt">
                                <a href="#" onclick="switchLoginMethod('passwordless')" class="link link-primary">
                                    Forgot password? Use magic link instead
                                </a>
                            </span>
                        </label>
                    </div>

                    <!-- Login Method Explanation -->
                    <div id="loginExplanation" class="bg-info/10 border border-info/20 rounded-lg p-4">
                        <h4 class="font-semibold text-info mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            How magic link login works
                        </h4>
                        <ol class="text-sm space-y-1 text-gray-700">
                            <li>1. Enter your email address</li>
                            <li>2. Click "Send Magic Link"</li>
                            <li>3. Check your email for a login link</li>
                            <li>4. Click the link to sign in instantly</li>
                        </ol>
                        <p class="text-xs text-gray-600 mt-3">
                            <i class="fas fa-shield-alt mr-1"></i>
                            More secure than passwords - links expire after 1 hour.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-sophisticated w-full btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span id="loginButtonText">Send Magic Link</span>
                    </button>
                    
                    <!-- Alternative method link -->
                    <div class="text-center">
                        <a href="#" onclick="switchLoginMethod('password')" class="link link-primary text-sm" id="altMethodLink">
                            <i class="fas fa-key mr-1"></i>
                            Use password instead
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center">
            <p class="text-gray-600">Don't have an account?</p>
            <a href="signup.php" class="btn btn-outline btn-primary mt-2">
                <i class="fas fa-user-plus mr-2"></i>
                Create Free Account
            </a>
        </div>

    </div>
</main>

<script>
let currentLoginMethod = 'passwordless';

function switchLoginMethod(method) {
    currentLoginMethod = method;
    
    // Update tab appearance
    document.getElementById('passwordlessTab').classList.remove('tab-active');
    document.getElementById('passwordTab').classList.remove('tab-active');
    
    if (method === 'passwordless') {
        document.getElementById('passwordlessTab').classList.add('tab-active');
    } else {
        document.getElementById('passwordTab').classList.add('tab-active');
    }
    
    // Show/hide password field
    const passwordField = document.getElementById('passwordField');
    const explanation = document.getElementById('loginExplanation');
    const buttonText = document.getElementById('loginButtonText');
    const altMethodLink = document.getElementById('altMethodLink');
    
    if (method === 'passwordless') {
        passwordField.style.display = 'none';
        passwordField.querySelector('input').required = false;
        
        explanation.innerHTML = `
            <h4 class="font-semibold text-info mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                How magic link login works
            </h4>
            <ol class="text-sm space-y-1 text-gray-700">
                <li>1. Enter your email address</li>
                <li>2. Click "Send Magic Link"</li>
                <li>3. Check your email for a login link</li>
                <li>4. Click the link to sign in instantly</li>
            </ol>
            <p class="text-xs text-gray-600 mt-3">
                <i class="fas fa-shield-alt mr-1"></i>
                More secure than passwords - links expire after 1 hour.
            </p>
        `;
        
        buttonText.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Magic Link';
        altMethodLink.innerHTML = '<i class="fas fa-key mr-1"></i>Use password instead';
        altMethodLink.onclick = () => switchLoginMethod('password');
        
    } else {
        passwordField.style.display = 'block';
        passwordField.querySelector('input').required = true;
        
        explanation.innerHTML = `
            <h4 class="font-semibold text-warning mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                Password login
            </h4>
            <p class="text-sm text-gray-700">
                Enter your email and password to sign in. If you forgot your password or don't have one, 
                you can always use the magic link option instead.
            </p>
            <p class="text-xs text-gray-600 mt-3">
                <i class="fas fa-lightbulb mr-1"></i>
                Tip: Magic link login is more secure and convenient.
            </p>
        `;
        
        buttonText.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
        altMethodLink.innerHTML = '<i class="fas fa-magic mr-1"></i>Use magic link instead';
        altMethodLink.onclick = () => switchLoginMethod('passwordless');
    }
}

// Form submission handling
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    const email = formData.get('email');
    
    // Show loading state
    submitBtn.disabled = true;
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Signing in...';
    
    try {
        let response, data;
        
        if (currentLoginMethod === 'passwordless') {
            // Magic link login
            response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            });
            
            data = await response.json();
            
            if (data.status === 'success') {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success mb-4';
                successDiv.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <span>Login link sent! Check your email and click the link to sign in.</span>
                `;
                
                const form = document.getElementById('loginForm');
                form.parentNode.insertBefore(successDiv, form);
                
                // Scroll to show success message
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
            } else {
                throw new Error(data.message || 'Failed to send login link');
            }
            
        } else {
            // Password login
            const password = formData.get('password');
            if (!password) {
                throw new Error('Please enter your password');
            }
            
            response = await fetch('/api/auth/password-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email, password: password })
            });
            
            data = await response.json();
            
            if (data.status === 'success') {
                // Generate JWT token
                const tokenResponse = await fetch('/api/auth/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        id: data.id,
                        email: data.email 
                    }),
                });
                
                const tokenData = await tokenResponse.json();
                
                if (tokenData.status === 'success') {
                    // Store JWT and user data
                    localStorage.setItem('jwt_token', tokenData.token);
                    localStorage.setItem('user_id', tokenData.user.id);
                    localStorage.setItem('user_email', tokenData.user.email);
                    localStorage.setItem('user_plan', tokenData.user.plan);
                    
                    // Redirect to child setup or worksheets
                    window.location.href = 'child-setup.php';
                } else {
                    throw new Error(tokenData.message || 'Failed to generate access token');
                }
            } else {
                throw new Error(data.message || 'Login failed');
            }
        }
        
    } catch (error) {
        console.error('Login error:', error);
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-error mb-4';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${error.message}</span>
        `;
        
        const form = document.getElementById('loginForm');
        form.parentNode.insertBefore(errorDiv, form);
        
        // Remove error after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
        
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
    }
});

// Pre-fill email from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const email = urlParams.get('email');
if (email) {
    document.querySelector('input[name="email"]').value = email;
}

// Initialize with passwordless method
document.addEventListener('DOMContentLoaded', function() {
    switchLoginMethod('passwordless');
});
</script>

</body>
</html> 