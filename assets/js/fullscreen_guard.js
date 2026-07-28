/* ==========================================================
   VOTIFY
   Fullscreen Security Guard
   File : assets/js/fullscreen_guard.js
========================================================== */

"use strict";

/* ==========================================================
   CONFIGURATION
========================================================== */

const VotingSecurity = {

    maxViolations: 2,

    violations: 0,

    logoutUrl: "../../backend/student/security_logout.php",

    isWarningVisible: false,

    isRestoringFullscreen: false,

    lastViolationTime: 0,

    cooldown: 1500

};

/* ==========================================================
   MODAL ELEMENTS
========================================================== */

let securityModal = null;

let violationReason = null;

let violationAttempt = null;

let resumeVotingButton = null;

/* ==========================================================
   INITIALIZE
========================================================== */

document.addEventListener(

    "DOMContentLoaded",

    () => {

        cacheElements();

        initializeSecurityEvents();

        initializeResumeButton();

        initializeFullscreen();

    }

);

/* ==========================================================
   CACHE ELEMENTS
========================================================== */

function cacheElements() {

    securityModal = document.getElementById(

        "securityWarningModal"

    );

    violationReason = document.getElementById(

        "securityViolationReason"

    );

    violationAttempt = document.getElementById(

        "securityAttempt"

    );

    resumeVotingButton = document.getElementById(

        "resumeVotingButton"

    );

}

/* ==========================================================
   ENTER FULLSCREEN
========================================================== */

async function initializeFullscreen() {

    if (

        document.fullscreenElement ||

        VotingSecurity.isRestoringFullscreen

    ) {

        return;

    }

    VotingSecurity.isRestoringFullscreen = true;

    try {

        await document.documentElement.requestFullscreen();

    }

    catch (error) {

        console.warn(

            "Unable to enter fullscreen.",

            error

        );

    }

    finally {

        setTimeout(

            () => {

                VotingSecurity.isRestoringFullscreen = false;

            },

            300

        );

    }

}

/* ==========================================================
   WARNING MODAL
========================================================== */

function showWarningModal(reason) {

    if (!securityModal) {

        return;

    }

    VotingSecurity.isWarningVisible = true;

    violationReason.textContent = reason;

    violationAttempt.textContent =

        VotingSecurity.violations;

    securityModal.classList.remove(

        "hidden"

    );

    securityModal.classList.add(

        "flex"

    );

}

/* ==========================================================
   CLOSE WARNING MODAL
========================================================== */

function closeWarningModal() {

    if (!securityModal) {

        return;

    }

    securityModal.classList.remove(

        "flex"

    );

    securityModal.classList.add(

        "hidden"

    );

    VotingSecurity.isWarningVisible = false;

}

/* ==========================================================
   RESUME BUTTON
========================================================== */

function initializeResumeButton() {

    if (!resumeVotingButton) {

        return;

    }

    resumeVotingButton.addEventListener(

        "click",

        async () => {

            closeWarningModal();

            await initializeFullscreen();

        }

    );

}

/* ==========================================================
   SECURITY EVENTS
========================================================== */

function initializeSecurityEvents() {

    /* ==============================================
       TAB SWITCH
    ============================================== */

    document.addEventListener(

        "visibilitychange",

        () => {

            if (

                document.hidden &&

                !VotingSecurity.isWarningVisible

            ) {

                registerViolation(

                    "Tab Switch Detected"

                );

            }

        }

    );

    /* ==============================================
       FULLSCREEN EXIT
    ============================================== */

    document.addEventListener(

        "fullscreenchange",

        () => {

            if (

                !document.fullscreenElement &&

                !VotingSecurity.isWarningVisible &&

                !VotingSecurity.isRestoringFullscreen

            ) {

                registerViolation(

                    "Fullscreen Exited"

                );

            }

        }

    );

    /* ==============================================
       WINDOW BLUR
    ============================================== */

    window.addEventListener(

        "blur",

        () => {

            if (

                document.fullscreenElement &&

                !VotingSecurity.isWarningVisible

            ) {

                registerViolation(

                    "Window Focus Lost"

                );

            }

        }

    );

}

/* ==========================================================
   REGISTER VIOLATION
========================================================== */

function registerViolation(reason) {

    const currentTime = Date.now();

    /* ==============================================
       PREVENT DUPLICATE EVENTS
    ============================================== */

    if (

        currentTime -

        VotingSecurity.lastViolationTime <

        VotingSecurity.cooldown

    ) {

        return;

    }

    VotingSecurity.lastViolationTime = currentTime;

    VotingSecurity.violations++;

    console.warn(

        "[VOTIFY]",

        reason,

        "Attempt:",

        VotingSecurity.violations

    );

    /* ==============================================
       FIRST WARNING
    ============================================== */

    if (

        VotingSecurity.violations <

        VotingSecurity.maxViolations

    ) {

        showWarningModal(

            reason

        );

        return;

    }

    /* ==============================================
       SECOND VIOLATION
    ============================================== */

    forceLogout();

}

/* ==========================================================
   FORCE LOGOUT
========================================================== */

function forceLogout() {

    if (

        securityModal

    ) {

        closeWarningModal();

    }

    const overlay = document.createElement(

        "div"

    );

    overlay.className =

        "fixed inset-0 z-[10000] flex items-center justify-center bg-black/90 backdrop-blur-md";

    overlay.innerHTML = `

        <div class="glass rounded-3xl p-10 w-[90%] max-w-md text-center">

            <div class="w-20 h-20 rounded-full bg-red-500/20 flex items-center justify-center mx-auto">

                <i class="ri-shield-flash-fill text-red-400 text-5xl"></i>

            </div>

            <h2 class="text-3xl font-bold mt-6">

                Security Violation

            </h2>

            <p class="text-slate-400 mt-4 leading-7">

                Multiple security violations were detected.

                <br><br>

                Your voting session has been terminated.

            </p>

        </div>

    `;

    document.body.appendChild(

        overlay

    );

    setTimeout(

        () => {

            window.location.replace(

                VotingSecurity.logoutUrl

            );

        },

        2500

    );

}