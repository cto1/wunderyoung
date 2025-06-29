<?php 
$page_title = "Sign In - Yes Homework";
$page_description = "Sign in to your Yes Homework account";
include 'include/header.html'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-6">Sign In</h2>
            
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

            <!-- Login Form -->
            <form id="login-form" class="space-y-4">
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
                    <input type="password" id="password" name="password" placeholder="Enter your password" class="input input-bordered" required>
                    <label class="label">
                        <a href="#" class="label-text-alt link link-primary">Forgot password?</a>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Remember me</span>
                        <input type="checkbox" id="remember" class="checkbox checkbox-primary">
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full btn-lg">
                    <span id="submit-text">Sign In</span>
                    <span id="loading-spinner" class="loading loading-spinner loading-sm hidden"></span>
                </button>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-sm text-gray-600">Don't have an account?</p>
                <a href="/app/signup.php" class="btn btn-outline btn-primary w-full mt-2">Create Account</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;
    
    // Show loading state
    setLoading(true);
    hideMessages();
    
    try {
        const response = await fetch('/api/UserAuthAPI.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Store token
            localStorage.setItem('authToken', result.token);
            
            // Store user info if remember is checked
            if (remember) {
                localStorage.setItem('userName', result.name);
                localStorage.setItem('userEmail', email);
            }
            
            // Redirect immediately to worksheets page
            window.location.href = '/app/worksheets.php';
        } else {
            showError(result.message || 'Sign in failed');
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
        submitText.textContent = 'Signing In...';
        spinner.classList.remove('hidden');
        submitButton.disabled = true;
    } else {
        submitText.textContent = 'Sign In';
        spinner.classList.add('hidden');
        submitButton.disabled = false;
    }
}

// Pre-fill email if passed in URL
const urlParams = new URLSearchParams(window.location.search);
const emailParam = urlParams.get('email');
if (emailParam) {
    document.getElementById('email').value = emailParam;
}

// Check if user is already logged in
const authToken = localStorage.getItem('authToken');
if (authToken) {
    // Redirect to worksheets page if already logged in
    window.location.href = '/app/worksheets.php';
}
</script>

<?php include 'include/footer.html'; ?> 