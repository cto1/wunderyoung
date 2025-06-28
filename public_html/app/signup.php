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
            <form id="signup-form">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" class="input input-bordered" required>
                </div>

                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Password (optional)</span>
                    </label>
                    <input type="password" id="password" class="input input-bordered" placeholder="Leave empty for passwordless signup" minlength="8">
                    <label class="label">
                        <span class="label-text-alt">Leave empty for passwordless account, or set a password (8+ characters)</span>
                    </label>
                </div>

                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-sophisticated" id="signup-btn">
                        <span class="loading loading-spinner loading-sm hidden" id="signup-spinner"></span>
                        <span id="signup-btn-text">Create Account</span>
                    </button>
                </div>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-sm mb-4">Already have an account?</p>
                <a href="login.php" class="btn btn-outline btn-sm">Login</a>
            </div>

            <!-- Terms Notice -->
            <div class="text-center mt-4">
                <p class="text-xs text-gray-600">
                    By creating an account, you agree to our 
                    <a href="../terms.php" class="link link-primary" target="_blank">Terms of Service</a> and 
                    <a href="../privacy.php" class="link link-primary" target="_blank">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('signup-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const signupBtn = document.getElementById('signup-btn');
    const signupSpinner = document.getElementById('signup-spinner');
    const signupBtnText = document.getElementById('signup-btn-text');
    
    // Show loading state
    signupBtn.disabled = true;
    signupSpinner.classList.remove('hidden');
    signupBtnText.textContent = 'Creating Account...';
    
    // Hide previous messages
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    
    try {
        const requestData = { email: email };
        if (password.trim()) {
            requestData.password = password;
        }
        
        const response = await fetch('../api/auth/signup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Show success message
            showSuccess('🎉 Account created successfully! Check your email for getting started instructions.');
            
            // Clear the form
            document.getElementById('signup-form').reset();
        } else {
            showError(result.message || 'Signup failed');
        }
        
    } catch (error) {
        console.error('Signup error:', error);
        showError('Network error. Please try again.');
    } finally {
        // Reset button state
        signupBtn.disabled = false;
        signupSpinner.classList.add('hidden');
        signupBtnText.textContent = 'Create Account';
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