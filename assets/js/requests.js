/* ==========================================================
   VOTIFY
   Requests Page JavaScript
========================================================== */

"use strict";

let currentStudentId = null;

let currentAction = null;

document.addEventListener("DOMContentLoaded", () => {

    initializeRequests();

});

/* ==========================================================
   INITIALIZE
========================================================== */

function initializeRequests() {

    initializeSearch();

    initializeViewButtons();

    initializeApproveButtons();

    initializeRejectButtons();

}

function showToast(type,title,message){

const toast=document.getElementById("requestToast");

const icon=document.getElementById("toastIcon");

const wrapper=document.getElementById("toastIconWrapper");

const toastTitle=document.getElementById("toastTitle");

const toastMessage=document.getElementById("toastMessage");

if(!toast)return;

if(wrapper){
    wrapper.className =
    "w-14 h-14 rounded-2xl flex items-center justify-center";
}

icon.className="text-3xl";

if(type==="success"){

wrapper?.classList.add("bg-green-500/20");

icon.classList.add(
"ri-checkbox-circle-fill",
"text-green-400"
);

}

else{

wrapper.classList.add("bg-red-500/20");

icon.classList.add(
"ri-close-circle-fill",
"text-red-400"
);

}

toastTitle.textContent=title;

toastMessage.textContent=message;

toast.classList.remove("translate-x-[120%]");

setTimeout(()=>{

toast.classList.add("translate-x-[120%]");

},3000);

}

/* ==========================================================
   SEARCH
========================================================== */

function initializeSearch() {

    const search =

        document.getElementById("requestSearch");

    if (!search) return;

    search.addEventListener("keyup", () => {

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

    });

}

/* ==========================================================
   VIEW BUTTON
========================================================== */

function initializeViewButtons() {

    document

    .querySelectorAll(".viewRequest")

    .forEach(button => {

        button.addEventListener("click", () => {

            const row = button.closest("tr");

            openStudentModal({

                name:

                row.cells[0]

                .querySelector(".font-semibold")

                .textContent,

                email:

                row.cells[0]

                .querySelector(".text-xs")

                .textContent,

                admission:

                row.cells[1].textContent,

                department:

                row.cells[2].textContent,

                year:

                row.cells[3].textContent,

                status:

                row.cells[5].textContent.trim()

            });

        });

    });

}

/* ==========================================================
   APPROVE
========================================================== */

function initializeApproveButtons() {

    document

    .querySelectorAll(".approveRequest")

    .forEach(button => {

        button.addEventListener("click", () => {

const id = button.dataset.id;

const row = button.closest("tr");

openConfirmationModal({

    title:"Approve Student",

    message:"Are you sure you want to approve this student?",

    icon:"ri-check-line",

    type:"approve",

    onConfirm(){

        approveStudent(id,row);

    }

});

            /*
            AJAX
            Block 5
            */

        });

    });

}

/* ==========================================================
   REJECT
========================================================== */

function initializeRejectButtons() {

    document

    .querySelectorAll(".rejectRequest")

    .forEach(button => {

        button.addEventListener("click", () => {

const id = button.dataset.id;

const row = button.closest("tr");

openConfirmationModal({

    title:"Reject Student",

    message:"Are you sure you want to reject this student?",

    icon:"ri-close-line",

    type:"reject",

    onConfirm(){

        return rejectStudent(id, row);

    }

});
            /*
            AJAX
            Block 5
            */

        });

    });

}

/* ==========================================================
   APPROVE STUDENT
========================================================== */

/* ==========================================================
   APPROVE STUDENT
========================================================== */

async function approveStudent(id, row){

    try{

        const response = await fetch(

            "../../backend/admin/approve-request.php",

            {

                method:"POST",

                headers:{

                    "Content-Type":

                    "application/x-www-form-urlencoded"

                },

                body:"id="+id

            }

        );

        const result = await response.json();

if(result.success){

    showToast(
        "success",
        "Student Approved",
        "Registration approved successfully."
    );

    removeRequestRow(row);

    updateStatistics("approve");

}

        else{

            showToast(

                "error",

                "Approval Failed",

                result.message

            );

        }

    }

    catch(error){

        console.error(error);

    }

}

/* ==========================================================
   REJECT STUDENT
========================================================== */

async function rejectStudent(id,row){

    try{

        const response = await fetch(

            "../../backend/admin/reject-request.php",

            {

                method:"POST",

                headers:{

                    "Content-Type":

                    "application/x-www-form-urlencoded"

                },

                body:"id="+id

            }

        );

        const result = await response.json();

        if(result.success){

            showToast(

                "success",

                "Student Rejected",

                "Registration rejected successfully."

            );

            removeRequestRow(row);

            updateStatistics("reject");

        }

        else{

            showToast(

                "error",

                "Reject Failed",

                result.message

            );

        }

    }

    catch(error){

        console.error(error);

    }

}

/* ==========================================================
   REMOVE REQUEST ROW
========================================================== */

function removeRequestRow(row){

    row.style.transition = "all .35s ease";

    row.style.opacity = "0";

    row.style.transform = "translateX(40px) scale(.96)";

    row.style.filter = "blur(4px)";

    setTimeout(()=>{

        row.remove();

        checkEmptyTable();

    },350);

}

/* ==========================================================
   UPDATE DASHBOARD COUNTERS
========================================================== */

function updateStatistics(action){

    const counters =

    document.querySelectorAll(".dashboard-card h2");

    if(counters.length < 4){

    return;

}

    const total = counters[0];

    const pending = counters[1];

    const approved = counters[2];

    const rejected = counters[3];

    /* Pending */

    pending.textContent =

    Math.max(

        0,

        parseInt(pending.textContent)-1

    );

    /* Total Pending Requests Card */

total.textContent =
Math.max(
    0,
    parseInt(total.textContent)-1
);

    if(action==="approve"){

        approved.textContent =

        parseInt(

            approved.textContent

        )+1;

    }

    else{

        rejected.textContent =

        parseInt(

            rejected.textContent

        )+1;

    }

}

/* ==========================================================
   EMPTY TABLE
========================================================== */

function checkEmptyTable(){

    const tbody =

    document.getElementById(

        "requestsTableBody"

    );

    const rows =

    tbody.querySelectorAll("tr");

    if(rows.length===0){

        tbody.innerHTML=`

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