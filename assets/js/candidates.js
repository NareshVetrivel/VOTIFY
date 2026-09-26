/* ==========================================================
   VOTIFY
   Candidate Management
   File : assets/js/candidates.js
========================================================== */

"use strict";


/* ==========================================================
   CANDIDATE STATE
========================================================== */

let editMode = false;

let editingCandidateId = null;


/* ==========================================================
   ADMISSION NUMBER VALIDATION
========================================================== */

/*
 * VOTIFY admission number format:
 *
 *     25CAPMCA080
 *
 * Structure:
 *     2 digits + CAPMCA + 3 digits
 *
 * Total:
 *     11 characters
 *
 * Example:
 *     25CAPMCA080
 */

const VOTIFY_ADMISSION_REGEX =
    /^[0-9]{2}CAPMCA[0-9]{3}$/;


function isValidAdmissionNumber(
    admissionNumber
){

    return VOTIFY_ADMISSION_REGEX.test(
        admissionNumber.trim().toUpperCase()
    );

}


/* ==========================================================
   VOTIFY CANDIDATES NAMESPACE
========================================================== */

window.VOTIFY_CANDIDATES =
    window.VOTIFY_CANDIDATES || {};

window.VOTIFY_CANDIDATES.electionStatus =
    "Ready";


/* ==========================================================
   READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeCandidates();

    }
);


/* ==========================================================
   INITIALIZE
========================================================== */

async function initializeCandidates(){

    initializeCandidateModal();

    initializeCandidateViewModal();

    initializePhotoPreview();

    initializeManifestoCounter();

    initializeStudentSearch();

    initializeCandidateForm();

    initializeCandidateFilters();

    initializeCandidateSearch();

    initializeEntriesFilter();
    initializeCandidateEmptyState();

    initializeViewCandidate();

    initializeEditCandidate();

    initializeDeleteCandidate();

    initializeExportExcel();


    await loadCandidateElectionStatus();


    applyCandidateModificationLock();

}


/* ==========================================================
   LOAD ELECTION STATUS
========================================================== */

async function loadCandidateElectionStatus(){

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
                "VOTIFY: Candidate election status request failed.",
                response.status
            );

            return false;

        }


        const result =
            await response.json();


        console.log(
            "VOTIFY Candidate Election Status Response:",
            result
        );


        if(
            result &&
            result.success === true &&
            typeof result.status === "string"
        ){

            window.VOTIFY_CANDIDATES.electionStatus =
                result.status.trim();

        }


        const allowedStatuses = [
            "Ready",
            "Started",
            "Stopped"
        ];


        if(
            !allowedStatuses.includes(
                window.VOTIFY_CANDIDATES.electionStatus
            )
        ){

            window.VOTIFY_CANDIDATES.electionStatus =
                "Ready";

        }


        console.log(
            "VOTIFY Current Candidate Election Status:",
            window.VOTIFY_CANDIDATES.electionStatus
        );


        applyCandidateModificationLock();


        return true;

    }

    catch(error){

        console.error(
            "VOTIFY Candidate Election Status Error:",
            error
        );

        return false;

    }

}


/* ==========================================================
   REFRESH STATUS BEFORE MODIFICATION
========================================================== */

async function refreshCandidateElectionStatus(){

    const loaded =
        await loadCandidateElectionStatus();


    if(!loaded){

        showToast(
            "error",
            "Unable to Check Election",
            "Please refresh the page and try again."
        );

        return false;

    }


    return true;

}


/* ==========================================================
   CHECK WHETHER MODIFICATION IS LOCKED
========================================================== */

function isCandidateModificationLocked(){

    return (
        window.VOTIFY_CANDIDATES.electionStatus ===
        "Started"
    );

}


/* ==========================================================
   APPLY CANDIDATE MODIFICATION LOCK
========================================================== */

/*
 * IMPORTANT:
 *
 * Do NOT use disabled=true while election is running.
 *
 * A disabled button does not fire click events.
 * Therefore the user cannot receive the permission toast.
 *
 * Instead:
 *
 *     Add    -> remains clickable
 *     Edit   -> remains clickable
 *     Delete -> remains clickable
 *
 * The click handlers perform the actual permission check.
 *
 * Backend also performs the final security check.
 *
 *
 * Election Started:
 *
 *     Add    -> Toast
 *     Edit   -> Toast
 *     Delete -> Toast
 *     View   -> Allowed
 *
 * Election Ready / Stopped:
 *
 *     Add    -> Allowed
 *     Edit   -> Allowed
 *     Delete -> Allowed
 *     View   -> Allowed
 */

function applyCandidateModificationLock(){

    const isRunning =
        isCandidateModificationLocked();


    /* ======================================================
       ADD CANDIDATE
    ====================================================== */

    const addButton =
        document.getElementById(
            "addCandidate"
        );


    if(addButton){

        /*
         * Keep clickable.
         *
         * Disabled buttons cannot trigger
         * the permission toast.
         */

        addButton.removeAttribute(
            "disabled"
        );


        if(isRunning){

            addButton.setAttribute(
                "aria-disabled",
                "true"
            );

            addButton.title =
                "Adding candidates is disabled while the election is running.";

            addButton.classList.add(
                "opacity-50",
                "cursor-not-allowed"
            );

            addButton.classList.remove(
                "hover:scale-105"
            );

        }
        else{

            addButton.removeAttribute(
                "aria-disabled"
            );

            addButton.title =
                "Add Candidate";

            addButton.classList.remove(
                "opacity-50",
                "cursor-not-allowed"
            );

            addButton.classList.add(
                "hover:scale-105"
            );

        }

    }


    /* ======================================================
       EDIT CANDIDATES
    ====================================================== */

    document
        .querySelectorAll(
            ".editCandidate"
        )
        .forEach(
            button => {

                /*
                 * Keep clickable so the permission
                 * toast can be displayed.
                 */

                button.removeAttribute(
                    "disabled"
                );


                if(isRunning){

                    button.setAttribute(
                        "aria-disabled",
                        "true"
                    );

                    button.title =
                        "Editing candidates is disabled while the election is running.";

                    button.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }
                else{

                    button.removeAttribute(
                        "aria-disabled"
                    );

                    button.title =
                        "Edit";

                    button.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

            }
        );


    /* ======================================================
       DELETE CANDIDATES
    ====================================================== */

    document
        .querySelectorAll(
            ".deleteCandidate"
        )
        .forEach(
            button => {

                /*
                 * Keep clickable so the permission
                 * toast can be displayed.
                 */

                button.removeAttribute(
                    "disabled"
                );


                if(isRunning){

                    button.setAttribute(
                        "aria-disabled",
                        "true"
                    );

                    button.title =
                        "Deleting candidates is disabled while the election is running.";

                    button.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }
                else{

                    button.removeAttribute(
                        "aria-disabled"
                    );

                    button.title =
                        "Delete";

                    button.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

            }
        );


    /*
     * View buttons are NEVER disabled.
     */

}


/* ==========================================================
   MODAL
========================================================== */

function initializeCandidateModal(){

    document
        .getElementById(
            "addCandidate"
        )
        ?.addEventListener(
            "click",
            async () => {

                const statusChecked =
                    await refreshCandidateElectionStatus();


                if(!statusChecked){

                    return;

                }


                if(
                    isCandidateModificationLocked()
                ){

                    showToast(
                        "error",
                        "Action Disabled",
                        "Adding candidates is disabled while the election is running."
                    );


                    applyCandidateModificationLock();

                    return;

                }


                resetCandidateForm();

                openCandidateModal();

            }
        );


    document
        .getElementById(
            "closeCandidateModal"
        )
        ?.addEventListener(
            "click",
            closeCandidateModal
        );


    document
        .getElementById(
            "cancelCandidate"
        )
        ?.addEventListener(
            "click",
            closeCandidateModal
        );


    document
        .getElementById(
            "candidateModal"
        )
        ?.addEventListener(
            "click",
            e => {

                if(
                    e.target.id ===
                    "candidateModal"
                ){

                    closeCandidateModal();

                }

            }
        );

}


/* ==========================================================
   OPEN MODAL
========================================================== */

function openCandidateModal(){

    const modal =
        document.getElementById(
            "candidateModal"
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


/* ==========================================================
   CLOSE MODAL
========================================================== */

function closeCandidateModal(){

    const modal =
        document.getElementById(
            "candidateModal"
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


/* ==========================================================
   RESET FORM
========================================================== */

function resetCandidateForm(){

    const form =
        document.getElementById(
            "candidateForm"
        );


    if(form){

        form.reset();

        document
            .getElementById(
                "candidateManifesto"
            )
            ?.blur();

    }


    document.getElementById(
        "candidateId"
    ).value = "";


    document.getElementById(
        "studentId"
    ).value = "";


    document.getElementById(
        "candidateName"
    ).value = "";


    document.getElementById(
        "candidateDepartment"
    ).value = "";


    document.getElementById(
        "candidateYear"
    ).value = "";


    document.getElementById(
        "manifestoCount"
    ).textContent =
        "0 / 255";


    const preview =
        document.getElementById(
            "photoPreview"
        );


    if(preview){

        preview.src = "";

        preview.classList.add(
            "hidden"
        );

    }


    editMode =
        false;

    editingCandidateId =
        null;


    document.getElementById(
        "admissionNo"
    ).readOnly =
        false;


    document.getElementById(
        "admissionNo"
    ).classList.remove(
        "cursor-not-allowed"
    );


    document.getElementById(
        "saveCandidate"
    ).innerHTML =
        '<i class="ri-save-line mr-2"></i>Save Candidate';


    document.getElementById(
        "candidateManifesto"
    ).readOnly =
        false;
    
    document.getElementById(
        "candidatePhoto"
    ).disabled =
        false;


    document.getElementById(
        "searchStudent"
    ).disabled =
        false;


    document.getElementById(
        "saveCandidate"
    ).classList.remove(
        "hidden"
    );

}


/* ==========================================================
   PHOTO PREVIEW
========================================================== */

function initializePhotoPreview(){

    const input =
        document.getElementById(
            "candidatePhoto"
        );


    const preview =
        document.getElementById(
            "photoPreview"
        );


    if(
        !input ||
        !preview
    ){

        return;

    }


    input.addEventListener(
        "change",
        () => {

            const file =
                input.files[0];


            if(!file){

                return;

            }


            preview.src =
                URL.createObjectURL(
                    file
                );


            preview.classList.remove(
                "hidden"
            );


            preview.onload = () => {

                URL.revokeObjectURL(
                    preview.src
                );

            };

        }
    );

}


/* ==========================================================
   MANIFESTO COUNTER
========================================================== */

function initializeManifestoCounter(){

    const textarea =
        document.getElementById(
            "candidateManifesto"
        );


    const counter =
        document.getElementById(
            "manifestoCount"
        );


    if(
        !textarea ||
        !counter
    ){

        return;

    }


    textarea.addEventListener(
        "input",
        () => {

            counter.textContent =
                textarea.value.length +
                " / 255";

        }
    );

}


/* ==========================================================
   STUDENT SEARCH
========================================================== */

function initializeStudentSearch(){

    const searchButton =
        document.getElementById(
            "searchStudent"
        );


    const admissionInput =
        document.getElementById(
            "admissionNo"
        );


    if(
        !searchButton ||
        !admissionInput
    ){

        return;

    }


    admissionInput.addEventListener(
        "input",
        () => {

            /*
             * Keep admission number uppercase.
             *
             * maxlength="11" is also present in the
             * candidate modal, but we enforce the limit
             * here as a second client-side safeguard.
             */

            admissionInput.value =
                admissionInput.value
                    .toUpperCase()
                    .slice(0, 11);


            /*
             * Remove browser-invalid whitespace.
             *
             * The final format is still enforced by the
             * exact regex below.
             */

            admissionInput.value =
                admissionInput.value.replace(
                    /\\s/g,
                    ""
                );


            /*
             * Show native validation state while typing.
             */

            if(
                admissionInput.value === ""
            ){

                admissionInput.setCustomValidity(
                    ""
                );

            }
            else if(
                !isValidAdmissionNumber(
                    admissionInput.value
                )
            ){

                admissionInput.setCustomValidity(
                    "Admission number must be exactly 11 characters in the format 25CAPMCA080."
                );

            }
            else{

                admissionInput.setCustomValidity(
                    ""
                );

            }

        }
    );


    searchButton.addEventListener(
        "click",
        searchStudent
    );


    admissionInput.addEventListener(
        "keydown",
        e => {

            if(
                e.key ===
                "Enter"
            ){

                e.preventDefault();

                searchStudent();

            }

        }
    );

}


/* ==========================================================
   SEARCH STUDENT
========================================================== */

async function searchStudent(){

    const admission =
        document
            .getElementById(
                "admissionNo"
            )
            .value
            .trim();


    if(
        admission ===
        ""
    ){

        showToast(
            "warning",
            "Admission Number",
            "Please enter admission number."
        );

        return;

    }


    /*
     * Exact admission-number validation.
     *
     * Required format:
     *     25CAPMCA080
     *
     * Exactly 11 characters.
     */

    if(
        !isValidAdmissionNumber(
            admission
        )
    ){

        showToast(
            "warning",
            "Invalid Admission Number",
            "Use exactly 11 characters in the format 25CAPMCA080."
        );


        document
            .getElementById(
                "admissionNo"
            )
            .focus();


        return;

    }


    const searchButton =
        document.getElementById(
            "searchStudent"
        );


    searchButton.disabled =
        true;


    searchButton.innerHTML =
        '<i class="ri-loader-4-line animate-spin"></i>';


    try{

        const response =
            await fetch(
                "../../backend/admin/check-student.php?admission_no=" +
                encodeURIComponent(
                    admission
                )
            );


        const result =
            await response.json();


        if(result.success){

            fillStudentDetails(
                result.student
            );

        }
        else{

            clearStudentDetails();

            showToast(
                "error",
                "Student Not Found",
                result.message
            );

        }

    }

    catch(error){

        console.error(
            "VOTIFY Student Search Error:",
            error
        );


        showToast(
            "error",
            "Server Error",
            "Unable to search student."
        );

    }

    finally{

        searchButton.disabled =
            false;

        searchButton.innerHTML =
            '<i class="ri-search-line text-xl"></i>';

    }

}


/* ==========================================================
   FILL STUDENT DETAILS
========================================================== */

function fillStudentDetails(
    student
){

    document.getElementById(
        "studentId"
    ).value =
        student.id;


    document.getElementById(
        "candidateName"
    ).value =
        student.full_name;


    document.getElementById(
        "candidateDepartment"
    ).value =
        student.department;


    document.getElementById(
        "candidateYear"
    ).value =
        student.year;


    document.getElementById(
        "candidatePhoto"
    ).focus();

}


/* ==========================================================
   CLEAR STUDENT DETAILS
========================================================== */

function clearStudentDetails(){

    document.getElementById(
        "studentId"
    ).value =
        "";


    document.getElementById(
        "candidateName"
    ).value =
        "";


    document.getElementById(
        "candidateDepartment"
    ).value =
        "";


    document.getElementById(
        "candidateYear"
    ).value =
        "";


    document.getElementById(
        "candidatePhoto"
    ).value =
        "";

}


/* ==========================================================
   FORM VALIDATION
========================================================== */

function initializeCandidateForm(){

    const form =
        document.getElementById(
            "candidateForm"
        );


    if(!form){

        return;

    }


    form.addEventListener(
        "submit",
        validateCandidateForm
    );

}


/* ==========================================================
   VALIDATE FORM
========================================================== */

function validateCandidateForm(
    e
){

    e.preventDefault();


    const admission =
        document.getElementById(
            "admissionNo"
        ).value.trim();


    const studentId =
        document.getElementById(
            "studentId"
        ).value.trim();


    const manifesto =
        document.getElementById(
            "candidateManifesto"
        ).value.trim();


    const photo =
        document.getElementById(
            "candidatePhoto"
        ).files.length;


    if(
        admission ===
        ""
    ){

        showToast(
            "warning",
            "Admission Number",
            "Please enter Admission Number."
        );


        document
            .getElementById(
                "admissionNo"
            )
            .focus();


        return;

    }


    /*
     * Exact admission-number validation.
     *
     * This prevents the form from being submitted with
     * an invalid admission number even if browser-native
     * validation is bypassed.
     */

    if(
        !isValidAdmissionNumber(
            admission
        )
    ){

        showToast(
            "warning",
            "Invalid Admission Number",
            "Admission number must be exactly 11 characters in the format 25CAPMCA080."
        );


        document
            .getElementById(
                "admissionNo"
            )
            .focus();


        return;

    }


    if(
        studentId ===
        ""
    ){

        showToast(
            "warning",
            "Search Student",
            "Search and verify the student first."
        );


        document
            .getElementById(
                "admissionNo"
            )
            .focus();


        return;

    }


    if(
        !editMode &&
        photo === 0
    ){

        showToast(
            "warning",
            "Candidate Photo",
            "Please upload candidate photo."
        );

        return;

    }


    if(
        manifesto ===
        ""
    ){

        showToast(
            "warning",
            "Manifesto",
            "Please enter candidate manifesto."
        );


        document
            .getElementById(
                "candidateManifesto"
            )
            .focus();


        return;

    }


    saveCandidate();

}


/* ==========================================================
   SAVE CANDIDATE
========================================================== */

async function saveCandidate(){

    const statusChecked =
        await refreshCandidateElectionStatus();


    if(
        !statusChecked
    ){

        return;

    }


    if(
        isCandidateModificationLocked()
    ){

        showToast(
            "error",
            "Action Disabled",

            editMode
                ? "Editing candidates is disabled while the election is running."
                : "Adding candidates is disabled while the election is running."
        );


        closeCandidateModal();

        applyCandidateModificationLock();

        return;

    }


    const form =
        document.getElementById(
            "candidateForm"
        );


    const saveButton =
        document.getElementById(
            "saveCandidate"
        );


    const originalButton =
        saveButton.innerHTML;


    saveButton.disabled =
        true;


    saveButton.innerHTML =
        '<i class="ri-loader-4-line animate-spin mr-2"></i>Saving...';


    try{

        const formData =
            new FormData(
                form
            );


        if(editMode){

            formData.append(
                "candidateId",
                editingCandidateId
            );

        }


    const url =
        editMode
            ? "../../backend/admin/update-candidate.php"
            : "../../backend/admin/add-candidate.php";


        const response =
            await fetch(
                url,
                {
                    method:
                        "POST",

                    credentials:
                        "same-origin",

                    headers: {
                        "Accept":
                            "application/json"
                    },

                    body:
                        formData
                }
            );


        const text =
            await response.text();


        let result;


        try{

            result =
                JSON.parse(
                    text
                );

        }

        catch(jsonError){

            console.error(
                "VOTIFY Candidate Save Invalid JSON:",
                text
            );


            throw new Error(
                "Invalid server response."
            );

        }


        if(
            result.success
        ){

            showToast(
                "success",

                editMode
                    ? "Candidate Updated"
                    : "Candidate Added",

                result.message
            );


            closeCandidateModal();


            const tableRefreshed =
                await refreshCandidateTableFromServer();


            if(!tableRefreshed){

                showToast(
                    "warning",
                    "Saved Successfully",
                    "Candidate was saved, but the table could not be refreshed automatically. Please refresh the page once."
                );

            }

        }
        else{

            if(
                response.status ===
                403
            ){

                window.VOTIFY_CANDIDATES.electionStatus =
                    "Started";

                applyCandidateModificationLock();

            }


            showToast(
                "error",

                response.status === 403
                    ? "Action Disabled"
                    : "Failed",

                result.message ||
                "Unable to save candidate."
            );

        }

    }

    catch(error){

        console.error(
            "VOTIFY Save Candidate Error:",
            error
        );


        showToast(
            "error",
            "Server Error",
            "Something went wrong."
        );

    }

    finally{

        saveButton.disabled =
            false;

        saveButton.innerHTML =
            originalButton;

    }

}


/* ==========================================================
   EMPTY STATE INITIALIZATION
========================================================== */

function initializeCandidateEmptyState(){

    const clearButton =
        document.getElementById(
            "clearCandidateSearch"
        );

    if(!clearButton){

        return;

    }


    /*
     * Clear the current search keyword and
     * immediately re-apply the table view.
     */

    clearButton.addEventListener(
        "click",
        () => {

            const searchInput =
                document.getElementById(
                    "candidateSearch"
                );

            if(searchInput){

                searchInput.value = "";

            }


            refreshCandidateTableView();

        }
    );

}


/* ==========================================================
   FILTER CANDIDATES
========================================================== */

function initializeCandidateFilters(){

    document
        .getElementById("filterAll")
        ?.addEventListener(
            "click",
            () => {

                setActiveFilter(
                    document.getElementById("filterAll")
                );

                refreshCandidateTableView();

            }
        );


    document
        .getElementById("filterFirstYear")
        ?.addEventListener(
            "click",
            () => {

                setActiveFilter(
                    document.getElementById("filterFirstYear")
                );

                refreshCandidateTableView();

            }
        );


    document
        .getElementById("filterSecondYear")
        ?.addEventListener(
            "click",
            () => {

                setActiveFilter(
                    document.getElementById("filterSecondYear")
                );

                refreshCandidateTableView();

            }
        );

}



/* ==========================================================
   SEARCH
========================================================== */

function initializeCandidateSearch(){

    const input =
        document.getElementById(
            "candidateSearch"
        );


    if(!input){

        return;

    }


    input.addEventListener(
        "input",
        () => {

            refreshCandidateTableView();

        }
    );

}



/* ==========================================================
   ACTIVE FILTER BUTTON
========================================================== */

function setActiveFilter(
    button
){

    if(!button){

        return;

    }


    document
        .querySelectorAll(
            ".filterButton"
        )
        .forEach(
            btn => {

                btn.classList.remove(
                    "btn-primary"
                );

                btn.classList.add(
                    "btn-outline"
                );

            }
        );


    button.classList.remove(
        "btn-outline"
    );


    button.classList.add(
        "btn-primary"
    );

}



/* ==========================================================
   ENTRIES
========================================================== */

function initializeEntriesFilter(){

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

            refreshCandidateTableView();

        }
    );


    /*
     * Apply the initial entries value after the page
     * and table rows are available.
     */

    refreshCandidateTableView();

}



/* ==========================================================
   VIEW CANDIDATE BUTTONS
========================================================== */

function initializeViewCandidate(){

    document
        .querySelectorAll(
            ".viewCandidate"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    () => {

                        viewCandidate(
                            button.dataset.id
                        );

                    }
                );

            }
        );

}


/* ==========================================================
   EDIT CANDIDATE BUTTONS
========================================================== */

function initializeEditCandidate(){

    document
        .querySelectorAll(
            ".editCandidate"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    async () => {

                        const statusChecked =
                            await refreshCandidateElectionStatus();


                        if(
                            !statusChecked
                        ){

                            return;

                        }


                        if(
                            isCandidateModificationLocked()
                        ){

                            showToast(
                                "error",
                                "Action Disabled",
                                "Editing candidates is disabled while the election is running."
                            );


                            applyCandidateModificationLock();

                            return;

                        }


                        editCandidate(
                            button.dataset.id
                        );

                    }
                );

            }
        );

}


/* ==========================================================
   DELETE CANDIDATE BUTTONS
========================================================== */

function initializeDeleteCandidate(){

    document
        .querySelectorAll(
            ".deleteCandidate"
        )
        .forEach(
            button => {

                button.addEventListener(
                    "click",
                    async () => {

                        const statusChecked =
                            await refreshCandidateElectionStatus();


                        if(
                            !statusChecked
                        ){

                            return;

                        }


                        if(
                            isCandidateModificationLocked()
                        ){

                            showToast(
                                "error",
                                "Action Disabled",
                                "Deleting candidates is disabled while the election is running."
                            );


                            applyCandidateModificationLock();

                            return;

                        }


                        deleteCandidate(
                            button.dataset.id
                        );

                    }
                );

            }
        );

}


/* ==========================================================
   EXPORT EXCEL
========================================================== */

function initializeExportExcel(){

    const exportButton =
        document.getElementById(
            "exportCandidates"
        );


    if(!exportButton){

        return;

    }


    exportButton.addEventListener(
        "click",
        () => {

            exportCandidatesExcel(
                exportButton
            );

        }
    );

}

/* ==========================================================
   EXPORT CANDIDATES EXCEL
========================================================== */

async function exportCandidatesExcel(
    exportButton
){

    if(!exportButton){

        return;

    }


    if(
        exportButton.disabled
    ){

        return;

    }


    const searchInput =
        document.getElementById(
            "candidateSearch"
        );


    const search =
        searchInput
            ? searchInput.value.trim()
            : "";


    let filter =
        "all";


    const firstYearButton =
        document.getElementById(
            "filterFirstYear"
        );


    const secondYearButton =
        document.getElementById(
            "filterSecondYear"
        );


    if(
        firstYearButton &&
        firstYearButton.classList.contains(
            "btn-primary"
        )
    ){

        filter =
            "first";

    }
    else if(
        secondYearButton &&
        secondYearButton.classList.contains(
            "btn-primary"
        )
    ){

        filter =
            "second";

    }


    const params =
        new URLSearchParams({
            search:
                search,

            filter:
                filter
        });


    const originalButtonHTML =
        exportButton.innerHTML;


    try{

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


        const response =
            await fetch(
                "../../backend/admin/export-candidates.php?" +
                params.toString(),
                {
                    method:
                        "GET",

                    headers: {
                        "Accept":
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain,text/html,application/json"
                    }
                }
            );


        if(!response.ok){

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const contentType =
            (
                response.headers.get(
                    "content-type"
                ) ||
                ""
            ).toLowerCase();


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

                    /* Keep original message. */

                }

            }


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


            if(!message){

                message =
                    "No candidates available to export.";

            }


            showToast(
                "warning",
                "No Records",
                message
            );


            return;

        }


        const blob =
            await response.blob();


        if(
            !blob ||
            blob.size === 0
        ){

            showToast(
                "warning",
                "No Records",
                "No candidates available to export."
            );


            return;

        }


        const downloadUrl =
            window.URL.createObjectURL(
                blob
            );


        let fileName =
            "VOTIFY_Candidates.xlsx";


        const contentDisposition =
            response.headers.get(
                "content-disposition"
            );


        if(
            contentDisposition
        ){

            const fileNameMatch =
                contentDisposition.match(
                    /filename\*?=(?:UTF-8'')?["']?([^;"']+)["']?/i
                );


            if(
                fileNameMatch &&
                fileNameMatch[1]
            ){

                try{

                    fileName =
                        decodeURIComponent(
                            fileNameMatch[1]
                        );

                }
                catch(error){

                    fileName =
                        fileNameMatch[1];

                }

            }

        }


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


        setTimeout(
            () => {

                window.URL.revokeObjectURL(
                    downloadUrl
                );

            },
            1000
        );


        showToast(
            "success",
            "Exported",
            "Candidate data exported successfully."
        );

    }

    catch(error){

        console.error(
            "VOTIFY Export Candidates Error:",
            error
        );


        showToast(
            "error",
            "Export Failed",
            "Unable to export candidate data. Please try again."
        );

    }

    finally{

        exportButton.disabled =
            false;


        exportButton.innerHTML =
            originalButtonHTML;

    }

}


/* ==========================================================
   CANDIDATE VIEW MODAL
========================================================== */

function initializeCandidateViewModal(){

    document
        .getElementById(
            "closeCandidateViewModal"
        )
        ?.addEventListener(
            "click",
            closeCandidateViewModal
        );


    document
        .getElementById(
            "closeCandidateView"
        )
        ?.addEventListener(
            "click",
            closeCandidateViewModal
        );


    document
        .getElementById(
            "candidateViewModal"
        )
        ?.addEventListener(
            "click",
            e => {

                if(
                    e.target.id ===
                    "candidateViewModal"
                ){

                    closeCandidateViewModal();

                }

            }
        );

}


/* ==========================================================
   OPEN CANDIDATE VIEW MODAL
========================================================== */

function openCandidateViewModal(){

    const modal =
        document.getElementById(
            "candidateViewModal"
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


/* ==========================================================
   CLOSE CANDIDATE VIEW MODAL
========================================================== */

function closeCandidateViewModal(){

    const modal =
        document.getElementById(
            "candidateViewModal"
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


/* ==========================================================
   VIEW CANDIDATE
========================================================== */

async function viewCandidate(
    id
){

    try{

        const response =
            await fetch(
                "../../backend/admin/get-candidate.php?id=" +
                encodeURIComponent(
                    id
                ),
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


        const result =
            await response.json();


        if(
            !result.success
        ){

            showToast(
                "error",
                "Failed",
                result.message
            );


            return;

        }


        const candidate =
            result.candidate;


        document.getElementById(
            "viewCandidatePhoto"
        ).src =
            "../../backend/candidate-photo.php?id=" +
            encodeURIComponent(
                candidate.id
            );


        document.getElementById(
            "viewCandidateName"
        ).textContent =
            candidate.full_name;


        document.getElementById(
            "viewCandidateAdmission"
        ).textContent =
            candidate.admission_no;


        document.getElementById(
            "viewCandidateDepartment"
        ).textContent =
            candidate.department;


        document.getElementById(
            "viewCandidateYear"
        ).textContent =
            candidate.year;


        document.getElementById(
            "viewCandidateManifesto"
        ).textContent =
            candidate.manifesto;


        openCandidateViewModal();

    }

    catch(error){

        console.error(
            "VOTIFY View Candidate Error:",
            error
        );


        showToast(
            "error",
            "Server Error",
            "Unable to load candidate."
        );

    }

}


/* ==========================================================
   EDIT CANDIDATE
========================================================== */

async function editCandidate(
    id
){

    try{

        const response =
            await fetch(
                "../../backend/admin/get-candidate.php?id=" +
                encodeURIComponent(
                    id
                ),
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


        const result =
            await response.json();


        if(
            !result.success
        ){

            showToast(
                "error",
                "Failed",
                result.message
            );


            return;

        }


        const candidate =
            result.candidate;


        const statusChecked =
            await refreshCandidateElectionStatus();


        if(
            !statusChecked
        ){

            return;

        }


        if(
            isCandidateModificationLocked()
        ){

            showToast(
                "error",
                "Action Disabled",
                "Editing candidates is disabled while the election is running."
            );


            applyCandidateModificationLock();

            return;

        }


        resetCandidateForm();


        editMode =
            true;

        editingCandidateId =
            candidate.id;


        document.getElementById(
            "candidateId"
        ).value =
            candidate.id;


        document.getElementById(
            "studentId"
        ).value =
            candidate.student_id;


        document.getElementById(
            "admissionNo"
        ).value =
            String(
                candidate.admission_no || ""
            )
                .toUpperCase()
                .slice(0, 11);


        document.getElementById(
            "candidateName"
        ).value =
            candidate.full_name;


        document.getElementById(
            "candidateDepartment"
        ).value =
            candidate.department;


        document.getElementById(
            "candidateYear"
        ).value =
            candidate.year;


        document.getElementById(
            "candidateManifesto"
        ).value =
            candidate.manifesto;


        document.getElementById(
            "manifestoCount"
        ).textContent =
            candidate.manifesto.length +
            " / 255";


        const preview =
            document.getElementById(
                "photoPreview"
            );


        preview.src =
            "../../backend/candidate-photo.php?id=" +
            encodeURIComponent(
                candidate.id
            );


        preview.classList.remove(
            "hidden"
        );


        document.getElementById(
            "admissionNo"
        ).readOnly =
            true;


        document.getElementById(
            "admissionNo"
        ).classList.add(
            "cursor-not-allowed"
        );


        document.getElementById(
            "candidateManifesto"
        ).readOnly =
            false;


        document.getElementById(
            "candidatePhoto"
        ).disabled =
            false;


        document.getElementById(
            "searchStudent"
        ).disabled =
            true;


        document.getElementById(
            "saveCandidate"
        ).classList.remove(
            "hidden"
        );


        document.getElementById(
            "saveCandidate"
        ).innerHTML =
            '<i class="ri-save-line mr-2"></i>Update Candidate';


        openCandidateModal();

    }

    catch(error){

        console.error(
            "VOTIFY Edit Candidate Error:",
            error
        );


        showToast(
            "error",
            "Server Error",
            "Unable to load candidate."
        );

    }

}



/* ==========================================================
   REFRESH CANDIDATE TABLE FROM SERVER
========================================================== */

/*
 * Refreshes only the candidate table after Add/Edit.
 *
 * IMPORTANT:
 * - Does NOT reload the browser page.
 * - Does NOT touch the backend.
 * - Re-reads the server-rendered candidate table.
 * - Preserves current search/filter/entries state.
 * - Rebinds View/Edit/Delete actions for the refreshed rows.
 */

async function refreshCandidateTableFromServer(){

    const searchInput =
        document.getElementById(
            "candidateSearch"
        );

    const currentSearch =
        searchInput
            ? searchInput.value
            : "";


    const entriesSelect =
        document.getElementById(
            "entriesSelect"
        );

    const currentEntries =
        entriesSelect
            ? entriesSelect.value
            : "";


    let currentFilter =
        "all";


    const firstYearButton =
        document.getElementById(
            "filterFirstYear"
        );


    const secondYearButton =
        document.getElementById(
            "filterSecondYear"
        );


    if(
        firstYearButton &&
        firstYearButton.classList.contains(
            "btn-primary"
        )
    ){

        currentFilter =
            "first";

    }
    else if(
        secondYearButton &&
        secondYearButton.classList.contains(
            "btn-primary"
        )
    ){

        currentFilter =
            "second";

    }


    try{

        const response =
            await fetch(
                window.location.href,
                {
                    method:
                        "GET",

                    credentials:
                        "same-origin",

                    cache:
                        "no-store",

                    headers: {
                        "Accept":
                            "text/html"
                    }
                }
            );


        if(
            !response.ok
        ){

            throw new Error(
                "Unable to refresh candidate table. HTTP " +
                response.status
            );

        }


        const html =
            await response.text();


        const parser =
            new DOMParser();


        const documentFromServer =
            parser.parseFromString(
                html,
                "text/html"
            );


        const newTableBody =
            documentFromServer.getElementById(
                "candidatesTableBody"
            );


        const currentTableBody =
            document.getElementById(
                "candidatesTableBody"
            );


        if(
            !newTableBody ||
            !currentTableBody
        ){

            throw new Error(
                "Candidate table body was not found."
            );

        }


        /*
         * Replace ONLY the tbody.
         *
         * The rest of the page remains untouched.
         */

        currentTableBody.innerHTML =
            newTableBody.innerHTML;


        /* Re-bind the clear-search control because the tbody was replaced. */
        initializeCandidateEmptyState();


        /*
         * Refresh candidate statistics if the page
         * contains these counters.
         */

        const statisticIds = [
            "totalCandidates",
            "firstYearCandidates",
            "secondYearCandidates"
        ];


        statisticIds.forEach(
            id => {

                const currentElement =
                    document.getElementById(
                        id
                    );


                const newElement =
                    documentFromServer.getElementById(
                        id
                    );


                if(
                    currentElement &&
                    newElement
                ){

                    currentElement.textContent =
                        newElement.textContent;

                }

            }
        );


        /*
         * Restore search text.
         */

        if(searchInput){

            searchInput.value =
                currentSearch;

        }


        /*
         * Restore entries value.
         */

        if(
            entriesSelect &&
            currentEntries !== ""
        ){

            const matchingOption =
                Array.from(
                    entriesSelect.options
                ).some(
                    option =>
                        option.value ===
                        currentEntries
                );


            if(matchingOption){

                entriesSelect.value =
                    currentEntries;

            }

        }


        /*
         * Restore active filter button.
         */

        const allFilterButton =
            document.getElementById(
                "filterAll"
            );


        if(
            currentFilter ===
            "first"
        ){

            if(firstYearButton){

                setActiveFilter(
                    firstYearButton
                );

            }

        }
        else if(
            currentFilter ===
            "second"
        ){

            if(secondYearButton){

                setActiveFilter(
                    secondYearButton
                );

            }

        }
        else if(
            allFilterButton
        ){

            setActiveFilter(
                allFilterButton
            );

        }


        /*
         * Re-bind only row-level actions.
         *
         * The old tbody was replaced, so its old listeners
         * no longer exist.
         *
         * Do NOT call initializeCandidateFilters(),
         * initializeCandidateSearch(), or
         * initializeEntriesFilter() here because those
         * controls still exist and already have listeners.
         */

        initializeViewCandidate();

        initializeEditCandidate();

        initializeDeleteCandidate();


        /*
         * Apply the current election modification lock
         * to the newly created Edit/Delete buttons.
         */

        applyCandidateModificationLock();


        /*
         * Finally apply the current search/filter/entries
         * state to the refreshed rows.
         */

        refreshCandidateTableView();


        return true;

    }

    catch(error){

        console.error(
            "VOTIFY Candidate Table Refresh Error:",
            error
        );


        return false;

    }

}


/* ==========================================================
   DELETE TABLE HELPERS
========================================================== */

function updateCandidateStatistics(deletedYear){

    const rows =
        document.querySelectorAll(
            "#candidatesTableBody tr[data-id]"
        );


    const totalElement =
        document.getElementById("totalCandidates");

    const firstYearElement =
        document.getElementById("firstYearCandidates");

    const secondYearElement =
        document.getElementById("secondYearCandidates");


    if(totalElement){

        totalElement.textContent = rows.length;

    }


    if(firstYearElement){

        firstYearElement.textContent =
            Array.from(rows).filter(
                row =>
                    row.dataset.year === "1st Year" ||
                    row.dataset.year === "I Year"
            ).length;

    }


    if(secondYearElement){

        secondYearElement.textContent =
            Array.from(rows).filter(
                row =>
                    row.dataset.year === "2nd Year" ||
                    row.dataset.year === "II Year"
            ).length;

    }

}


function updateCandidateEmptyState(
    totalRows,
    matchingRows,
    activeFilter,
    keyword
){

    const emptyState =
        document.getElementById(
            "candidatesEmptyState"
        );


    if(!emptyState){

        return;

    }


    const icon =
        document.getElementById(
            "candidateEmptyIcon"
        );


    const title =
        document.getElementById(
            "candidateEmptyTitle"
        );


    const message =
        document.getElementById(
            "candidateEmptyMessage"
        );


    const label =
        document.getElementById(
            "candidateEmptyLabel"
        );


    const clearButton =
        document.getElementById(
            "clearCandidateSearch"
        );


    /*
     * There are no candidates in the database.
     */

    if(totalRows === 0){

        emptyState.classList.remove(
            "hidden"
        );


        if(icon){

            icon.className =
                "ri-user-add-line text-5xl text-slate-400";

        }


        if(label){

            label.innerHTML =
                '<i class="ri-team-line" aria-hidden="true"></i>' +
                ' Candidate Directory';

        }


        if(title){

            title.textContent =
                "No Candidates Found";

        }


        if(message){

            message.textContent =
                "No candidates have been added yet. Add a candidate to start building the election directory.";

        }


        if(clearButton){

            clearButton.classList.add(
                "hidden"
            );

        }


        return;

    }


    /*
     * Candidates exist, but the current search/filter
     * combination produced no matching result.
     */

    if(matchingRows === 0){

        emptyState.classList.remove(
            "hidden"
        );


        if(icon){

            icon.className =
                keyword
                    ? "ri-search-eye-line text-5xl text-slate-400"
                    : "ri-filter-off-line text-5xl text-slate-400";

        }


        if(label){

            label.innerHTML =
                keyword
                    ? '<i class="ri-search-line" aria-hidden="true"></i> Search Result'
                    : '<i class="ri-filter-3-line" aria-hidden="true"></i> Filter Result';

        }


        if(title){

            if(keyword){

                title.textContent =
                    "No Matching Candidates";

            }
            else if(activeFilter === "first"){

                title.textContent =
                    "No 1st Year Candidates";

            }
            else if(activeFilter === "second"){

                title.textContent =
                    "No 2nd Year Candidates";

            }
            else{

                title.textContent =
                    "No Candidates Found";

            }

        }


        if(message){

            if(keyword && activeFilter === "first"){

                message.textContent =
                    "No 1st Year candidates match your search. Try a different name, admission number, department, year, or manifesto.";

            }
            else if(keyword && activeFilter === "second"){

                message.textContent =
                    "No 2nd Year candidates match your search. Try a different name, admission number, department, year, or manifesto.";

            }
            else if(keyword){

                message.textContent =
                    "No candidates match your search. Try a different name, admission number, department, year, or manifesto.";

            }
            else if(activeFilter === "first"){

                message.textContent =
                    "There are currently no candidates listed under the 1st Year filter.";

            }
            else if(activeFilter === "second"){

                message.textContent =
                    "There are currently no candidates listed under the 2nd Year filter.";

            }
            else{

                message.textContent =
                    "No candidates match the current view.";

            }

        }


        if(clearButton){

            if(keyword){

                clearButton.classList.remove(
                    "hidden"
                );

            }
            else{

                clearButton.classList.add(
                    "hidden"
                );

            }

        }


        return;

    }


    /*
     * At least one candidate matches.
     * Hide the empty state.
     */

    emptyState.classList.add(
        "hidden"
    );


    if(clearButton){

        clearButton.classList.add(
            "hidden"
        );

    }

}


function showEmptyCandidateState(){

    const tbody =
        document.getElementById(
            "candidatesTableBody"
        );


    if(!tbody){

        return;

    }


    const rows =
        tbody.querySelectorAll(
            "tr[data-id]"
        );


    updateCandidateEmptyState(
        rows.length,
        0,
        "all",
        ""
    );

}



function refreshCandidateTableView(){

    const tbody =
        document.getElementById(
            "candidatesTableBody"
        );


    if(!tbody){

        return;

    }


    const rows =
        Array.from(
            tbody.querySelectorAll(
                "tr[data-id]"
            )
        );


    const totalRows =
        rows.length;


    const searchInput =
        document.getElementById(
            "candidateSearch"
        );


    const keyword =
        searchInput
            ? searchInput.value
                .trim()
                .toLowerCase()
            : "";


    let activeFilter =
        "all";


    const firstYearButton =
        document.getElementById(
            "filterFirstYear"
        );


    const secondYearButton =
        document.getElementById(
            "filterSecondYear"
        );


    if(
        firstYearButton &&
        firstYearButton.classList.contains(
            "btn-primary"
        )
    ){

        activeFilter =
            "first";

    }
    else if(
        secondYearButton &&
        secondYearButton.classList.contains(
            "btn-primary"
        )
    ){

        activeFilter =
            "second";

    }


    const select =
        document.getElementById(
            "entriesSelect"
        );


    const limit =
        select
            ? parseInt(
                select.value,
                10
            )
            : totalRows;


    const safeLimit =
        Number.isFinite(limit) &&
        limit > 0
            ? limit
            : totalRows;


    let matchingRows =
        0;


    let visibleIndex =
        0;


    rows.forEach(
        row => {

            const searchText =
                (
                    row.dataset.search ||
                    ""
                )
                    .toLowerCase();


            const year =
                (
                    row.dataset.year ||
                    ""
                ).trim();


            const matchesSearch =
                searchText.includes(
                    keyword
                );


            const matchesFilter =
                activeFilter === "all"
                    ? true
                    : activeFilter === "first"
                        ? (
                            year === "1st Year" ||
                            year === "I Year"
                        )
                        : (
                            year === "2nd Year" ||
                            year === "II Year"
                        );


            if(
                matchesSearch &&
                matchesFilter
            ){

                matchingRows++;


                if(
                    visibleIndex <
                    safeLimit
                ){

                    row.style.display =
                        "";

                    visibleIndex++;

                }
                else{

                    row.style.display =
                        "none";

                }

            }
            else{

                row.style.display =
                    "none";

            }

        }
    );


    updateCandidateEmptyState(
        totalRows,
        matchingRows,
        activeFilter,
        keyword
    );

}


function removeCandidateFromTable(candidateId){

    const tbody =
        document.getElementById("candidatesTableBody");


    if(!tbody){

        return;

    }


    const rows =
        tbody.querySelectorAll("tr[data-id]");


    let removed = false;


    rows.forEach(row => {

        if(
            String(row.dataset.id) ===
            String(candidateId)
        ){

            row.remove();
            removed = true;

        }

    });


    if(!removed){

        console.warn(
            "VOTIFY: Deleted candidate row was not found in the table.",
            candidateId
        );

        return;

    }


    updateCandidateStatistics();
    refreshCandidateTableView();
    applyCandidateModificationLock();

}


/* ==========================================================
   DELETE CANDIDATE
========================================================== */

async function deleteCandidate(
    id
){

    const statusChecked =
        await refreshCandidateElectionStatus();


    if(
        !statusChecked
    ){

        return;

    }


    if(
        isCandidateModificationLocked()
    ){

        showToast(
            "error",
            "Action Disabled",
            "Deleting candidates is disabled while the election is running."
        );


        applyCandidateModificationLock();

        return;

    }


    openConfirmationModal({

        type:
            "reject",

        icon:
            "ri-delete-bin-6-line",

        title:
            "Delete Candidate",

        message:
            "Are you sure you want to delete this candidate? This action cannot be undone.",

        onConfirm:
            async () => {

                const latestStatus =
                    await refreshCandidateElectionStatus();


                if(
                    !latestStatus
                ){

                    return false;

                }


                if(
                    isCandidateModificationLocked()
                ){

                    showToast(
                        "error",
                        "Action Disabled",
                        "Deleting candidates is disabled while the election is running."
                    );


                    applyCandidateModificationLock();

                    return false;

                }


                try{

                    const response =
                        await fetch(
                            "../../backend/admin/delete-candidate.php",
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
                                    "candidateId=" +
                                    encodeURIComponent(
                                        id
                                    )
                            }
                        );


                    const result =
                        await response.json();


                    if(
                        result.success
                    ){

                        showToast(
                            "success",
                            "Deleted",
                            result.message
                        );


                        removeCandidateFromTable(id);


                        return true;

                    }


                    if(
                        response.status ===
                        403
                    ){

                        window.VOTIFY_CANDIDATES.electionStatus =
                            "Started";

                        applyCandidateModificationLock();

                    }


                    showToast(
                        "error",

                        response.status === 403
                            ? "Action Disabled"
                            : "Delete Failed",

                        result.message ||
                        "Unable to delete candidate."
                    );


                    return false;

                }

                catch(error){

                    console.error(
                        "VOTIFY Delete Candidate Error:",
                        error
                    );


                    showToast(
                        "error",
                        "Server Error",
                        "Unable to delete candidate."
                    );


                    return false;

                }

            }

    });

}


/* ==========================================================
   READY MESSAGE
========================================================== */

console.log(
    "%cVOTIFY Candidates Ready",
    "color:#22C55E;font-size:14px;font-weight:bold;"
);