/* ==========================================================
   VOTIFY
   Admin History JavaScript
   File : assets/js/history.js
========================================================== */

"use strict";


/* ==========================================================
   GLOBAL VARIABLES
========================================================== */

let rows = [];

let filteredRows = [];

let currentPage = 1;

let rowsPerPage = 10;


/* ==========================================================
   DOCUMENT READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeHistory();

});


/* ==========================================================
   INITIALIZE HISTORY
========================================================== */

function initializeHistory() {

    const tableBody =
        document.getElementById(
            "historyTableBody"
        );

    if (!tableBody) {

        console.warn(
            "VOTIFY History: Table body not found."
        );

        return;

    }


    /* ======================================================
       GET ONLY REAL LOG ROWS
    ====================================================== */

    rows =
        Array.from(
            tableBody.querySelectorAll("tr")
        ).filter(row => {

            /*
             * Ignore the server-side
             * "No Logs Available" row.
             */

            const emptyCell =
                row.querySelector(
                    'td[colspan="5"]'
                );

            return !emptyCell;

        });


    filteredRows =
        [...rows];


    /* ======================================================
       INITIALIZE COMPONENTS
    ====================================================== */

    initializeSearch();

    initializeFilter();

    initializePagination();


    /* ======================================================
       INITIAL RENDER
    ====================================================== */

    renderTable();


    console.log(
        "%cVOTIFY History Ready",
        "color:#3B82F6;font-size:14px;font-weight:bold;"
    );

}


/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch() {

    const search =
        document.getElementById(
            "historySearch"
        );

    if (!search) {
        return;
    }


    search.addEventListener(
        "input",
        () => {

            applyFilters();

        }
    );

}


/* ==========================================================
   FILTER
========================================================== */

function initializeFilter() {

    const filter =
        document.getElementById(
            "actionFilter"
        );

    const entries =
        document.getElementById(
            "entriesSelect"
        );


    /* ======================================================
       ACTION FILTER
    ====================================================== */

    if (filter) {

        filter.addEventListener(
            "change",
            () => {

                applyFilters();

            }
        );

    }


    /* ======================================================
       ENTRIES FILTER
    ====================================================== */

    if (entries) {

        const initialValue =
            parseInt(
                entries.value,
                10
            );

        if (
            Number.isFinite(initialValue) &&
            initialValue > 0
        ) {

            rowsPerPage =
                initialValue;

        }


        entries.addEventListener(
            "change",
            () => {

                const selectedValue =
                    parseInt(
                        entries.value,
                        10
                    );


                if (
                    Number.isFinite(selectedValue) &&
                    selectedValue > 0
                ) {

                    rowsPerPage =
                        selectedValue;

                } else {

                    rowsPerPage = 10;

                }


                currentPage = 1;

                renderTable();

            }
        );

    }

}


/* ==========================================================
   APPLY SEARCH + ACTION FILTER
========================================================== */

function applyFilters() {

    const search =
        document.getElementById(
            "historySearch"
        );

    const filter =
        document.getElementById(
            "actionFilter"
        );


    const keyword =
        search
            ? search.value
                .toLowerCase()
                .trim()
            : "";


    const action =
        filter
            ? filter.value
                .toLowerCase()
                .trim()
            : "";


    filteredRows =
        rows.filter(
            row => {

                const text =
                    row.innerText
                        .toLowerCase()
                        .trim();


                const actionCell =
                    row.cells[1]
                        ? row.cells[1]
                            .innerText
                            .toLowerCase()
                            .trim()
                        : "";


                const searchMatch =
                    keyword === "" ||
                    text.includes(keyword);


                const actionMatch =
                    action === "" ||
                    actionCell.includes(action);


                return (
                    searchMatch &&
                    actionMatch
                );

            }
        );


    /* ======================================================
       ALWAYS RETURN TO PAGE 1
       AFTER SEARCH/FILTER
    ====================================================== */

    currentPage = 1;

    renderTable();

}


/* ==========================================================
   PAGINATION INITIALIZATION
========================================================== */

function initializePagination() {

    const prev =
        document.getElementById(
            "prevPage"
        );

    const next =
        document.getElementById(
            "nextPage"
        );


    /* ======================================================
       PREVIOUS BUTTON
    ====================================================== */

    if (prev) {

        prev.addEventListener(
            "click",
            () => {

                if (
                    currentPage <= 1
                ) {

                    return;

                }


                currentPage--;

                renderTable();

            }
        );

    }


    /* ======================================================
       NEXT BUTTON
    ====================================================== */

    if (next) {

        next.addEventListener(
            "click",
            () => {

                const totalPages =
                    getTotalPages();


                if (
                    currentPage >=
                    totalPages
                ) {

                    return;

                }


                currentPage++;

                renderTable();

            }
        );

    }

}


/* ==========================================================
   GET TOTAL PAGES
========================================================== */

function getTotalPages() {

    if (
        filteredRows.length === 0 ||
        rowsPerPage <= 0
    ) {

        return 1;

    }


    return Math.max(
        1,
        Math.ceil(
            filteredRows.length /
            rowsPerPage
        )
    );

}


/* ==========================================================
   RENDER TABLE
========================================================== */

function renderTable() {

    /* ======================================================
       SAFETY
    ====================================================== */

    if (rowsPerPage <= 0) {

        rowsPerPage = 10;

    }


    const totalPages =
        getTotalPages();


    /* ======================================================
       KEEP CURRENT PAGE VALID
    ====================================================== */

    if (
        currentPage > totalPages
    ) {

        currentPage =
            totalPages;

    }


    if (
        currentPage < 1
    ) {

        currentPage = 1;

    }


    /* ======================================================
       HIDE ALL REAL ROWS
    ====================================================== */

    rows.forEach(
        row => {

            row.style.display =
                "none";

        }
    );


    /* ======================================================
       CALCULATE CURRENT PAGE RANGE
    ====================================================== */

    const startIndex =
        (currentPage - 1) *
        rowsPerPage;


    const endIndex =
        startIndex +
        rowsPerPage;


    /* ======================================================
       SHOW CURRENT PAGE ROWS
    ====================================================== */

    filteredRows
        .slice(
            startIndex,
            endIndex
        )
        .forEach(
            row => {

                row.style.display =
                    "";

            }
        );


    /* ======================================================
       UPDATE PAGINATION INFO
    ====================================================== */

    updateInfo();


    /* ======================================================
       UPDATE PAGE BUTTONS
    ====================================================== */

    renderPageButtons();


    /* ======================================================
       UPDATE PREVIOUS / NEXT
    ====================================================== */

    updateNavigationButtons();


    /* ======================================================
       UPDATE EMPTY STATE
    ====================================================== */

    updateEmptyState();

}


/* ==========================================================
   UPDATE TABLE INFORMATION
========================================================== */

function updateInfo() {

    const info =
        document.getElementById(
            "historyInfo"
        );


    const total =
        filteredRows.length;


    let start = 0;

    let end = 0;


    if (total > 0) {

        start =
            (currentPage - 1) *
            rowsPerPage + 1;


        end =
            Math.min(
                currentPage *
                rowsPerPage,
                total
            );

    }


    if (info) {

        info.innerHTML = `
            Showing
            <strong class="text-slate-200">
                ${start}
            </strong>
            to
            <strong class="text-slate-200">
                ${end}
            </strong>
            of
            <strong class="text-slate-200">
                ${total}
            </strong>
            entries
        `;

    }


    /* ======================================================
       LEGACY CURRENT PAGE ELEMENT
       Kept for compatibility
    ====================================================== */

    const currentPageElement =
        document.getElementById(
            "currentPage"
        );


    if (currentPageElement) {

        currentPageElement.textContent =
            currentPage;

    }

}


/* ==========================================================
   RENDER NUMBERED PAGE BUTTONS
========================================================== */

function renderPageButtons() {

    const container =
        document.getElementById(
            "historyPageButtons"
        );


    if (!container) {
        return;
    }


    /* ======================================================
       CLEAR PREVIOUS BUTTONS
    ====================================================== */

    container.innerHTML = "";


    const totalPages =
        getTotalPages();


    /*
     * No need to show numbered pagination
     * when there is only one page.
     */

    if (
        totalPages <= 1
    ) {

        return;

    }


    /* ======================================================
       CREATE PAGE BUTTON
    ====================================================== */

    const createPageButton =
        (pageNumber) => {

            const button =
                document.createElement(
                    "button"
                );


            button.type =
                "button";


            button.textContent =
                pageNumber;


            button.setAttribute(
                "aria-label",
                `Go to page ${pageNumber}`
            );


            button.setAttribute(
                "aria-current",
                pageNumber === currentPage
                    ? "page"
                    : "false"
            );


            button.className =
                `
                inline-flex
                items-center
                justify-center
                h-9
                min-w-9
                px-2
                rounded-lg
                text-sm
                font-semibold
                transition
                duration-200
                ${
                    pageNumber === currentPage
                        ? `
                        bg-gradient-to-r
                        from-blue-500
                        to-pink-500
                        text-white
                        shadow-lg
                        shadow-blue-500/20
                        `
                        : `
                        border
                        border-white/10
                        bg-white/5
                        text-slate-300
                        hover:bg-white/10
                        hover:text-white
                        `
                }
                `
                .replace(
                    /\s+/g,
                    " "
                )
                .trim();


            button.addEventListener(
                "click",
                () => {

                    if (
                        pageNumber ===
                        currentPage
                    ) {

                        return;

                    }


                    currentPage =
                        pageNumber;

                    renderTable();

                }
            );


            return button;

        };


    /* ======================================================
       PAGE RANGE
    ====================================================== */

    /*
     * For a small number of pages:
     *
     * 1 2 3 4 5
     *
     * For larger sets we keep the pagination
     * compact instead of generating many buttons.
     */

    if (
        totalPages <= 5
    ) {

        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            container.appendChild(
                createPageButton(page)
            );

        }

        return;

    }


    /* ======================================================
       LARGE PAGINATION
    ====================================================== */

    const pages = [];


    pages.push(1);


    if (currentPage > 3) {

        pages.push("ellipsis-left");

    }


    const startPage =
        Math.max(
            2,
            currentPage - 1
        );


    const endPage =
        Math.min(
            totalPages - 1,
            currentPage + 1
        );


    for (
        let page = startPage;
        page <= endPage;
        page++
    ) {

        pages.push(page);

    }


    if (
        currentPage <
        totalPages - 2
    ) {

        pages.push("ellipsis-right");

    }


    pages.push(totalPages);


    /* ======================================================
       RENDER PAGE RANGE
    ====================================================== */

    pages.forEach(
        page => {

            if (
                typeof page ===
                "string"
            ) {

                const ellipsis =
                    document.createElement(
                        "span"
                    );


                ellipsis.textContent =
                    "…";


                ellipsis.className =
                    `
                    inline-flex
                    items-center
                    justify-center
                    h-9
                    min-w-7
                    px-1
                    text-slate-500
                    text-sm
                    `
                    .replace(
                        /\s+/g,
                        " "
                    )
                    .trim();


                container.appendChild(
                    ellipsis
                );


                return;

            }


            container.appendChild(
                createPageButton(page)
            );

        }
    );

}


/* ==========================================================
   UPDATE PREVIOUS / NEXT BUTTONS
========================================================== */

function updateNavigationButtons() {

    const prev =
        document.getElementById(
            "prevPage"
        );


    const next =
        document.getElementById(
            "nextPage"
        );


    const totalPages =
        getTotalPages();


    const hasMultiplePages =
        filteredRows.length >
        rowsPerPage;


    /* ======================================================
       PREVIOUS
    ====================================================== */

    if (prev) {

        prev.disabled =
            currentPage <= 1 ||
            !hasMultiplePages;

    }


    /* ======================================================
       NEXT
    ====================================================== */

    if (next) {

        next.disabled =
            currentPage >=
            totalPages ||
            !hasMultiplePages;

    }

}


/* ==========================================================
   EMPTY STATE
========================================================== */

function updateEmptyState() {

    const tableBody =
        document.getElementById(
            "historyTableBody"
        );


    if (!tableBody) {
        return;
    }


    /*
     * Remove previously generated
     * JavaScript empty-state row.
     */

    const existingEmptyState =
        document.getElementById(
            "historyJsEmptyState"
        );


    if (existingEmptyState) {

        existingEmptyState.remove();

    }


    /* ======================================================
       NO FILTERED RESULTS
    ====================================================== */

    if (
        filteredRows.length === 0
    ) {

        const emptyRow =
            document.createElement(
                "tr"
            );


        emptyRow.id =
            "historyJsEmptyState";


        emptyRow.innerHTML = `
            <td
                colspan="5"
                class="
                px-6
                py-12
                text-center
                text-slate-400">

                <div
                    class="
                    flex
                    flex-col
                    items-center
                    justify-center
                    gap-2">

                    <i
                        class="
                        ri-file-search-line
                        text-3xl
                        text-slate-500">
                    </i>

                    <p
                        class="
                        text-base
                        font-medium
                        text-slate-300">

                        No matching logs found.

                    </p>

                    <p
                        class="
                        text-sm
                        text-slate-500">

                        Try changing your search or filter.

                    </p>

                </div>

            </td>
        `;


        tableBody.appendChild(
            emptyRow
        );

    }

}


/* ==========================================================
   READY
========================================================== */

console.log(
    "%cVOTIFY History JS Loaded",
    "color:#3B82F6;font-size:14px;font-weight:bold;"
);