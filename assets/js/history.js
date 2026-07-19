/* ==========================================================
   VOTIFY
   Admin History JavaScript
   File : assets/js/history.js
========================================================== */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    initializeHistory();

});

/* ==========================================================
   INITIALIZE
========================================================== */

function initializeHistory() {

rows = Array.from(
    document.querySelectorAll(
        "#historyTableBody tr"
    )
);

filteredRows = [...rows];

initializeSearch();

initializeFilter();

initializePagination();
}

/* ==========================================================
   GLOBAL VARIABLES
========================================================== */

let rows = [];

let filteredRows = [];

let currentPage = 1;

let rowsPerPage = 10;

/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch() {

    const search =
        document.getElementById("historySearch");

    if (!search) return;

    search.addEventListener("keyup", () => {

        const keyword =
            search.value
            .toLowerCase()
            .trim();

        filteredRows = rows.filter(row => {

            return row.innerText
                .toLowerCase()
                .includes(keyword);

        });

        currentPage = 1;

        renderTable();

    });

}

/* ==========================================================
   FILTER
========================================================== */

function initializeFilter() {

    const filter =
        document.getElementById("actionFilter");

    const entries =
        document.getElementById("entriesSelect");

    if (filter) {

        filter.addEventListener("change", () => {

            applyFilters();

        });

    }

    if (entries) {

        entries.addEventListener("change", () => {

            rowsPerPage =
                parseInt(entries.value);

            currentPage = 1;

            renderTable();

        });

    }

}

/* ==========================================================
   APPLY FILTERS
========================================================== */

function applyFilters() {

    const keyword =
        document
        .getElementById("historySearch")
        .value
        .toLowerCase();

    const action =
        document
        .getElementById("actionFilter")
        .value
        .toLowerCase();

    filteredRows = rows.filter(row => {

const text =
    row.innerText.toLowerCase();

const actionCell =
    row.cells[1].innerText.toLowerCase();

const searchMatch =
    text.includes(keyword);

const actionMatch =
    action === "" ||
    actionCell.includes(action);

        return searchMatch && actionMatch;

    });

    currentPage = 1;

    renderTable();

}

/* ==========================================================
   PAGINATION
========================================================== */

function initializePagination() {

    const prev =
        document.getElementById("prevPage");

    const next =
        document.getElementById("nextPage");

    if (prev) {

        prev.addEventListener("click", () => {

            if (currentPage > 1) {

                currentPage--;

                renderTable();

            }

        });

    }

    if (next) {

        next.addEventListener("click", () => {

const totalPages =
Math.max(
1,
Math.ceil(
filteredRows.length /
rowsPerPage
)
);

            if (currentPage < totalPages) {

                currentPage++;

                renderTable();

            }

        });

    }

    renderTable();

}

/* ==========================================================
   RENDER TABLE
========================================================== */

function renderTable() {

    rows.forEach(row => {

        row.style.display = "none";

    });

    const start =
        (currentPage - 1) * rowsPerPage;

    const end =
        start + rowsPerPage;

    filteredRows
        .slice(start, end)
        .forEach(row => {

            row.style.display = "";

        });

    updateInfo();

    const page =
document.getElementById("currentPage");

if(page){

page.textContent=currentPage;

}
}

/* ==========================================================
   TABLE INFO
========================================================== */

function updateInfo() {

    const info =
        document.getElementById("historyInfo");

    const page =
        document.getElementById("currentPage");

    const total =
        filteredRows.length;

    let start =
        (currentPage - 1) *
        rowsPerPage + 1;

    let end =
        Math.min(
            currentPage *
            rowsPerPage,
            total
        );

    if (total === 0) {

        start = 0;

        end = 0;

    }

    if (info) {

        info.innerHTML =

        `Showing
        <strong>${start}</strong>
        to
        <strong>${end}</strong>
        of
        <strong>${total}</strong>
        entries`;

    }

    if (page) {

        page.textContent =
            currentPage;

    }

}

/* ==========================================================
   READY
========================================================== */

console.log(

    "%cHistory Ready",

    "color:#3B82F6;font-size:14px;font-weight:bold;"

);