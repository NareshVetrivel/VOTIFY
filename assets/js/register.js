/* ==========================================================
   VOTIFY
   Student Registration
   File : assets/js/register.js
========================================================== */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("registerForm");

    if (!form) return;

    form.addEventListener("submit", registerStudent);

});

/* ==========================================
   FULL NAME
========================================== */

const fullName=document.getElementById("fullName");

fullName.addEventListener("input",()=>{

    fullName.value=fullName.value
        .replace(/[^A-Za-z\s]/g,"")
        .replace(/\s{2,}/g," ");

});

/* ==========================================
   ADMISSION NUMBER
========================================== */

const admission=document.getElementById("admissionNo");

admission.addEventListener("input",()=>{

    admission.value=admission.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g,"");

});

/* ==========================================
   DROPDOWN VALIDATION
========================================== */

const department=document.getElementById("department");
const year=document.getElementById("year");

department.addEventListener("change",()=>{

    if(department.value===""){

        showFieldError(
            "department",
            "Please select your department."
        );

    }

    else{

        clearSingleError("departmentError");

    }

});

year.addEventListener("change",()=>{

    if(year.value===""){

        showFieldError(
            "year",
            "Please select your year."
        );

    }

    else{

        clearSingleError("yearError");

    }

});

/* ==========================================
   REGISTER STUDENT
========================================== */

async function registerStudent(event) {

    event.preventDefault();

    clearErrors();
    const departmentValid=
validateDropdown(
    "department",
    "departmentError",
    "Please select your department."
);

const yearValid=
validateDropdown(
    "year",
    "yearError",
    "Please select your year."
);

if(
    !departmentValid ||
    !yearValid
){

    return;

}

    const form = document.getElementById("registerForm");

    const submitButton = form.querySelector("button[type='submit']");

submitButton.disabled = true;

submitButton.innerHTML = `
<i class="ri-loader-4-line animate-spin"></i>
Registering...
`;

const formData = new FormData(form);

    try {

        const response = await fetch(

            "../../backend/student/register.php",

            {

                method: "POST",

                body: formData

            }

        );

        const result = await response.json();
submitButton.disabled = false;

submitButton.innerHTML = `
<i class="ri-user-add-line"></i>
Register Now
`;
        if (result.status === "success") {

            showSuccessToast(result.message);

            setTimeout(() => {

                window.location.href = "../../index.html";

            }, 1800);

        }

        else {

            showFieldError(

                result.field,

                result.message

            );

        }

    }

    catch (error) {

        console.error(error);

        alert("Unable to connect to the server.");

    }

}


/* ==========================================
   FIELD ERROR
========================================== */

function showFieldError(field, message) {

    const errorMap = {

        fullName: "fullNameError",

        dob: "dobError",

        admissionNo: "admissionError",

        phone: "phoneError",

        email: "emailError",

        department: "departmentError",

        year: "yearError",

        gender: "genderError",

        password: "passwordError",

        confirmPassword: "confirmPasswordError"

    };

    const errorElement = document.getElementById(

        errorMap[field]

    );

    if (!errorElement) return;

    errorElement.innerText = message;

    errorElement.classList.remove("hidden");

    document.getElementById(field)?.focus();

}


/* ==========================================
   CLEAR ERRORS
========================================== */

function clearErrors() {

    document

    .querySelectorAll(

        "[id$='Error']"

    )

    .forEach(error => {

        error.innerText = "";

        error.classList.add("hidden");

    });

}


/* ==========================================
   SUCCESS TOAST
========================================== */

function showSuccessToast(message) {

    const toast = document.getElementById(

        "successToast"

    );

    if (!toast) {

        alert(message);

        return;

    }

    toast.querySelector("p").innerText = message;

    toast.classList.remove(

        "translate-x-[120%]"

    );
setTimeout(()=>{

toast.classList.add("translate-x-[120%]");

},2500);
}

togglePassword(
    "password",
    "togglePassword"
);

togglePassword(
    "confirmPassword",
    "toggleConfirmPassword"
);

function togglePassword(inputId, buttonId){

    const input=document.getElementById(inputId);

    const button=document.getElementById(buttonId);

    if(!input || !button) return;

    button.onclick=()=>{

        if(input.type==="password"){

            input.type="text";

            button.innerHTML='<i class="ri-eye-off-line text-xl"></i>';

        }

        else{

            input.type="password";

            button.innerHTML='<i class="ri-eye-line text-xl"></i>';

        }

    };

}

function initializePasswordStrength(){

const password=document.getElementById("password");

const meter=document.getElementById("passwordStrength");

const bar=document.getElementById("strengthBar");

const text=document.getElementById("strengthText");

if(!password) return;

password.addEventListener("focus",()=>{

meter.classList.remove("hidden");

});

password.addEventListener("blur",()=>{

setTimeout(()=>{

meter.classList.add("hidden");

},150);

});

password.addEventListener("input",()=>{

let score=0;

const value=password.value;

if(value.length>=8) score++;

if(/[A-Z]/.test(value)) score++;

if(/[0-9]/.test(value)) score++;

if(/[^A-Za-z0-9]/.test(value)) score++;

const colors=[
"",
"bg-red-500",
"bg-yellow-500",
"bg-blue-500",
"bg-green-500"
];

const labels=[
"",
"Weak",
"Fair",
"Good",
"Strong"
];

bar.className=`h-full rounded-full transition-all ${colors[score]}`;

bar.style.width=(score*25)+"%";

text.innerHTML=labels[score];

});

}

const phone=document.getElementById("phone");

phone.addEventListener("input",()=>{

phone.value=phone.value.replace(/\D/g,"");

});

/* ==========================================
   PASSWORD STRENGTH
========================================== */

const password=document.getElementById("password");

const meter=document.getElementById("passwordStrength");

const bar=document.getElementById("strengthBar");

const text=document.getElementById("strengthText");

password.addEventListener("focus",()=>{

    meter.classList.remove("hidden");

});

password.addEventListener("blur",()=>{

    setTimeout(()=>{

        meter.classList.add("hidden");

    },150);

});

password.addEventListener("input",()=>{

    let score=0;

    const value=password.value;

    if(value.length>=8) score++;

    if(/[A-Z]/.test(value)) score++;

    if(/[0-9]/.test(value)) score++;

    if(/[^A-Za-z0-9]/.test(value)) score++;

    if(score==0){

        bar.style.width="0%";

        text.innerHTML="Password Strength";

        return;

    }

    if(score==1){

        bar.style.width="25%";

        bar.className="h-full bg-red-500 rounded-full transition-all";

        text.innerHTML="🔴 Weak";

    }

    if(score==2){

        bar.style.width="50%";

        bar.className="h-full bg-yellow-500 rounded-full transition-all";

        text.innerHTML="🟡 Fair";

    }

    if(score==3){

        bar.style.width="75%";

        bar.className="h-full bg-blue-500 rounded-full transition-all";

        text.innerHTML="🔵 Good";

    }

    if(score==4){

        bar.style.width="100%";

        bar.className="h-full bg-green-500 rounded-full transition-all";

        text.innerHTML="🟢 Strong";

    }

});

function clearSingleError(id){

    const error=document.getElementById(id);

    if(!error) return;

    error.innerHTML="";

    error.classList.add("hidden");

}