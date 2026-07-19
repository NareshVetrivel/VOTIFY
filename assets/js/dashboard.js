/* ==========================================================
   VOTIFY
   Admin Dashboard JavaScript
========================================================== */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    initializeDashboard();

});

/* ==========================================================
   INITIALIZE DASHBOARD
========================================================== */

function initializeDashboard() {

    initializeSidebar();

    initializeCounters();

    initializeElectionControls();

    initializeHoverEffects();

    if(document.getElementById("totalStudents")){

        initializeLiveDashboard();

    }

}
/* ==========================================================
   MOBILE SIDEBAR
========================================================== */

function initializeSidebar() {

    const menuButton = document.getElementById("menuButton");

    const closeButton = document.getElementById("closeSidebar");

    const sidebar = document.getElementById("adminSidebar");

    const overlay = document.getElementById("sidebarOverlay");

    if (!menuButton || !sidebar || !overlay) {

        return;

    }

    /* ======================================
       OPEN SIDEBAR
    ======================================= */

menuButton.addEventListener("click", () => {

    sidebar.classList.remove("-translate-x-full");

    sidebar.classList.add("translate-x-0");

    overlay.classList.remove("hidden");

});

    /* ======================================
       CLOSE BUTTON
    ======================================= */

    if (closeButton) {

        closeButton.addEventListener("click", closeSidebar);

    }

    /* ======================================
       OVERLAY CLOSE
    ======================================= */

    overlay.addEventListener("click", closeSidebar);

    /* ======================================
       MENU CLICK CLOSE
    ======================================= */

    sidebar.querySelectorAll("a").forEach(link => {

        link.addEventListener("click", () => {

            if (window.innerWidth < 1024) {

                closeSidebar();

            }

        });

    });

    /* ======================================
       DESKTOP RESET
    ======================================= */

window.addEventListener("resize", () => {

    if (window.innerWidth >= 1024) {

        overlay.classList.add("hidden");

        sidebar.classList.remove("-translate-x-full");

        sidebar.classList.add("translate-x-0");

    }

    else {

        overlay.classList.add("hidden");

        sidebar.classList.remove("translate-x-0");

        sidebar.classList.add("-translate-x-full");

    }

});

function closeSidebar() {

    sidebar.classList.remove("translate-x-0");

    sidebar.classList.add("-translate-x-full");

    overlay.classList.add("hidden");

}

}

/* ==========================================================
   COUNTER ANIMATION
========================================================== */

function animateCounter(id, target) {

    const element = document.getElementById(id);

    if (!element) return;

    let current = 0;

    const increment = Math.max(1, Math.ceil(target / 50));

    const timer = setInterval(() => {

        current += increment;

        if (current >= target) {

            current = target;

            clearInterval(timer);

        }

        element.textContent = current;

    }, 25);

}

/* ==========================================================
   INITIALIZE COUNTERS
========================================================== */

function initializeCounters() {

    const totalStudents =
        document.getElementById("totalStudents");

    const pendingStudents =
        document.getElementById("pendingStudents");

    const approvedStudents =
        document.getElementById("approvedStudents");

    if (totalStudents) {

        animateCounter(

            "totalStudents",

            parseInt(totalStudents.textContent)

        );

    }

    if (pendingStudents) {

        animateCounter(

            "pendingStudents",

            parseInt(pendingStudents.textContent)

        );

    }

    if (approvedStudents) {

        animateCounter(

            "approvedStudents",

            parseInt(approvedStudents.textContent)

        );

    }

}

/* ==========================================================
   CARD HOVER EFFECT
========================================================== */

function initializeHoverEffects() {

    document.querySelectorAll(".dashboard-card").forEach(card => {

        card.addEventListener("mouseenter", () => {

            card.style.transform = "translateY(-8px)";

            card.style.transition = ".35s";

        });

        card.addEventListener("mouseleave", () => {

            card.style.transform = "translateY(0)";

        });

    });

}

/* ==========================================================
   TOAST NOTIFICATION
========================================================== */

function showToast(type, title, message) {

    const toast =
        document.getElementById("dashboardToast");

    const icon =
        document.getElementById("toastIcon");

    const toastTitle =
        document.getElementById("toastTitle");

    const toastMessage =
        document.getElementById("toastMessage");

    if (!toast) return;

    toast.classList.remove(

        "bg-green-600",

        "bg-red-600"

    );

    if (type === "success") {

        toast.classList.add("bg-green-600");

        icon.className =
        "ri-checkbox-circle-fill text-3xl";

    }

    else {

        toast.classList.add("bg-red-600");

        icon.className =
        "ri-close-circle-fill text-3xl";

    }

    toastTitle.textContent = title;

    toastMessage.textContent = message;

    toast.classList.remove("translate-x-[120%]");

    setTimeout(() => {

        toast.classList.add("translate-x-[120%]");

    }, 3000);

}

/* ==========================================================
   ELECTION CONTROLS
========================================================== */

function initializeElectionControls() {

    const startButton = document.getElementById("startElection");
    const stopButton = document.getElementById("stopElection");
    const statusBadge = document.getElementById("electionStatus");

    if (!startButton || !stopButton || !statusBadge) {

        return;

    }

    /* ==========================================
       UPDATE STATUS
    ========================================== */

    async function updateElection(status) {

        try {

            const response = await fetch(

                "../../backend/admin/update-election.php",

                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                        "application/x-www-form-urlencoded"

                    },

                    body: "status=" + encodeURIComponent(status)

                }

            );

            const result = await response.json();

            if (!result.success) {

showToast(

    "error",

    "Operation Failed",

    result.message

);

                return;

            }

            /* ===============================
               UPDATE UI
            =============================== */

            if (status === "Started") {

                statusBadge.innerHTML =
                "🟢 Election Running";

                startButton.disabled = true;

                stopButton.disabled = false;

                showToast(

    "success",

    "Election Started",

    "Online voting has started successfully."

);

            }

            else if (status === "Stopped") {

                statusBadge.innerHTML =
                "🔴 Election Closed";

                startButton.disabled = false;

                stopButton.disabled = true;

                showToast(

    "success",

    "Election Stopped",

    "Voting has been closed successfully."

);

            }

            else {

                statusBadge.innerHTML =
                "🟡 Ready";

                startButton.disabled = false;

                stopButton.disabled = false;

            }

        }

        catch (error) {

showToast(

    "error",

    "Connection Error",

    "Unable to update election status."

);

        }

    }

    /* ==========================================
       START BUTTON
    ========================================== */

    startButton.addEventListener("click", () => {

        updateElection("Started");

    });

    /* ==========================================
       STOP BUTTON
    ========================================== */

    stopButton.addEventListener("click", () => {

        updateElection("Stopped");

    });

}

/* ==========================================================
LIVE DASHBOARD REFRESH
========================================================== */

function initializeLiveDashboard() {

    refreshDashboard();

    setInterval(refreshDashboard,10000);

}

async function refreshDashboard(){

    try{

        const response =
        await fetch(

        "../../backend/admin/dashboard-status.php"

        );

        const data =
        await response.json();

        if(!data.success){

            return;

        }

        /* ==========================
           Counters
        ========================== */

const totalStudents =
document.getElementById("totalStudents");

const pendingStudents =
document.getElementById("pendingStudents");

const approvedStudents =
document.getElementById("approvedStudents");

if(totalStudents){

    totalStudents.textContent =
    data.total;

}

if(pendingStudents){

    pendingStudents.textContent =
    data.pending;

}

if(approvedStudents){

    approvedStudents.textContent =
    data.approved;

}

        /* ==========================
           Status
        ========================== */

const badge =
document.getElementById("electionStatus");

const start =
document.getElementById("startElection");

const stop =
document.getElementById("stopElection");

if(!badge || !start || !stop){

    return;

}

        if(data.status==="Started"){

            badge.innerHTML=
            "🟢 Election Running";

            start.disabled=true;

            stop.disabled=false;

        }

        else if(data.status==="Stopped"){

            badge.innerHTML=
            "🔴 Election Closed";

            start.disabled=false;

            stop.disabled=true;

        }

        else{

            badge.innerHTML=
            "🟡 Ready";

            start.disabled=false;

            stop.disabled=false;

        }

    }

catch(error){

    console.error(error);

}

}

/* ==========================================================
   DESKTOP LOGOUT MODAL
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    const logoutButton =
        document.getElementById("desktopLogout");

    const modal =
        document.getElementById("logoutModal");

    const cancel =
        document.getElementById("cancelLogout");

    if (

        window.innerWidth >= 768 &&

        logoutButton &&

        modal

    ) {

        logoutButton.addEventListener("click", e => {

            e.preventDefault();

            modal.classList.remove("hidden");

            modal.classList.add("flex");

        });

    }

    if (cancel) {

        cancel.addEventListener("click", () => {

            modal.classList.remove("flex");

            modal.classList.add("hidden");

        });

    }

    modal?.addEventListener("click", e => {

        if (e.target === modal) {

            modal.classList.remove("flex");

            modal.classList.add("hidden");

        }

    });

});