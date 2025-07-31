document.addEventListener('DOMContentLoaded', () => {

    // ----- Email Capture Popup Function -----
    const sessionKey = 'popupShown';
    const popupDelay = 3000;
    const modalCheckbox = document.querySelector("#workbook-modal");
    const hasBeenShown = false;

    if (modalCheckbox && !hasBeenShown) {
        setTimeout(() => {
            modalCheckbox.checked = true;
            sessionStorage.setItem(sessionKey, 'true');
        }, popupDelay);
    } else {
        console.log("Modal already shown or checkbox not found, skipping...");
    }

    // ----- Dropdowns Auto Closing -----
    const dropdowns = document.querySelectorAll("details[data-auto-close]");

    if (dropdowns.length > 0) {
        document.addEventListener("click", function (e) {
            dropdowns.forEach((details) => {
                if (details.open && !details.contains(e.target)) {
                    details.removeAttribute("open");
                }
            });
        });

        dropdowns.forEach((el) => {
            el.addEventListener("toggle", function () {
                if (el.open) {
                    dropdowns.forEach((other) => {
                        if (other !== el) other.removeAttribute("open");
                    });
                }
            });
        });

        let resizeTimeout;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                dropdowns.forEach((details) => {
                    details.removeAttribute("open");
                });
            }, 200);
        });
    }

});







