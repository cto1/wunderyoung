<?php 
$page_title = "Magic Login - Yes Homework";
$page_description = "Secure magic link login";
include '../app/include/header.html'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="card w-full max-w-md card-sophisticated shadow-2xl">
        <div class="card-body text-center">
            <div id="loading" class="space-y-4">
                <div class="loading loading-spinner loading-lg text-primary"></div>
                <h2 class="text-xl font-semibold">🔐 Signing you in...</h2>
                <p class="text-base-content">Please wait while we verify your magic link</p>
            </div>
            
            <div id="success" class="space-y-4 hidden">
                <div class="text-6xl">✨</div>
                <h2 class="text-2xl font-bold text-success">Welcome back!</h2>
                <p class="text-base-content">You have been successfully signed in.</p>
                <p class="text-sm text-base-content">Redirecting to your dashboard...</p>
            </div>
            
            <div id="error" class="space-y-4 hidden">
                <div class="text-6xl">❌</div>
                <h2 class="text-2xl font-bold text-error">Login Failed</h2>
                <p id="error-message" class="text-base-content"></p>
                <a href="/app/login.php" class="btn btn-primary">Try Again</a>
            </div>
        </div>
    </div>
</div>

<script>
async function handleMagicLogin() {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    
    if (!token) {
        showError('Invalid magic link. Please request a new one.');
        return;
    }
    
    try {
        const response = await fetch('/api/auth/magic-login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                token: token
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Store token
            localStorage.setItem('authToken', result.token);
            localStorage.setItem('userName', result.name);
            
            showSuccess();
            
            // Redirect to worksheets page after 2 seconds
            setTimeout(() => {
                window.location.href = '/app/worksheets.php';
            }, 2000);
        } else {
            showError(result.message || 'Magic link has expired or is invalid. Please request a new one.');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    }
}

function showSuccess() {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('success').classList.remove('hidden');
    document.getElementById('error').classList.add('hidden');
}

function showError(message) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('success').classList.add('hidden');
    document.getElementById('error').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

// Start the magic login process when page loads
handleMagicLogin();
</script>

<?php include '../app/include/footer.html'; ?>