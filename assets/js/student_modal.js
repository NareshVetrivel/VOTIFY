/* ==========================================================
   VOTIFY
   Student Details Modal
========================================================== */

"use strict";

/* ==========================================================
   OPEN MODAL
========================================================== */

function openStudentModal(student){

    document.getElementById("studentName").textContent =
    student.name;

    document.getElementById("studentAdmission").textContent =
    student.admission;

    document.getElementById("studentEmail").textContent =
    student.email;

    document.getElementById("studentDepartment").textContent =
    student.department;

    document.getElementById("studentYear").textContent =
    student.year;

    document.getElementById("studentStatus").textContent =
    student.status;

    const modal =
    document.getElementById("studentModal");

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

/* ==========================================================
   CLOSE MODAL
========================================================== */

function closeStudentModal(){

    const modal =
    document.getElementById("studentModal");

    modal.classList.remove("flex");

    modal.classList.add("hidden");

}

/* ==========================================================
   EVENTS
========================================================== */

document.addEventListener("DOMContentLoaded",()=>{

    document
    .getElementById("closeStudentModal")
    ?.addEventListener("click",closeStudentModal);

    document
    .getElementById("studentClose")
    ?.addEventListener("click",closeStudentModal);

    document
    .getElementById("studentModal")
    ?.addEventListener("click",(e)=>{

        if(e.target.id==="studentModal"){

            closeStudentModal();

        }

    });

});