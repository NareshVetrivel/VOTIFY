/* ==========================================================
   VOTIFY
   Admin Login
   File : assets/js/admin-login.js
========================================================== */

"use strict";


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    const form =
        document.getElementById("adminLoginForm");

    if (!form) return;

    form.addEventListener(
        "submit",
        loginAdmin
    );

    initializePasswordToggle();

});


/* ==========================================================
   LOGIN
========================================================== */

async function loginAdmin(event) {

    event.preventDefault();

    clearErrors();


    /* ======================================================
       GET FORM VALUES
    ====================================================== */

    const usernameElement =
        document.getElementById("username");

    const passwordElement =
        document.getElementById("password");


    if (
        !usernameElement ||
        !passwordElement
    ) {

        console.error(
            "VOTIFY Admin Login: Login form fields not found."
        );

        showErrorToast(
            "Login form configuration error."
        );

        return;

    }


    const username =
        usernameElement.value.trim();

    const password =
        passwordElement.value;


    let valid = true;


    /* ======================================================
       USERNAME VALIDATION
    ====================================================== */

    if (username === "") {

        showFieldError(
            "usernameError",
            "Enter Admin Username"
        );

        valid = false;

    }


    /* ======================================================
       PASSWORD VALIDATION
    ====================================================== */

    if (password === "") {

        showFieldError(
            "passwordError",
            "Enter Password"
        );

        valid = false;

    }


    /* ======================================================
       STOP IF VALIDATION FAILS
    ====================================================== */

    if (!valid) {

        return;

    }


    /* ======================================================
       FORM DATA
    ====================================================== */

    const formData =
        new FormData();

    formData.append(
        "username",
        username
    );

    formData.append(
        "password",
        password
    );


    /* ======================================================
       START LOADING STATE
    ====================================================== */

    setLoginLoading(true);


    /* ======================================================
       BACKEND LOGIN REQUEST
    ====================================================== */

    try {

        const response =
            await fetch(

                "../../backend/admin/login.php",

                {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin",
                    cache: "no-store"
                }

            );


        /* ==================================================
           READ RAW RESPONSE FIRST
        ================================================== */

        const responseText =
            await response.text();


        /*
         * Keep the raw response available in the browser
         * console for production debugging.
         *
         * Do not expose server-side error details to users.
         */

        console.log(
            "VOTIFY Admin Login Response:",
            {
                status: response.status,
                statusText: response.statusText,
                contentType:
                    response.headers.get(
                        "content-type"
                    ),
                body: responseText
            }
        );


        /* ==================================================
           EMPTY RESPONSE
        ================================================== */

        if (
            responseText.trim() === ""
        ) {

            console.error(
                "VOTIFY Admin Login: Empty server response."
            );

            showErrorToast(
                "Server returned an empty response."
            );

            setLoginLoading(false);

            return;

        }


        /* ==================================================
           PARSE JSON RESPONSE
        ================================================== */

        let result;

        try {

            result =
                JSON.parse(
                    responseText
                );

        }

        catch (jsonError) {

            console.error(
                "VOTIFY Admin Login: Invalid JSON response.",
                jsonError
            );


            /*
             * Backend may have returned HTML/PHP error output.
             * Do not expose the raw server response to users.
             */

            showErrorToast(
                "Server returned an invalid response."
            );

            setLoginLoading(false);

            return;

        }


        /* ==================================================
           LOGIN SUCCESS
        ================================================== */

        if (
            result &&
            result.status === "success"
        ) {

            /*
             * Keep loading state active
             * until dashboard redirect.
             */

            showSuccessToast();


            /*
             * Existing redirect timing preserved.
             */

            setTimeout(() => {

                window.location.href =
                    "../admin/dashboard.php";

            }, 1800);


            return;

        }


        /* ==================================================
           LOGIN FAILED
        ================================================== */

        const errorMessage =
            (
                result &&
                typeof result.message === "string" &&
                result.message.trim() !== ""
            )
                ? result.message
                : "Invalid Username or Password";


        showErrorToast(
            errorMessage
        );


        /*
         * Allow another attempt.
         */

        setLoginLoading(false);

    }


    /* ======================================================
       NETWORK / FETCH ERROR
    ====================================================== */

    catch (error) {

        console.error(
            "VOTIFY Admin Login Network Error:",
            error
        );


        showErrorToast(
            "Unable to connect to the login server."
        );


        /*
         * Restore login button.
         */

        setLoginLoading(false);

    }

}


/* ==========================================================
   LOGIN BUTTON LOADING STATE
========================================================== */

function setLoginLoading(isLoading) {

    const button =
        document.getElementById(
            "loginButton"
        );

    if (!button) return;


    const icon =
        document.getElementById(
            "loginButtonIcon"
        );

    const text =
        document.getElementById(
            "loginButtonText"
        );


    /* ======================================================
       START LOADING
    ====================================================== */

    if (isLoading) {

        /* ----------------------------------------------
           Disable button
        ---------------------------------------------- */

        button.disabled = true;


        /* ----------------------------------------------
           Accessibility
        ---------------------------------------------- */

        button.setAttribute(
            "aria-busy",
            "true"
        );


        /* ----------------------------------------------
           Force not-allowed cursor
        ---------------------------------------------- */

        button.style.setProperty(
            "cursor",
            "not-allowed",
            "important"
        );


        /* ----------------------------------------------
           Visual disabled state
        ---------------------------------------------- */

        button.style.setProperty(
            "opacity",
            "0.65",
            "important"
        );


        button.classList.add(
            "opacity-65"
        );


        /* ----------------------------------------------
           Loading spinner
        ---------------------------------------------- */

        if (icon) {

            icon.className =
                "ri-loader-4-line animate-spin text-lg";

        }


        /* ----------------------------------------------
           Loading text
        ---------------------------------------------- */

        if (text) {

            text.textContent =
                "Logging in...";

        }


        return;

    }


    /* ======================================================
       STOP LOADING
    ====================================================== */

    button.disabled = false;


    button.removeAttribute(
        "aria-busy"
    );


    /* ----------------------------------------------
       Restore cursor
    ---------------------------------------------- */

    button.style.removeProperty(
        "cursor"
    );


    /* ----------------------------------------------
       Restore opacity
    ---------------------------------------------- */

    button.style.removeProperty(
        "opacity"
    );


    button.classList.remove(
        "opacity-65"
    );


    /* ----------------------------------------------
       Restore icon
    ---------------------------------------------- */

    if (icon) {

        icon.className =
            "ri-login-circle-line";

    }


    /* ----------------------------------------------
       Restore text
    ---------------------------------------------- */

    if (text) {

        text.textContent =
            "Login Securely";

    }

}


/* ==========================================================
   PASSWORD TOGGLE
========================================================== */

function initializePasswordToggle() {

    const password =
        document.getElementById(
            "password"
        );

    const toggle =
        document.getElementById(
            "togglePassword"
        );


    if (
        !password ||
        !toggle
    ) {

        return;

    }


    toggle.onclick = function () {

        if (
            password.type ===
            "password"
        ) {

            password.type =
                "text";


            this.innerHTML =
                '<i class="ri-eye-off-line text-xl"></i>';

        }

        else {

            password.type =
                "password";


            this.innerHTML =
                '<i class="ri-eye-line text-xl"></i>';

        }

    };

}


/* ==========================================================
   FIELD ERROR
========================================================== */

function showFieldError(
    id,
    message
) {

    const error =
        document.getElementById(id);


    if (!error) return;


    error.innerText =
        message;


    error.classList.remove(
        "hidden"
    );

}


/* ==========================================================
   CLEAR ERRORS
========================================================== */

function clearErrors() {

    document
        .querySelectorAll(
            "[id$='Error']"
        )
        .forEach(error => {

            error.innerText = "";

            error.classList.add(
                "hidden"
            );

        });

}


/* ==========================================================
   SUCCESS TOAST
========================================================== */

function showSuccessToast() {

    const toast =
        document.getElementById(
            "successToast"
        );


    if (!toast) return;


    toast.classList.remove(
        "translate-x-[120%]"
    );

}


/* ==========================================================
   ERROR TOAST
========================================================== */

function showErrorToast(
    message
) {

    const toast =
        document.getElementById(
            "errorToast"
        );

    const text =
        document.getElementById(
            "errorToastMessage"
        );


    if (
        !toast ||
        !text
    ) {

        return;

    }


    text.innerText =
        message;


    toast.classList.remove(
        "translate-x-[120%]"
    );


    setTimeout(() => {

        toast.classList.add(
            "translate-x-[120%]"
        );

    }, 2500);

}