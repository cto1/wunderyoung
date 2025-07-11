<?php include 'include/header.html'; ?>

<body>

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-base-200 px-4 py-16">

        <!-- Animated background elements -->
        <div class="animate-float-1 absolute top-0 left-0 -z-0 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="animate-float-1 absolute bottom-0 right-0 -z-0 h-96 w-96 rounded-full bg-accent/10 blur-3xl" style="animation-delay: -5s;"></div>

        <!-- Content -->
        <div class="relative w-full max-w-md rounded-xl border border-white/10 bg-gradient-to-br from-base-200 to-base-300 shadow-2xl backdrop-blur-lg">
            <div class="relative rounded-[12px] bg-base-100/80 p-6 sm:p-8 backdrop-blur-lg border border-primary">

                <!-- Border elements -->
                <div class="pointer-events-none absolute top-0 left-0 h-10 w-10 rounded-tl-xl border-l-4 border-t-4 border-primary/50"></div>
                <div class="pointer-events-none absolute top-0 right-0 h-10 w-10 rounded-tr-xl border-r-4 border-t-4 border-primary/50"></div>
                <div class="pointer-events-none absolute bottom-0 left-0 h-10 w-10 rounded-bl-xl border-l-4 border-b-4 border-primary/50"></div>
                <div class="pointer-events-none absolute bottom-0 right-0 h-10 w-10 rounded-br-xl border-r-4 border-b-4 border-primary/50"></div>

                <div>
                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="484.000000pt" height="346.000000pt"
                        viewBox="0 0 484.000000 346.000000" preserveAspectRatio="xMidYMid meet"
                        class="h-10 w-10 text-primary mx-auto">
                        <g transform="translate(0.000000,346.000000) scale(0.100000,-0.100000)" fill="currentColor"
                            stroke="none">
                            <path
                                d="M2220 3449 c-501 -55 -925 -239 -1307 -566 -440 -375 -726 -927 -782 -1507 -7 -64 -11 -364 -11 -718 l0 -608 215 0 215 0 0 613 c0 623 3 697 40 876 54 256 167 509 320 716 188 252 420 446 695 580 203 99 354 146 580 181 88 13 151 15 315 11 221 -6 307 -19 502 -76 442 -129 867 -463 1088 -856 l34 -60 -49 38 c-221 169 -500 300 -760 356 -275 60 -586 53 -863 -18 -314 -81 -603 -246 -834 -475 -245 -242 -402 -513 -491 -846 -50 -187 -57 -268 -57 -669 l0 -371 209 0 208 0 6 357 c7 425 17 503 92 715 123 344 420 655 770 806 252 109 577 142 852 87 392 -80 727 -314 938 -657 25 -39 43 -74 40 -76 -2 -3 -22 7 -43 21 -124 81 -339 164 -506 194 -119 21 -342 21 -463 -1 -386 -68 -716 -286 -928 -614 -65 -101 -129 -240 -159 -346 -31 -106 -56 -286 -56 -402 l0 -84 214 0 213 0 6 133 c6 154 27 240 87 365 98 205 241 348 443 447 291 141 634 122 913 -50 91 -56 232 -192 289 -279 88 -136 141 -308 151 -498 l7 -118 215 0 214 0 -5 653 c-4 594 -7 664 -25 784 -77 489 -282 902 -625 1260 -355 369 -813 604 -1343 688 -120 19 -447 27 -564 14z" />
                        </g>
                    </svg>
                    </div>

                <!-- Headline  -->
                <div class="text-center">

                    <!-- Login Form Title + SubTitle -->
                    <div id="login-form-title">
                        <h2 class="mt-2 font-['Teko'] font-bold uppercase tracking-tight leading-tight">
                            <span class="text-5xl bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent"><?= __("login_title_password"); ?></span>
                        </h2>
                        <p class="text-base-content"><?= __("login_subtitle_password"); ?></p>
                    </div>

                    <!-- Magic Form Title + SubTitle -->
                    <div id="magic-form-title" class="hidden">
                        <h2 class="mt-2 font-['Teko'] font-bold uppercase tracking-tight leading-tight">
                            <span class="text-5xl bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent"><?= __("login_title_part1"); ?></span>
                            <br>
                            <span class="text-3xl relative -top-2"><?= __("login_title_part2"); ?></span>
                        </h2>
                        <p class="text-base-content"><?= __("login_subtitle_magic_link_1"); ?></p>
                        <p class="text-sm text-base-content"><?= __("login_subtitle_magic_link_2"); ?></p>
                    </div>
                </div>

                <!-- Gradient Border -->
                <div class="h-[2px] w-full bg-gradient-to-r from-primary via-accent to-secondary my-6"></div>

                <!-- Form -->
                <form id="auth-form" class="space-y-4"
                    data-text-login="<?= __("login_button_login"); ?>"
                    data-text-magic-link="<?= __("login_button_magic_link"); ?>"
                    data-text-loading="<?= __("login_button_loading"); ?>"
                    data-toggle-magic="<?= __("login_toggle_to_magic_link"); ?>"
                    data-toggle-password="<?= __("login_toggle_to_password"); ?>">

                    <!-- Email Input -->
                    <div class="form-control">
                        <input type="email" id="loginEmailInput" name="email" placeholder="<?= __("login_email_label"); ?>" class="input input-bordered w-full border-base-content bg-base-100 focus:border-primary focus:outline-none h-14" required>
                    </div>

                    <!-- Pasword Input -->
                    <div class="form-control" id="passwordField">
                        <div class="relative">
                            <input type="password" id="loginPasswordInput" name="password" placeholder="<?= __("login_placeholder_password"); ?>" class="input input-bordered w-full border-base-content bg-base-100 focus:border-primary focus:outline-none pr-10 h-14" required>
                            <button type="button" id="password-toggle-button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/70 hover:text-primary">
                                <i id="password-toggle-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Toggle Link  -->
                    <div class="mt-4 text-center">
                        <button id="toggleAuthMethod" type="button" class="link-hover link text-sm text-base-content hover:text-primary underline">
                        </button>
                    </div>

                    <!-- Button -->
                    <button type="button" id="loginButton" class="btn h-auto min-h-0 w-full rounded-md border-none bg-gradient-to-r from-primary via-accent to-secondary py-3.5 font-['Teko'] text-xl uppercase tracking-wider text-white transition-all duration-500 hover:-translate-y-0.5 bg-[length:200%_auto] hover:bg-[position:100%_0]">
                    </button>

                    <!-- Message Container -->
                    <p id="messageContainer" class="text-center text-sm font-semibold"></p>

                </form>

                <!-- Gradient Border -->
                <div class="h-[2px] w-full bg-gradient-to-r from-primary via-accent to-secondary my-6"></div>

                <!-- Bottom -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-base-content">
                        <?= __("login_terms_prefix"); ?>
                        <a href="/terms.php" class="link hover:text-primary"><?= __("login_terms_link"); ?></a> &
                        <a href="/privacy.php" class="link hover:text-primary"><?= __("login_privacy_link"); ?></a>
                    </p>
                </div>

            </div>
        </div>
    </main>

    <!-- ---------- JS Scripts ---------- -->

    <!-- [Login-Page Scripts] -->
    <script src="js/login-page-scripts.js"></script>

</body>

</html>