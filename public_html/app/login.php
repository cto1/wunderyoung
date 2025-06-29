<?php 
$page_title = "Login - Yes Homework";
$page_description = "Login to your Yes Homework account";
include 'include/header.html'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-6">Welcome Back</h2>
            
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
            <form id="login-form">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" class="input input-bordered" required placeholder="Enter your email address">
                </div>

                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-sophisticated" id="login-btn">
                        <span class="loading loading-spinner loading-sm hidden" id="login-spinner"></span>
                        <span id="login-btn-text">Send Login Link</span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-magic mr-1"></i>
                    We'll send you a secure login link via email
                </p>
            </div>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-sm mb-4">Don't have an account?</p>
                <a href="signup.php" class="btn btn-outline btn-sm">Create Account</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const loginBtn = document.getElementById('login-btn');
    const loginSpinner = document.getElementById('login-spinner');
    const loginBtnText = document.getElementById('login-btn-text');
    
    // Show loading state
    loginBtn.disabled = true;
    loginSpinner.classList.remove('hidden');
    loginBtnText.textContent = 'Sending...';
    
    // Hide previous messages
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    
    try {
        // Send magic link
        const response = await fetch('/app/proxy-server/proxy.php?api=auth_login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email
            })
        });
        
        // Debug: Log the raw response
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        console.log('Response status:', response.status);
        
        // Try to parse JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response was:', responseText);
            throw new Error('Invalid JSON response from server');
        }
        
        if (result.status === 'success') {
            showSuccess('Check your email! A login link has been sent to ' + email);
        } else {
            showError(result.message || 'Failed to send login link');
        }
        
    } catch (error) {
        console.error('Login error:', error);
        showError('Network error. Please try again.');
    } finally {
        // Reset button state
        loginBtn.disabled = false;
        loginSpinner.classList.add('hidden');
        loginBtnText.textContent = 'Send Login Link';
    }
});

function showSuccess(message) {
    document.getElementById('success-text').textContent = message;
    document.getElementById('success-message').classList.remove('hidden');
}

function showError(message) {
    document.getElementById('error-text').textContent = message;
    document.getElementById('error-message').classList.remove('hidden');
}
</script>

</body>
</html> 