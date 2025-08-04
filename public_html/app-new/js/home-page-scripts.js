document.addEventListener('DOMContentLoaded', () => {

    //  --- [Home-Page] Hero Section Visual Functionality ---
    const playButton = document.getElementById('play-button');
    const desktopImageGroups = document.querySelectorAll('.image-group');
    const mobileImageGroups = document.querySelectorAll('.mobile-image-group');
    let currentDesktopGroupIndex = 0;
    let currentMobileGroupIndex = 0;
    const isMobileScreen = () => window.innerWidth < 640;
    if (playButton && (desktopImageGroups.length > 0 || mobileImageGroups.length > 0)) {
        playButton.addEventListener('click', () => {
            if (isMobileScreen()) {
                if (mobileImageGroups.length > 0) {
                    mobileImageGroups[currentMobileGroupIndex].classList.add('hidden');
                    currentMobileGroupIndex = (currentMobileGroupIndex + 1) % mobileImageGroups.length;
                    mobileImageGroups[currentMobileGroupIndex].classList.remove('hidden');
                }
            } else {
                if (desktopImageGroups.length > 0) {
                    desktopImageGroups[currentDesktopGroupIndex].classList.add('hidden');
                    currentDesktopGroupIndex = (currentDesktopGroupIndex + 1) % desktopImageGroups.length;
                    desktopImageGroups[currentDesktopGroupIndex].classList.remove('hidden');
                }
            }
        });
    }


    // --- [Home-Page] Features Section Demo Preview ---
    const languageSelect = document.getElementById('language-select');
    const demoOutput = document.getElementById('demo-output');
    if (languageSelect && demoOutput) {
        languageSelect.addEventListener('change', (event) => {
            const selectedText = event.target.options[event.target.selectedIndex].text;
            demoOutput.textContent = selectedText;
            demoOutput.classList.remove('text-base-content/40');
            demoOutput.classList.add('text-base-content', 'font-bold', 'text-2xl', 'font-[\'Nunito\']', 'tracking-wider');
        });
    }

    
    // --- [Home-Page] Social Proof Section Reviews Slider ---
    var enableReviewsSlider = true;
    if (enableReviewsSlider) {
        var swiper = new Swiper(".reviewsSwiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            navigation: {
                nextEl: ".swiper-button-next-custom",
                prevEl: ".swiper-button-prev-custom",
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            keyboard: {
                enabled: true,
            },
            mousewheel: {
                invert: true,
            },
        });
    } else {
        console.log("Swiper for reviews is not enabled.");
    }

});