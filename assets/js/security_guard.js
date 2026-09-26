/* ==========================================================
   VOTIFY
   Security Guard
   File : assets/js/security_guard.js
========================================================== */

"use strict";


/* ==========================================================
   PRODUCTION / TESTING MODE
========================================================== */

/*
    false = Normal VOTIFY security behavior
    true  = Temporarily disable security for development testing

    IMPORTANT:
    Keep this FALSE for production.
*/

const VOTIFY_TESTING_MODE = false;


/* ==========================================================
   SECURITY CONFIGURATION
========================================================== */

const VotingSecurity = {

    maxWarnings: 2,

    warningCount: 0,

    logoutUrl:
        "../../backend/student/security_logout.php",

    isFullscreenRequested: false,

    modal: null,

    reasonText: null,

    warningText: null,

    resumeButton: null,

    isHandlingViolation: false,

    ignoreNextFullscreenEvent: false,

    entryModal: null,

    startVotingButton: null

};


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        cacheElements();

        initializeSecurity();

    }
);


/* ==========================================================
   CACHE DOM ELEMENTS
========================================================== */

function cacheElements() {

    VotingSecurity.modal =
        document.getElementById(
            "securityWarningModal"
        );


    VotingSecurity.reasonText =
        document.getElementById(
            "securityViolationReason"
        );


    VotingSecurity.warningText =
        document.getElementById(
            "securityAttempt"
        );


    VotingSecurity.resumeButton =
        document.getElementById(
            "resumeVotingButton"
        );


    VotingSecurity.entryModal =
        document.getElementById(
            "securityEntryModal"
        );


    VotingSecurity.startVotingButton =
        document.getElementById(
            "startVotingButton"
        );

}


/* ==========================================================
   INITIALIZE SECURITY
========================================================== */

function initializeSecurity() {

    /*
        During testing, completely pause security.
    */

    if (VOTIFY_TESTING_MODE) {

        console.info(
            "VOTIFY: Security Guard is PAUSED (Testing Mode)."
        );

        hideEntryModalForTesting();

        return;

    }


    /*
        Production security initialization.
    */

    initializeEventListeners();

    initializeResumeButton();

    initializeStartVotingButton();

}


/* ==========================================================
   TESTING MODE ENTRY MODAL
========================================================== */

function hideEntryModalForTesting() {

    if (
        !VotingSecurity.entryModal
    ) {

        return;

    }


    VotingSecurity.entryModal.classList.add(
        "hidden"
    );

    VotingSecurity.entryModal.classList.remove(
        "flex"
    );

}


/* ==========================================================
   REQUEST FULLSCREEN
========================================================== */

async function requestFullscreen() {

    /*
        Testing mode:
        Never request fullscreen.
    */

    if (VOTIFY_TESTING_MODE) {

        console.info(
            "VOTIFY: Fullscreen skipped (Testing Mode)."
        );

        return true;

    }


    /*
        Already fullscreen.
    */

    if (
        document.fullscreenElement
    ) {

        VotingSecurity.isFullscreenRequested =
            true;

        return true;

    }


    /*
        Browser support check.
    */

    if (
        !document.documentElement.requestFullscreen
    ) {

        console.error(
            "VOTIFY: Fullscreen API is not supported by this browser."
        );

        return false;

    }


    try {

        VotingSecurity.ignoreNextFullscreenEvent =
            true;


        await document.documentElement.requestFullscreen();


        VotingSecurity.isFullscreenRequested =
            !!document.fullscreenElement;


        /*
            Allow future fullscreenchange events
            to be processed normally.
        */

        setTimeout(
            () => {

                VotingSecurity.ignoreNextFullscreenEvent =
                    false;

            },
            1000
        );


        return !!document.fullscreenElement;

    }

    catch (error) {

        VotingSecurity.ignoreNextFullscreenEvent =
            false;


        VotingSecurity.isFullscreenRequested =
            false;


        console.error(
            "VOTIFY: Fullscreen request failed.",
            error
        );


        return false;

    }

}


/* ==========================================================
   INITIALIZE SECURITY EVENTS
========================================================== */

function initializeEventListeners() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    document.addEventListener(
        "fullscreenchange",
        handleFullscreenChange
    );


    document.addEventListener(
        "visibilitychange",
        handleVisibilityChange
    );


    window.addEventListener(
        "blur",
        handleWindowBlur
    );

}


/* ==========================================================
   FULLSCREEN CHANGE
========================================================== */

function handleFullscreenChange() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    VotingSecurity.isFullscreenRequested =
        !!document.fullscreenElement;


    /*
        Ignore the fullscreenchange event generated
        immediately after our own fullscreen request.
    */

    if (
        VotingSecurity.ignoreNextFullscreenEvent
    ) {

        return;

    }


    /*
        If fullscreen was exited while voting,
        register a security violation.
    */

    if (
        !document.fullscreenElement
    ) {

        registerViolation(
            "You exited Full Screen Mode."
        );

    }

}


/* ==========================================================
   TAB SWITCH
========================================================== */

function handleVisibilityChange() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    if (
        document.hidden
    ) {

        registerViolation(
            "You switched to another tab."
        );

    }

}


/* ==========================================================
   WINDOW BLUR
========================================================== */

function handleWindowBlur() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    if (
        !document.hasFocus() &&
        document.fullscreenElement
    ) {

        registerViolation(
            "You switched to another application."
        );

    }

}


/* ==========================================================
   REGISTER SECURITY VIOLATION
========================================================== */

function registerViolation(reason) {

    if (VOTIFY_TESTING_MODE) {

        console.info(
            "VOTIFY: Security violation ignored (Testing Mode):",
            reason
        );

        return;

    }


    if (
        VotingSecurity.isHandlingViolation
    ) {

        return;

    }


    VotingSecurity.isHandlingViolation =
        true;


    setTimeout(
        () => {

            VotingSecurity.isHandlingViolation =
                false;

        },
        500
    );


    /*
        Maximum warning protection.
    */

    if (
        VotingSecurity.warningCount >=
        VotingSecurity.maxWarnings
    ) {

        return;

    }


    VotingSecurity.warningCount++;


    /*
        Warning 1:
        Show warning modal.
    */

    if (
        VotingSecurity.warningCount <
        VotingSecurity.maxWarnings
    ) {

        showWarningModal(
            reason
        );

        return;

    }


    /*
        Warning 2:
        Force logout.
    */

    forceLogout();

}


/* ==========================================================
   SHOW WARNING MODAL
========================================================== */

function showWarningModal(reason) {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    if (
        !VotingSecurity.modal
    ) {

        return;

    }


    if (
        VotingSecurity.reasonText
    ) {

        VotingSecurity.reasonText.textContent =
            reason;

    }


    if (
        VotingSecurity.warningText
    ) {

        VotingSecurity.warningText.textContent =
            `Warning ${VotingSecurity.warningCount} of ${VotingSecurity.maxWarnings}`;

    }


    VotingSecurity.modal.classList.remove(
        "hidden"
    );

    VotingSecurity.modal.classList.add(
        "flex"
    );

}


/* ==========================================================
   HIDE WARNING MODAL
========================================================== */

function hideWarningModal() {

    if (
        !VotingSecurity.modal
    ) {

        return;

    }


    VotingSecurity.modal.classList.remove(
        "flex"
    );

    VotingSecurity.modal.classList.add(
        "hidden"
    );

}


/* ==========================================================
   RESUME BUTTON
========================================================== */

function initializeResumeButton() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    if (
        !VotingSecurity.resumeButton
    ) {

        return;

    }


    VotingSecurity.resumeButton.addEventListener(
        "click",
        async () => {

            const fullscreenStarted =
                await requestFullscreen();


            if (
                fullscreenStarted
            ) {

                hideWarningModal();

            }

        }
    );

}


/* ==========================================================
   START SECURE VOTING
========================================================== */

function initializeStartVotingButton() {

    if (VOTIFY_TESTING_MODE) {

        return;

    }


    if (
        !VotingSecurity.startVotingButton ||
        !VotingSecurity.entryModal
    ) {

        console.error(
            "VOTIFY: Secure voting entry elements not found."
        );

        return;

    }


    VotingSecurity.startVotingButton.addEventListener(
        "click",
        async () => {

            /*
                Prevent accidental multiple clicks.
            */

            VotingSecurity.startVotingButton.disabled =
                true;


            VotingSecurity.startVotingButton.classList.add(
                "opacity-70",
                "cursor-wait"
            );


            /*
                Request fullscreen directly from the
                user's button click.
            */

            const fullscreenStarted =
                await requestFullscreen();


            /*
                Fullscreen successfully entered.
            */

            if (
                fullscreenStarted
            ) {

                VotingSecurity.entryModal.classList.add(
                    "hidden"
                );

                VotingSecurity.entryModal.classList.remove(
                    "flex"
                );


                console.info(
                    "VOTIFY: Secure voting started."
                );

            }

            else {

                /*
                    Fullscreen failed.
                    Keep entry modal visible.
                */

                console.warn(
                    "VOTIFY: Secure voting could not start because fullscreen was not enabled."
                );


                /*
                    Show button again.
                */

                VotingSecurity.startVotingButton.disabled =
                    false;

                VotingSecurity.startVotingButton.classList.remove(
                    "opacity-70",
                    "cursor-wait"
                );

            }

        }
    );

}


/* ==========================================================
   FORCE LOGOUT
========================================================== */

function forceLogout() {

    if (VOTIFY_TESTING_MODE) {

        console.info(
            "VOTIFY: Force logout skipped (Testing Mode)."
        );

        return;

    }


    console.warn(
        "VOTIFY: Maximum security warnings reached. Logging out."
    );


    window.location.replace(
        VotingSecurity.logoutUrl
    );

}


/* ==========================================================
   PREVENT F11
========================================================== */

document.addEventListener(
    "keydown",
    (event) => {

        if (VOTIFY_TESTING_MODE) {

            return;

        }


        if (
            event.key === "F11"
        ) {

            event.preventDefault();

        }

    }
);


/* ==========================================================
   GLOBAL FULLSCREEN STATE TRACKER
========================================================== */

document.addEventListener(
    "fullscreenchange",
    () => {

        VotingSecurity.isFullscreenRequested =
            !!document.fullscreenElement;

    }
);


/* ==========================================================
   END OF FILE
========================================================== */