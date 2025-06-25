/* ----------------------- Validate And Referesh Token ---------------------- */
/* 
 * IMPORTANT: This file requires api-utils.js to be loaded first
 * Add this to your HTML: <script src="js/api-utils.js"></script>
 */

let jwt_token = "";
console.log(jwt_token);

// Check token and refresh if needed
async function ensureValidToken() {
    jwt_token = localStorage.getItem('jwt_token');
    
    // If token is missing, redirect immediately
    if (!jwt_token) {
        window.location.href = './login.php';
        return false;
    }

    try {
        const tokenParts = jwt_token.split('.');
        const payload = JSON.parse(atob(tokenParts[1]));
        const expiryTime = payload.exp * 1000; // Convert to milliseconds
        const currentTime = Date.now();

        // If token is about to expire (less than 5 minutes remaining)
        if (expiryTime - currentTime < 5 * 60 * 1000) {
            // Check if api object is available
            if (typeof api === 'undefined' || typeof api.refreshToken !== 'function') {
                console.error('API utilities not loaded. Please include api-utils.js before this script.');
                window.location.href = './login.php';
                return false;
            }
            
            const newToken = await api.refreshToken();
            if (newToken) {
                localStorage.setItem('jwt_token', newToken);
                jwt_token = newToken;
                console.log('Token refreshed successfully');
            } else {
                console.warn('Token refresh failed - user may need to re-login');
                window.location.href = './login.php';
                return false;
            }
        }

        return true;
    } catch (error) {
        console.error('JWT validation error:', error);
        window.location.href = './login.php';
        return false;
    }
}

ensureValidToken().then(valid => {
    if (!valid) {
        console.warn('Token is invalid or refresh failed.');
    } else {
        console.log('Token is valid');
    }
});

console.log(jwt_token);

// Local storage utility functions
const getStoredData = (key) => {
    try {
        return localStorage.getItem(key);
    } catch (error) {
        console.error('Error reading from localStorage:', error);
        return null;
    }
};

const setStoredData = (key, value) => {
    try {
        localStorage.setItem(key, value);
    } catch (error) {
        console.error('Error writing to localStorage:', error);
    }
};

const removeStoredData = (key) => {
    try {
        localStorage.removeItem(key);
    } catch (error) {
        console.error('Error removing from localStorage:', error);
    }
};

const clearStoredData = () => {
    try {
        localStorage.clear();
    } catch (error) {
        console.error('Error clearing localStorage:', error);
    }
};

// Export functions for use in other files
window.getStoredData = getStoredData;
window.setStoredData = setStoredData;
window.removeStoredData = removeStoredData;
window.clearStoredData = clearStoredData;