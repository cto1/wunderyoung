/* ----------------------- API Utility Functions ----------------------- */

/**
 * API utility object containing common API functions
 */
const api = {
    /**
     * Refresh JWT token using the refresh endpoint
     * @returns {string|null} New token or null if refresh failed
     */
    async refreshToken() {
        const currentToken = localStorage.getItem('jwt_token');
        if (!currentToken) {
            console.warn('No current token found for refresh');
            return null;
        }
        
        try {
            console.log('Refreshing JWT token...');
            
            const response = await fetch('/api/auth/refresh-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${currentToken}`
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.token) {
                console.log('Token refreshed successfully');
                return data.token;
            } else {
                console.error('Token refresh failed:', data.message || 'Unknown error');
                return null;
            }
            
        } catch (error) {
            console.error('Token refresh request failed:', error);
            return null;
        }
    },

    /**
     * Make an authenticated API request
     * @param {string} url - The API endpoint URL
     * @param {object} options - Fetch options (method, headers, body, etc.)
     * @returns {Promise<Response>} Fetch response
     */
    async authenticatedRequest(url, options = {}) {
        const token = localStorage.getItem('jwt_token');
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                ...(token && { 'Authorization': `Bearer ${token}` }),
                ...options.headers
            }
        };
        
        return fetch(url, { ...defaultOptions, ...options });
    },

    /**
     * Check if user is authenticated
     * @returns {boolean} True if user has a valid token
     */
    isAuthenticated() {
        const token = localStorage.getItem('jwt_token');
        if (!token) return false;
        
        try {
            const tokenParts = token.split('.');
            const payload = JSON.parse(atob(tokenParts[1]));
            const expiryTime = payload.exp * 1000;
            const currentTime = Date.now();
            
            return currentTime < expiryTime;
        } catch (error) {
            console.error('Token validation error:', error);
            return false;
        }
    },

    /**
     * Get current user data from token
     * @returns {object|null} User data or null if no valid token
     */
    getCurrentUser() {
        const token = localStorage.getItem('jwt_token');
        if (!token) return null;
        
        try {
            const tokenParts = token.split('.');
            const payload = JSON.parse(atob(tokenParts[1]));
            return payload.data;
        } catch (error) {
            console.error('Error parsing token:', error);
            return null;
        }
    },

    /**
     * Logout user by clearing all stored data
     */
    logout() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('org_id');
        localStorage.removeItem('user_email');
        localStorage.removeItem('user_id');
        localStorage.removeItem('user_role');
        
        // Redirect to login page
        window.location.href = './login.php';
    },

    /**
     * Check for JWT/localStorage mismatches and fix them
     * @returns {boolean} True if data is consistent, false if mismatch was found and fixed
     */
    validateTokenConsistency() {
        const token = localStorage.getItem('jwt_token');
        const localUserId = localStorage.getItem('user_id');
        const localOrgId = localStorage.getItem('org_id');
        const localEmail = localStorage.getItem('user_email');
        
        if (!token) {
            console.warn('No JWT token found');
            return false;
        }
        
        try {
            const tokenParts = token.split('.');
            const payload = JSON.parse(atob(tokenParts[1]));
            const jwtData = payload.data;
            
            // Check for mismatches
            const mismatches = [];
            if (localUserId !== jwtData.user_id) {
                mismatches.push(`user_id: localStorage(${localUserId}) != JWT(${jwtData.user_id})`);
            }
            if (localOrgId !== jwtData.org_id) {
                mismatches.push(`org_id: localStorage(${localOrgId}) != JWT(${jwtData.org_id})`);
            }
            if (localEmail !== jwtData.email) {
                mismatches.push(`email: localStorage(${localEmail}) != JWT(${jwtData.email})`);
            }
            
            if (mismatches.length > 0) {
                console.error('🚨 JWT/localStorage mismatch detected:');
                mismatches.forEach(mismatch => console.error('  - ' + mismatch));
                console.error('🔧 Clearing invalid data and redirecting to login...');
                
                // Clear all data and redirect
                this.logout();
                return false;
            }
            
            return true;
            
        } catch (error) {
            console.error('Error validating token consistency:', error);
            console.error('🔧 Clearing invalid token and redirecting to login...');
            this.logout();
            return false;
        }
    },

    /**
     * Make a simple authenticated API request
     * @param {string} endpoint - API endpoint (without /api prefix)
     * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
     * @param {object} data - Request body data (for POST/PUT requests)
     * @returns {Promise<object>} Response data
     */
    async makeRequest(endpoint, method = 'GET', data = null) {
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            throw new Error('No authentication token');
        }

        const options = {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(`/api${endpoint}`, options);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    }
};

// Make api object globally available
window.api = api; 