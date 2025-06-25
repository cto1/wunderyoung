/* --------------------------- Environment Configuration --------------------------- */

// Function to get the correct proxy URL
function getProxyUrl() {
    return 'proxy-server/proxy.php';
}

/* --------------------------- Dual Authentication Support --------------------------- */

let authMethod = 'password'; // Default to password login

// Toggle between authentication methods
document.getElementById('toggleAuthMethod').addEventListener('click', function() {
    const passwordField = document.getElementById('passwordField');
    const toggleButton = document.getElementById('toggleAuthMethod');
    const loginButton = document.getElementById('loginButton');
    
    if (authMethod === 'password') {
        // Switch to magic link mode
        authMethod = 'magic-link';
        passwordField.style.display = 'none';
        toggleButton.textContent = 'Or use password instead';
        loginButton.textContent = 'Send Magic Link';
    } else {
        // Switch to password mode
        authMethod = 'password';
        passwordField.style.display = 'block';
        toggleButton.textContent = 'Or use magic link instead';
        loginButton.textContent = 'Login';
    }
});

// Main login handler
document.getElementById("loginButton").addEventListener("click", async function () {
    const loginButton = document.getElementById("loginButton");
    const email = document.getElementById("loginEmailInput").value;

    // Validate the email input
    if (!email || !validateEmail(email)) {
        showMessage("Please enter a valid email.", "error");
        return;
    }

    if (authMethod === 'password') {
        await handlePasswordLogin(email, loginButton);
    } else {
        await handleMagicLinkLogin(email, loginButton);
    }
});

// Handle password-based login
async function handlePasswordLogin(email, loginButton) {
    const password = document.getElementById("loginPasswordInput").value;
    
    if (!password) {
        showMessage("Please enter your password.", "error");
        return;
    }
    
    // Disable button and show loading state
    loginButton.disabled = true;
    loginButton.textContent = "Logging in...";
    
    try {
        const response = await fetch("/api/auth/password-login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ email, password }),
        });
        
        const result = await response.json();
        
        if (result.status === "success") {
            // Generate JWT token using the existing flow
            const tokenResponse = await fetch('/api/auth/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    user_id: result.user_id,
                    email: result.email 
                }),
            });
            
            const tokenData = await tokenResponse.json();
            
            if (tokenData.status === 'success') {
                // Store JWT and user data (same as magic link flow)
                localStorage.setItem('jwt_token', tokenData.token);
                localStorage.setItem('user_id', tokenData.user.user_id);
                localStorage.setItem('org_id', tokenData.user.org_id);
                localStorage.setItem('user_email', tokenData.user.email);
                localStorage.setItem('user_role', tokenData.user.role);
                
                // Success message
                showMessage("Login successful! Redirecting...", "success");
                
                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = '/app/vault.php';
                }, 1000);
            } else {
                showMessage(tokenData.message, "error");
            }
        } else {
            // Handle specific error types
            if (result.message.includes('Too many failed attempts')) {
                showMessage(result.message + " You can still use magic link login.", "error");
                // Show magic link option
                setTimeout(() => {
                    if (authMethod === 'password') {
                        document.getElementById('toggleAuthMethod').click();
                    }
                }, 3000);
            } else if (result.message.includes('Account temporarily locked')) {
                showMessage(result.message + " Magic link login is still available.", "error");
                // Show magic link option
                setTimeout(() => {
                    if (authMethod === 'password') {
                        document.getElementById('toggleAuthMethod').click();
                    }
                }, 3000);
            } else {
                showMessage(result.message, "error");
            }
        }
    } catch (error) {
        console.error("Password login error:", error);
        showMessage("Login failed. Please try again.", "error");
    } finally {
        loginButton.disabled = false;
        loginButton.textContent = "Login";
    }
}

// Handle magic link login (existing functionality)
async function handleMagicLinkLogin(email, loginButton) {
    // Disable button and show requesting... text
    loginButton.textContent = "Requesting...";
    loginButton.disabled = true;

    // Payload for the API request
    const payload = { email: email };

    try {
        const response = await fetch(`${getProxyUrl()}?api=request_login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (result.status === "success") {
            window.location.href = "./check-email.php";
        } else {
            showMessage(result.message, "error");
        }
    } catch (error) {
        console.error("Magic link error:", error);
        showMessage("Something went wrong. Please try again.", "error");
    } finally {
        // Reset button state after request completes
        loginButton.textContent = "Send Magic Link";
        loginButton.disabled = false;
    }
}

// Simple email validation function
function validateEmail(email) {
    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(email);
}

// Function to show messages on the webpage
function showMessage(message, type) {
    const messageContainer = document.getElementById("messageContainer");
    messageContainer.textContent = message;

    if (type === "success") {
        messageContainer.className = "absolute left-0 -bottom-0.5 text-xs font-semibold ml-1 text-green-500";
    } else {
        messageContainer.className = "absolute left-0 -bottom-0.5 text-xs font-semibold ml-1 text-red-500";
    }

    setTimeout(() => {
        messageContainer.textContent = "";
    }, 10000);
}

// Initialize the page in password mode
document.addEventListener('DOMContentLoaded', function() {
    const loginButton = document.getElementById('loginButton');
    if (loginButton && authMethod === 'password') {
        loginButton.textContent = 'Login';
    }
});
