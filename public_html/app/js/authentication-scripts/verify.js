
// Function to get the correct proxy URL
function getProxyUrl() {
    return 'proxy-server/proxy.php';
}

// Function to verify token from email and get JWT
async function verifyAndGetToken(email, token) {
    try {
        // Step 1: Verify the email token
        const verifyResponse = await fetch(`${getProxyUrl()}?api=verify_login&email=${encodeURIComponent(email)}&token=${token}`);
        const verifyData = await verifyResponse.json();
        
        if (verifyData.status !== "success") {
            throw new Error(verifyData.message || "Failed to verify email token");
        }
        
        // Step 2: Exchange the verified user data for a JWT
        const tokenResponse = await fetch("proxy-server/proxy.php?api=JWT_token", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ 
                user_id: verifyData.user_id,
                email: verifyData.email
            }),
        });

        const tokenData = await tokenResponse.json();

        if (tokenData.status !== "success" || !tokenData.token) {
            throw new Error(tokenData.message || "Failed to get token");
        }

        // ✅ Store token securely
        localStorage.setItem("jwt_token", tokenData.token);
        if (tokenData.user) {
            localStorage.setItem("user_id", tokenData.user.user_id);
            localStorage.setItem("org_id", tokenData.user.org_id);
            localStorage.setItem("user_email", tokenData.user.email);
            localStorage.setItem("user_role", tokenData.user.role);
        }
        console.log("jwt_token", localStorage.getItem("jwt_token"));
        console.log("user_id", localStorage.getItem("user_id"));
        console.log("org_id", localStorage.getItem("org_id"));
        console.log("user_email", localStorage.getItem("user_email"));
        console.log("user_role", localStorage.getItem("user_role"));

        return tokenData;
    } catch (error) {
        console.error("Token verification error:", error);
        throw error;
    }
}
