document.addEventListener('DOMContentLoaded', function () {

    const authForm = document.getElementById('auth-form');
    if (!authForm) return;

    const emailInput = document.getElementById('loginEmailInput');
    const passwordField = document.getElementById('passwordField');
    const passwordInput = document.getElementById('loginPasswordInput');
    const loginButton = document.getElementById('loginButton');
    const toggleButton = document.getElementById('toggleAuthMethod');
    const messageContainer = document.getElementById('messageContainer');
    const loginFormTitle = document.getElementById('login-form-title');
    const magicFormTitle = document.getElementById('magic-form-title');
    const passwordToggleButton = document.getElementById('password-toggle-button');
    const passwordToggleIcon = document.getElementById('password-toggle-icon');

    let authMethod = 'password';

    const validateEmail = (email) => /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email);

    const showMessage = (message, type = 'error') => {
        messageContainer.textContent = message;
        messageContainer.className = `text-center text-sm font-semibold ${type === 'success' ? 'text-success' : 'text-error'}`;

        setTimeout(() => {
        messageContainer.textContent = '';
    }, 3000);
    };

    const updateUI = () => {
        const isPasswordMode = authMethod === 'password';

        passwordField.classList.toggle('hidden', !isPasswordMode);
        loginFormTitle.classList.toggle('hidden', !isPasswordMode);
        magicFormTitle.classList.toggle('hidden', isPasswordMode);
        passwordInput.required = isPasswordMode;

        loginButton.textContent = isPasswordMode ? authForm.dataset.textLogin : authForm.dataset.textMagicLink;
        toggleButton.textContent = isPasswordMode ? authForm.dataset.toggleMagic : authForm.dataset.togglePassword;
    };

    const handleFormSubmit = async () => {
        const email = emailInput.value;
        const password = passwordInput.value;

        if (!validateEmail(email)) {
            return showMessage('Please enter a valid email address.', 'error');
        }
        if (authMethod === 'password' && !password) {
            return showMessage('Please enter your password.', 'error');
        }

        loginButton.disabled = true;
        loginButton.textContent = authForm.dataset.textLoading;
        messageContainer.textContent = '';

        try {
            let endpoint = authMethod === 'password' ? '/api/auth/password-login' : '/api/auth/passwordless-signup';
            let payload = authMethod === 'password' ? { email, password } : { email };

            const response = await fetch(endpoint, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
            });
            const result = await response.json();

            if (result.status === "success") {
                if (authMethod === 'password' && result.token) {
                    localStorage.setItem('jwt_token', result.token);
                    showMessage('Success! Redirecting you now...', 'success');
                    setTimeout(() => { window.location.href = authForm.dataset.redirectUrl; }, 1000);
                } else {
                    showMessage('Magic link sent! Please check your email.', 'success');
                }
            } else {
                showMessage(result.message || 'An unknown error occurred.', "error");
            }
        } catch (error) {
            showMessage('A network error occurred. Please try again later.', 'error');
        } finally {
            loginButton.disabled = false;
            updateUI();
        }
    };

    toggleButton.addEventListener('click', () => {
        authMethod = (authMethod === 'password') ? 'magic-link' : 'password';
        messageContainer.textContent = '';
        updateUI();
    });

    loginButton.addEventListener('click', handleFormSubmit);

    passwordInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            handleFormSubmit();
        }
    });

    passwordToggleButton.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        passwordToggleIcon.classList.toggle('fa-eye', !isPassword);
        passwordToggleIcon.classList.toggle('fa-eye-slash', isPassword);
    });

    updateUI();
});