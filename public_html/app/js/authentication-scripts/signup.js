/* --------------------------- Environment Configuration --------------------------- */

// Function to get the correct proxy URL
function getProxyUrl() {
    return 'proxy-server/proxy.php';
}

/* --------------------------- Signup API Response -------------------------- */

document.getElementById("signupButton").addEventListener("click", async function () {

    const signupButton = document.getElementById("signupButton");
    const email = document.getElementById("signupEmailInput").value;

    // Validate the email input
    if (!email || !validateEmail(email)) {
        showMessage("Please enter a valid email.", "error");
        return;
    }

    // Show signing... text
    signupButton.textContent = "Signing...";
    signupButton.disabled = true;

    // Payload for the API request
    const payload = {
        email: email
    };

    try {
        const response = await fetch(`${getProxyUrl()}?api=signup`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (response.ok) {
            if (result.status === "error") {
                showMessage(`${result.message}`, "error");
            } else {
                showMessage("Signup successful! Redirecting...", "success");
                setTimeout(() => {
                    window.location.href = "./login.php";
                }, 1500);
            }
        } else {
            showMessage(`Error: ${result.message}`, "error");
        }
    } catch (error) {
        console.error("Error:", error);
        showMessage("Something went wrong. Please try again.", "error");
    } finally {
        // Reset button text and state
        signupButton.textContent = "Sign Up";
        signupButton.disabled = false;
    }
});

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
    }, 2000);
}
