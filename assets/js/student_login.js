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
    "security_check.php";


/* ==========================================================
   ALREADY VOTED PAGE
========================================================== */

const ALREADY_VOTED_URL =
    "already_voted.php";


/* ==========================================================
   SHOW ERROR
========================================================== */

function showError(
    element,
    message
){

    if(!element){

        return;

    }

    element.textContent = message;

    element.classList.remove("hidden");

}


/* ==========================================================
   HIDE ERROR
========================================================== */

function hideError(
    element
){

    if(!element){

        return;

    }

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

    return String(value || "").trim();

}


/* ==========================================================
   PASSWORD VISIBILITY TOGGLE
========================================================== */

function togglePasswordVisibility(){

    if(
        !passwordInput ||
        !togglePasswordIcon
    ){

        return;

    }


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

if(
    passwordInput &&
    loginForm
){

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
        admissionInput?.value
    );

    const dob = clean(
        dobInput?.value
    );

    const email = clean(
        emailInput?.value
    ).toLowerCase();

    const password = clean(
        passwordInput?.value
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

    if(!successToast){

        return;

    }


    const messageElement =
        successToast.querySelector(
            "p.font-semibold"
        );


    if(messageElement){

        messageElement.textContent =
            message;

    }


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

    if(!successToast){

        return;

    }


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

    if(
        !errorToast ||
        !errorToastTitle ||
        !errorToastMessage
    ){

        return;

    }


    errorToastTitle.textContent =
        title;

    errorToastMessage.textContent =
        message;


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

    if(!errorToast){

        return;

    }


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

    if(!loginButton){

        return;

    }


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

    if(!loginButton){

        return;

    }


    loginButton.disabled = false;


    loginButton.innerHTML = `

        <i class="ri-login-circle-line text-xl"></i>

        Login Securely

    `;

}


/* ==========================================================
   CHECK ALREADY VOTED RESPONSE
========================================================== */

function isAlreadyVotedResponse(
    result
){

    if(!result){

        return false;

    }


    /* ======================================================
       DIRECT BACKEND FLAG
       ------------------------------------------------------
       Supported if backend returns:
       already_voted: true
    ====================================================== */

    if(
        result.already_voted === true ||
        result.already_voted === "true"
    ){

        return true;

    }


    /* ======================================================
       MESSAGE CHECK
       ------------------------------------------------------
       Handles minor differences in:
       - Capitalization
       - Full stop
       - Extra spaces
    ====================================================== */

    const message =
        String(
            result.message || ""
        )
        .trim()
        .toLowerCase();


    if(

        message.includes(
            "already cast your vote"
        )

        ||

        message.includes(
            "already voted"
        )

        ||

        message.includes(
            "vote already cast"
        )

    ){

        return true;

    }


    return false;

}


/* ==========================================================
   LOGIN REQUEST
========================================================== */

if(loginForm){

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


            /* ==============================================
               DISABLE LOGIN BUTTON
            ============================================== */

            disableLoginButton();


            /* ==============================================
               FORM DATA
            ============================================== */

            const formData =
                new FormData();


            formData.append(

                "admissionNo",

                clean(
                    admissionInput?.value
                )

            );


            formData.append(

                "dob",

                clean(
                    dobInput?.value
                )

            );


            formData.append(

                "collegeEmail",

                clean(
                    emailInput?.value
                ).toLowerCase()

            );


            formData.append(

                "password",

                passwordInput?.value || ""

            );


            try{


                /* ==========================================
                   FETCH LOGIN REQUEST
                ========================================== */

                const response =
                    await fetch(

                        LOGIN_API,

                        {
                            method : "POST",
                            body : formData,
                            cache : "no-store"
                        }

                    );


                /* ==========================================
                   RESPONSE JSON
                ========================================== */

                const result =
                    await response.json();


                /* ==========================================
                   ALREADY VOTED CHECK
                   ------------------------------------------------
                   IMPORTANT:
                   This check happens BEFORE showing
                   any error toast.
                ========================================== */

                if(
                    isAlreadyVotedResponse(
                        result
                    )
                ){

                    window.location.replace(
                        ALREADY_VOTED_URL
                    );

                    return;

                }


                /* ==========================================
                   LOGIN SUCCESS
                ========================================== */

                if(result.success){

                    if(loginButton){

                        loginButton.disabled =
                            true;

                    }


                    showSuccessToast(
                        result.message ||
                        "Login Successful"
                    );


                    setTimeout(

                        function(){

                            window.location.replace(
                                DASHBOARD_URL
                            );

                        },

                        1500

                    );


                    return;

                }


                /* ==========================================
                   LOGIN FAILED
                ========================================== */

                showErrorToast(

                    "Login Failed",

                    result.message ||
                    "Invalid login credentials."

                );


                enableLoginButton();

            }


            /* ==============================================
               SERVER / NETWORK ERROR
            ============================================== */

            catch(error){

                console.error(
                    "VOTIFY Login Error:",
                    error
                );


                showErrorToast(

                    "Server Error",

                    "Unable to connect to the server. Please try again."

                );


                enableLoginButton();

            }

        }

    );

}


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