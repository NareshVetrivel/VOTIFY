/* ==========================================================
   VOTIFY
   Canvassing Reports
   File : assets/js/canvassing.js
========================================================== */

"use strict";


/* ==========================================================
   GLOBALS
========================================================== */

let canvassingSearchKeyword = "";

let canvassingCurrentYear = "all";


/* ==========================================================
   VOTIFY CANVASSING NAMESPACE
========================================================== */

window.VOTIFY_CANVASSING =
    window.VOTIFY_CANVASSING || {};

window.VOTIFY_CANVASSING.electionStatus =
    "Ready";


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeCanvassing();

    }
);


/* ==========================================================
   INITIALIZE CANVASSING
========================================================== */

async function initializeCanvassing(){

    console.log("1 Search");

    initializeCanvassingSearch();


    console.log("2 Year Filters");

    initializeCanvassingFilters();


    console.log("3 Table");

    initializeCanvassingTable();


    console.log("4 Export");

    initializeCanvassingExportButton();


    /*
     * Load the current election status from the server.
     *
     * This determines whether the certificate/export
     * button should be available.
     */

    await loadCanvassingElectionStatus();


    console.log(
        "%cVOTIFY Canvassing Ready",
        "color:#3B82F6;font-size:14px;font-weight:bold;"
    );

}


/* ==========================================================
   LOAD ELECTION STATUS
========================================================== */

async function loadCanvassingElectionStatus(){

    try{

        const response =
            await fetch(
                "../../backend/admin/dashboard-status.php",
                {
                    method:
                        "GET",

                    credentials:
                        "same-origin",

                    cache:
                        "no-store",

                    headers: {
                        "Accept":
                            "application/json"
                    }
                }
            );


        if(!response.ok){

            console.warn(
                "VOTIFY: Unable to load election status.",
                response.status
            );

            return false;

        }


        const result =
            await response.json();


        console.log(
            "VOTIFY Canvassing Election Status:",
            result
        );


        if(
            result &&
            result.success === true &&
            typeof result.status === "string"
        ){

            window.VOTIFY_CANVASSING.electionStatus =
                result.status.trim();

        }


        /*
         * Only these statuses are valid.
         */

        const allowedStatuses = [

            "Ready",

            "Started",

            "Stopped"

        ];


        if(
            !allowedStatuses.includes(
                window.VOTIFY_CANVASSING.electionStatus
            )
        ){

            window.VOTIFY_CANVASSING.electionStatus =
                "Ready";

        }


        applyCanvassingExportLock();


        return true;

    }

    catch(error){

        console.error(
            "VOTIFY Canvassing Election Status Error:",
            error
        );


        return false;

    }

}


/* ==========================================================
   REFRESH ELECTION STATUS
========================================================== */

async function refreshCanvassingElectionStatus(){

    return await loadCanvassingElectionStatus();

}


/* ==========================================================
   CHECK ELECTION RUNNING
========================================================== */

function isCanvassingElectionRunning(){

    return (
        window.VOTIFY_CANVASSING.electionStatus ===
        "Started"
    );

}


/* ==========================================================
   APPLY EXPORT LOCK
========================================================== */

/*
 * IMPORTANT:
 *
 * Do NOT use disabled=true.
 *
 * The Export Report element is an <a> tag.
 * We need the click event to fire so that we can
 * show the permission toast.
 *
 * Therefore:
 *
 * Election Started:
 *
 *     - visually locked
 *     - cursor-not-allowed
 *     - click still works
 *     - toast appears
 *     - navigation blocked
 *
 * Election Ready / Stopped:
 *
 *     - normal button
 *     - navigation allowed
 *
 */

function applyCanvassingExportLock(){

    const exportButton =
        document.getElementById(
            "exportCanvassingReport"
        );


    if(!exportButton){

        return;

    }


    const isRunning =
        isCanvassingElectionRunning();


    if(isRunning){

        /*
         * Visual locked state.
         */

        exportButton.setAttribute(
            "aria-disabled",
            "true"
        );


        exportButton.setAttribute(
            "title",
            "Certificate export is disabled while the election is running."
        );


        exportButton.classList.add(
            "opacity-50",
            "cursor-not-allowed"
        );


        exportButton.classList.remove(
            "hover:brightness-110",
            "hover:shadow-[0_0_30px_rgba(139,92,246,0.45)]"
        );

    }

    else{

        /*
         * Normal state.
         */

        exportButton.removeAttribute(
            "aria-disabled"
        );


        exportButton.setAttribute(
            "title",
            "Export Report"
        );


        exportButton.classList.remove(
            "opacity-50",
            "cursor-not-allowed"
        );


        exportButton.classList.add(
            "hover:brightness-110",
            "hover:shadow-[0_0_30px_rgba(139,92,246,0.45)]"
        );

    }

}


/* ==========================================================
   SEARCH
========================================================== */

function initializeCanvassingSearch(){

    const searchInput =
        document.getElementById(
            "canvassingSearch"
        );


    if(!searchInput){

        return;

    }


    searchInput.addEventListener(
        "input",
        debounceCanvassing(
            () => {

                canvassingSearchKeyword =
                    searchInput.value
                    .toLowerCase()
                    .trim();


                updateCanvassingTable();

            },
            200
        )
    );

}


/* ==========================================================
   YEAR FILTERS
========================================================== */

function initializeCanvassingFilters(){

    const filterButtons =
        document.querySelectorAll(
            ".canvassing-year-btn"
        );


    if(!filterButtons.length){

        return;

    }


    filterButtons.forEach(
        button => {

            button.addEventListener(
                "click",
                () => {

                    const selectedYear =
                        button.dataset.year;


                    if(!selectedYear){

                        return;

                    }


                    canvassingCurrentYear =
                        selectedYear;


                    updateCanvassingFilterButtons(
                        button
                    );


                    updateCanvassingTable();

                }
            );

        }
    );

}


/* ==========================================================
   ACTIVE FILTER BUTTON
========================================================== */

function updateCanvassingFilterButtons(
    activeButton
){

    document
        .querySelectorAll(
            ".canvassing-year-btn"
        )
        .forEach(
            button => {

                button.classList.remove(

                    "bg-gradient-to-r",

                    "from-blue-500",

                    "via-purple-500",

                    "to-pink-500",

                    "text-white",

                    "shadow-lg",

                    "shadow-purple-500/20"

                );


                button.classList.add(

                    "bg-white/5",

                    "border",

                    "border-white/10",

                    "text-slate-300"

                );

            }
        );


    activeButton.classList.remove(

        "bg-white/5",

        "border-white/10",

        "text-slate-300"

    );


    activeButton.classList.add(

        "bg-gradient-to-r",

        "from-blue-500",

        "via-purple-500",

        "to-pink-500",

        "text-white",

        "shadow-lg",

        "shadow-purple-500/20"

    );

}


/* ==========================================================
   TABLE INITIALIZATION
========================================================== */

function initializeCanvassingTable(){

    const tableBody =
        document.getElementById(
            "canvassingTableBody"
        );


    if(!tableBody){

        return;

    }


    updateCanvassingTable();

}


/* ==========================================================
   GET CANVASSING ROWS
========================================================== */

function getCanvassingRows(){

    return Array.from(

        document.querySelectorAll(

            "#canvassingTableBody tr.canvassing-row"

        )

    );

}


/* ==========================================================
   UPDATE TABLE
========================================================== */

function updateCanvassingTable(){

    const rows =
        getCanvassingRows();


    if(!rows.length){

        updateCanvassingEmptyState(
            false
        );

        return;

    }


    let visibleRows = 0;


    rows.forEach(
        row => {

            const candidateName =
                (
                    row.dataset.name || ""
                )
                .toLowerCase();


            const candidateYear =
                (
                    row.dataset.year || ""
                );


            const searchMatch =
                candidateName.includes(
                    canvassingSearchKeyword
                );


            const yearMatch =

                canvassingCurrentYear ===
                    "all"

                ||

                candidateYear ===
                    canvassingCurrentYear;


            const shouldShow =
                searchMatch &&
                yearMatch;


            if(shouldShow){

                row.style.display = "";

                visibleRows++;

            }

            else{

                row.style.display =
                    "none";

            }

        }
    );


    updateCanvassingEmptyState(
        visibleRows === 0
    );

}


/* ==========================================================
   EMPTY / NO RESULTS STATE
========================================================== */

function updateCanvassingEmptyState(
    showNoResults
){

    const noResults =
        document.getElementById(
            "canvassingNoResults"
        );


    if(!noResults){

        return;

    }


    if(showNoResults){

        noResults.classList.remove(
            "hidden"
        );

    }

    else{

        noResults.classList.add(
            "hidden"
        );

    }

}


/* ==========================================================
   EXPORT BUTTON INITIALIZATION
========================================================== */

function initializeCanvassingExportButton(){

    const exportButton =
        document.getElementById(
            "exportCanvassingReport"
        );


    if(!exportButton){

        return;

    }


    exportButton.addEventListener(
        "click",
        handleCanvassingExport
    );


    /*
     * Initial visual state.
     */

    applyCanvassingExportLock();

}


/* ==========================================================
   HANDLE EXPORT
========================================================== */

async function handleCanvassingExport(
    event
){

    /*
     * ALWAYS prevent the default <a> navigation first.
     *
     * We decide whether navigation is allowed after
     * checking the latest election status.
     */

    event.preventDefault();


    const exportButton =
        document.getElementById(
            "exportCanvassingReport"
        );


    if(!exportButton){

        return;

    }


    /*
     * Refresh status immediately before export.
     *
     * This prevents a stale page from allowing
     * certificate access after the election starts.
     */

    const statusChecked =
        await refreshCanvassingElectionStatus();


    if(!statusChecked){

        showCanvassingToast(
            "error",
            "Unable to Check Election",
            "Please refresh the page and try again."
        );

        return;

    }


    /* ======================================================
       ELECTION RUNNING
    ====================================================== */

    if(
        isCanvassingElectionRunning()
    ){

        applyCanvassingExportLock();


        showCanvassingToast(
            "warning",
            "Export Disabled",
            "Certificate export is disabled while the election is running."
        );


        return;

    }


    /* ======================================================
       ELECTION READY / STOPPED
    ====================================================== */

    /*
     * Only after the latest status confirms that the election
     * is NOT running do we allow navigation.
     */

    const targetUrl =
        exportButton.getAttribute(
            "href"
        );


    if(
        !targetUrl
    ){

        showCanvassingToast(
            "error",
            "Export Error",
            "Certificate page is unavailable."
        );

        return;

    }


    window.location.href =
        targetUrl;

}


/* ==========================================================
   CANVASSING TOAST
========================================================== */

/*
 * Use the existing VOTIFY toast system.
 *
 * The fallback console message prevents JavaScript
 * failure if toast.js is unavailable.
 */

function showCanvassingToast(
    type,
    title,
    message
){

    if(
        typeof showToast ===
        "function"
    ){

        showToast(
            type,
            title,
            message
        );

        return;

    }


    console.warn(
        title + ": " + message
    );

}


/* ==========================================================
   DEBOUNCE
========================================================== */

function debounceCanvassing(

    callback,

    delay = 300

){

    let timer;


    return (...args) => {

        clearTimeout(timer);


        timer = setTimeout(
            () => {

                callback(...args);

            },
            delay
        );

    };

}


/* ==========================================================
   FINAL STATUS
========================================================== */

console.log(

    "%cVOTIFY Canvassing Module Loaded",

    "color:#8B5CF6;font-size:14px;font-weight:bold;"

);