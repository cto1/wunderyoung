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

});







