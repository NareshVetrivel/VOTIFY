/* ==========================================================
   VOTIFY
   Candidate Management
========================================================== */

"use strict";

let editMode = false;

let editingCandidateId = null;

/* ==========================================================
   READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeCandidates();

});

/* ==========================================================
   INITIALIZE
========================================================== */

function initializeCandidates(){

initializeCandidateModal();

initializeCandidateViewModal();

initializePhotoPreview();

initializeManifestoCounter();

initializeStudentSearch();

initializeCandidateForm();

initializeCandidateFilters();

initializeCandidateSearch();

initializeEntriesFilter();

initializeViewCandidate();

initializeEditCandidate();

initializeDeleteCandidate();

initializeExportExcel();
}

/* ==========================================================
   MODAL
========================================================== */

function initializeCandidateModal(){

    document

    .getElementById("addCandidate")

    ?.addEventListener("click",()=>{

        resetCandidateForm();

        openCandidateModal();

    });

    document

    .getElementById("closeCandidateModal")

    ?.addEventListener(

        "click",

        closeCandidateModal

    );

    document

    .getElementById("cancelCandidate")

    ?.addEventListener(

        "click",

        closeCandidateModal

    );

    document

    .getElementById("candidateModal")

    ?.addEventListener("click",e=>{

        if(e.target.id==="candidateModal"){

            closeCandidateModal();

        }

    });

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

    modal.classList.remove("hidden");

    modal.classList.add("flex");

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

    modal.classList.remove("flex");

    modal.classList.add("hidden");

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

        document.getElementById(

            "candidateManifesto"

        ).blur();

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

    ).textContent = "0 / 255";

const preview =

document.getElementById(

    "photoPreview"

);

preview.src = "";

preview.classList.add("hidden");

editMode = false;

editingCandidateId = null;

document.getElementById("admissionNo").readOnly = false;

document.getElementById("admissionNo").classList.remove("cursor-not-allowed");

document.getElementById("saveCandidate").innerHTML =
'<i class="ri-save-line mr-2"></i>Save Candidate';

document.getElementById("candidateManifesto").readOnly = false;

document.getElementById("candidatePhoto").disabled = false;

document.getElementById("searchStudent").disabled = false;

document.getElementById("saveCandidate").classList.remove("hidden");

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

    if(!input || !preview){

        return;

    }

    input.addEventListener(

        "change",

        ()=>{

            const file =

            input.files[0];

            if(!file){

                return;

            }

preview.src = URL.createObjectURL(file);

preview.classList.remove("hidden");

preview.onload = () => {

    URL.revokeObjectURL(preview.src);

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

    if(!textarea || !counter){

        return;

    }

    textarea.addEventListener(

        "input",

        ()=>{

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
document.getElementById("searchStudent");

const admissionInput =
document.getElementById("admissionNo");

if(!searchButton || !admissionInput){

    return;

}

admissionInput.addEventListener("input",()=>{

    admissionInput.value =
    admissionInput.value.toUpperCase();

});

    if(!searchButton || !admissionInput){

        return;

    }

    searchButton.addEventListener(

        "click",

        searchStudent

    );

    admissionInput.addEventListener(

        "keydown",

        e=>{

            if(e.key==="Enter"){

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

    .getElementById("admissionNo")

    .value

    .trim();

    if(admission===""){

        showToast(

            "warning",

            "Admission Number",

            "Please enter admission number."

        );

        return;

    }

    const searchButton =

    document.getElementById(

        "searchStudent"

    );

    searchButton.disabled = true;

    searchButton.innerHTML =

    '<i class="ri-loader-4-line animate-spin"></i>';

    try{

        const response =

        await fetch(

        "../../backend/admin/check-student.php?admission_no="

        +

        encodeURIComponent(admission)

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

        console.error(error);

    }

    finally{

        searchButton.disabled = false;

        searchButton.innerHTML =

        '<i class="ri-search-line text-xl"></i>';

    }

}

/* ==========================================================
   FILL STUDENT DETAILS
========================================================== */

function fillStudentDetails(student){

    document.getElementById(

        "studentId"

    ).value = student.id;

    document.getElementById(

        "candidateName"

    ).value = student.full_name;

    document.getElementById(

        "candidateDepartment"

    ).value = student.department;

    document.getElementById(

        "candidateYear"

    ).value = student.year;

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

    ).value="";

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

    "candidatePhoto"

).value = "";

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

function validateCandidateForm(e){

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

    if(admission===""){

        showToast(

            "warning",

            "Admission Number",

            "Please enter Admission Number."

        );

        document.getElementById(

            "admissionNo"

        ).focus();

        return;

    }

    if(studentId===""){

        showToast(

            "warning",

            "Search Student",

            "Search and verify the student first."

        );

        document.getElementById(

            "admissionNo"

        ).focus();

        return;

    }

    if(!editMode && photo===0){

        showToast(

            "warning",

            "Candidate Photo",

            "Please upload candidate photo."

        );

        return;

    }

    if(manifesto===""){

        showToast(

            "warning",

            "Manifesto",

            "Please enter candidate manifesto."

        );

        document.getElementById(

            "candidateManifesto"

        ).focus();

        return;

    }

    saveCandidate();

}

/* ==========================================================
   SAVE CANDIDATE
========================================================== */

/* ==========================================================
   SAVE CANDIDATE
========================================================== */

async function saveCandidate(){

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

    saveButton.disabled = true;

    saveButton.innerHTML =

    '<i class="ri-loader-4-line animate-spin mr-2"></i>Saving...';

    try{

        const formData =

        new FormData(form);

        if(editMode){

    formData.append(
        "candidateId",
        editingCandidateId
    );

}

const url = editMode

? "../../backend/admin/update-candidate.php"

: "../../backend/admin/add-candidate.php";

const response = await fetch(
    url,
    {
        method:"POST",
        body:formData
    }
);

const text = await response.text();

const result = JSON.parse(text);

        if(result.success){

            showToast(

                "success",

                "Success",

                result.message

            );

            closeCandidateModal();

            setTimeout(()=>{

                location.reload();

            },800);

        }

        else{

            showToast(

                "error",

                "Failed",

                result.message

            );

        }

    }

    catch(error){

        console.error(error);

        showToast(

            "error",

            "Server Error",

            "Something went wrong."

        );

    }

    finally{

        saveButton.disabled = false;

        saveButton.innerHTML =

        originalButton;

    }

}

/* ==========================================================
   FILTER CANDIDATES
========================================================== */

function initializeCandidateFilters(){

const rows =
document.querySelectorAll(
"#candidatesTableBody tr"
);

document.getElementById("filterAll")
?.addEventListener("click",()=>{

setActiveFilter(
document.getElementById("filterAll")
);

rows.forEach(row=>{

row.style.display="";

});

});

document.getElementById("filterFirstYear")
?.addEventListener("click",()=>{

setActiveFilter(
document.getElementById("filterFirstYear")
);

rows.forEach(row=>{

row.style.display=

row.dataset.year==="1st Year" ||
row.dataset.year==="I Year"

? ""

: "none";

});

});

document.getElementById("filterSecondYear")
?.addEventListener("click",()=>{

setActiveFilter(
document.getElementById("filterSecondYear")
);

rows.forEach(row=>{

row.style.display=

row.dataset.year==="2nd Year" ||
row.dataset.year==="II Year"

? ""

: "none";

});

});

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

input.addEventListener("input",()=>{

const keyword =
input.value.toLowerCase();

document
.querySelectorAll(
"#candidatesTableBody tr"
)
.forEach(row=>{

const search =
row.dataset.search || "";

row.style.display=

search.includes(keyword)

? ""

: "none";

});

});

}

/* ==========================================================
   ACTIVE FILTER BUTTON
========================================================== */

function setActiveFilter(button){

document
.querySelectorAll(".filterButton")
.forEach(btn=>{

btn.classList.remove("btn-primary");

btn.classList.add("btn-outline");

});

button.classList.remove("btn-outline");

button.classList.add("btn-primary");

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

select.addEventListener("change",()=>{

const limit =
parseInt(select.value);

const rows =
document.querySelectorAll(
"#candidatesTableBody tr"
);

rows.forEach((row,index)=>{

row.style.display=

index < limit

? ""

: "none";

});

});

select.dispatchEvent(

new Event("change")

);

}

function initializeViewCandidate(){

    document
    .querySelectorAll(".viewCandidate")
    .forEach(button=>{

        button.addEventListener(
            "click",
            ()=>{

                viewCandidate(
                    button.dataset.id
                );

            }

        );

    });

}

function initializeEditCandidate(){

document
.querySelectorAll(".editCandidate")
.forEach(button=>{

button.addEventListener(
"click",
()=>{

editCandidate(
button.dataset.id
);

});

});

}

function initializeDeleteCandidate(){

document
.querySelectorAll(".deleteCandidate")
.forEach(button=>{

button.addEventListener(
"click",
()=>{

deleteCandidate(
button.dataset.id
);

});

});

}

function initializeExportExcel(){

document
.getElementById(
"exportCandidates"
)
?.addEventListener(
"click",
()=>{

const search =

document.getElementById(
"candidateSearch"
).value.trim();

let filter = "all";

if(

document.getElementById(
"filterFirstYear"
).classList.contains("btn-primary")

){

filter = "first";

}

else if(

document.getElementById(
"filterSecondYear"
).classList.contains("btn-primary")

){

filter = "second";

}

window.location.href =

"../../backend/admin/export-candidates.php"

+

"?search="

+

encodeURIComponent(search)

+

"&filter="

+

encodeURIComponent(filter);

});

}

function initializeCandidateViewModal(){

    document
    .getElementById("closeCandidateViewModal")
    ?.addEventListener(
        "click",
        closeCandidateViewModal
    );

    document
    .getElementById("closeCandidateView")
    ?.addEventListener(
        "click",
        closeCandidateViewModal
    );

    document
    .getElementById("candidateViewModal")
    ?.addEventListener("click",e=>{

        if(e.target.id==="candidateViewModal"){

            closeCandidateViewModal();

        }

    });

}

function openCandidateViewModal(){

    const modal=document.getElementById(
        "candidateViewModal"
    );

    if(!modal){

        return;

    }

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

function closeCandidateViewModal(){

    const modal=document.getElementById(
        "candidateViewModal"
    );

    if(!modal){

        return;

    }

    modal.classList.remove("flex");

    modal.classList.add("hidden");

}

/* ==========================================================
   VIEW CANDIDATE
========================================================== */

async function viewCandidate(id){

    try{

        const response = await fetch(
            "../../backend/admin/get-candidate.php?id=" +
            encodeURIComponent(id)
        );

        const result = await response.json();

        if(!result.success){

            showToast(
                "error",
                "Failed",
                result.message
            );

            return;
        }

        const candidate = result.candidate;

        document.getElementById("viewCandidatePhoto").src =
        "../../backend/candidate-photo.php?id=" +
        encodeURIComponent(candidate.id);

        document.getElementById("viewCandidateName").textContent =
        candidate.full_name;

        document.getElementById("viewCandidateAdmission").textContent =
        candidate.admission_no;

        document.getElementById("viewCandidateDepartment").textContent =
        candidate.department;

        document.getElementById("viewCandidateYear").textContent =
        candidate.year;

        document.getElementById("viewCandidateManifesto").textContent =
        candidate.manifesto;

        openCandidateViewModal();

    }

    catch(error){

        console.error(error);

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

async function editCandidate(id){

    try{

        const response = await fetch(

            "../../backend/admin/get-candidate.php?id=" +

            encodeURIComponent(id)

        );

        const result = await response.json();

        if(!result.success){

            showToast(
                "error",
                "Failed",
                result.message
            );

            return;

        }

        const candidate = result.candidate;

        resetCandidateForm();

        editMode = true;

        editingCandidateId = candidate.id;

        document.getElementById("candidateId").value =
        candidate.id;

        document.getElementById("studentId").value =
        candidate.student_id;

        document.getElementById("admissionNo").value =
        candidate.admission_no;

        document.getElementById("candidateName").value =
        candidate.full_name;

        document.getElementById("candidateDepartment").value =
        candidate.department;

        document.getElementById("candidateYear").value =
        candidate.year;

        document.getElementById("candidateManifesto").value =
        candidate.manifesto;

        document.getElementById("manifestoCount").textContent =
        candidate.manifesto.length + " / 255";

        const preview =
        document.getElementById("photoPreview");

        preview.src =
        "../../backend/candidate-photo.php?id=" +
        encodeURIComponent(candidate.id);

        preview.classList.remove("hidden");

        /* ---------- EDIT MODE ---------- */

        document.getElementById("admissionNo").readOnly = true;

        document.getElementById("admissionNo").classList.add(
            "cursor-not-allowed"
        );

        document.getElementById("candidateManifesto").readOnly = false;

        document.getElementById("candidatePhoto").disabled = false;

        document.getElementById("searchStudent").disabled = true;

        document.getElementById("saveCandidate").classList.remove("hidden");

        document.getElementById("saveCandidate").innerHTML =
        '<i class="ri-save-line mr-2"></i>Update Candidate';

        openCandidateModal();

    }

    catch(error){

        console.error(error);

        showToast(
            "error",
            "Server Error",
            "Unable to load candidate."
        );

    }

}

/* ==========================================================
   DELETE CANDIDATE
========================================================== */

async function deleteCandidate(id){

    openConfirmationModal({

        type: "reject",

        icon: "ri-delete-bin-6-line",

        title: "Delete Candidate",

        message: "Are you sure you want to delete this candidate? This action cannot be undone.",

        onConfirm: async ()=>{

            try{

                const response = await fetch(

                    "../../backend/admin/delete-candidate.php",

                    {

                        method:"POST",

                        headers:{

                            "Content-Type":"application/x-www-form-urlencoded"

                        },

                        body:"candidateId="+encodeURIComponent(id)

                    }

                );

                const result = await response.json();

                if(result.success){

                    showToast(

                        "success",

                        "Deleted",

                        result.message

                    );

                    setTimeout(()=>{

                        location.reload();

                    },800);

                }

                else{

                    showToast(

                        "error",

                        "Delete Failed",

                        result.message

                    );

                }

            }

            catch(error){

                console.error(error);

                showToast(

                    "error",

                    "Server Error",

                    "Unable to delete candidate."

                );

            }

        }

    });

}

/* ==========================================================
   READY
========================================================== */

console.log(

"%cVOTIFY Candidates Ready",

"color:#22C55E;font-size:14px;font-weight:bold;"

);