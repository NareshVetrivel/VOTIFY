/* ==========================================================
   VOTIFY
   Requests Page JavaScript
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

document.addEventListener("DOMContentLoaded", () => {

    initializeRequests();

});


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
 * Expected global function:
 *
 * showToast(type, title, message)
 *
 * We do NOT create another toast design here.
 */

function requestToast(
    type,
    title,
    message
) {

    if (
        typeof window.showToast === "function"
    ) {

        window.showToast(
            type,
            title,
            message
        );

        return;
    }


    console.warn(
        "VOTIFY: toast.js is not loaded."
    );

}


/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch() {

    const search =
        document.getElementById(
            "requestSearch"
        );

    if (!search) return;


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
                .forEach(row => {

                    row.style.display =
                        row.innerText
                            .toLowerCase()
                            .includes(keyword)
                            ? ""
                            : "none";

                });

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
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const row =
                        button.closest("tr");

                    if (!row) return;


                    openStudentModal({

                        name:
                            row.cells[0]
                                .querySelector(
                                    ".font-semibold"
                                )
                                .textContent,

                        email:
                            row.cells[0]
                                .querySelector(
                                    ".text-xs"
                                )
                                .textContent,

                        admission:
                            row.cells[1]
                                .textContent,

                        department:
                            row.cells[2]
                                .textContent,

                        year:
                            row.cells[3]
                                .textContent,

                        status:
                            row.cells[5]
                                .textContent
                                .trim()

                    });

                }
            );

        });

}


/* ==========================================================
   APPROVE
========================================================== */

function initializeApproveButtons() {

    document
        .querySelectorAll(
            ".approveRequest"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const id =
                        button.dataset.id;

                    const row =
                        button.closest("tr");

                    if (!id || !row) {
                        return;
                    }


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

        });

}


/* ==========================================================
   REJECT
========================================================== */

function initializeRejectButtons() {

    document
        .querySelectorAll(
            ".rejectRequest"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const id =
                        button.dataset.id;

                    const row =
                        button.closest("tr");

                    if (!id || !row) {
                        return;
                    }


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

        });

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
                        encodeURIComponent(id)

                }

            );


        if (!response.ok) {

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        if (result.success) {

            requestToast(

                "success",

                "Student Approved",

                "Registration approved successfully."

            );


            removeRequestRow(row);

            updateStatistics(
                "approve"
            );

        }

        else {

            requestToast(

                "error",

                "Approval Failed",

                result.message ||
                "Unable to approve student."

            );

        }

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
                        encodeURIComponent(id)

                }

            );


        if (!response.ok) {

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const result =
            await response.json();


        /* ==================================================
           REJECT SUCCESS
        ================================================== */

        if (result.success) {

            requestToast(

                "success",

                "Student Rejected",

                "Registration rejected successfully."

            );


            /*
             * Remove request from the current
             * admin table only after the backend
             * confirms successful deletion.
             */

            removeRequestRow(row);


            /*
             * Update dashboard counters.
             */

            updateStatistics(
                "reject"
            );

        }

        else {

            requestToast(

                "error",

                "Reject Failed",

                result.message ||
                "Unable to reject student."

            );

        }

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

    }

}


/* ==========================================================
   REMOVE REQUEST ROW
========================================================== */

function removeRequestRow(
    row
) {

    if (!row) return;


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

            if (row.parentNode) {

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


    if (counters.length < 4) {

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
                pending.textContent
            ) - 1

        );


    /* ======================================================
       TOTAL PENDING REQUESTS
    ====================================================== */

    total.textContent =
        Math.max(

            0,

            parseInt(
                total.textContent
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
                approved.textContent
            ) + 1;

    }

    else if (
        action === "reject"
    ) {

        /*
         * IMPORTANT:
         *
         * Rejected students are now deleted
         * from the students table.
         *
         * The counter here represents the
         * admin action during the current page
         * session.
         */

        rejected.textContent =
            parseInt(
                rejected.textContent
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

        <i class="ri-inbox-archive-line text-7xl text-slate-500"></i>

    </div>

    <h3 class="text-2xl font-bold text-white">

        No Pending Requests

    </h3>

    <p class="mt-3 text-slate-400">

        All student registration requests have been processed.

    </p>

</div>

</td>

</tr>

`;

    }

}