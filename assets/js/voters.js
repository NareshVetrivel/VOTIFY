/* ==========================================================
   VOTIFY
   Voters Management
   File : assets/js/voters.js
========================================================== */

"use strict";


/* ==========================================================
   GLOBALS
========================================================== */

let currentPage = 1;

let rowsPerPage = 10;

let currentFilter = "all";

let searchKeyword = "";


/* ==========================================================
   YEAR DROPDOWN GLOBAL STATE
========================================================== */

let yearDropdownInitialized = false;

let yearDropdownMenuOriginalParent = null;

let yearDropdownMenuOriginalNextSibling = null;

let yearDropdownOutsideHandler = null;

let yearDropdownEscapeHandler = null;

let yearDropdownRepositionHandler = null;


/* ==========================================================
   READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeVoters();

    }
);


/* ==========================================================
   INITIALIZE
========================================================== */

function initializeVoters(){

    console.log("1 Search");

    initializeSearch();


    console.log("2 Entries");

    initializeEntries();


    console.log("3 Filters");

    initializeFilters();


    console.log("4 Pagination");

    initializePagination();


    console.log("5 View");

    initializeViewButtons();


    console.log("6 Edit");

    initializeEditButtons();


    console.log("7 Delete");

    initializeDeleteButtons();


    console.log("8 Form");

    initializeVoterForm();


    console.log("9 Export");

    initializeExport();


    console.log("10 Year Dropdown");

    initializeYearDropdown();


    console.log("11 Full Name");

    initializeFullNameInput();

}


/* ==========================================================
   FULL NAME — UPPERCASE
========================================================== */

function initializeFullNameInput(){

    const fullName =
        document.getElementById(
            "fullName"
        );


    if(!fullName){

        return;

    }


    fullName.addEventListener(
        "input",
        () => {

            fullName.value =
                fullName.value.toUpperCase();

        }
    );


    /*
     * Make already loaded value uppercase.
     */

    if(fullName.value){

        fullName.value =
            fullName.value.toUpperCase();

    }

}


/* ==========================================================
   YEAR CUSTOM DROPDOWN
========================================================== */

function initializeYearDropdown(){

    if(yearDropdownInitialized){

        return;

    }


    const dropdown =
        document.getElementById(
            "yearDropdown"
        );


    const button =
        document.getElementById(
            "yearDropdownButton"
        );


    const menu =
        document.getElementById(
            "yearDropdownMenu"
        );


    const hiddenInput =
        document.getElementById(
            "year"
        );


    const text =
        document.getElementById(
            "yearDropdownText"
        );


    const icon =
        document.getElementById(
            "yearDropdownIcon"
        );


    if(
        !dropdown ||
        !button ||
        !menu ||
        !hiddenInput ||
        !text
    ){

        console.warn(
            "VOTIFY: Year dropdown elements not found."
        );

        return;

    }


    yearDropdownInitialized = true;


    /* ======================================================
       STORE ORIGINAL POSITION
    ====================================================== */

    yearDropdownMenuOriginalParent =
        menu.parentNode;


    yearDropdownMenuOriginalNextSibling =
        menu.nextSibling;


    /* ======================================================
       MOVE MENU TO BODY
       
       This prevents the dropdown from being
       merged/clipped inside the scrollable modal.
    ====================================================== */

    document.body.appendChild(
        menu
    );


    /* ======================================================
       DROPDOWN MENU BASE STYLE
       
       Design is preserved.
       We only change positioning/layering.
    ====================================================== */

    menu.style.position =
        "fixed";

    menu.style.zIndex =
        "2147483646";

    menu.style.marginTop =
        "0";

    menu.style.transform =
        "none";


    /* ======================================================
       POSITION DROPDOWN
    ====================================================== */

    function positionYearDropdown(){

        if(
            menu.classList.contains(
                "hidden"
            )
        ){

            return;

        }


        const buttonRect =
            button.getBoundingClientRect();


        const viewportWidth =
            window.innerWidth;


        const viewportHeight =
            window.innerHeight;


        const gap =
            8;


        const menuHeight =
            menu.offsetHeight;


        const buttonHeight =
            buttonRect.height;


        /*
         * Keep same width as trigger.
         */

        const width =
            buttonRect.width;


        /*
         * Default: open downward.
         */

        let top =
            buttonRect.bottom +
            gap;


        /*
         * If there is not enough space below,
         * open upward.
         */

        if(
            top + menuHeight >
            viewportHeight - 12
        ){

            const upwardTop =
                buttonRect.top -
                menuHeight -
                gap;


            if(
                upwardTop >= 12
            ){

                top =
                    upwardTop;

            }

        }


        /*
         * Keep dropdown inside viewport horizontally.
         */

        let left =
            buttonRect.left;


        if(
            left + width >
            viewportWidth - 12
        ){

            left =
                viewportWidth -
                width -
                12;

        }


        if(left < 12){

            left = 12;

        }


        menu.style.top =
            Math.round(top) +
            "px";


        menu.style.left =
            Math.round(left) +
            "px";


        menu.style.width =
            Math.round(width) +
            "px";

    }


    /* ======================================================
       OPEN
    ====================================================== */

    function openYearDropdown(){

        menu.classList.remove(
            "hidden"
        );


        button.setAttribute(
            "aria-expanded",
            "true"
        );


        if(icon){

            icon.classList.add(
                "rotate-180"
            );

        }


        /*
         * Position after menu becomes visible,
         * so offsetHeight is available.
         */

        requestAnimationFrame(
            () => {

                positionYearDropdown();

            }
        );

    }


    /* ======================================================
       CLOSE
    ====================================================== */

    function closeYearDropdown(){

        menu.classList.add(
            "hidden"
        );


        button.setAttribute(
            "aria-expanded",
            "false"
        );


        if(icon){

            icon.classList.remove(
                "rotate-180"
            );

        }

    }


    /* ======================================================
       TOGGLE
    ====================================================== */

    function toggleYearDropdown(){

        if(
            menu.classList.contains(
                "hidden"
            )
        ){

            openYearDropdown();

        }
        else{

            closeYearDropdown();

        }

    }


    /* ======================================================
       SET YEAR VALUE
    ====================================================== */

    window.setYearDropdownValue =
        function(value){

            const validValues = [
                "I Year",
                "II Year"
            ];


            /*
             * Safety fallback.
             */

            if(
                !validValues.includes(
                    value
                )
            ){

                value =
                    "I Year";

            }


            hiddenInput.value =
                value;


            text.textContent =
                value;


            /* ==================================================
               UPDATE CHECK ICONS
            ================================================== */

            menu
                .querySelectorAll(
                    ".yearOption"
                )
                .forEach(
                    option => {

                        const optionValue =
                            option.dataset.value;


                        const check =
                            option.querySelector(
                                ".yearCheck"
                            );


                        if(
                            optionValue ===
                            value
                        ){

                            option.classList.add(
                                "bg-blue-500/10",
                                "text-blue-400"
                            );


                            if(check){

                                check.classList.remove(
                                    "hidden"
                                );

                            }

                        }
                        else{

                            option.classList.remove(
                                "bg-blue-500/10",
                                "text-blue-400"
                            );


                            if(check){

                                check.classList.add(
                                    "hidden"
                                );

                            }

                        }

                    }
                );

        };


    /* ======================================================
       DROPDOWN BUTTON
    ====================================================== */

    button.addEventListener(
        "click",
        event => {

            event.preventDefault();

            event.stopPropagation();

            toggleYearDropdown();

        }
    );


    /* ======================================================
       YEAR OPTIONS
    ====================================================== */

    menu
        .querySelectorAll(
            ".yearOption"
        )
        .forEach(
            option => {

                option.addEventListener(
                    "click",
                    event => {

                        event.preventDefault();

                        event.stopPropagation();


                        const value =
                            option.dataset.value;


                        if(!value){

                            return;

                        }


                        window.setYearDropdownValue(
                            value
                        );


                        closeYearDropdown();

                    }
                );

            }
        );


    /* ======================================================
       OUTSIDE CLICK
    ====================================================== */

    yearDropdownOutsideHandler =
        event => {

            const clickedInsideButton =
                button.contains(
                    event.target
                );


            const clickedInsideMenu =
                menu.contains(
                    event.target
                );


            if(
                !clickedInsideButton &&
                !clickedInsideMenu
            ){

                closeYearDropdown();

            }

        };


    document.addEventListener(
        "click",
        yearDropdownOutsideHandler
    );


    /* ======================================================
       ESCAPE
    ====================================================== */

    yearDropdownEscapeHandler =
        event => {

            if(
                event.key ===
                "Escape"
            ){

                closeYearDropdown();

            }

        };


    document.addEventListener(
        "keydown",
        yearDropdownEscapeHandler
    );


    /* ======================================================
       REPOSITION ON SCROLL
    ====================================================== */

    yearDropdownRepositionHandler =
        () => {

            if(
                !menu.classList.contains(
                    "hidden"
                )
            ){

                positionYearDropdown();

            }

        };


    window.addEventListener(
        "resize",
        yearDropdownRepositionHandler
    );


    window.addEventListener(
        "scroll",
        yearDropdownRepositionHandler,
        true
    );


    /* ======================================================
       DEFAULT VALUE
    ====================================================== */

    window.setYearDropdownValue(
        hiddenInput.value ||
        "I Year"
    );

}


/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch(){

    const searchInput =
        document.getElementById(
            "voterSearch"
        );


    if(!searchInput){

        return;

    }


    searchInput.addEventListener(
        "keyup",
        () => {

            searchKeyword =
                searchInput.value
                    .toLowerCase()
                    .trim();


            currentPage = 1;


            updateTable();

        }
    );

}


/* ==========================================================
   FILTER BUTTONS
========================================================== */

function initializeFilters(){

    const all =
        document.getElementById(
            "filterAll"
        );


    const voted =
        document.getElementById(
            "filterVoted"
        );


    const unvoted =
        document.getElementById(
            "filterUnvoted"
        );


    if(
        !all ||
        !voted ||
        !unvoted
    ){

        return;

    }


    all.addEventListener(
        "click",
        () => {

            currentFilter =
                "all";


            currentPage =
                1;


            updateFilterButtons(
                all
            );


            updateTable();

        }
    );


    voted.addEventListener(
        "click",
        () => {

            currentFilter =
                "voted";


            currentPage =
                1;


            updateFilterButtons(
                voted
            );


            updateTable();

        }
    );


    unvoted.addEventListener(
        "click",
        () => {

            currentFilter =
                "unvoted";


            currentPage =
                1;


            updateFilterButtons(
                unvoted
            );


            updateTable();

        }
    );

}


/* ==========================================================
   ENTRIES
========================================================== */

function initializeEntries(){

    const select =
        document.getElementById(
            "entriesSelect"
        );


    if(!select){

        return;

    }


    select.addEventListener(
        "change",
        () => {

            rowsPerPage =
                parseInt(
                    select.value,
                    10
                );


            currentPage =
                1;


            updateTable();

        }
    );

}


/* ==========================================================
   PAGINATION
========================================================== */

function initializePagination(){

    const prevButton =
        document.getElementById(
            "prevPage"
        );


    const nextButton =
        document.getElementById(
            "nextPage"
        );


    if(prevButton){

        prevButton.addEventListener(
            "click",
            () => {

                if(
                    currentPage > 1
                ){

                    currentPage--;

                    updateTable();

                }

            }
        );

    }


    if(nextButton){

        nextButton.addEventListener(
            "click",
            () => {

                const totalRows =
                    getFilteredRows()
                        .length;


                const totalPages =
                    Math.ceil(
                        totalRows /
                        rowsPerPage
                    );


                if(
                    currentPage <
                    totalPages
                ){

                    currentPage++;

                    updateTable();

                }

            }
        );

    }


    updateTable();

}


/* ==========================================================
   FILTERED ROWS
========================================================== */

function getFilteredRows(){

    const rows =
        Array.from(
            document.querySelectorAll(
                "#votersTableBody tr[data-id]"
            )
        );


    return rows.filter(
        row => {

            const text =
                row.innerText
                    .toLowerCase();


            const status =
                row.cells[4]
                    ?.innerText
                    .trim()
                    .toLowerCase();


            const searchMatch =
                text.includes(
                    searchKeyword
                );


            if(
                currentFilter ===
                "voted"
            ){

                return (
                    searchMatch &&
                    status === "voted"
                );

            }


            if(
                currentFilter ===
                "unvoted"
            ){

                return (
                    searchMatch &&
                    status === "unvoted"
                );

            }


            return searchMatch;

        }
    );

}


/* ==========================================================
   UPDATE TABLE
========================================================== */

function updateTable(){

    const rows =
        getFilteredRows();


    const totalRows =
        rows.length;


    const totalPages =
        Math.max(
            1,
            Math.ceil(
                totalRows /
                rowsPerPage
            )
        );


    if(
        currentPage >
        totalPages
    ){

        currentPage =
            totalPages;

    }


    document
        .querySelectorAll(
            "#votersTableBody tr[data-id]"
        )
        .forEach(
            row => {

                row.style.display =
                    "none";

            }
        );


    const start =
        (currentPage - 1) *
        rowsPerPage;


    const end =
        start +
        rowsPerPage;


    rows
        .slice(
            start,
            end
        )
        .forEach(
            row => {

                row.style.display =
                    "";

            }
        );


    const showingStart =
        document.getElementById(
            "showingStart"
        );


    const showingEnd =
        document.getElementById(
            "showingEnd"
        );


    const totalRecords =
        document.getElementById(
            "totalRecords"
        );


    if(showingStart){

        showingStart.textContent =
            totalRows === 0
            ? 0
            : start + 1;

    }


    if(showingEnd){

        showingEnd.textContent =
            Math.min(
                end,
                totalRows
            );

    }


    if(totalRecords){

        totalRecords.textContent =
            totalRows;

    }


    if(
        typeof renderPagination ===
        "function"
    ){

        renderPagination(
            totalPages
        );

    }

}


/* ==========================================================
   RENDER PAGINATION
========================================================== */

function renderPagination(
    totalPages
){

    const container =
        document.getElementById(
            "paginationNumbers"
        );


    if(!container){

        return;

    }


    container.innerHTML =
        "";


    for(
        let i = 1;
        i <= totalPages;
        i++
    ){

        const button =
            document.createElement(
                "button"
            );


        button.type =
            "button";


        button.textContent =
            i;


        button.className =
            i === currentPage
            ? "btn-primary"
            : "btn-outline";


        button.addEventListener(
            "click",
            () => {

                currentPage =
                    i;


                updateTable();

            }
        );


        container.appendChild(
            button
        );

    }

}


/* ==========================================================
   UPDATE TABLE ROW
========================================================== */

function updateTableRow(){

    const voterId =
        document.getElementById(
            "voterId"
        );


    if(!voterId){

        return;

    }


    const id =
        voterId.value;


    const row =
        document.querySelector(
            'tr[data-id="' +
            id +
            '"]'
        );


    if(!row){

        return;

    }


    /* ======================================================
       NAME
    ====================================================== */

    const nameElement =
        row.cells[0]
            ?.querySelector(
                ".font-semibold"
            );


    const fullName =
        document.getElementById(
            "fullName"
        );


    if(
        nameElement &&
        fullName
    ){

        nameElement.textContent =
            fullName.value
                .toUpperCase();

    }


    /* ======================================================
       YEAR
    ====================================================== */

    const yearInput =
        document.getElementById(
            "year"
        );


    if(
        row.cells[3] &&
        yearInput
    ){

        row.cells[3].textContent =
            yearInput.value;

    }

}


/* ==========================================================
   ACTIVE FILTER BUTTON
========================================================== */

function updateFilterButtons(
    active
){

    document
        .querySelectorAll(
            ".filterButton"
        )
        .forEach(
            button => {

                button.classList.remove(
                    "btn-primary"
                );


                button.classList.add(
                    "btn-outline"
                );

            }
        );


    if(!active){

        return;

    }


    active.classList.remove(
        "btn-outline"
    );


    active.classList.add(
        "btn-primary"
    );

}


/* ==========================================================
   LOAD VOTER
========================================================== */

async function loadVoter(
    id
){

    try{

        const response =
            await fetch(

                "../../backend/admin/get-voter.php?id=" +
                encodeURIComponent(id)

            );


        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        if(
            !result.success
        ){

            showToast(
                "error",
                "Failed",
                result.message ||
                "Unable to load voter."
            );

            return;

        }


        const student =
            result.student;


        /* ==================================================
           VALUES
        ================================================== */

        document.getElementById(
            "voterId"
        ).value =
            student.id;


        document.getElementById(
            "fullName"
        ).value =
            (
                student.full_name ||
                ""
            ).toUpperCase();


        document.getElementById(
            "admissionNo"
        ).value =
            student.admission_no;


        document.getElementById(
            "collegeEmail"
        ).value =
            student.college_email;


        document.getElementById(
            "phone"
        ).value =
            student.phone;


        document.getElementById(
            "gender"
        ).value =
            student.gender;


        document.getElementById(
            "department"
        ).value =
            student.department;


        /* ==================================================
           YEAR DROPDOWN
        ================================================== */

        if(
            typeof window.setYearDropdownValue ===
            "function"
        ){

            window.setYearDropdownValue(
                student.year
            );

        }
        else{

            const yearInput =
                document.getElementById(
                    "year"
                );


            if(yearInput){

                yearInput.value =
                    student.year;

            }

        }


        /* ==================================================
           VOTE STATUS
        ================================================== */

        document.getElementById(
            "voteStatus"
        ).value =
            student.vote_status;


        /* ==================================================
           OPEN MODAL
        ================================================== */

        openVoterModal();

    }

    catch(error){

        console.error(
            "VOTIFY Load Voter Error:",
            error
        );


        showToast(
            "error",
            "Error",
            "Unable to load voter information."
        );

    }

}


/* ==========================================================
   OPEN VOTER MODAL
========================================================== */

function openVoterModal(){

    const modal =
        document.getElementById(
            "voterModal"
        );


    if(!modal){

        return;

    }


    /* ======================================================
       CLOSE YEAR DROPDOWN FIRST
    ====================================================== */

    const yearMenu =
        document.getElementById(
            "yearDropdownMenu"
        );


    if(yearMenu){

        yearMenu.classList.add(
            "hidden"
        );

    }


    const yearIcon =
        document.getElementById(
            "yearDropdownIcon"
        );


    if(yearIcon){

        yearIcon.classList.remove(
            "rotate-180"
        );

    }


    modal.classList.remove(
        "hidden"
    );


    modal.classList.add(
        "flex"
    );


    /*
     * Prevent background page scrolling.
     */

    document.body.style.overflow =
        "hidden";

}


/* ==========================================================
   CLOSE VOTER MODAL
========================================================== */

function closeVoterModal(){

    const modal =
        document.getElementById(
            "voterModal"
        );


    if(!modal){

        return;

    }


    /* ======================================================
       CLOSE YEAR DROPDOWN
    ====================================================== */

    const yearMenu =
        document.getElementById(
            "yearDropdownMenu"
        );


    if(yearMenu){

        yearMenu.classList.add(
            "hidden"
        );

    }


    const yearIcon =
        document.getElementById(
            "yearDropdownIcon"
        );


    if(yearIcon){

        yearIcon.classList.remove(
            "rotate-180"
        );

    }


    modal.classList.remove(
        "flex"
    );


    modal.classList.add(
        "hidden"
    );


    /*
     * Restore background scrolling.
     */

    document.body.style.overflow =
        "";

}


/* ==========================================================
   MODAL CLOSE BUTTONS
========================================================== */

document
    .getElementById(
        "closeVoterModal"
    )
    ?.addEventListener(
        "click",
        closeVoterModal
    );


document
    .getElementById(
        "cancelVoter"
    )
    ?.addEventListener(
        "click",
        closeVoterModal
    );


document
    .getElementById(
        "voterModal"
    )
    ?.addEventListener(
        "click",
        event => {

            if(
                event.target.id ===
                "voterModal"
            ){

                closeVoterModal();

            }

        }
    );


/* ==========================================================
   EXPORT
========================================================== */

function initializeExport(){

    const exportButton =
        document.getElementById(
            "exportExcel"
        );


    if(!exportButton){

        return;

    }


    exportButton.addEventListener(
        "click",
        () => {

            exportExcel(
                exportButton
            );

        }
    );

}


/* ==========================================================
   EXPORT FUNCTION
========================================================== */

async function exportExcel(
    exportButton
){

    if(!exportButton){

        return;

    }


    /* ======================================================
       PREVENT DOUBLE CLICK
    ====================================================== */

    if(
        exportButton.disabled
    ){

        return;

    }


    /* ======================================================
       BUILD FILTER PARAMETERS
    ====================================================== */

    const params =
        new URLSearchParams({

            search:
                searchKeyword,

            filter:
                currentFilter

        });


    /* ======================================================
       STORE ORIGINAL BUTTON
    ====================================================== */

    const originalButtonHTML =
        exportButton.innerHTML;


    try{

        /* ==================================================
           BUTTON LOADING
        ================================================== */

        exportButton.disabled =
            true;


        exportButton.innerHTML = `

            <span
                class="inline-flex items-center justify-center gap-2">

                <i
                    class="ri-loader-4-line animate-spin text-xl"
                    aria-hidden="true">
                </i>

                Exporting...

            </span>

        `;


        /* ==================================================
           REQUEST EXPORT
           
           IMPORTANT:
           fetch prevents browser navigation.
        ================================================== */

        const response =
            await fetch(

                "../../backend/admin/export-voters.php?" +
                params.toString(),

                {

                    method:
                        "GET",

                    headers: {

                        "Accept":
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain,application/json"

                    }

                }

            );


        /* ==================================================
           HTTP ERROR
        ================================================== */

        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        /* ==================================================
           CHECK CONTENT TYPE
        ================================================== */

        const contentType =
            (
                response.headers.get(
                    "content-type"
                ) ||
                ""
            ).toLowerCase();


        /* ==================================================
           EMPTY / TEXT RESPONSE
           
           Backend currently sends:
           "No records available for export."
        ================================================== */

        if(
            contentType.includes(
                "text/plain"
            ) ||
            contentType.includes(
                "text/html"
            ) ||
            contentType.includes(
                "application/json"
            )
        ){

            const responseText =
                await response.text();


            let message =
                responseText.trim();


            /*
             * If backend returns JSON,
             * try to extract its message.
             */

            if(
                contentType.includes(
                    "application/json"
                )
            ){

                try{

                    const data =
                        JSON.parse(
                            responseText
                        );


                    message =
                        data.message ||
                        message;

                }
                catch(error){

                    /*
                     * Keep original response text
                     * if it is not valid JSON.
                     */

                }

            }


            /*
             * Remove any accidental HTML tags
             * from backend output.
             */

            const temp =
                document.createElement(
                    "div"
                );


            temp.innerHTML =
                message;


            message =
                (
                    temp.textContent ||
                    temp.innerText ||
                    ""
                ).trim();


            /*
             * Empty message fallback.
             */

            if(!message){

                message =
                    "No voters available to export.";

            }


            showToast(

                "warning",

                "No Records",

                message

            );


            return;

        }


        /* ==================================================
           EXCEL FILE RESPONSE
        ================================================== */

        const blob =
            await response.blob();


        /*
         * Safety check:
         * Do not download an empty file.
         */

        if(
            !blob ||
            blob.size === 0
        ){

            showToast(

                "warning",

                "No Records",

                "No voters available to export."

            );


            return;

        }


        /* ==================================================
           CREATE DOWNLOAD URL
        ================================================== */

        const downloadUrl =
            window.URL.createObjectURL(
                blob
            );


        /* ==================================================
           FILE NAME
        ================================================== */

        let fileName =
            "VOTIFY_Voters.xlsx";


        const contentDisposition =
            response.headers.get(
                "content-disposition"
            );


        if(contentDisposition){

            const fileNameMatch =
                contentDisposition.match(
                    /filename\*?=(?:UTF-8'')?["']?([^;"']+)["']?/i
                );


            if(
                fileNameMatch &&
                fileNameMatch[1]
            ){

                fileName =
                    decodeURIComponent(
                        fileNameMatch[1]
                    );

            }

        }


        /* ==================================================
           DOWNLOAD FILE
        ================================================== */

        const downloadLink =
            document.createElement(
                "a"
            );


        downloadLink.href =
            downloadUrl;


        downloadLink.download =
            fileName;


        downloadLink.style.display =
            "none";


        document.body.appendChild(
            downloadLink
        );


        downloadLink.click();


        downloadLink.remove();


        /* ==================================================
           RELEASE OBJECT URL
        ================================================== */

        setTimeout(
            () => {

                window.URL.revokeObjectURL(
                    downloadUrl
                );

            },
            1000
        );


        /* ==================================================
           SUCCESS TOAST
        ================================================== */

        showToast(

            "success",

            "Exported",

            "Voter data exported successfully."

        );

    }

    catch(error){

        console.error(
            "VOTIFY Export Voters Error:",
            error
        );


        showToast(

            "error",

            "Export Failed",

            "Unable to export voter data. Please try again."

        );

    }

    finally{

        /* ==================================================
           RESTORE BUTTON
        ================================================== */

        exportButton.disabled =
            false;


        exportButton.innerHTML =
            originalButtonHTML;

    }

}


/* ==========================================================
   VOTER FORM
========================================================== */

function initializeVoterForm(){

    const form =
        document.getElementById(
            "voterForm"
        );


    if(!form){

        return;

    }


    form.addEventListener(
        "submit",
        updateVoter
    );

}


/* ==========================================================
   UPDATE VOTER
========================================================== */

async function updateVoter(
    e
){

    e.preventDefault();


    const form =
        document.getElementById(
            "voterForm"
        );


    if(!form){

        return;

    }


    const saveButton =
        form.querySelector(
            'button[type="submit"]'
        );


    if(!saveButton){

        return;

    }


    /* ======================================================
       UPPERCASE NAME
    ====================================================== */

    const fullNameInput =
        document.getElementById(
            "fullName"
        );


    if(fullNameInput){

        fullNameInput.value =
            fullNameInput.value
                .toUpperCase();

    }


    /* ======================================================
       CLOSE DROPDOWN BEFORE SAVE
    ====================================================== */

    const yearMenu =
        document.getElementById(
            "yearDropdownMenu"
        );


    if(yearMenu){

        yearMenu.classList.add(
            "hidden"
        );

    }


    const yearIcon =
        document.getElementById(
            "yearDropdownIcon"
        );


    if(yearIcon){

        yearIcon.classList.remove(
            "rotate-180"
        );

    }


    /* ======================================================
       LOADING
    ====================================================== */

    saveButton.disabled =
        true;


    saveButton.innerHTML = `

        <span
            class="inline-flex items-center gap-2">

            <i
                class="ri-loader-4-line animate-spin">
            </i>

            Saving...

        </span>

    `;


    /* ======================================================
       FORM DATA
    ====================================================== */

    const formData =
        new FormData();


    formData.append(
        "id",
        document.getElementById(
            "voterId"
        ).value
    );


    formData.append(
        "full_name",
        fullNameInput
            ? fullNameInput.value
            : ""
    );


    formData.append(
        "phone",
        document.getElementById(
            "phone"
        ).value
    );


    formData.append(
        "year",
        document.getElementById(
            "year"
        ).value
    );


    try{

        const response =
            await fetch(

                "../../backend/admin/update-voter.php",

                {

                    method:
                        "POST",

                    body:
                        formData

                }

            );


        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        if(
            result.success
        ){

            updateTableRow();


            closeVoterModal();


            showToast(

                "success",

                "Updated",

                result.message ||
                "Voter information updated successfully."

            );

        }
        else{

            showToast(

                "error",

                "Failed",

                result.message ||
                "Unable to update voter."

            );

        }

    }

    catch(error){

        console.error(
            "VOTIFY Update Voter Error:",
            error
        );


        showToast(

            "error",

            "Error",

            "Something went wrong. Please try again."

        );

    }

    finally{

        saveButton.disabled =
            false;


        saveButton.innerHTML =

            '<i class="ri-save-line mr-2"></i>Save Changes';

    }

}


/* ==========================================================
   DELETE BUTTONS
========================================================== */

function initializeDeleteButtons(){

    document
        .querySelectorAll(
            ".deleteVoter"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    () => {

                        const id =
                            button.dataset.id;


                        const row =
                            button.closest(
                                "tr"
                            );


                        if(
                            !id ||
                            !row
                        ){

                            return;

                        }


                        openConfirmationModal({

                            title:
                                "Delete Voter",

                            message:
                                "Delete this voter permanently?",

                            icon:
                                "ri-delete-bin-line",

                            type:
                                "reject",

                            onConfirm(){

                                return deleteVoter(
                                    id,
                                    row
                                );

                            }

                        });

                    }
                );

            }
        );

}


/* ==========================================================
   DELETE VOTER
========================================================== */

async function deleteVoter(
    id,
    row
){

    try{

        const response =
            await fetch(

                "../../backend/admin/delete-voter.php",

                {

                    method:
                        "POST",

                    headers: {

                        "Content-Type":
                            "application/x-www-form-urlencoded"

                    },

                    body:
                        "id=" +
                        encodeURIComponent(
                            id
                        )

                }

            );


        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        if(
            result.success
        ){

            removeVoterRow(
                row
            );


            updateCardsAfterDelete(
                row
            );


            showToast(

                "success",

                "Deleted",

                result.message ||
                "Voter deleted successfully."

            );


            return true;

        }


        showToast(

            "error",

            "Delete Failed",

            result.message ||
            "Unable to delete voter."

        );


        return false;

    }

    catch(error){

        console.error(
            "VOTIFY Delete Error:",
            error
        );


        showToast(

            "error",

            "Error",

            "Something went wrong."

        );


        return false;

    }

}


/* ==========================================================
   REMOVE ROW
========================================================== */

function removeVoterRow(
    row
){

    if(!row){

        return;

    }


    row.style.transition =
        ".35s";


    row.style.opacity =
        "0";


    row.style.transform =
        "translateX(40px) scale(.96)";


    row.style.filter =
        "blur(4px)";


    setTimeout(
        () => {

            if(
                row.parentNode
            ){

                row.remove();

            }


            checkEmptyVoters();


            updateTable();

        },
        350
    );

}


/* ==========================================================
   UPDATE CARDS
========================================================== */

function updateCardsAfterDelete(
    row
){

    if(!row){

        return;

    }


    const approved =
        document.getElementById(
            "approvedStudents"
        );


    const voted =
        document.getElementById(
            "votedStudents"
        );


    const unvoted =
        document.getElementById(
            "unvotedStudents"
        );


    if(approved){

        approved.textContent =

            Math.max(

                0,

                parseInt(
                    approved.textContent,
                    10
                ) - 1

            );

    }


    const status =
        row.dataset.status;


    if(
        status === "Voted"
    ){

        if(voted){

            voted.textContent =

                Math.max(

                    0,

                    parseInt(
                        voted.textContent,
                        10
                    ) - 1

                );

        }

    }
    else{

        if(unvoted){

            unvoted.textContent =

                Math.max(

                    0,

                    parseInt(
                        unvoted.textContent,
                        10
                    ) - 1

                );

        }

    }

}


/* ==========================================================
   EMPTY TABLE
========================================================== */

function checkEmptyVoters(){

    const tbody =
        document.getElementById(
            "votersTableBody"
        );


    if(!tbody){

        return;

    }


    if(
        tbody.querySelectorAll(
            "tr[data-id]"
        ).length === 0
    ){

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    class="py-16 text-center text-slate-400">

                    <div
                        class="flex justify-center mb-6">

                        <i
                            class="
                            ri-user-search-line
                            text-7xl
                            text-slate-500">
                        </i>

                    </div>

                    <h3
                        class="
                        text-2xl
                        font-bold
                        text-white">

                        No Approved Voters

                    </h3>

                    <p
                        class="mt-3 text-slate-400">

                        No approved students available.

                    </p>

                </td>

            </tr>

        `;

    }

}


/* ==========================================================
   VIEW BUTTONS
========================================================== */

function initializeViewButtons(){

    document
        .querySelectorAll(
            ".viewVoter"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    () => {

                        loadStudentDetails(
                            button.dataset.id
                        );

                    }
                );

            }
        );

}


/* ==========================================================
   LOAD STUDENT DETAILS
========================================================== */

async function loadStudentDetails(
    id
){

    try{

        const response =
            await fetch(

                "../../backend/admin/get-voter.php?id=" +
                encodeURIComponent(id)

            );


        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        if(
            !result.success
        ){

            showToast(

                "error",

                "Failed",

                result.message ||
                "Unable to load student details."

            );

            return;

        }


        const student =
            result.student;


        document.getElementById(
            "studentName"
        ).textContent =
            student.full_name;


        document.getElementById(
            "studentAdmission"
        ).textContent =
            student.admission_no;


        document.getElementById(
            "studentEmail"
        ).textContent =
            student.college_email;


        document.getElementById(
            "studentDepartment"
        ).textContent =
            student.department;


        document.getElementById(
            "studentYear"
        ).textContent =
            student.year;


        document.getElementById(
            "studentStatus"
        ).textContent =
            student.vote_status;


        openStudentModal();

    }

    catch(error){

        console.error(
            "VOTIFY Student Details Error:",
            error
        );


        showToast(

            "error",

            "Error",

            "Unable to load student details."

        );

    }

}


/* ==========================================================
   STUDENT MODAL
========================================================== */

function openStudentModal(){

    const modal =
        document.getElementById(
            "studentModal"
        );


    if(!modal){

        return;

    }


    modal.classList.remove(
        "hidden"
    );


    modal.classList.add(
        "flex"
    );

}


function closeStudentModal(){

    const modal =
        document.getElementById(
            "studentModal"
        );


    if(!modal){

        return;

    }


    modal.classList.remove(
        "flex"
    );


    modal.classList.add(
        "hidden"
    );

}


document
    .getElementById(
        "closeStudentModal"
    )
    ?.addEventListener(
        "click",
        closeStudentModal
    );


document
    .getElementById(
        "closeStudentButton"
    )
    ?.addEventListener(
        "click",
        closeStudentModal
    );


document
    .getElementById(
        "studentModal"
    )
    ?.addEventListener(
        "click",
        event => {

            if(
                event.target.id ===
                "studentModal"
            ){

                closeStudentModal();

            }

        }
    );


/* ==========================================================
   EDIT BUTTONS
========================================================== */

function initializeEditButtons(){

    document
        .querySelectorAll(
            ".editVoter"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    () => {

                        loadVoter(
                            button.dataset.id
                        );

                    }
                );

            }
        );

}


/* ==========================================================
   FINAL READY
========================================================== */

console.log(

    "%cVOTIFY Voters Ready",

    "color:#3B82F6;font-size:14px;font-weight:bold;"

);