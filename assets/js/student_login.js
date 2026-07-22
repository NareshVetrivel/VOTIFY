/* ==========================================================
   VOTIFY
   Student Login JavaScript
   File : assets/js/student_login.js
========================================================== */

"use strict";

/* ==========================================================
   DOM ELEMENTS
========================================================== */

const loginForm = document.getElementById(
    "studentLoginForm"
);

const admissionInput = document.getElementById(
    "admissionNo"
);

const dobInput = document.getElementById(
    "dob"
);

const emailInput = document.getElementById(
    "collegeEmail"
);

const passwordInput = document.getElementById(
    "password"
);

const togglePassword = document.getElementById(
    "togglePassword"
);

const togglePasswordIcon = document.getElementById(
    "togglePasswordIcon"
);

const loginButton = document.getElementById(
    "loginButton"
);

/* ==========================================================
   ERROR ELEMENTS
========================================================== */

const admissionError = document.getElementById(
    "admissionError"
);

const dobError = document.getElementById(
    "dobError"
);

const emailError = document.getElementById(
    "emailError"
);

const passwordError = document.getElementById(
    "passwordError"
);

/* ==========================================================
   TOAST ELEMENTS
========================================================== */

const successToast = document.getElementById(
    "successToast"
);

const errorToast = document.getElementById(
    "errorToast"
);

const errorToastTitle = document.getElementById(
    "errorToastTitle"
);

const errorToastMessage = document.getElementById(
    "errorToastMessage"
);

/* ==========================================================
   BACKEND URL
========================================================== */

const LOGIN_API =

"../../backend/student/login.php";

/* ==========================================================
   REDIRECT PAGE
========================================================== */

const DASHBOARD_URL =

"candidate_selection.php";

/* ==========================================================
   SHOW ERROR
========================================================== */

function showError(

    element,

    message

){

    element.textContent = message;

    element.classList.remove("hidden");

}

/* ==========================================================
   HIDE ERROR
========================================================== */

function hideError(

    element

){

    element.textContent = "";

    element.classList.add("hidden");

}

/* ==========================================================
   CLEAR ALL ERRORS
========================================================== */

function clearErrors(){

    hideError(admissionError);

    hideError(dobError);

    hideError(emailError);

    hideError(passwordError);

}

/* ==========================================================
   TRIM INPUT
========================================================== */

function clean(

    value

){

    return value.trim();

}

/* ==========================================================
   PASSWORD VISIBILITY TOGGLE
========================================================== */

function togglePasswordVisibility(){

    if(passwordInput.type === "password"){

        passwordInput.type = "text";

        togglePasswordIcon.classList.remove(
            "ri-eye-line"
        );

        togglePasswordIcon.classList.add(
            "ri-eye-off-line"
        );

    }

    else{

        passwordInput.type = "password";

        togglePasswordIcon.classList.remove(
            "ri-eye-off-line"
        );

        togglePasswordIcon.classList.add(
            "ri-eye-line"
        );

    }

}

/* ==========================================================
   PASSWORD TOGGLE EVENT
========================================================== */

if(togglePassword){

    togglePassword.addEventListener(

        "click",

        togglePasswordVisibility

    );

}

/* ==========================================================
   ENTER KEY SUPPORT
========================================================== */

if(passwordInput){

    passwordInput.addEventListener(

        "keydown",

        function(event){

            if(event.key === "Enter"){

                event.preventDefault();

                loginForm.requestSubmit();

            }

        }

    );

}
/* ==========================================================
   AUTO HIDE ERROR ON INPUT
========================================================== */

if(admissionInput){

    admissionInput.addEventListener(

        "input",

        function(){

            hideError(admissionError);

        }

    );

}

if(dobInput){

    dobInput.addEventListener(

        "input",

        function(){

            hideError(dobError);

        }

    );

}

if(emailInput){

    emailInput.addEventListener(

        "input",

        function(){

            hideError(emailError);

        }

    );

}

if(passwordInput){

    passwordInput.addEventListener(

        "input",

        function(){

            hideError(passwordError);

        }

    );

}

/* ==========================================================
   VALIDATE LOGIN FORM
========================================================== */

function validateForm(){

    clearErrors();

    let isValid = true;

    /* ======================================================
       GET VALUES
    ====================================================== */

    const admissionNo = clean(

        admissionInput.value

    );

    const dob = clean(

        dobInput.value

    );

    const email = clean(

        emailInput.value

    ).toLowerCase();

    const password = clean(

        passwordInput.value

    );

    /* ======================================================
       ADMISSION NUMBER
    ====================================================== */

    if(admissionNo === ""){

        showError(

            admissionError,

            "Admission Number is required."

        );

        isValid = false;

    }

    else if(admissionNo.length < 3){

        showError(

            admissionError,

            "Enter a valid Admission Number."

        );

        isValid = false;

    }

    /* ======================================================
       DATE OF BIRTH
    ====================================================== */

    if(dob === ""){

        showError(

            dobError,

            "Date of Birth is required."

        );

        isValid = false;

    }

    /* ======================================================
       COLLEGE EMAIL
    ====================================================== */

    const emailPattern =

    /^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/;

    if(email === ""){

        showError(

            emailError,

            "College Email is required."

        );

        isValid = false;

    }

    else if(

        !emailPattern.test(email)

    ){

        showError(

            emailError,

            "Enter a valid College Email."

        );

        isValid = false;

    }

    /* ======================================================
       PASSWORD
    ====================================================== */

    if(password === ""){

        showError(

            passwordError,

            "Password is required."

        );

        isValid = false;

    }

    else if(password.length < 8){

        showError(

            passwordError,

            "Password must contain at least 8 characters."

        );

        isValid = false;

    }

    /* ======================================================
       RESULT
    ====================================================== */

    return isValid;

}

/* ==========================================================
   SHOW SUCCESS TOAST
========================================================== */

function showSuccessToast(

    message = "Login Successful"

){

    successToast.querySelector(

        "p.font-semibold"

    ).textContent = message;

    successToast.classList.remove(

        "translate-x-[120%]"

    );

    successToast.classList.add(

        "translate-x-0"

    );

}

/* ==========================================================
   HIDE SUCCESS TOAST
========================================================== */

function hideSuccessToast(){

    successToast.classList.remove(

        "translate-x-0"

    );

    successToast.classList.add(

        "translate-x-[120%]"

    );

}

/* ==========================================================
   SHOW ERROR TOAST
========================================================== */

function showErrorToast(

    title,

    message

){

    errorToastTitle.textContent = title;

    errorToastMessage.textContent = message;

    errorToast.classList.remove(

        "translate-x-[120%]"

    );

    errorToast.classList.add(

        "translate-x-0"

    );

    setTimeout(

        hideErrorToast,

        4000

    );

}

/* ==========================================================
   HIDE ERROR TOAST
========================================================== */

function hideErrorToast(){

    errorToast.classList.remove(

        "translate-x-0"

    );

    errorToast.classList.add(

        "translate-x-[120%]"

    );

}

/* ==========================================================
   DISABLE LOGIN BUTTON
========================================================== */

function disableLoginButton(){

    loginButton.disabled = true;

    loginButton.innerHTML = `

        <i class="ri-loader-4-line animate-spin text-xl"></i>

        Logging in...

    `;

}

/* ==========================================================
   ENABLE LOGIN BUTTON
========================================================== */

function enableLoginButton(){

    loginButton.disabled = false;

    loginButton.innerHTML = `

        <i class="ri-login-circle-line text-xl"></i>

        Login Securely

    `;

}

/* ==========================================================
   LOGIN REQUEST
========================================================== */

loginForm.addEventListener(

    "submit",

    async function(event){

        event.preventDefault();

        clearErrors();

        /* ==============================================
           FRONTEND VALIDATION
        ============================================== */

        if(!validateForm()){

            return;

        }

        disableLoginButton();

        /* ==============================================
           FORM DATA
        ============================================== */

        const formData = new FormData();

        formData.append(

            "admissionNo",

            clean(

                admissionInput.value

            )

        );

        formData.append(

            "dob",

            clean(

                dobInput.value

            )

        );

        formData.append(

            "collegeEmail",

            clean(

                emailInput.value

            ).toLowerCase()

        );

        formData.append(

            "password",

            passwordInput.value

        );

        try{

            /* ==========================================
               FETCH REQUEST
            ========================================== */

            const response = await fetch(

                LOGIN_API,

                {

                    method : "POST",

                    body : formData

                }

            );

            const result = await response.json();

            /* ==========================================
               LOGIN SUCCESS
            ========================================== */

            if(result.success){

                loginButton.disabled = true;

                showSuccessToast(

                    result.message

                );

                setTimeout(

                    function(){

                        window.location.href =

                        DASHBOARD_URL;

                    },

                    1500

                );

            }

            /* ==========================================
               LOGIN FAILED
            ========================================== */

            else{

                showErrorToast(

                    "Login Failed",

                    result.message

                );

                enableLoginButton();

            }

        }

        /* ==============================================
           SERVER ERROR
        ============================================== */

        catch(error){

            console.error(error);

            showErrorToast(

                "Server Error",

                "Unable to connect to the server. Please try again."

            );

            enableLoginButton();

        }

    }

);

/* ==========================================================
   INITIALIZE STUDENT LOGIN
========================================================== */

document.addEventListener(

    "DOMContentLoaded",

    function(){

        /* ==============================================
           HIDE TOASTS
        ============================================== */

        hideSuccessToast();

        hideErrorToast();

        /* ==============================================
           CLEAR FORM ERRORS
        ============================================== */

        clearErrors();

        /* ==============================================
           ENABLE LOGIN BUTTON
        ============================================== */

        enableLoginButton();

        /* ==============================================
           AUTO FOCUS
        ============================================== */

        if(admissionInput){

            admissionInput.focus();

        }

        /* ==============================================
           CONSOLE LOG
        ============================================== */

        console.log(

            "VOTIFY Student Login Initialized"

        );

    }

);