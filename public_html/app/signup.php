<?php 
$page_title = 'Get Started - Daily Homework';
include 'include/auth-header.html'; 
?>

<main class="min-h-screen py-12 px-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Progress Steps -->
        <div class="steps steps-horizontal w-full mb-8">
            <div class="step step-primary">Sign Up</div>
            <div class="step">Add Child</div>
            <div class="step">Start Learning</div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-rocket text-2xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4">Start your free trial</h1>
            <p class="text-lg text-gray-600">
                Join hundreds of families enjoying screen-free homework
            </p>
        </div>

        <!-- Signup Form -->
        <div class="card card-sophisticated shadow-2xl">
            <div class="card-body p-8">
                
                <!-- Benefits Preview -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-lg p-6 mb-8">
                    <h3 class="font-bold text-lg mb-4 text-center">
                        <i class="fas fa-gift text-primary mr-2"></i>
                        What you'll get free forever
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <i class="fas fa-calendar-check text-primary text-2xl mb-2"></i>
                            <h4 class="font-semibold">Daily Worksheets</h4>
                            <p class="text-sm text-gray-600">Fresh content every morning</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-child text-secondary text-2xl mb-2"></i>
                            <h4 class="font-semibold">1 Child</h4>
                            <p class="text-sm text-gray-600">Perfect for getting started</p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-book text-accent text-2xl mb-2"></i>
                            <h4 class="font-semibold">Rotating Subjects</h4>
                            <p class="text-sm text-gray-600">Maths & English weekly</p>
                        </div>
                    </div>
                </div>

                <!-- Security Method Selection -->
                <div class="mb-8">
                    <h3 class="font-bold text-lg mb-4 text-center">
                        <i class="fas fa-shield-alt text-success mr-2"></i>
                        Choose your security method
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Passwordless Option -->
                        <div class="card bg-gradient-to-br from-success/10 to-primary/10 border-2 border-success/20 hover:border-success/40 transition-all cursor-pointer" onclick="selectAuthMethod('passwordless')">
                            <div class="card-body p-6 text-center">
                                <div class="badge badge-success mb-3">RECOMMENDED</div>
                                <i class="fas fa-magic text-success text-3xl mb-3"></i>
                                <h4 class="font-bold text-lg">Passwordless</h4>
                                <p class="text-sm text-gray-600 mb-4">More secure, no passwords to remember</p>
                                <ul class="text-xs text-left space-y-1">
                                    <li>✅ Login via email links</li>
                                    <li>✅ No password to forget</li>
                                    <li>✅ Enhanced security</li>
                                    <li>✅ Quick access</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Traditional Option -->
                        <div class="card bg-base-200 border-2 border-base-300 hover:border-base-400 transition-all cursor-pointer" onclick="selectAuthMethod('traditional')">
                            <div class="card-body p-6 text-center">
                                <div class="badge badge-outline mb-3">FAMILIAR</div>
                                <i class="fas fa-key text-primary text-3xl mb-3"></i>
                                <h4 class="font-bold text-lg">Traditional</h4>
                                <p class="text-sm text-gray-600 mb-4">Classic email + password setup</p>
                                <ul class="text-xs text-left space-y-1">
                                    <li>✅ Familiar process</li>
                                    <li>✅ Set your own password</li>
                                    <li>⚠️ Need to remember password</li>
                                    <li>⚠️ Can be less secure</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form starts here -->
                <form id="signupForm" class="space-y-6">
                    <input type="hidden" name="auth_method" id="authMethod" value="passwordless">
                    
                    <!-- Email Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Email Address</span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               placeholder="parent@example.com" 
                               class="input input-bordered w-full" 
                               required>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">We'll send your worksheets here</span>
                        </label>
                    </div>

                    <!-- How it works explanation -->
                    <div id="howItWorksExplanation" class="bg-info/10 border border-info/20 rounded-lg p-4">
                        <h4 class="font-semibold text-info mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            How passwordless signup works
                        </h4>
                        <ol class="text-sm space-y-1 text-gray-700">
                            <li>1. Enter your email and click "Create Account"</li>
                            <li>2. Check your email for a welcome message with a login link</li>
                            <li>3. Click the link to access your account instantly</li>
                            <li>4. For future logins, just request a new link - no password needed!</li>
                        </ol>
                        <p class="text-xs text-gray-600 mt-3">
                            <i class="fas fa-shield-alt mr-1"></i>
                            More secure than passwords because login links expire after 1 hour and are single-use.
                        </p>
                    </div>

                    <!-- Password Field (hidden by default) -->
                    <div class="form-control" id="passwordField" style="display: none;">
                        <label class="label">
                            <span class="label-text font-semibold">Create Password</span>
                            <span class="label-text-alt text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               placeholder="Choose a secure password" 
                               class="input input-bordered w-full">
                        <label class="label">
                            <span class="label-text-alt text-gray-500">At least 8 characters</span>
                        </label>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" name="terms" class="checkbox checkbox-primary mr-3" required>
                            <span class="label-text">
                                I agree to the 
                                <a href="/terms.php" target="_blank" class="link link-primary">Terms of Service</a> 
                                and 
                                <a href="/privacy.php" target="_blank" class="link link-primary">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Marketing Opt-in -->
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input type="checkbox" name="marketing" class="checkbox checkbox-secondary mr-3" checked>
                            <span class="label-text">
                                Send me tips and updates about Daily Homework (optional)
                            </span>
                        </label>
                    </div>

                    <!-- Sign Up Button -->
                    <button type="submit" class="btn btn-sophisticated w-full btn-lg">
                        <i class="fas fa-user-plus mr-2"></i>
                        <span id="signupButtonText">Create Account - No Password Needed</span>
                    </button>

                </form>

                <!-- Divider -->
                <div class="divider my-6">Already have an account?</div>

                <!-- Sign In Link -->
                <div class="text-center">
                    <a href="login.php" class="btn btn-outline btn-primary">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In Instead
                    </a>
                </div>

            </div>
        </div>

        <!-- Trust Signals -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-shield-alt text-success"></i>
                <span class="text-sm">100% Secure</span>
            </div>
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-credit-card text-info"></i>
                <span class="text-sm">No Credit Card</span>
            </div>
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <i class="fas fa-times-circle text-warning"></i>
                <span class="text-sm">Cancel Anytime</span>
            </div>
        </div>

        <!-- Upgrade Preview -->
        <div class="mt-12">
            <div class="card bg-gradient-to-r from-warning/10 to-orange-100 border border-warning/20">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">
                                <i class="fas fa-star text-warning mr-2"></i>
                                Want more? Upgrade to Premium
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                3 children • All subjects • Full personalization • €9/month
                            </p>
                        </div>
                        <div class="badge badge-warning">
                            Most Popular
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
let selectedAuthMethod = 'passwordless';

// Function to select authentication method
function selectAuthMethod(method) {
    selectedAuthMethod = method;
    
    // Update hidden field
    document.getElementById('authMethod').value = method;
    
    // Update visual selection
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        if (card.onclick) {
            card.classList.remove('border-success', 'border-primary');
            card.classList.add('border-base-300');
        }
    });
    
    // Highlight selected card
    const selectedCard = event.currentTarget;
    selectedCard.classList.remove('border-base-300');
    selectedCard.classList.add(method === 'passwordless' ? 'border-success' : 'border-primary');
    
    // Show/hide password field and update explanation
    const passwordField = document.getElementById('passwordField');
    const explanation = document.getElementById('howItWorksExplanation');
    const buttonText = document.getElementById('signupButtonText');
    
    if (method === 'passwordless') {
        passwordField.style.display = 'none';
        passwordField.querySelector('input').required = false;
        explanation.innerHTML = `
            <h4 class="font-semibold text-info mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                How passwordless signup works
            </h4>
            <ol class="text-sm space-y-1 text-gray-700">
                <li>1. Enter your email and click "Create Account"</li>
                <li>2. Check your email for a welcome message with a login link</li>
                <li>3. Click the link to access your account instantly</li>
                <li>4. For future logins, just request a new link - no password needed!</li>
            </ol>
            <p class="text-xs text-gray-600 mt-3">
                <i class="fas fa-shield-alt mr-1"></i>
                More secure than passwords because login links expire after 1 hour and are single-use.
            </p>
        `;
        buttonText.textContent = 'Create Account - No Password Needed';
    } else {
        passwordField.style.display = 'block';
        passwordField.querySelector('input').required = true;
        explanation.innerHTML = `
            <h4 class="font-semibold text-warning mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                How traditional signup works
            </h4>
            <ol class="text-sm space-y-1 text-gray-700">
                <li>1. Enter your email and create a password</li>
                <li>2. Click "Create Account" to sign up</li>
                <li>3. Check your email for a welcome message with a login link</li>
                <li>4. For future logins, you can use either your password or request a magic link</li>
            </ol>
            <p class="text-xs text-gray-600 mt-3">
                <i class="fas fa-key mr-1"></i>
                You'll have both password and passwordless login options available.
            </p>
        `;
        buttonText.textContent = 'Create Account';
    }
}

// Form handling
document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm mr-2"></span>Creating Account...';
    
    try {
        // Prepare data for API
        const requestData = {
            email: formData.get('email'),
            terms: formData.get('terms') ? true : false,
            marketing: formData.get('marketing') ? true : false,
            auth_method: selectedAuthMethod
        };
        
        // Add password only if traditional method and password was provided
        if (selectedAuthMethod === 'traditional' && formData.get('password')) {
            requestData.password = formData.get('password');
        }
        
        // Call the correct API endpoint
        const response = await fetch('/api/auth/signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            // Show success message
            const successMessage = selectedAuthMethod === 'passwordless' 
                ? 'Account created! Check your email for a login link to get started.'
                : 'Account created! Check your email for a welcome message with your login link.';
            
            // Create success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success mb-4';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${successMessage}</span>
            `;
            
            // Insert alert at top of form
            const form = document.getElementById('signupForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Scroll to top to show success message
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Reset form
            form.reset();
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = 'login.php?signup=success&email=' + encodeURIComponent(requestData.email);
            }, 3000);
            
        } else {
            // Show error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-error mb-4';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${data.message || 'Signup failed. Please try again.'}</span>
            `;
            
            const form = document.getElementById('signupForm');
            form.parentNode.insertBefore(errorDiv, form);
            
            // Remove error after 5 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }
        
    } catch (error) {
        console.error('Signup error:', error);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-error mb-4';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>Something went wrong. Please try again.</span>
        `;
        
        const form = document.getElementById('signupForm');
        form.parentNode.insertBefore(errorDiv, form);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="fas fa-user-plus mr-2"></i><span id="signupButtonText">${selectedAuthMethod === 'passwordless' ? 'Create Account - No Password Needed' : 'Create Account'}</span>`;
    }
});

// Pre-fill email from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const email = urlParams.get('email');
if (email) {
    document.querySelector('input[name="email"]').value = email;
}

// Initialize with passwordless selected
document.addEventListener('DOMContentLoaded', function() {
    // Find and click the passwordless card to set initial state
    const passwordlessCard = document.querySelector('.card');
    if (passwordlessCard && passwordlessCard.onclick) {
        passwordlessCard.classList.add('border-success');
        passwordlessCard.classList.remove('border-base-300');
    }
});
</script>

</body>
</html> 