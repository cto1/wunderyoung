<?php 
$page_title = "Sign In - Yes Homework";
$page_description = "Sign in or create your Yes Homework account";
include 'include/header.html'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-6">✨ Magic Link Access</h2>
            
            <div class="text-center mb-6">
                <p class="text-base-content">Enter your email to sign in or create an account</p>
                <p class="text-sm text-base-content mt-2">We'll send you a secure link - no passwords needed!</p>
            </div>
            
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

            <!-- Magic Link Form -->
            <form id="magic-link-form" class="space-y-6">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Email Address</span>
                    </label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" class="input input-bordered input-lg" required>
                </div>

                <button type="submit" class="btn btn-primary w-full btn-lg">
                    <span id="submit-text">🚀 Send Magic Link</span>
                    <span id="loading-spinner" class="loading loading-spinner loading-sm hidden"></span>
                </button>
            </form>
                
            <div id="magic-success" class="alert alert-success hidden mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="font-bold">✨ Magic link sent!</h3>
                    <div class="text-xs">Check your email and click the link to access your account</div>
                </div>
            </div>

            <div class="text-center mt-6">
                <p class="text-xs text-base-content">
                    By continuing, you agree to our 
                    <a href="/terms.php" class="link link-primary">Terms of Service</a> and 
                    <a href="/privacy.php" class="link link-primary">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('magic-link-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    
    // Show loading state
    setLoading(true);
    hideMessages();
    
    try {
        // Try passwordless signup first (handles both new users and existing users)
        const response = await fetch('/api/auth/passwordless-signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showSuccess();
            document.getElementById('magic-link-form').style.display = 'none';
        } else {
            showError(result.message || 'Failed to send magic link');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    } finally {
        setLoading(false);
    }
});

function showSuccess() {
    document.getElementById('magic-success').classList.remove('hidden');
    document.getElementById('error-message').classList.add('hidden');
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
    document.getElementById('success-message').classList.add('hidden');
}

function hideMessages() {
    document.getElementById('magic-success').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
}

function setLoading(loading) {
    const submitText = document.getElementById('submit-text');
    const spinner = document.getElementById('loading-spinner');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (loading) {
        submitText.textContent = 'Sending magic link...';
        spinner.classList.remove('hidden');
        submitButton.disabled = true;
    } else {
        submitText.textContent = '🚀 Send Magic Link';
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