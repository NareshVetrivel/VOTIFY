/* ==========================================================
   VOTIFY
   Security Guard
   File : assets/js/security_guard.js
========================================================== */

"use strict";

/* ==========================================================
   SECURITY CONFIGURATION
========================================================== */

const VotingSecurity = {

    maxWarnings: 2,

    warningCount: 0,

    logoutUrl: "../../backend/student/security_logout.php",

    isFullscreenRequested: false,

    modal: null,

    reasonText: null,

    warningText: null,

    resumeButton: null,

    isHandlingViolation: false,

    ignoreNextFullscreenEvent: false,

    entryModal: null,

    startVotingButton: null,

};

/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    cacheElements();

    initializeSecurity();

});

/* ==========================================================
   CACHE DOM ELEMENTS
========================================================== */

function cacheElements() {

    VotingSecurity.modal =
        document.getElementById("securityWarningModal");

    VotingSecurity.reasonText =
        document.getElementById("securityViolationReason");

    VotingSecurity.warningText =
        document.getElementById("securityAttempt");

    VotingSecurity.resumeButton =
        document.getElementById("resumeVotingButton");

    VotingSecurity.entryModal =
        document.getElementById("securityEntryModal");

    VotingSecurity.startVotingButton =
        document.getElementById("startVotingButton");

}

/* ==========================================================
   INITIALIZE SECURITY
========================================================== */

function initializeSecurity() {

    initializeEventListeners();

    initializeResumeButton();

    initializeStartVotingButton();

}

/* ==========================================================
   REQUEST FULLSCREEN
========================================================== */

async function requestFullscreen() {

    if (document.fullscreenElement) {

        return;

    }

    try {

        VotingSecurity.ignoreNextFullscreenEvent = true;

        await document.documentElement.requestFullscreen();

        setTimeout(() => {

            VotingSecurity.ignoreNextFullscreenEvent = false;

        }, 1000);

    }

    catch (error) {

        console.warn(

            "Fullscreen request blocked.",

            error

        );

    }

}

/* ==========================================================
   INITIALIZE EVENTS
========================================================== */

function initializeEventListeners() {

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
   FULLSCREEN EXIT
========================================================== */

function handleFullscreenChange() {

    if (VotingSecurity.ignoreNextFullscreenEvent) {

        return;

    }

    if (!document.fullscreenElement) {

        registerViolation(

            "You exited Full Screen Mode."

        );

    }

}

/* ==========================================================
   TAB SWITCH
========================================================== */

function handleVisibilityChange() {

    if (document.hidden) {

        registerViolation(

            "You switched to another tab."

        );

    }

}

/* ==========================================================
   WINDOW BLUR
========================================================== */

function handleWindowBlur() {

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

    if (VotingSecurity.isHandlingViolation) {

        return;

    }

    VotingSecurity.isHandlingViolation = true;

    setTimeout(() => {

        VotingSecurity.isHandlingViolation = false;

    }, 500);

    if (

        VotingSecurity.warningCount >=

        VotingSecurity.maxWarnings

    ) {

        return;

    }

    VotingSecurity.warningCount++;

    if (

        VotingSecurity.warningCount <

        VotingSecurity.maxWarnings

    ) {

        showWarningModal(reason);

    }

    else {

        forceLogout();

    }

}

/* ==========================================================
   SHOW WARNING MODAL
========================================================== */

function showWarningModal(reason) {

    VotingSecurity.reasonText.textContent = reason;

    VotingSecurity.warningText.textContent =
        `Warning ${VotingSecurity.warningCount} of ${VotingSecurity.maxWarnings}`;

    VotingSecurity.modal.classList.remove("hidden");
    VotingSecurity.modal.classList.add("flex");

}

/* ==========================================================
   HIDE WARNING MODAL
========================================================== */

function hideWarningModal() {

    VotingSecurity.modal.classList.remove("flex");
    VotingSecurity.modal.classList.add("hidden");

}

/* ==========================================================
   RESUME BUTTON
========================================================== */

function initializeResumeButton() {

if(VotingSecurity.resumeButton){

    VotingSecurity.resumeButton.addEventListener(

        "click",

        async () => {

            await requestFullscreen();

            hideWarningModal();

        }

    );

}

}

/* ==========================================================
   START SECURE VOTING
========================================================== */

function initializeStartVotingButton() {

    if (

        !VotingSecurity.startVotingButton ||

        !VotingSecurity.entryModal

    ) {

        return;

    }

    VotingSecurity.startVotingButton.addEventListener(

        "click",

        async () => {

            await requestFullscreen();

            if (document.fullscreenElement) {

                VotingSecurity.entryModal.classList.add(

                    "hidden"

                );

            }

        }

    );

}

/* ==========================================================
   FORCE LOGOUT
========================================================== */

function forceLogout() {

    window.location.replace(

        VotingSecurity.logoutUrl

    );

}

/* ==========================================================
   PREVENT MULTIPLE FULLSCREEN REQUESTS
========================================================== */

document.addEventListener(

    "keydown",

    (event) => {

        if (

            event.key === "F11"

        ) {

            event.preventDefault();

        }

    }

);

/* ==========================================================
   RESET FULLSCREEN FLAG
========================================================== */

document.addEventListener(

    "fullscreenchange",

    () => {

        VotingSecurity.isFullscreenRequested =

            !!document.fullscreenElement;

    }

);

/* ==========================================================
   END OF SECURITY GUARD
========================================================== */