<?php 
$page_title = "Sign Up - Yes Homework";
$page_description = "Create your Yes Homework account";
include 'include/header.html'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-6">Create Account</h2>
            
            <!-- Success Message -->
            <div id="success-message" class="alert alert-success hidden mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="success-text"></span>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="alert alert-error hidden mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="error-text"></span>
            </div>

            <!-- Signup Form -->
            <form id="signup-form" class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Full Name</span>
                    </label>
                    <input type="text" id="name" name="name" placeholder="Your full name" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Address</span>
                    </label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="password" name="password" placeholder="Create a password" class="input input-bordered" required>
                    <label class="label">
                        <span class="label-text-alt">At least 6 characters</span>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Confirm Password</span>
                    </label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">I agree to the <a href="/terms.php" class="link link-primary">Terms of Service</a> and <a href="/privacy.php" class="link link-primary">Privacy Policy</a></span>
                        <input type="checkbox" id="terms" class="checkbox checkbox-primary" required>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full btn-lg">
                    <span id="submit-text">Create Account</span>
                    <span id="loading-spinner" class="loading loading-spinner loading-sm hidden"></span>
                </button>
            </form>

            <div class="divider">OR</div>

            <!-- Passwordless Signup -->
            <div class="space-y-4">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-700">✨ Passwordless Signup</h3>
                    <p class="text-sm text-gray-500">Create account with just your email</p>
                </div>
                
                <form id="passwordless-form" class="space-y-4">
                    <div class="form-control">
                        <input type="email" id="passwordless-email" placeholder="your@email.com" class="input input-bordered" required>
                    </div>
                    
                    <button type="submit" class="btn btn-secondary w-full">
                        <span id="passwordless-text">🚀 Create Account & Send Magic Link</span>
                        <span id="passwordless-spinner" class="loading loading-spinner loading-sm hidden"></span>
                    </button>
                </form>
                
                <div id="passwordless-success" class="alert alert-info hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>🎉 Account created! Check your email for a magic login link.</span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="text-center">
                <p class="text-sm text-gray-600">Already have an account?</p>
                <a href="/app/login.php" class="btn btn-outline btn-primary w-full mt-2">Sign In</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('signup-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const terms = document.getElementById('terms').checked;
    
    // Validation
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }
    
    if (password.length < 6) {
        showError('Password must be at least 6 characters');
        return;
    }
    
    if (!terms) {
        showError('Please agree to the Terms of Service and Privacy Policy');
        return;
    }
    
    // Show loading state
    setLoading(true);
    hideMessages();
    
    try {
        const response = await fetch('/api/UserAuthAPI.php?action=signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email,
                password: password
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Store token
            localStorage.setItem('authToken', result.token);
            showSuccess('Account created successfully! Redirecting...');
            
            // Redirect to worksheets page
            setTimeout(() => {
                window.location.href = '/app/worksheets.php';
            }, 1500);
        } else {
            showError(result.message || 'Signup failed');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    } finally {
        setLoading(false);
    }
});

function showSuccess(message) {
    document.getElementById('success-text').textContent = message;
    document.getElementById('success-message').classList.remove('hidden');
    document.getElementById('error-message').classList.add('hidden');
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
    document.getElementById('success-message').classList.add('hidden');
}

function hideMessages() {
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
}

function setLoading(loading) {
    const submitText = document.getElementById('submit-text');
    const spinner = document.getElementById('loading-spinner');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (loading) {
        submitText.textContent = 'Creating Account...';
        spinner.classList.remove('hidden');
        submitButton.disabled = true;
    } else {
        submitText.textContent = 'Create Account';
        spinner.classList.add('hidden');
        submitButton.disabled = false;
    }
}

// Passwordless Signup
document.getElementById('passwordless-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('passwordless-email').value;
    
    // Show loading state
    const passwordlessText = document.getElementById('passwordless-text');
    const passwordlessSpinner = document.getElementById('passwordless-spinner');
    const passwordlessBtn = document.querySelector('#passwordless-form button[type="submit"]');
    
    passwordlessText.textContent = 'Creating account...';
    passwordlessSpinner.classList.remove('hidden');
    passwordlessBtn.disabled = true;
    hideMessages();
    
    try {
        // Use dedicated passwordless signup endpoint
        const signupResponse = await fetch('/api/auth/passwordless-signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email
            })
        });
        
        const signupResult = await signupResponse.json();
        
        if (signupResult.status === 'success') {
            document.getElementById('passwordless-success').classList.remove('hidden');
            document.getElementById('passwordless-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'none';
        } else {
            showError(signupResult.message || 'Failed to create account');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    } finally {
        passwordlessText.textContent = '🚀 Create Account & Send Magic Link';
        passwordlessSpinner.classList.add('hidden');
        passwordlessBtn.disabled = false;
    }
});

// Pre-fill email if passed in URL
const urlParams = new URLSearchParams(window.location.search);
const emailParam = urlParams.get('email');
if (emailParam) {
    document.getElementById('email').value = emailParam;
    document.getElementById('passwordless-email').value = emailParam;
}
</script>

<?php include 'include/footer.html'; ?> 