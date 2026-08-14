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
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeCanvassing();

});


/* ==========================================================
   INITIALIZE CANVASSING
========================================================== */

function initializeCanvassing(){

    console.log("1 Search");

    initializeCanvassingSearch();


    console.log("2 Year Filters");

    initializeCanvassingFilters();


    console.log("3 Table");

    initializeCanvassingTable();


    console.log("4 Export");

    initializeCanvassingExportButton();


    console.log(
        "%cVOTIFY Canvassing Ready",
        "color:#3B82F6;font-size:14px;font-weight:bold;"
    );

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


    filterButtons.forEach(button => {

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

    });

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
        .forEach(button => {

            /* ==========================================
               RESET BUTTON
            ========================================== */

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

        });


    /* ==========================================
       ACTIVE BUTTON
    ========================================== */

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


    /* ======================================================
       NO CANDIDATES
    ====================================================== */

    if(!rows.length){

        updateCanvassingEmptyState(
            false
        );

        return;

    }


    let visibleRows = 0;


    /* ======================================================
       CHECK EACH ROW
    ====================================================== */

    rows.forEach(row => {


        /* ==================================================
           CANDIDATE NAME
        ================================================== */

        const candidateName =

            (
                row.dataset.name || ""
            )
            .toLowerCase();


        /* ==================================================
           CANDIDATE YEAR
        ================================================== */

        const candidateYear =

            (
                row.dataset.year || ""
            );


        /* ==================================================
           SEARCH MATCH
        ================================================== */

        const searchMatch =

            candidateName.includes(

                canvassingSearchKeyword

            );


        /* ==================================================
           YEAR MATCH
        ================================================== */

        const yearMatch =

            canvassingCurrentYear === "all"

            ||

            candidateYear ===
            canvassingCurrentYear;


        /* ==================================================
           FINAL MATCH
        ================================================== */

        const shouldShow =

            searchMatch &&
            yearMatch;


        /* ==================================================
           SHOW / HIDE ROW
        ================================================== */

        if(shouldShow){

            row.style.display = "";

            visibleRows++;

        }

        else{

            row.style.display = "none";

        }

    });


    /* ======================================================
       EMPTY SEARCH RESULT
    ====================================================== */

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

}


/* ==========================================================
   HANDLE EXPORT
========================================================== */

function handleCanvassingExport(){

    /*
       ======================================================
       TEMPORARY EXPORT HANDLER
       ======================================================

       Excel/PDF export is intentionally disabled.

       Final certificate generation will be implemented
       after the election result workflow is finalized.

       Planned result:

       #1 → Chairman
       #2 → Vice-Chairman
       #3 → Joint Secretary

       Remaining candidates will be shown in the
       final result summary.

       ======================================================
    */


    if(typeof showToast === "function"){

        showToast(

            "warning",

            "Result Certificate",

            "Certificate generation will be available after the election result is finalized."

        );

        return;

    }


    console.log(

        "Canvassing export clicked."

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