/* ==========================================================
   VOTIFY
   Secure Voting Entry
   File : assets/js/security_check.js
========================================================== */

"use strict";

/* ==========================================================
   DOM ELEMENTS
========================================================== */

const agreeCheckbox = document.getElementById(
    "agreeCheckbox"
);

const continueButton = document.getElementById(
    "continueButton"
);

const fullscreenError = document.getElementById(
    "fullscreenError"
);

const customCheckbox = document.getElementById(
    "customCheckbox"
);

const checkboxIcon = document.getElementById(
    "checkboxIcon"
);

/* ==========================================================
   CONFIGURATION
========================================================== */

/*
 * security_check.php and voting.php are inside
 * the same pages/student/ directory.
 */
const VOTING_URL = "voting.php";

/* ==========================================================
   PROCESSING STATE
========================================================== */

let isProcessing = false;

/* ==========================================================
   SHOW FULLSCREEN ERROR
========================================================== */

function showFullscreenError() {

    if (!fullscreenError) {
        return;
    }

    fullscreenError.classList.remove(
        "hidden"
    );

}

/* ==========================================================
   HIDE FULLSCREEN ERROR
========================================================== */

function hideFullscreenError() {

    if (!fullscreenError) {
        return;
    }

    fullscreenError.classList.add(
        "hidden"
    );

}

/* ==========================================================
   UPDATE CUSTOM CHECKBOX
========================================================== */

function updateCustomCheckbox() {

    if (
        !agreeCheckbox ||
        !customCheckbox ||
        !checkboxIcon
    ) {

        return;

    }

    if (agreeCheckbox.checked) {

        customCheckbox.classList.remove(
            "border-slate-500",
            "bg-white/5"
        );

        customCheckbox.classList.add(
            "border-blue-500",
            "bg-blue-600",
            "shadow-lg",
            "shadow-blue-500/40",
            "scale-110"
        );

        checkboxIcon.classList.remove(
            "hidden"
        );

    }

    else {

        customCheckbox.classList.remove(
            "border-blue-500",
            "bg-blue-600",
            "shadow-lg",
            "shadow-blue-500/40",
            "scale-110"
        );

        customCheckbox.classList.add(
            "border-slate-500",
            "bg-white/5"
        );

        checkboxIcon.classList.add(
            "hidden"
        );

    }

}

/* ==========================================================
   ENABLE CONTINUE BUTTON
========================================================== */

function enableContinueButton() {

    if (!continueButton) {
        return;
    }

    continueButton.disabled = false;

    continueButton.classList.remove(
        "opacity-50",
        "cursor-not-allowed"
    );

}

/* ==========================================================
   DISABLE CONTINUE BUTTON
========================================================== */

function disableContinueButton() {

    if (!continueButton) {
        return;
    }

    continueButton.disabled = true;

    continueButton.classList.add(
        "opacity-50",
        "cursor-not-allowed"
    );

}

/* ==========================================================
   SHOW LOADING STATE
========================================================== */

function showLoadingState() {

    if (!continueButton) {
        return;
    }

    isProcessing = true;

    continueButton.disabled = true;

    continueButton.innerHTML = `

        <i class="ri-loader-4-line animate-spin text-xl"></i>

        Entering Secure Voting Room...

    `;

}

/* ==========================================================
   RESTORE BUTTON
========================================================== */

function restoreButton() {

    if (!continueButton) {
        return;
    }

    isProcessing = false;

    continueButton.innerHTML = `

        <i class="ri-arrow-right-circle-line text-xl"></i>

        Enter Secure Voting Room

    `;

    if (
        agreeCheckbox &&
        agreeCheckbox.checked
    ) {

        enableContinueButton();

    }

    else {

        disableContinueButton();

    }

}

/* ==========================================================
   CHECKBOX EVENT
========================================================== */

if (agreeCheckbox) {

    agreeCheckbox.addEventListener(
        "change",
        function () {

            hideFullscreenError();

            updateCustomCheckbox();

            if (agreeCheckbox.checked) {

                enableContinueButton();

            }

            else {

                disableContinueButton();

            }

        }
    );

}

/* ==========================================================
   INITIAL STATE
========================================================== */

disableContinueButton();

hideFullscreenError();

updateCustomCheckbox();

/* ==========================================================
   FULLSCREEN SUPPORT
========================================================== */

async function requestSecureFullscreen() {

    /*
     * Browser does not support Fullscreen API.
     */
    if (
        !document.documentElement.requestFullscreen
    ) {

        console.error(
            "Fullscreen API is not supported."
        );

        showFullscreenError();

        restoreButton();

        return false;

    }

    try {

        await document.documentElement.requestFullscreen();

        /*
         * Fullscreen successfully requested.
         * Continue to voting page.
         */
        window.location.href = VOTING_URL;

        return true;

    }

    catch (error) {

        console.error(
            "Fullscreen Error:",
            error
        );

        showFullscreenError();

        restoreButton();

        return false;

    }

}

/* ==========================================================
   CONTINUE BUTTON
========================================================== */

if (continueButton) {

    continueButton.addEventListener(
        "click",
        async function () {

            /*
             * Prevent multiple clicks.
             */
            if (isProcessing) {
                return;
            }

            hideFullscreenError();

            /*
             * Agreement is mandatory.
             */
            if (
                !agreeCheckbox ||
                !agreeCheckbox.checked
            ) {

                updateCustomCheckbox();

                disableContinueButton();

                return;

            }

            /*
             * Show loading state before
             * requesting fullscreen.
             */
            showLoadingState();

            await requestSecureFullscreen();

        }
    );

}

/* ==========================================================
   FULLSCREEN CHANGE EVENT
========================================================== */

document.addEventListener(
    "fullscreenchange",
    function () {

        /*
         * Fullscreen entered successfully.
         */
        if (document.fullscreenElement) {

            hideFullscreenError();

            return;

        }

        /*
         * If processing is active and the user
         * exits fullscreen before navigation,
         * restore the button.
         */
        if (isProcessing) {

            showFullscreenError();

            restoreButton();

        }

    }
);

/* ==========================================================
   PAGE VISIBILITY
========================================================== */

document.addEventListener(
    "visibilitychange",
    function () {

        /*
         * Do not change voting flow while the page
         * is being navigated.
         */
        if (
            document.visibilityState === "visible"
        ) {

            /*
             * Only clear stale fullscreen error
             * when the page is not processing.
             */
            if (!isProcessing) {

                hideFullscreenError();

            }

        }

    }
);

/* ==========================================================
   KEYBOARD ACCESSIBILITY
========================================================== */

if (agreeCheckbox) {

    agreeCheckbox.addEventListener(
        "keydown",
        function (event) {

            /*
             * Allow Space key to toggle the
             * custom checkbox naturally.
             */
            if (event.key === " ") {

                event.preventDefault();

                agreeCheckbox.checked =
                    !agreeCheckbox.checked;

                agreeCheckbox.dispatchEvent(
                    new Event("change")
                );

            }

        }
    );

}

/* ==========================================================
   INITIALIZE
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        disableContinueButton();

        hideFullscreenError();

        updateCustomCheckbox();

        console.log(
            "VOTIFY Secure Voting Entry Initialized"
        );

    }
);