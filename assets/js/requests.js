/* ==========================================================
   VOTIFY
   Requests Page JavaScript
   File : assets/js/requests.js
========================================================== */

"use strict";


/* ==========================================================
   GLOBAL STATE
========================================================== */

let currentStudentId = null;

let currentAction = null;


/* ==========================================================
   INITIALIZE
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeRequests();

    }
);


/* ==========================================================
   INITIALIZE REQUESTS
========================================================== */

function initializeRequests() {

    initializeSearch();

    initializeViewButtons();

    initializeApproveButtons();

    initializeRejectButtons();

}


/* ==========================================================
   TOAST HELPER
========================================================== */

/*
 * Uses the existing VOTIFY toast.js design.
 *
 * Existing toast function:
 *
 * showToast(type, title, message)
 *
 * No new toast UI is created.
 *
 * Inline transform is used as a safety fallback so the
 * existing toast remains visible even if Tailwind does not
 * process the dynamically-added translate-x-0 class.
 */

function requestToast(
    type,
    title,
    message
) {

    const toast =
        document.getElementById(
            "requestToast"
        );


    /* ------------------------------------------------------
       TOAST ELEMENT CHECK
    ------------------------------------------------------ */

    if (!toast) {

        console.error(
            "VOTIFY: #requestToast element not found."
        );

        return;

    }


    /* ------------------------------------------------------
       USE EXISTING toast.js
    ------------------------------------------------------ */

    if (
        typeof window.showToast ===
        "function"
    ) {

        window.showToast(
            type,
            title,
            message
        );


        /*
         * Safety fallback:
         *
         * Make sure the existing toast is actually moved
         * into the visible position.
         *
         * This does NOT create a new toast design.
         */

        toast.style.transform =
            "translateX(0)";


        toast.style.opacity =
            "1";


        toast.style.visibility =
            "visible";


        toast.style.pointerEvents =
            "auto";


        /*
         * Keep the existing VOTIFY toast above every
         * modal/overlay.
         */

        toast.style.position =
            "fixed";


        toast.style.zIndex =
            "2147483647";


        /*
         * Hide again using the same existing toast timing.
         */

        clearTimeout(
            window.requestPageToastTimer
        );


        window.requestPageToastTimer =
            setTimeout(
                () => {

                    toast.style.transform =
                        "translateX(120%)";

                    toast.style.opacity =
                        "";

                    toast.style.visibility =
                        "";

                    toast.style.pointerEvents =
                        "";

                },
                3500
            );


        return;

    }


    /* ======================================================
       FALLBACK
       toast.js was not loaded.
    ====================================================== */

    console.error(
        "VOTIFY: toast.js is not loaded. " +
        "Please check requests.php script order."
    );


    /*
     * We intentionally do not create a different toast UI.
     *
     * The project must use the existing VOTIFY toast design.
     */

}


/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch() {

    const search =
        document.getElementById(
            "requestSearch"
        );


    if (!search) {

        return;

    }


    search.addEventListener(
        "keyup",
        () => {

            const keyword =
                search.value
                    .toLowerCase()
                    .trim();


            document
                .querySelectorAll(
                    "#requestsTableBody tr"
                )
                .forEach(
                    row => {

                        row.style.display =
                            row.innerText
                                .toLowerCase()
                                .includes(keyword)
                                ? ""
                                : "none";

                    }
                );

        }
    );

}


/* ==========================================================
   VIEW BUTTON
========================================================== */

function initializeViewButtons() {

    document
        .querySelectorAll(
            ".viewRequest"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    () => {

                        const row =
                            button.closest(
                                "tr"
                            );


                        if (!row) {

                            return;

                        }


                        const nameElement =
                            row.cells[0]
                                ?.querySelector(
                                    ".font-semibold"
                                );


                        const emailElement =
                            row.cells[0]
                                ?.querySelector(
                                    ".text-xs"
                                );


                        openStudentModal({

                            name:
                                nameElement
                                    ? nameElement
                                        .textContent
                                        .trim()
                                    : "",

                            email:
                                emailElement
                                    ? emailElement
                                        .textContent
                                        .trim()
                                    : "",

                            admission:
                                row.cells[1]
                                    ? row.cells[1]
                                        .textContent
                                        .trim()
                                    : "",

                            department:
                                row.cells[2]
                                    ? row.cells[2]
                                        .textContent
                                        .trim()
                                    : "",

                            year:
                                row.cells[3]
                                    ? row.cells[3]
                                        .textContent
                                        .trim()
                                    : "",

                            status:
                                row.cells[5]
                                    ? row.cells[5]
                                        .textContent
                                        .trim()
                                    : ""

                        });

                    }
                );

            }
        );

}


/* ==========================================================
   APPROVE BUTTON
========================================================== */

function initializeApproveButtons() {

    document
        .querySelectorAll(
            ".approveRequest"
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


                        if (
                            !id ||
                            !row
                        ) {

                            return;

                        }


                        currentStudentId =
                            id;

                        currentAction =
                            "approve";


                        openConfirmationModal({

                            title:
                                "Approve Student",

                            message:
                                "Are you sure you want to approve this student?",

                            icon:
                                "ri-check-line",

                            type:
                                "approve",

                            onConfirm() {

                                return approveStudent(
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
   REJECT BUTTON
========================================================== */

function initializeRejectButtons() {

    document
        .querySelectorAll(
            ".rejectRequest"
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


                        if (
                            !id ||
                            !row
                        ) {

                            return;

                        }


                        currentStudentId =
                            id;

                        currentAction =
                            "reject";


                        openConfirmationModal({

                            title:
                                "Reject Student",

                            message:
                                "Are you sure you want to reject this student?",

                            icon:
                                "ri-close-line",

                            type:
                                "reject",

                            onConfirm() {

                                return rejectStudent(
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
   APPROVE STUDENT
========================================================== */

async function approveStudent(
    id,
    row
) {

    try {

        const response =
            await fetch(

                "../../backend/admin/approve-request.php",

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


        /* --------------------------------------------------
           HTTP ERROR
        -------------------------------------------------- */

        if (!response.ok) {

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        /* --------------------------------------------------
           JSON RESPONSE
        -------------------------------------------------- */

        const result =
            await response.json();


        console.log(
            "VOTIFY Approve Response:",
            result
        );


        /* ==================================================
           APPROVE SUCCESS
        ================================================== */

        if (
            result.success
        ) {

            /*
             * Show existing VOTIFY toast.
             */

            requestToast(

                "success",

                "Student Approved",

                "Registration approved successfully."

            );


            /*
             * Remove approved request
             * from pending table.
             */

            removeRequestRow(
                row
            );


            /*
             * Update dashboard counters.
             */

            updateStatistics(
                "approve"
            );


            currentStudentId =
                null;

            currentAction =
                null;


            /*
             * Tell confirmation modal that
             * backend operation succeeded.
             */

            return true;

        }


        /* ==================================================
           APPROVE FAILED
        ================================================== */

        requestToast(

            "error",

            "Approval Failed",

            result.message ||
            "Unable to approve student."

        );


        return false;

    }

    catch (error) {

        console.error(
            "VOTIFY Approve Error:",
            error
        );


        requestToast(

            "error",

            "Approval Failed",

            "Unable to process the request. Please try again."

        );


        return false;

    }

}


/* ==========================================================
   REJECT STUDENT
========================================================== */

async function rejectStudent(
    id,
    row
) {

    try {

        const response =
            await fetch(

                "../../backend/admin/reject-request.php",

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


        /* --------------------------------------------------
           HTTP ERROR
        -------------------------------------------------- */

        if (!response.ok) {

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        /* --------------------------------------------------
           JSON RESPONSE
        -------------------------------------------------- */

        const result =
            await response.json();


        console.log(
            "VOTIFY Reject Response:",
            result
        );


        /* ==================================================
           REJECT SUCCESS
        ================================================== */

        if (
            result.success
        ) {

            /*
             * Show existing VOTIFY toast.
             */

            requestToast(

                "success",

                "Student Rejected",

                "Registration rejected successfully."

            );


            /*
             * Remove rejected request
             * from current table.
             */

            removeRequestRow(
                row
            );


            /*
             * Update dashboard counters.
             */

            updateStatistics(
                "reject"
            );


            currentStudentId =
                null;

            currentAction =
                null;


            /*
             * Tell confirmation modal that
             * backend operation succeeded.
             */

            return true;

        }


        /* ==================================================
           REJECT FAILED
        ================================================== */

        requestToast(

            "error",

            "Reject Failed",

            result.message ||
            "Unable to reject student."

        );


        return false;

    }

    catch (error) {

        console.error(
            "VOTIFY Reject Error:",
            error
        );


        requestToast(

            "error",

            "Reject Failed",

            "Unable to process the request. Please try again."

        );


        return false;

    }

}


/* ==========================================================
   REMOVE REQUEST ROW
========================================================== */

function removeRequestRow(
    row
) {

    if (!row) {

        return;

    }


    row.style.transition =
        "all .35s ease";


    row.style.opacity =
        "0";


    row.style.transform =
        "translateX(40px) scale(.96)";


    row.style.filter =
        "blur(4px)";


    setTimeout(
        () => {

            if (
                row.parentNode
            ) {

                row.remove();

            }


            checkEmptyTable();

        },
        350
    );

}


/* ==========================================================
   UPDATE DASHBOARD COUNTERS
========================================================== */

function updateStatistics(
    action
) {

    const counters =
        document.querySelectorAll(
            ".dashboard-card h2"
        );


    if (
        counters.length < 4
    ) {

        return;

    }


    const total =
        counters[0];


    const pending =
        counters[1];


    const approved =
        counters[2];


    const rejected =
        counters[3];


    /* ======================================================
       PENDING
    ====================================================== */

    pending.textContent =
        Math.max(

            0,

            parseInt(
                pending.textContent,
                10
            ) - 1

        );


    /* ======================================================
       TOTAL
    ====================================================== */

    total.textContent =
        Math.max(

            0,

            parseInt(
                total.textContent,
                10
            ) - 1

        );


    /* ======================================================
       APPROVED / REJECTED
    ====================================================== */

    if (
        action === "approve"
    ) {

        approved.textContent =
            parseInt(
                approved.textContent,
                10
            ) + 1;

    }

    else if (
        action === "reject"
    ) {

        /*
         * Rejected students are deleted
         * from the students table.
         *
         * This counter represents the
         * rejection action during the
         * current page session.
         */

        rejected.textContent =
            parseInt(
                rejected.textContent,
                10
            ) + 1;

    }

}


/* ==========================================================
   EMPTY TABLE
========================================================== */

function checkEmptyTable() {

    const tbody =
        document.getElementById(
            "requestsTableBody"
        );


    if (!tbody) {

        return;

    }


    const rows =
        tbody.querySelectorAll(
            "tr"
        );


    if (
        rows.length === 0
    ) {

        tbody.innerHTML = `

            <tr class="fade-up">

                <td colspan="7">

                    <div class="py-16 text-center">

                        <div class="flex justify-center mb-6">

                            <i
                                class="ri-inbox-archive-line text-7xl text-slate-500">
                            </i>

                        </div>

                        <h3
                            class="text-2xl font-bold text-white">

                            No Pending Requests

                        </h3>

                        <p
                            class="mt-3 text-slate-400">

                            All student registration requests have been processed.

                        </p>

                    </div>

                </td>

            </tr>

        `;

    }

}


/* ==========================================================
   END OF REQUESTS.JS
========================================================== */