/* ==========================================================
   VOTIFY
   Admin Dashboard JavaScript
   File : assets/js/dashboard.js
========================================================== */

"use strict";


/* ==========================================================
   CURRENT ELECTION SESSION TIMING
========================================================== */

/*
 * These variables belong only to the current page session.
 *
 * IMPORTANT:
 * We do NOT depend on the old PHP start timestamp after
 * the administrator starts a new election.
 */

let currentElectionStartTimestamp = null;

let currentElectionStopTimestamp = null;

let currentElectionStatus = "Ready";


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeDashboard();

    initializeLogoutModal();

});


/* ==========================================================
   INITIALIZE DASHBOARD
========================================================== */

function initializeDashboard() {

    initializeSidebar();

    initializeElectionControls();

    initializeElectionTimer();

    initializeHoverEffects();

}


/* ==========================================================
   MOBILE SIDEBAR
========================================================== */

function initializeSidebar() {

    const menuButton =
        document.getElementById("menuButton");

    const closeButton =
        document.getElementById("closeSidebar");

    const sidebar =
        document.getElementById("adminSidebar");

    const overlay =
        document.getElementById("sidebarOverlay");


    if (
        !menuButton ||
        !sidebar ||
        !overlay
    ) {

        return;

    }


    /* ==========================================
       OPEN SIDEBAR
    ========================================== */

    menuButton.addEventListener(
        "click",
        () => {

            sidebar.classList.remove(
                "-translate-x-full"
            );

            sidebar.classList.add(
                "translate-x-0"
            );

            overlay.classList.remove(
                "hidden"
            );

        }
    );


    /* ==========================================
       CLOSE BUTTON
    ========================================== */

    if (closeButton) {

        closeButton.addEventListener(
            "click",
            closeSidebar
        );

    }


    /* ==========================================
       OVERLAY CLOSE
    ========================================== */

    overlay.addEventListener(
        "click",
        closeSidebar
    );


    /* ==========================================
       MENU LINK CLOSE
    ========================================== */

    sidebar
        .querySelectorAll("a")
        .forEach(link => {

            link.addEventListener(
                "click",
                () => {

                    if (
                        window.innerWidth < 1024
                    ) {

                        closeSidebar();

                    }

                }
            );

        });


    /* ==========================================
       DESKTOP / MOBILE RESET
    ========================================== */

    window.addEventListener(
        "resize",
        () => {

            if (
                window.innerWidth >= 1024
            ) {

                overlay.classList.add(
                    "hidden"
                );

                sidebar.classList.remove(
                    "-translate-x-full"
                );

                sidebar.classList.add(
                    "translate-x-0"
                );

            }

            else {

                overlay.classList.add(
                    "hidden"
                );

                sidebar.classList.remove(
                    "translate-x-0"
                );

                sidebar.classList.add(
                    "-translate-x-full"
                );

            }

        }
    );


    /* ==========================================
       CLOSE SIDEBAR
    ========================================== */

    function closeSidebar() {

        sidebar.classList.remove(
            "translate-x-0"
        );

        sidebar.classList.add(
            "-translate-x-full"
        );

        overlay.classList.add(
            "hidden"
        );

    }

}


/* ==========================================================
   CARD HOVER EFFECT
========================================================== */

function initializeHoverEffects() {

    /*
     * Current dashboard cards use the "glass" class.
     *
     * We support both:
     * .dashboard-card
     * .glass
     *
     * This keeps the existing HTML untouched.
     */

    const cards =
        document.querySelectorAll(
            ".dashboard-card, .glass"
        );


    cards.forEach(card => {

        /*
         * Do not apply this effect to:
         * sidebar / modal / toast elements.
         *
         * Only cards that are inside the
         * main dashboard area are targeted.
         */

        if (
            !card.closest("main")
        ) {

            return;

        }


        card.style.transition =
            "transform 0.35s ease, box-shadow 0.35s ease";


        card.addEventListener(
            "mouseenter",
            () => {

                card.style.transform =
                    "translateY(-6px)";


                card.style.boxShadow =
                    "0 0 30px rgba(59, 130, 246, 0.28), 0 20px 45px rgba(0, 0, 0, 0.25)";

            }
        );


        card.addEventListener(
            "mouseleave",
            () => {

                card.style.transform =
                    "translateY(0)";


                card.style.boxShadow =
                    "";

            }
        );

    });

}


/* ==========================================================
   TOAST NOTIFICATION
========================================================== */

function showToast(
    type,
    title,
    message
) {

    const toast =
        document.getElementById(
            "dashboardToast"
        );

    const icon =
        document.getElementById(
            "toastIcon"
        );

    const toastTitle =
        document.getElementById(
            "toastTitle"
        );

    const toastMessage =
        document.getElementById(
            "toastMessage"
        );


    if (!toast) {

        return;

    }


    toast.classList.remove(
        "bg-green-600",
        "bg-red-600"
    );


    /* ==========================================
       SUCCESS
    ========================================== */

    if (
        type === "success"
    ) {

        toast.classList.add(
            "bg-green-600"
        );


        if (icon) {

            icon.className =
                "ri-checkbox-circle-fill text-3xl";

        }

    }


    /* ==========================================
       ERROR
    ========================================== */

    else {

        toast.classList.add(
            "bg-red-600"
        );


        if (icon) {

            icon.className =
                "ri-close-circle-fill text-3xl";

        }

    }


    if (toastTitle) {

        toastTitle.textContent =
            title;

    }


    if (toastMessage) {

        toastMessage.textContent =
            message;

    }


    toast.classList.remove(
        "translate-x-[120%]"
    );


    setTimeout(
        () => {

            toast.classList.add(
                "translate-x-[120%]"
            );

        },
        3000
    );

}


/* ==========================================================
   ELECTION CONTROLS
========================================================== */

function initializeElectionControls() {

    const startButton =
        document.getElementById(
            "startElection"
        );

    const stopButton =
        document.getElementById(
            "stopElection"
        );


    if (
        !startButton ||
        !stopButton
    ) {

        return;

    }


    /* ==========================================
       UPDATE ELECTION
    ========================================== */

    async function updateElection(
        status
    ) {

        /*
         * Prevent duplicate requests.
         */

        if (
            startButton.disabled &&
            stopButton.disabled
        ) {

            return;

        }


        /* ======================================
           TEMPORARY BUTTON LOCK
        ====================================== */

        startButton.disabled = true;

        stopButton.disabled = true;


        applyElectionButtonState();


        try {

            const response =
                await fetch(

                    "../../backend/admin/update-election.php",

                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/x-www-form-urlencoded"

                        },

                        body:
                            "status=" +
                            encodeURIComponent(
                                status
                            )

                    }

                );


            /* ==================================
               HTTP ERROR
            ================================== */

            if (
                !response.ok
            ) {

                throw new Error(
                    "HTTP Error " +
                    response.status
                );

            }


            const result =
                await response.json();


            /* ==================================
               BACKEND FAILURE
            ================================== */

            if (
                !result.success
            ) {

                showToast(

                    "error",

                    "Operation Failed",

                    result.message ||
                    "Unable to update election status."

                );


                restoreElectionButtons();

                return;

            }


            /*
             * IMPORTANT:
             *
             * update-election.php now returns
             * the exact server/database timestamp.
             */

            const serverTimestamp =
                normalizeTimestamp(
                    result.timestamp
                );


            /* ==================================
               ELECTION STARTED
            ================================== */

            if (
                status === "Started"
            ) {

                /*
                 * Use backend timestamp.
                 *
                 * Do NOT use the old
                 * window.VOTIFY_ELECTION_DATA
                 * timestamp here.
                 */

                currentElectionStartTimestamp =
                    serverTimestamp ||
                    Math.floor(
                        Date.now() / 1000
                    );


                currentElectionStopTimestamp =
                    null;


                currentElectionStatus =
                    "Started";


                updateElectionTiming(

                    "Started",

                    currentElectionStartTimestamp,

                    null

                );


                updateElectionStatusCard(
                    "Started"
                );


                startButton.disabled =
                    true;

                stopButton.disabled =
                    false;


                applyElectionButtonState();


                showToast(

                    "success",

                    "Election Started",

                    "Online voting has started successfully."

                );

            }


            /* ==================================
               ELECTION STOPPED
            ================================== */

            else if (
                status === "Stopped"
            ) {

                /*
                 * Use backend timestamp for
                 * the exact stop event.
                 */

                currentElectionStopTimestamp =
                    serverTimestamp ||
                    Math.floor(
                        Date.now() / 1000
                    );


                /*
                 * CRITICAL FIX:
                 *
                 * Use the current election's
                 * start timestamp stored in
                 * memory.
                 *
                 * Never use the old PHP
                 * timestamp here.
                 */

                if (
                    !currentElectionStartTimestamp
                ) {

                    /*
                     * If the page was refreshed
                     * during an active election,
                     * retrieve the current state
                     * from backend before calculating.
                     */

                    await refreshElectionState();

                }


                updateElectionTiming(

                    "Stopped",

                    currentElectionStartTimestamp,

                    currentElectionStopTimestamp

                );


                currentElectionStatus =
                    "Stopped";


                updateElectionStatusCard(
                    "Stopped"
                );


                startButton.disabled =
                    false;

                stopButton.disabled =
                    true;


                applyElectionButtonState();


                showToast(

                    "success",

                    "Election Stopped",

                    "Voting has been closed successfully."

                );

            }


            /* ==================================
               READY
            ================================== */

            else if (
                status === "Ready"
            ) {

                currentElectionStatus =
                    "Ready";

                currentElectionStartTimestamp =
                    null;

                currentElectionStopTimestamp =
                    null;


                updateElectionTiming(

                    "Ready",

                    null,

                    null

                );


                updateElectionStatusCard(
                    "Ready"
                );


                startButton.disabled =
                    false;

                stopButton.disabled =
                    true;


                applyElectionButtonState();

            }


            /*
             * Refresh server state after
             * successful operation.
             *
             * This keeps the dashboard in
             * sync with the database.
             */

            setTimeout(
                refreshElectionState,
                500
            );

        }


        catch (error) {

            console.error(
                "Election Update Error:",
                error
            );


            showToast(

                "error",

                "Connection Error",

                "Unable to update election status."

            );


            restoreElectionButtons();

        }

    }


    /* ==========================================
       START BUTTON
    ========================================== */

    startButton.addEventListener(
        "click",
        () => {

            if (
                startButton.disabled
            ) {

                return;

            }


            updateElection(
                "Started"
            );

        }
    );


    /* ==========================================
       STOP BUTTON
    ========================================== */

    stopButton.addEventListener(
        "click",
        () => {

            if (
                stopButton.disabled
            ) {

                return;

            }


            updateElection(
                "Stopped"
            );

        }
    );


    /* ==========================================
       INITIAL BUTTON STATE
    ========================================== */

    applyElectionButtonState();


    /* ==========================================
       RESTORE BUTTONS
    ========================================== */

    function restoreElectionButtons() {

        if (
            currentElectionStatus ===
            "Started"
        ) {

            startButton.disabled =
                true;

            stopButton.disabled =
                false;

        }

        else if (
            currentElectionStatus ===
            "Stopped"
        ) {

            startButton.disabled =
                false;

            stopButton.disabled =
                true;

        }

        else {

            startButton.disabled =
                false;

            stopButton.disabled =
                true;

        }


        applyElectionButtonState();

    }


    /*
     * Make button-state helper globally
     * available to refreshElectionState().
     */

    window.applyElectionButtonState =
        applyElectionButtonState;

}


/* ==========================================================
   ELECTION TIMER
========================================================== */

function initializeElectionTimer() {

    const data =
        window.VOTIFY_ELECTION_DATA;


    const durationElement =
        document.getElementById(
            "electionRunningDuration"
        );


    if (
        !durationElement
    ) {

        return;

    }


    /* ==========================================
       INITIAL DATABASE STATE
    ========================================== */

    if (data) {

        currentElectionStatus =
            data.status ||
            "Ready";


        currentElectionStartTimestamp =
            normalizeTimestamp(
                data.startTimestamp
            );


        currentElectionStopTimestamp =
            normalizeTimestamp(
                data.stopTimestamp
            );

    }


    let initialDuration =
        Number(
            data?.initialDuration
        ) || 0;


    /* ==========================================
       FORMAT DURATION
    ========================================== */

    function formatDuration(
        totalSeconds
    ) {

        totalSeconds =
            Math.max(
                0,
                Math.floor(
                    totalSeconds
                )
            );


        const hours =
            Math.floor(
                totalSeconds / 3600
            );


        const minutes =
            Math.floor(
                (
                    totalSeconds % 3600
                ) / 60
            );


        const seconds =
            totalSeconds % 60;


        return (

            String(hours)
                .padStart(2, "0")

            + ":" +

            String(minutes)
                .padStart(2, "0")

            + ":" +

            String(seconds)
                .padStart(2, "0")

        );

    }


    /* ==========================================
       UPDATE TIMER
    ========================================== */

    function updateTimer() {

        let duration =
            initialDuration;


        /* ======================================
           RUNNING
        ====================================== */

        if (

            currentElectionStatus ===
            "Started"

            &&

            currentElectionStartTimestamp

        ) {

            const currentTimestamp =
                Math.floor(
                    Date.now() / 1000
                );


            duration =
                Math.max(

                    0,

                    currentTimestamp -
                    currentElectionStartTimestamp

                );


            initialDuration =
                duration;

        }


        /* ======================================
           STOPPED
        ====================================== */

        else if (

            currentElectionStatus ===
            "Stopped"

            &&

            currentElectionStartTimestamp

            &&

            currentElectionStopTimestamp

        ) {

            duration =
                Math.max(

                    0,

                    currentElectionStopTimestamp -
                    currentElectionStartTimestamp

                );

        }


        /* ======================================
           READY
        ====================================== */

        else {

            duration =
                0;

        }


        durationElement.textContent =
            formatDuration(
                duration
            );


        updateDurationStatus(

            currentElectionStatus,

            duration

        );

    }


    /* ==========================================
       DURATION STATUS TEXT
    ========================================== */

    function updateDurationStatus(
        status,
        duration
    ) {

        const statusText =
            document.getElementById(
                "durationStatus"
            );


        if (!statusText) {

            return;

        }


        if (
            status === "Started"
        ) {

            statusText.textContent =
                "Election is running";


            statusText.classList.remove(
                "text-slate-500"
            );


            statusText.classList.add(
                "text-green-400"
            );

        }

        else if (
            status === "Stopped"
        ) {

            statusText.textContent =
                duration > 0
                    ? "Election completed"
                    : "Election not started";


            statusText.classList.remove(
                "text-green-400"
            );


            statusText.classList.add(
                "text-slate-500"
            );

        }

        else {

            statusText.textContent =
                "Election not started";


            statusText.classList.remove(
                "text-green-400"
            );


            statusText.classList.add(
                "text-slate-500"
            );

        }

    }


    /* ==========================================
       GLOBAL TIMER UPDATE
    ========================================== */

    window.updateElectionTimerState =
        function (

            newStatus,

            newStartTimestamp,

            newStopTimestamp

        ) {

            currentElectionStatus =
                newStatus ||
                "Ready";


            if (
                newStartTimestamp !== null &&
                newStartTimestamp !== undefined
            ) {

                currentElectionStartTimestamp =
                    normalizeTimestamp(
                        newStartTimestamp
                    );

            }


            if (
                newStopTimestamp !== null &&
                newStopTimestamp !== undefined
            ) {

                currentElectionStopTimestamp =
                    normalizeTimestamp(
                        newStopTimestamp
                    );

            }


            if (
                currentElectionStatus ===
                "Started"
            ) {

                currentElectionStopTimestamp =
                    null;

                initialDuration =
                    0;

            }


            if (
                currentElectionStatus ===
                "Stopped"

                &&

                currentElectionStartTimestamp

                &&

                currentElectionStopTimestamp

            ) {

                initialDuration =
                    Math.max(

                        0,

                        currentElectionStopTimestamp -
                        currentElectionStartTimestamp

                    );

            }


            updateTimer();

        };


    /* ==========================================
       INITIAL TIMER DISPLAY
    ========================================== */

    updateTimer();


    /* ==========================================
       LIVE TIMER
    ========================================== */

    setInterval(
        () => {

            if (
                currentElectionStatus ===
                "Started"
            ) {

                updateTimer();

            }

        },
        1000
    );


    /* ==========================================
       UPDATE TIMING DISPLAY
    ========================================== */

    window.updateElectionTiming =
        function (

            newStatus,

            newStartTimestamp,

            newStopTimestamp

        ) {

            if (
                typeof
                window.updateElectionTimerState ===
                "function"
            ) {

                window.updateElectionTimerState(

                    newStatus,

                    newStartTimestamp,

                    newStopTimestamp

                );

            }


            updateTimingDisplay(

                newStartTimestamp,

                newStopTimestamp,

                newStatus

            );

        };

}


/* ==========================================================
   UPDATE ELECTION TIMING DISPLAY
========================================================== */

function updateTimingDisplay(

    startTimestamp,

    stopTimestamp,

    status

) {

    const startElement =
        document.getElementById(
            "electionStartTime"
        );


    const stopElement =
        document.getElementById(
            "electionStopTime"
        );


    /* ==========================================
       START TIME
    ========================================== */

    if (
        startElement
    ) {

        if (
            startTimestamp
        ) {

            startElement.textContent =
                formatTime(
                    startTimestamp
                );

        }

        else {

            startElement.textContent =
                "--:--";

        }

    }


    /* ==========================================
       STOP TIME
    ========================================== */

    if (
        stopElement
    ) {

        if (
            status === "Started"
        ) {

            stopElement.textContent =
                "Running...";

        }

        else if (
            stopTimestamp
        ) {

            stopElement.textContent =
                formatTime(
                    stopTimestamp
                );

        }

        else {

            stopElement.textContent =
                "--:--";

        }

    }

}


/* ==========================================================
   UPDATE ELECTION STATUS CARD
========================================================== */

function updateElectionStatusCard(
    status
) {

    const statusText =
        document.getElementById(
            "electionStatusText"
        );


    const statusDescription =
        document.getElementById(
            "electionStatusDescription"
        );


    const statusIcon =
        document.getElementById(
            "electionStatusIcon"
        );


    if (
        statusText
    ) {

        if (
            status === "Started"
        ) {

            statusText.textContent =
                "Running";

        }

        else if (
            status === "Stopped"
        ) {

            statusText.textContent =
                "Stopped";

        }

        else {

            statusText.textContent =
                "Ready";

        }

    }


    if (
        statusDescription
    ) {

        if (
            status === "Started"
        ) {

            statusDescription.textContent =
                "Election is currently active";

        }

        else if (
            status === "Stopped"
        ) {

            statusDescription.textContent =
                "Election is not active";

        }

        else {

            statusDescription.textContent =
                "Election is ready to start";

        }

    }


    if (
        statusIcon
    ) {

        statusIcon.classList.remove(

            "bg-green-500/20",

            "bg-red-500/20",

            "bg-yellow-500/20"

        );


        if (
            status === "Started"
        ) {

            statusIcon.classList.add(
                "bg-green-500/20"
            );


            statusIcon.innerHTML =
                '<i class="ri-checkbox-circle-line text-3xl text-green-400"></i>';

        }

        else if (
            status === "Stopped"
        ) {

            statusIcon.classList.add(
                "bg-red-500/20"
            );


            statusIcon.innerHTML =
                '<i class="ri-close-circle-line text-3xl text-red-400"></i>';

        }

        else {

            statusIcon.classList.add(
                "bg-yellow-500/20"
            );


            statusIcon.innerHTML =
                '<i class="ri-time-line text-3xl text-yellow-400"></i>';

        }

    }


    /* ==========================================
       COMPATIBILITY STATUS
    ========================================== */

    const hiddenStatus =
        document.getElementById(
            "electionStatus"
        );


    if (
        hiddenStatus
    ) {

        hiddenStatus.textContent =
            status;

    }

}


/* ==========================================================
   REFRESH ELECTION STATE
========================================================== */

async function refreshElectionState() {

    try {

        const response =
            await fetch(

                "../../backend/admin/dashboard-status.php",

                {

                    method: "GET",

                    cache: "no-store"

                }

            );


        if (
            !response.ok
        ) {

            return;

        }


        const data =
            await response.json();


        if (
            !data.success
        ) {

            return;

        }


        /* ==========================================
           CURRENT STATUS
        ========================================== */

        currentElectionStatus =
            data.status ||
            "Ready";


        /* ==========================================
           CURRENT START TIMESTAMP
        ========================================== */

        if (
            data.startTimestamp !==
            undefined
        ) {

            currentElectionStartTimestamp =
                normalizeTimestamp(
                    data.startTimestamp
                );

        }


        /* ==========================================
           CURRENT STOP TIMESTAMP
        ========================================== */

        if (
            data.stopTimestamp !==
            undefined
        ) {

            currentElectionStopTimestamp =
                normalizeTimestamp(
                    data.stopTimestamp
                );

        }


        /* ==========================================
           UPDATE STATUS CARD
        ========================================== */

        updateElectionStatusCard(

            currentElectionStatus

        );


        /* ==========================================
           UPDATE TIMING
        ========================================== */

        updateTimingDisplay(

            currentElectionStartTimestamp,

            currentElectionStopTimestamp,

            currentElectionStatus

        );


        /* ==========================================
           UPDATE TIMER
        ========================================== */

        if (
            currentElectionStatus ===
            "Started"
        ) {

            currentElectionStopTimestamp =
                null;

        }


        if (
            currentElectionStatus ===
            "Stopped"
            &&
            currentElectionStartTimestamp
            &&
            currentElectionStopTimestamp
        ) {

            const duration =
                Math.max(

                    0,

                    currentElectionStopTimestamp -
                    currentElectionStartTimestamp

                );


            const durationElement =
                document.getElementById(
                    "electionRunningDuration"
                );


            if (
                durationElement
            ) {

                durationElement.textContent =
                    formatDurationValue(
                        duration
                    );

            }

        }


        /* ==========================================
           UPDATE BUTTONS
        ========================================== */

        const startButton =
            document.getElementById(
                "startElection"
            );


        const stopButton =
            document.getElementById(
                "stopElection"
            );


        if (
            startButton &&
            stopButton
        ) {

            if (
                currentElectionStatus ===
                "Started"
            ) {

                startButton.disabled =
                    true;

                stopButton.disabled =
                    false;

            }

            else if (
                currentElectionStatus ===
                "Stopped"
            ) {

                startButton.disabled =
                    false;

                stopButton.disabled =
                    true;

            }

            else {

                startButton.disabled =
                    false;

                stopButton.disabled =
                    true;

            }


            applyElectionButtonState();

        }

    }

    catch (error) {

        console.error(

            "Election state refresh failed:",

            error

        );

    }

}


/* ==========================================================
   FORMAT DURATION VALUE
========================================================== */

function formatDurationValue(
    totalSeconds
) {

    totalSeconds =
        Math.max(

            0,

            Math.floor(
                totalSeconds
            )

        );


    const hours =
        Math.floor(
            totalSeconds / 3600
        );


    const minutes =
        Math.floor(

            (
                totalSeconds % 3600
            ) / 60

        );


    const seconds =
        totalSeconds % 60;


    return (

        String(hours)
            .padStart(2, "0")

        + ":" +

        String(minutes)
            .padStart(2, "0")

        + ":" +

        String(seconds)
            .padStart(2, "0")

    );

}


/* ==========================================================
   GET CURRENT ELECTION STATUS
========================================================== */

function getElectionStatus() {

    const statusElement =
        document.getElementById(
            "electionStatus"
        );


    if (
        statusElement
    ) {

        return (

            statusElement.textContent ||
            "Ready"

        ).trim();

    }


    return (
        currentElectionStatus ||
        "Ready"
    );

}


/* ==========================================================
   GET CURRENT START TIMESTAMP
========================================================== */

function getCurrentStartTimestamp() {

    /*
     * IMPORTANT:
     *
     * Return the current election's timestamp.
     *
     * Never read the old page-load timestamp
     * from window.VOTIFY_ELECTION_DATA here.
     */

    return normalizeTimestamp(

        currentElectionStartTimestamp

    );

}


/* ==========================================================
   NORMALIZE TIMESTAMP
========================================================== */

function normalizeTimestamp(
    timestamp
) {

    if (
        timestamp === null ||
        timestamp === undefined ||
        timestamp === ""
    ) {

        return null;

    }


    const value =
        Number(
            timestamp
        );


    if (
        Number.isNaN(value) ||
        value <= 0
    ) {

        return null;

    }


    /*
     * Support both seconds and milliseconds.
     */

    if (
        value > 100000000000
    ) {

        return Math.floor(
            value / 1000
        );

    }


    return Math.floor(
        value
    );

}


/* ==========================================================
   FORMAT TIME
========================================================== */

function formatTime(
    timestamp
) {

    const normalized =
        normalizeTimestamp(
            timestamp
        );


    if (
        !normalized
    ) {

        return "--:--";

    }


    const date =
        new Date(
            normalized * 1000
        );


    return date.toLocaleTimeString(

        [],

        {

            hour:
                "2-digit",

            minute:
                "2-digit",

            hour12:
                true

        }

    );

}


/* ==========================================================
   BUTTON STATE HELPER
========================================================== */

function applyElectionButtonState() {

    const startButton =
        document.getElementById(
            "startElection"
        );


    const stopButton =
        document.getElementById(
            "stopElection"
        );


    if (
        !startButton ||
        !stopButton
    ) {

        return;

    }


    [
        startButton,
        stopButton
    ]
        .forEach(button => {

            if (
                button.disabled
            ) {

                button.classList.add(

                    "cursor-not-allowed",

                    "opacity-60"

                );


                button.classList.remove(
                    "cursor-pointer"
                );

            }

            else {

                button.classList.remove(

                    "cursor-not-allowed",

                    "opacity-60"

                );


                button.classList.add(
                    "cursor-pointer"
                );

            }

        });

}


/* ==========================================================
   DESKTOP LOGOUT MODAL
========================================================== */

function initializeLogoutModal() {

    const logoutButton =
        document.getElementById(
            "desktopLogout"
        );


    const modal =
        document.getElementById(
            "logoutModal"
        );


    const cancel =
        document.getElementById(
            "cancelLogout"
        );


    if (
        !logoutButton ||
        !modal
    ) {

        return;

    }


    /* ==========================================
       DESKTOP LOGOUT
    ========================================== */

    if (
        window.innerWidth >= 768
    ) {

        logoutButton.addEventListener(
            "click",
            event => {

                event.preventDefault();


                modal.classList.remove(
                    "hidden"
                );


                modal.classList.add(
                    "flex"
                );

            }
        );

    }


    /* ==========================================
       CANCEL LOGOUT
    ========================================== */

    if (
        cancel
    ) {

        cancel.addEventListener(
            "click",
            () => {

                modal.classList.remove(
                    "flex"
                );


                modal.classList.add(
                    "hidden"
                );

            }
        );

    }


    /* ==========================================
       CLICK OUTSIDE MODAL
    ========================================== */

    modal.addEventListener(
        "click",
        event => {

            if (
                event.target === modal
            ) {

                modal.classList.remove(
                    "flex"
                );


                modal.classList.add(
                    "hidden"
                );

            }

        }
    );

}