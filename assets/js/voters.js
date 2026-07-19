/* ==========================================================
   VOTIFY
   Voters Management
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
   READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeVoters();

});

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
}

/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch(){

    const searchInput =
    document.getElementById("voterSearch");

    if(!searchInput){

        return;

    }

    searchInput.addEventListener("keyup",()=>{

        searchKeyword =
        searchInput.value
        .toLowerCase()
        .trim();

        updateTable();

    });

}

/* ==========================================================
   FILTER BUTTONS
========================================================== */

function initializeFilters(){

    const all =
    document.getElementById("filterAll");

    const voted =
    document.getElementById("filterVoted");

    const unvoted =
    document.getElementById("filterUnvoted");

    if(!all){

        return;

    }

    all.addEventListener("click",()=>{

        currentFilter="all";

        updateFilterButtons(all);

        updateTable();

    });

    voted.addEventListener("click",()=>{

        currentFilter="voted";

        updateFilterButtons(voted);

        updateTable();

    });

    unvoted.addEventListener("click",()=>{

        currentFilter="unvoted";

        updateFilterButtons(unvoted);

        updateTable();

    });

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

    select.addEventListener("change",()=>{

        rowsPerPage =

        parseInt(select.value);

        currentPage = 1;

        updateTable();

    });

}

/* ==========================================================
   INITIALIZE PAGINATION
========================================================== */

function initializePagination(){

    const prevButton =
    document.getElementById("prevPage");

    const nextButton =
    document.getElementById("nextPage");

    if(prevButton){

        prevButton.addEventListener("click",()=>{

            if(currentPage>1){

                currentPage--;

                updateTable();

            }

        });

    }

    if(nextButton){

        nextButton.addEventListener("click",()=>{

            const totalRows =
            getFilteredRows().length;

            const totalPages =
            Math.ceil(totalRows/rowsPerPage);

            if(currentPage<totalPages){

                currentPage++;

                updateTable();

            }

        });

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

    return rows.filter(row=>{

        const text =
        row.innerText.toLowerCase();

        const status =
        row.cells[4]
        .innerText
        .trim()
        .toLowerCase();

        const searchMatch =
        text.includes(searchKeyword);

        if(currentFilter==="voted"){

            return searchMatch &&
            status==="voted";

        }

        if(currentFilter==="unvoted"){

            return searchMatch &&
            status==="unvoted";

        }

        return searchMatch;

    });

}

/* ==========================================================
   UPDATE TABLE
========================================================== */

function updateTable(){

    const rows = getFilteredRows();

    const totalRows = rows.length;

    const totalPages =

    Math.max(

        1,

        Math.ceil(totalRows / rowsPerPage)

    );

    if(currentPage > totalPages){

        currentPage = totalPages;

    }

    /* ==========================================
       Hide All Rows
    ========================================== */

    document

    .querySelectorAll("#votersTableBody tr[data-id]")

    .forEach(row=>{

        row.style.display="none";

    });

    /* ==========================================
       Show Current Page Rows
    ========================================== */

    const start =

    (currentPage-1) * rowsPerPage;

    const end =

    start + rowsPerPage;

    rows

    .slice(start,end)

    .forEach(row=>{

        row.style.display="";

    });

    /* ==========================================
       Showing Text
    ========================================== */

    const showingStart =

    document.getElementById("showingStart");

    const showingEnd =

    document.getElementById("showingEnd");

    const totalRecords =

    document.getElementById("totalRecords");

    if(showingStart){

        showingStart.textContent =

        totalRows==0

        ? 0

        : start+1;

    }

    if(showingEnd){

        showingEnd.textContent =

        Math.min(end,totalRows);

    }

    if(totalRecords){

        totalRecords.textContent =

        totalRows;

    }

if(typeof renderPagination === "function"){

    renderPagination(totalPages);

}

}

/* ==========================================================
   RENDER PAGINATION
========================================================== */

function renderPagination(totalPages){

    const container =
    document.getElementById("paginationNumbers");

    if(!container){

        return;

    }

    container.innerHTML = "";

    for(let i = 1; i <= totalPages; i++){

        const button =
        document.createElement("button");

        button.textContent = i;

        button.className =
        i === currentPage
        ? "btn-primary"
        : "btn-outline";

        button.addEventListener("click",()=>{

            currentPage = i;

            updateTable();

        });

        container.appendChild(button);

    }

}

/* ==========================================================
   UPDATE TABLE
========================================================== */

function updateTableRow(){

    const id = document.getElementById("voterId").value;

    const row = document.querySelector(
        'tr[data-id="'+id+'"]'
    );

    if(!row) return;

    const nameElement =
    row.cells[0].querySelector(".font-semibold");

    if(nameElement){

        nameElement.textContent =
        document.getElementById("fullName").value;

    }

    if(row.cells[3]){

        row.cells[3].textContent =
        document.getElementById("year").value;

    }

}

/* ==========================================================
   ACTIVE FILTER BUTTON
========================================================== */

function updateFilterButtons(active){

    document
    .querySelectorAll(".filterButton")
    .forEach(button=>{

        button.classList.remove(

            "btn-primary"

        );

        button.classList.add(

            "btn-outline"

        );

    });

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

async function loadVoter(id){

    try{

        const response =
        await fetch(

        "../../backend/admin/get-voter.php?id="+id

        );

        const result =
        await response.json();

        if(!result.success){

            alert(result.message);

            return;

        }

        const student =
        result.student;

        document.getElementById("voterId").value =
        student.id;

        document.getElementById("fullName").value =
        student.full_name;

        document.getElementById("admissionNo").value =
        student.admission_no;

        document.getElementById("collegeEmail").value =
        student.college_email;

        document.getElementById("phone").value =
        student.phone;

        document.getElementById("gender").value =
        student.gender;

        document.getElementById("department").value =
        student.department;

        document.getElementById("year").value =
        student.year;

        document.getElementById("voteStatus").value =
        student.vote_status;

        openVoterModal();

    }

    catch(error){

        console.error(error);

    }

}

/* ==========================================================
   MODAL
========================================================== */

function openVoterModal(){

    const modal =
    document.getElementById("voterModal");

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

function closeVoterModal(){

    const modal =
    document.getElementById("voterModal");

    modal.classList.remove("flex");

    modal.classList.add("hidden");

}

document

.getElementById("closeVoterModal")

?.addEventListener(

"click",

closeVoterModal

);

document

.getElementById("cancelVoter")

?.addEventListener(

"click",

closeVoterModal

);

document

.getElementById("voterModal")

?.addEventListener("click",e=>{

if(e.target.id==="voterModal"){

closeVoterModal();

}

});

/* ==========================================================
   EXPORT EXCEL
========================================================== */

function initializeExport(){

    const exportButton =

    document.getElementById(
        "exportExcel"
    );

    if(!exportButton){

        return;

    }

    exportButton.addEventListener("click",()=>{

        exportExcel();

    });

}

/* ==========================================================
   EXPORT FUNCTION
========================================================== */

function exportExcel(){

    const params =

    new URLSearchParams({

        search : searchKeyword,

        filter : currentFilter

    });

    window.location.href =

    "../../backend/admin/export-voters.php?"

    + params.toString();

}

/* ==========================================================
   UPDATE VOTER
========================================================== */

function initializeVoterForm(){

const form=document.getElementById("voterForm");

if(!form)return;

form.addEventListener(

"submit",

updateVoter

);

}

async function updateVoter(e){

e.preventDefault();

const form=document.getElementById("voterForm");

const saveButton =

form.querySelector(

'button[type="submit"]'

);

saveButton.disabled = true;

saveButton.innerHTML = "Saving...";

const formData=new FormData();

formData.append(

"id",

document.getElementById("voterId").value

);

formData.append(

"full_name",

document.getElementById("fullName").value

);

formData.append(

"phone",

document.getElementById("phone").value

);

formData.append(

"year",

document.getElementById("year").value

);


try{

const response=

await fetch(

"../../backend/admin/update-voter.php",

{

method:"POST",

body:formData

}

);

const result = await response.json();

if(result.success){

    closeVoterModal();

    updateTableRow();

    showToast(
        "success",
        "Updated",
        result.message
    );

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

}

finally{

saveButton.disabled = false;

saveButton.innerHTML =

'<i class="ri-save-line mr-2"></i>Save Changes';

}

}

/* ==========================================================
DELETE BUTTONS
========================================================== */

function initializeDeleteButtons(){

    document
    .querySelectorAll(".deleteVoter")
    .forEach(button=>{

        button.addEventListener("click",()=>{

            const id = button.dataset.id;

            const row = button.closest("tr");

            openConfirmationModal({

                title:"Delete Voter",

                message:"Delete this voter permanently?",

                icon:"ri-delete-bin-line",

                type:"reject",

                onConfirm(){

                    deleteVoter(id,row);

                }

            });

        });

    });

}

async function deleteVoter(id,row){

try{

const response = await fetch(

"../../backend/admin/delete-voter.php",

{

method:"POST",

headers:{

"Content-Type":"application/x-www-form-urlencoded"

},

body:"id="+encodeURIComponent(id)

}

);

const result = await response.json();

if(result.success){

removeVoterRow(row);

updateCardsAfterDelete(row);

showToast(

"success",

"Deleted",

result.message

);

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

"Error",

"Something went wrong."

);

}

}

/* ==========================================================
REMOVE ROW
========================================================== */

function removeVoterRow(row){

row.style.transition=".35s";

row.style.opacity="0";

row.style.transform=

"translateX(40px) scale(.96)";

row.style.filter="blur(4px)";

setTimeout(()=>{

row.remove();

checkEmptyVoters();

updateTable();

},350);

}

/* ==========================================================
UPDATE CARDS
========================================================== */

function updateCardsAfterDelete(row){

const approved =
document.getElementById("approvedStudents");

const voted =
document.getElementById("votedStudents");

const unvoted =
document.getElementById("unvotedStudents");

if(approved){

approved.textContent =

Math.max(

0,

parseInt(approved.textContent)-1

);

}

const status =

row.dataset.status;

if(status==="Voted"){

if(voted){

voted.textContent=

Math.max(

0,

parseInt(voted.textContent)-1

);

}

}

else{

if(unvoted){

unvoted.textContent=

Math.max(

0,

parseInt(unvoted.textContent)-1

);

}

}

}

/* ==========================================================
EMPTY TABLE
========================================================== */

function checkEmptyVoters(){

const tbody=

document.getElementById(

"votersTableBody"

);

if(

tbody.querySelectorAll("tr").length

===0

){

tbody.innerHTML=`

<tr>

<td colspan="7"

class="py-16 text-center text-slate-400">

<div class="flex justify-center mb-6">

<i class="ri-user-search-line text-7xl text-slate-500"></i>

</div>

<h3 class="text-2xl font-bold text-white">

No Approved Voters

</h3>

<p class="mt-3 text-slate-400">

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
    .querySelectorAll(".viewVoter")
    .forEach(button=>{

        button.addEventListener("click",()=>{

            loadStudentDetails(button.dataset.id);

        });

    });

}

/* ==========================================================
   LOAD STUDENT DETAILS
========================================================== */

async function loadStudentDetails(id){

    try{

        const response = await fetch(

            "../../backend/admin/get-voter.php?id="+id

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

        const student = result.student;

        document.getElementById("studentName").textContent =
        student.full_name;

        document.getElementById("studentAdmission").textContent =
        student.admission_no;

        document.getElementById("studentEmail").textContent =
        student.college_email;

        document.getElementById("studentDepartment").textContent =
        student.department;

        document.getElementById("studentYear").textContent =
        student.year;

        document.getElementById("studentStatus").textContent =
        student.vote_status;

        openStudentModal();

    }

    catch(error){

        console.error(error);

    }

}

/* ==========================================================
   STUDENT MODAL
========================================================== */

function openStudentModal(){

    const modal = document.getElementById("studentModal");

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

function closeStudentModal(){

    const modal = document.getElementById("studentModal");

    modal.classList.remove("flex");

    modal.classList.add("hidden");

}

document
.getElementById("closeStudentModal")
?.addEventListener(
"click",
closeStudentModal
);

document
.getElementById("closeStudentButton")
?.addEventListener(
"click",
closeStudentModal
);

document
.getElementById("studentModal")
?.addEventListener("click",e=>{

    if(e.target.id==="studentModal"){

        closeStudentModal();

    }

});

/* ==========================================================
   EDIT BUTTONS
========================================================== */

function initializeEditButtons(){

    document
    .querySelectorAll(".editVoter")
    .forEach(button=>{

        button.addEventListener("click",()=>{

            loadVoter(button.dataset.id);

        });

    });

}

/* ==========================================================
READY
========================================================== */

console.log(

"%cVOTIFY Voters Ready",

"color:#3B82F6;font-size:14px;font-weight:bold;"

);