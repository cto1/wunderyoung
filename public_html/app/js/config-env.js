/**
 * Environment Configuration
 * Simple environment detection for proxy URLs
 */

// Function to get the correct proxy URL based on environment
function getProxyUrl() {
    return 'proxy-server/proxy.php';
}

// Make it globally available
if (typeof window !== 'undefined') {
    window.getProxyUrl = getProxyUrl;
}