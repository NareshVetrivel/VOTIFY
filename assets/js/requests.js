/* ==========================================================
   VOTIFY
   Requests Page JavaScript
   File : assets/js/requests.js
========================================================== */

"use strict";


/* ==========================================================
   VOTIFY REQUESTS NAMESPACE
========================================================== */

/*
 * IMPORTANT:
 *
 * Do NOT create a global variable such as:
 *
 *     let currentElectionStatus
 *
 * because another VOTIFY script may already use the
 * same global identifier.
 *
 * Store Requests-page state inside a dedicated namespace.
 */

window.VOTIFY_REQUESTS =
    window.VOTIFY_REQUESTS || {};


window.VOTIFY_REQUESTS.electionStatus =
    (
        typeof window.VOTIFY_ELECTION_STATUS === "string"
    )
        ? window.VOTIFY_ELECTION_STATUS.trim()
        : "Ready";


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

async function initializeRequests() {

    /*
     * Search works independently.
     */
    initializeSearch();


    /*
     * Use ONE event-delegation listener for:
     *
     * View
     * Approve
     * Reject
     *
     * This keeps button handling reliable.
     */
    initializeRequestTableActions();


    /*
     * Make sure the correct initial empty state
     * is displayed.
     */
    checkEmptyTable();


    /*
     * Load the latest election status.
     *
     * View does NOT depend on this.
     *
     * Approve / Reject do.
     */
    await loadElectionStatus();


    /*
     * Apply the current visual state.
     */
    applyElectionRequestLock();

}


/* ==========================================================
   LOAD ELECTION STATUS
========================================================== */

async function loadElectionStatus() {

    try {

        const response =
            await fetch(
                "../../backend/admin/dashboard-status.php",
                {
                    method: "GET",

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


        /* --------------------------------------------------
           HTTP ERROR
        -------------------------------------------------- */

        if (!response.ok) {

            console.warn(
                "VOTIFY: Election status request failed.",
                response.status
            );

            return false;

        }


        /* --------------------------------------------------
           JSON RESPONSE
        -------------------------------------------------- */

        const result =
            await response.json();


        console.log(
            "VOTIFY Election Status Response:",
            result
        );


        /*
         * dashboard-status.php returns:
         *
         * {
         *     success: true,
         *     status: "Ready"
         * }
         */
        if (
            result &&
            result.success === true &&
            typeof result.status === "string"
        ) {

            window.VOTIFY_REQUESTS.electionStatus =
                result.status.trim();

        }


        /* --------------------------------------------------
           VALIDATE STATUS
        -------------------------------------------------- */

        const allowedStatuses = [
            "Ready",
            "Started",
            "Stopped"
        ];


        if (
            !allowedStatuses.includes(
                window.VOTIFY_REQUESTS.electionStatus
            )
        ) {

            window.VOTIFY_REQUESTS.electionStatus =
                "Ready";

        }


        console.log(
            "VOTIFY Current Requests Election Status:",
            window.VOTIFY_REQUESTS.electionStatus
        );


        /*
         * Update visual state.
         */
        applyElectionRequestLock();


        return true;

    }

    catch (error) {

        console.error(
            "VOTIFY Election Status Error:",
            error
        );

        return false;

    }

}


/* ==========================================================
   REFRESH STATUS BEFORE ACTION
========================================================== */

async function refreshElectionStatusBeforeAction() {

    const loaded =
        await loadElectionStatus();


    /*
     * If the status API fails, do not silently
     * assume that the election is Ready.
     */
    if (!loaded) {

        requestToast(
            "error",
            "Unable to Check Election",
            "Please refresh the page and try again."
        );

        return false;

    }


    return true;

}


/* ==========================================================
   APPLY ELECTION REQUEST LOCK
========================================================== */

function applyElectionRequestLock() {

    const isElectionRunning =
        window.VOTIFY_REQUESTS.electionStatus ===
        "Started";


    /* ======================================================
       APPROVE BUTTONS
    ====================================================== */

    document
        .querySelectorAll(
            ".approveRequest"
        )
        .forEach(
            button => {

                if (isElectionRunning) {

                    button.setAttribute(
                        "aria-disabled",
                        "true"
                    );

                    button.title =
                        "Approval is disabled while the election is running.";

                    button.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

                else {

                    button.removeAttribute(
                        "aria-disabled"
                    );

                    button.title =
                        "Approve Student";

                    button.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

            }
        );


    /* ======================================================
       REJECT BUTTONS
    ====================================================== */

    document
        .querySelectorAll(
            ".rejectRequest"
        )
        .forEach(
            button => {

                if (isElectionRunning) {

                    button.setAttribute(
                        "aria-disabled",
                        "true"
                    );

                    button.title =
                        "Rejection is disabled while the election is running.";

                    button.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

                else {

                    button.removeAttribute(
                        "aria-disabled"
                    );

                    button.title =
                        "Reject Student";

                    button.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

            }
        );


    /*
     * VIEW BUTTONS ARE NEVER DISABLED.
     *
     * Admin can always view student details.
     */

}


/* ==========================================================
   REQUEST TABLE ACTIONS
========================================================== */

/*
 * ONE EVENT-DELEGATION LISTENER
 *
 * Handles:
 *
 *     View
 *     Approve
 *     Reject
 *
 * This also continues working if rows are dynamically
 * removed/replaced.
 */

function initializeRequestTableActions() {

    const tableBody =
        document.getElementById(
            "requestsTableBody"
        );


    if (!tableBody) {

        console.error(
            "VOTIFY: #requestsTableBody not found."
        );

        return;

    }


    tableBody.addEventListener(
        "click",
        async event => {

            const button =
                event.target.closest(
                    "button"
                );


            /*
             * Click was not on a button.
             */
            if (!button) {

                return;

            }


            /*
             * Ignore unrelated buttons.
             */
            if (
                !button.classList.contains(
                    "viewRequest"
                ) &&
                !button.classList.contains(
                    "approveRequest"
                ) &&
                !button.classList.contains(
                    "rejectRequest"
                ) &&
                !button.classList.contains(
                    "clearRequestSearch"
                )
            ) {

                return;

            }


            event.preventDefault();

            event.stopPropagation();


            /* ==================================================
               CLEAR SEARCH
            ================================================== */

            if (
                button.classList.contains(
                    "clearRequestSearch"
                )
            ) {

                clearRequestSearch();

                return;

            }


            /* ==================================================
               VIEW
            ================================================== */

            if (
                button.classList.contains(
                    "viewRequest"
                )
            ) {

                handleViewRequest(
                    button
                );

                return;

            }


            /* ==================================================
               APPROVE
            ================================================== */

            if (
                button.classList.contains(
                    "approveRequest"
                )
            ) {

                await handleApproveRequest(
                    button
                );

                return;

            }


            /* ==================================================
               REJECT
            ================================================== */

            if (
                button.classList.contains(
                    "rejectRequest"
                )
            ) {

                await handleRejectRequest(
                    button
                );

            }

        }
    );

}


/* ==========================================================
   VIEW REQUEST
========================================================== */

function handleViewRequest(
    button
) {

    const row =
        button.closest(
            "tr"
        );


    if (!row) {

        console.warn(
            "VOTIFY: Student request row not found."
        );

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


    /*
     * View must work in:
     *
     * Ready
     * Started
     * Stopped
     */
    if (
        typeof openStudentModal !==
        "function"
    ) {

        console.error(
            "VOTIFY: openStudentModal() is not available."
        );

        return;

    }


    openStudentModal({

        name:
            nameElement
                ? nameElement.textContent.trim()
                : "",

        email:
            emailElement
                ? emailElement.textContent.trim()
                : "",

        admission:
            row.cells[1]
                ? row.cells[1].textContent.trim()
                : "",

        department:
            row.cells[2]
                ? row.cells[2].textContent.trim()
                : "",

        year:
            row.cells[3]
                ? row.cells[3].textContent.trim()
                : "",

        status:
            row.cells[5]
                ? row.cells[5].textContent.trim()
                : ""

    });

}


/* ==========================================================
   APPROVE REQUEST
========================================================== */

async function handleApproveRequest(
    button
) {

    /*
     * Always check the latest election status
     * before opening confirmation.
     */
    const statusChecked =
        await refreshElectionStatusBeforeAction();


    if (!statusChecked) {

        return;

    }


    /* ======================================================
       ELECTION RUNNING
    ====================================================== */

    if (
        window.VOTIFY_REQUESTS.electionStatus ===
        "Started"
    ) {

        /*
         * Confirmation modal must NOT open.
         */
        requestToast(
            "error",
            "Action Disabled",
            "Student approval is disabled while the election is running."
        );

        return;

    }


    /* ======================================================
       GET REQUEST DATA
    ====================================================== */

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

        console.warn(
            "VOTIFY: Invalid approve request data."
        );

        return;

    }


    currentStudentId =
        id;

    currentAction =
        "approve";


    /* ======================================================
       CONFIRMATION MODAL
    ====================================================== */

    if (
        typeof openConfirmationModal !==
        "function"
    ) {

        console.error(
            "VOTIFY: openConfirmationModal() is not available."
        );

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


/* ==========================================================
   REJECT REQUEST
========================================================== */

async function handleRejectRequest(
    button
) {

    /*
     * Always check latest election status
     * before opening confirmation.
     */
    const statusChecked =
        await refreshElectionStatusBeforeAction();


    if (!statusChecked) {

        return;

    }


    /* ======================================================
       ELECTION RUNNING
    ====================================================== */

    if (
        window.VOTIFY_REQUESTS.electionStatus ===
        "Started"
    ) {

        /*
         * Confirmation modal must NOT open.
         */
        requestToast(
            "error",
            "Action Disabled",
            "Student rejection is disabled while the election is running."
        );

        return;

    }


    /* ======================================================
       GET REQUEST DATA
    ====================================================== */

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

        console.warn(
            "VOTIFY: Invalid reject request data."
        );

        return;

    }


    currentStudentId =
        id;

    currentAction =
        "reject";


    /* ======================================================
       CONFIRMATION MODAL
    ====================================================== */

    if (
        typeof openConfirmationModal !==
        "function"
    ) {

        console.error(
            "VOTIFY: openConfirmationModal() is not available."
        );

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


/* ==========================================================
   REQUEST PAGE TOAST
========================================================== */

/*
 * IMPORTANT:
 *
 * Do NOT call window.showToast() here.
 *
 * dashboard.js also defines a global showToast()
 * function for #dashboardToast.
 *
 * Requests page uses #requestToast.
 *
 * Therefore this function directly controls
 * #requestToast and completely avoids the global
 * showToast() conflict.
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


    if (!toast) {

        console.error(
            "VOTIFY: #requestToast element not found."
        );

        return;

    }


    const wrapper =
        document.getElementById(
            "toastIconWrapper"
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


    /* ======================================================
       RESET ICON
    ====================================================== */

    if (wrapper) {

        wrapper.className =
            "w-14 h-14 rounded-2xl flex items-center justify-center";

    }


    if (icon) {

        icon.className =
            "text-3xl";

    }


    /* ======================================================
       SUCCESS
    ====================================================== */

    if (
        type === "success"
    ) {

        if (wrapper) {

            wrapper.classList.add(
                "bg-green-500/20"
            );

        }


        if (icon) {

            icon.classList.add(
                "ri-checkbox-circle-fill",
                "text-green-400"
            );

        }

    }


    /* ======================================================
       ERROR
    ====================================================== */

    else if (
        type === "error"
    ) {

        if (wrapper) {

            wrapper.classList.add(
                "bg-red-500/20"
            );

        }


        if (icon) {

            icon.classList.add(
                "ri-close-circle-fill",
                "text-red-400"
            );

        }

    }


    /* ======================================================
       WARNING / OTHER
    ====================================================== */

    else {

        if (wrapper) {

            wrapper.classList.add(
                "bg-yellow-500/20"
            );

        }


        if (icon) {

            icon.classList.add(
                "ri-error-warning-fill",
                "text-yellow-400"
            );

        }

    }


    /* ======================================================
       UPDATE TEXT
    ====================================================== */

    if (toastTitle) {

        toastTitle.textContent =
            title;

    }


    if (toastMessage) {

        toastMessage.textContent =
            message;

    }


    /* ======================================================
       SHOW TOAST
    ====================================================== */

    toast.classList.remove(
        "translate-x-[120%]"
    );

    toast.classList.add(
        "translate-x-0"
    );


    /*
     * Force visibility.
     *
     * This makes the toast reliable even if another
     * CSS rule interferes with the Tailwind classes.
     */

    toast.style.opacity =
        "1";

    toast.style.visibility =
        "visible";

    toast.style.transform =
        "translateX(0)";


    toast.style.pointerEvents =
        "auto";


    /* ======================================================
       AUTO HIDE
    ====================================================== */

    clearTimeout(
        window.requestToastTimer
    );


    window.requestToastTimer =
        setTimeout(
            () => {

                toast.classList.remove(
                    "translate-x-0"
                );

                toast.classList.add(
                    "translate-x-[120%]"
                );


                toast.style.opacity =
                    "0";

                toast.style.visibility =
                    "hidden";

                toast.style.transform =
                    "translateX(120%)";


                toast.style.pointerEvents =
                    "none";

            },
            3500
        );

}


/* ==========================================================
   SEARCH
========================================================== */

/*
 * Search is handled completely on the client side.
 *
 * IMPORTANT:
 *
 * Only real request rows are searched:
 *
 *     tr[data-id]
 *
 * Empty-state rows are never included in the search.
 *
 * This prevents the empty-state text itself from becoming
 * searchable and causing incorrect results.
 */

function initializeSearch() {

    const search =
        document.getElementById(
            "requestSearch"
        );


    if (!search) {

        return;

    }


    /*
     * Use input instead of keyup.
     *
     * This also works with:
     *
     * - Paste
     * - Mobile keyboards
     * - Browser autofill
     * - Voice input
     */
    search.addEventListener(
        "input",
        () => {

            updateRequestSearchResults();

        }
    );


    /*
     * Show the correct initial state.
     */
    updateRequestSearchResults();

}


/* ==========================================================
   UPDATE SEARCH RESULTS
========================================================== */

function updateRequestSearchResults() {

    const search =
        document.getElementById(
            "requestSearch"
        );


    const tableBody =
        document.getElementById(
            "requestsTableBody"
        );


    if (
        !tableBody
    ) {

        return;

    }


    const keyword =
        search
            ? search.value
                .toLowerCase()
                .trim()
            : "";


    /*
     * Only real student rows.
     *
     * Empty-state rows do not have data-id.
     */
    const rows =
        Array.from(
            tableBody.querySelectorAll(
                "tr[data-id]"
            )
        );


    let visibleRows =
        0;


    rows.forEach(
        row => {

            const rowText =
                row.innerText
                    .toLowerCase()
                    .trim();


            const matches =
                keyword === "" ||
                rowText.includes(
                    keyword
                );


            row.style.display =
                matches
                    ? ""
                    : "none";


            if (matches) {

                visibleRows++;

            }

        }
    );


    /*
     * Display the correct empty state.
     */
    updateRequestEmptyState(
        rows.length,
        visibleRows,
        keyword
    );

}


/* ==========================================================
   UPDATE REQUEST EMPTY STATE
========================================================== */

function updateRequestEmptyState(
    totalRows,
    visibleRows,
    keyword
) {

    const tableBody =
        document.getElementById(
            "requestsTableBody"
        );


    if (!tableBody) {

        return;

    }


    /*
     * Remove the PHP-generated static empty row.
     *
     * The JavaScript-controlled state below replaces it
     * with a smarter dynamic empty state.
     */
    tableBody
        .querySelectorAll(
            "tr:not([data-id])"
        )
        .forEach(
            row => {

                row.remove();

            }
        );


    /*
     * If matching rows exist, there is no need
     * to show an empty state.
     */
    if (
        visibleRows > 0
    ) {

        return;

    }


    const emptyRow =
        document.createElement(
            "tr"
        );


    emptyRow.id =
        "requestsEmptyState";


    emptyRow.className =
        "fade-up";


    const cell =
        document.createElement(
            "td"
        );


    cell.colSpan =
        7;


    cell.className =
        "px-6 py-16";


    const wrapper =
        document.createElement(
            "div"
        );


    wrapper.className =
        "flex flex-col items-center justify-center text-center";


    /* ======================================================
       ICON CONTAINER
    ====================================================== */

    const iconWrapper =
        document.createElement(
            "div"
        );


    iconWrapper.className =
        "w-20 h-20 rounded-3xl bg-blue-500/10 border border-blue-500/10 flex items-center justify-center mb-6";


    const icon =
        document.createElement(
            "i"
        );


    if (
        keyword !== ""
    ) {

        icon.className =
            "ri-search-eye-line text-5xl text-blue-400";

    }

    else {

        icon.className =
            "ri-inbox-archive-line text-5xl text-slate-400";

    }


    icon.setAttribute(
        "aria-hidden",
        "true"
    );


    iconWrapper.appendChild(
        icon
    );


    /* ======================================================
       SMALL STATUS LABEL
    ====================================================== */

    const label =
        document.createElement(
            "span"
        );


    label.className =
        "inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4";


    const labelIcon =
        document.createElement(
            "i"
        );


    labelIcon.className =
        keyword !== ""
            ? "ri-filter-3-line"
            : "ri-inbox-line";


    label.appendChild(
        labelIcon
    );


    const labelText =
        document.createElement(
            "span"
        );


    labelText.textContent =
        keyword !== ""
            ? "Search Result"
            : "Request Queue";


    label.appendChild(
        labelText
    );


    /* ======================================================
       TITLE
    ====================================================== */

    const title =
        document.createElement(
            "h3"
        );


    title.className =
        "text-2xl sm:text-3xl font-bold text-white";


    title.textContent =
        keyword !== ""
            ? "No Matching Students"
            : "No Pending Registration Requests";


    /* ======================================================
       DESCRIPTION
    ====================================================== */

    const message =
        document.createElement(
            "p"
        );


    message.className =
        "mt-3 max-w-md text-sm sm:text-base leading-7 text-slate-400";


    if (
        keyword !== ""
    ) {

        message.textContent =
            "No students matched your search. Try another name, admission number, department, or email.";

    }

    else {

        message.textContent =
            "There are currently no registration requests waiting for review.";

    }


    /* ======================================================
       SEARCH CLEAR BUTTON
    ====================================================== */

    if (
        keyword !== ""
    ) {

        const clearButton =
            document.createElement(
                "button"
            );


        clearButton.type =
            "button";


        clearButton.className =
            "clearRequestSearch mt-6 inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500/20 hover:border-blue-500/30 transition-all duration-200";


        clearButton.innerHTML =
            `
                <i
                    class="ri-close-circle-line"
                    aria-hidden="true">
                </i>

                <span>
                    Clear Search
                </span>
            `;


        wrapper.appendChild(
            iconWrapper
        );

        wrapper.appendChild(
            label
        );

        wrapper.appendChild(
            title
        );

        wrapper.appendChild(
            message
        );

        wrapper.appendChild(
            clearButton
        );

    }

    else {

        wrapper.appendChild(
            iconWrapper
        );

        wrapper.appendChild(
            label
        );

        wrapper.appendChild(
            title
        );

        wrapper.appendChild(
            message
        );

    }


    cell.appendChild(
        wrapper
    );


    emptyRow.appendChild(
        cell
    );


    tableBody.appendChild(
        emptyRow
    );

}


/* ==========================================================
   CLEAR REQUEST SEARCH
========================================================== */

function clearRequestSearch() {

    const search =
        document.getElementById(
            "requestSearch"
        );


    if (!search) {

        return;

    }


    search.value =
        "";


    search.focus();


    updateRequestSearchResults();

}


/* ==========================================================
   APPROVE STUDENT
========================================================== */

async function approveStudent(
    id,
    row
) {

    /*
     * Frontend safety check.
     *
     * Backend performs the real security check.
     */
    if (
        window.VOTIFY_REQUESTS.electionStatus ===
        "Started"
    ) {

        requestToast(
            "error",
            "Action Disabled",
            "Student approval is disabled while the election is running."
        );

        return false;

    }


    try {

        const response =
            await fetch(
                "../../backend/admin/approve-request.php",
                {
                    method:
                        "POST",

                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded",

                        "Accept":
                            "application/json"
                    },

                    credentials:
                        "same-origin",

                    body:
                        "id=" +
                        encodeURIComponent(
                            id
                        )
                }
            );


        const result =
            await response.json();


        console.log(
            "VOTIFY Approve Response:",
            result
        );


        /* ==================================================
           SUCCESS
        ================================================== */

        if (
            result.success
        ) {

            requestToast(
                "success",
                "Student Approved",
                "Registration approved successfully."
            );


            removeRequestRow(
                row
            );


            updateStatistics(
                "approve"
            );


            currentStudentId =
                null;

            currentAction =
                null;


            return true;

        }


        /* ==================================================
           FAILURE
        ================================================== */

        requestToast(
            "error",

            response.status === 403
                ? "Action Disabled"
                : "Approval Failed",

            result.message ||
            "Unable to approve student."
        );


        /*
         * Backend says election is running.
         */
        if (
            response.status === 403
        ) {

            window.VOTIFY_REQUESTS.electionStatus =
                "Started";

            applyElectionRequestLock();

        }


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

    /*
     * Frontend safety check.
     *
     * Backend performs the real security check.
     */
    if (
        window.VOTIFY_REQUESTS.electionStatus ===
        "Started"
    ) {

        requestToast(
            "error",
            "Action Disabled",
            "Student rejection is disabled while the election is running."
        );

        return false;

    }


    try {

        const response =
            await fetch(
                "../../backend/admin/reject-request.php",
                {
                    method:
                        "POST",

                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded",

                        "Accept":
                            "application/json"
                    },

                    credentials:
                        "same-origin",

                    body:
                        "id=" +
                        encodeURIComponent(
                            id
                        )
                }
            );


        const result =
            await response.json();


        console.log(
            "VOTIFY Reject Response:",
            result
        );


        /* ==================================================
           SUCCESS
        ================================================== */

        if (
            result.success
        ) {

            requestToast(
                "success",
                "Student Rejected",
                "Registration rejected successfully."
            );


            removeRequestRow(
                row
            );


            updateStatistics(
                "reject"
            );


            currentStudentId =
                null;

            currentAction =
                null;


            return true;

        }


        /* ==================================================
           FAILURE
        ================================================== */

        requestToast(
            "error",

            response.status === 403
                ? "Action Disabled"
                : "Reject Failed",

            result.message ||
            "Unable to reject student."
        );


        /*
         * Backend says election is running.
         */
        if (
            response.status === 403
        ) {

            window.VOTIFY_REQUESTS.electionStatus =
                "Started";

            applyElectionRequestLock();

        }


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


            /*
             * Recalculate search result state
             * after the row has been removed.
             */
            updateRequestSearchResults();


            /*
             * Re-apply button state in case rows
             * are still present.
             */
            applyElectionRequestLock();

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

/*
 * This function now works together with search.
 *
 * It does NOT simply check whether the tbody contains
 * any <tr>.
 *
 * Instead it checks real request rows:
 *
 *     tr[data-id]
 *
 * and then lets the search system decide whether the
 * correct empty state should be:
 *
 *     No Pending Registration Requests
 *
 * OR
 *
 *     No Matching Students
 */

function checkEmptyTable() {

    updateRequestSearchResults();

}


/* ==========================================================
   END OF REQUESTS.JS
========================================================== */

console.log(
    "%cVOTIFY Requests Ready",
    "color:#3B82F6;font-size:14px;font-weight:bold;"
);