
/* -------------------------------------------------------------------------- */
/* ----------------------- Environment Configuration ------------------------- */
/* -------------------------------------------------------------------------- */

// Function to get the correct proxy URL
function getProxyUrl() {
    return 'proxy-server/proxy.php';
}

/* -------------------------------------------------------------------------- */
/*                     Listen for Custom Event Dispatched                     */
/* -------------------------------------------------------------------------- */

/* ------------------------------ (Logo Picker) ----------------------------- */

// Listen for the custom event dispatched from fetchCurrentLogo()
document.addEventListener('logoSettingsFetched', (event) => {
    const { logoPickerSrc } = event.detail;

    // Save the fetched logo source in localStorage for persistence
    if (logoPickerSrc) {
        localStorage.setItem('logoSrc', logoPickerSrc);
    }

    // Update all elements with the 'dynamic_main_logo' class
    const dynamicGroupElements = document.querySelectorAll('.dynamic_main_logo');
    dynamicGroupElements.forEach((element) => {
        element.src = logoPickerSrc || './assets/logos/exactsum.png'; // Fallback to default logo if null/undefined
    });
});


/* -------------------------------------------------------------------------- */
/*         On page load, retrieve the stored resources and apply them         */
/* -------------------------------------------------------------------------- */

/* ------------------------------ (Logo Picker) ----------------------------- */

document.addEventListener('DOMContentLoaded', async () => {
    const cacheKey = 'logoSrc';

    // Check if the logo is stored in localStorage
    const storedLogoSrc = localStorage.getItem(cacheKey);

    if (storedLogoSrc) {
        // Use cached logo instantly
        applyLogo(storedLogoSrc);
    } else {
        try {
            // Fetch API data for the logo (adjust the URL as needed)
            const response = await fetch(`${getProxyUrl()}?api=get_settings&org_id=${localStorage.getItem('org_id')}`, {
                method: 'GET',
                cache: 'force-cache',
                keepalive: true,
                headers: { 
                    "Connection": 'keep-alive', 
                    "Authorization": `Bearer ${jwt_token}` 
                }
            });

            if (!response.ok) throw new Error('Failed to fetch logo');

            const data = await response.json();
            const logoSrc = data.logoSrc || './assets/logos/exactsum.png';

            // Store in LocalStorage (no expiry)
            localStorage.setItem(cacheKey, logoSrc);

            // Preload and apply logo
            applyLogo(logoSrc);
        } catch (error) {
            console.error('Error fetching logo:', error);
        }
    }
});

function applyLogo(logoSrc) {
    const preloadedImg = new Image();
    preloadedImg.src = logoSrc;
    preloadedImg.onload = () => {
        document.querySelectorAll('.dynamic_main_logo').forEach((img) => {
            img.src = logoSrc;
        });
    };
}