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

    const form = document.getElementById("adminLoginForm");

    if (!form) return;

    form.addEventListener("submit", loginAdmin);

    initializePasswordToggle();

});


/* ==========================================================
   LOGIN
========================================================== */

async function loginAdmin(event) {

    event.preventDefault();

    clearErrors();

    const username =
        document.getElementById("username").value.trim();

    const password =
        document.getElementById("password").value;

    let valid = true;

    /* Username */

    if (username === "") {

        showFieldError(
            "usernameError",
            "Enter Admin Username"
        );

        valid = false;

    }

    /* Password */

    if (password === "") {

        showFieldError(
            "passwordError",
            "Enter Password"
        );

        valid = false;

    }

    if (!valid) return;

    const formData = new FormData();

    formData.append("username", username);

    formData.append("password", password);

    try {

        const response = await fetch(

            "../../backend/admin/login.php",

            {

                method: "POST",

                body: formData

            }

        );

        const result = await response.json();

        if (result.status === "success") {

            showSuccessToast();

            setTimeout(() => {

                window.location.href =
                "../admin/dashboard.php";

            }, 1800);

        }

        else {

            showErrorToast(result.message);

        }

    }

    catch (error) {

        console.error(error);

        showErrorToast(

            "Server Connection Failed."

        );

    }

}


/* ==========================================================
   PASSWORD TOGGLE
========================================================== */

function initializePasswordToggle() {

    const password =
        document.getElementById("password");

    const toggle =
        document.getElementById("togglePassword");

    if (!password || !toggle) return;

    toggle.onclick = function () {

        if (password.type === "password") {

            password.type = "text";

            this.innerHTML =
            '<i class="ri-eye-off-line text-xl"></i>';

        }

        else {

            password.type = "password";

            this.innerHTML =
            '<i class="ri-eye-line text-xl"></i>';

        }

    };

}


/* ==========================================================
   FIELD ERROR
========================================================== */

function showFieldError(id, message) {

    const error =
        document.getElementById(id);

    if (!error) return;

    error.innerText = message;

    error.classList.remove("hidden");

}


/* ==========================================================
   CLEAR ERRORS
========================================================== */

function clearErrors() {

    document

    .querySelectorAll("[id$='Error']")

    .forEach(error => {

        error.innerText = "";

        error.classList.add("hidden");

    });

}


/* ==========================================================
   SUCCESS TOAST
========================================================== */

function showSuccessToast() {

    const toast =
        document.getElementById("successToast");

    toast.classList.remove(

        "translate-x-[120%]"

    );

}


/* ==========================================================
   ERROR TOAST
========================================================== */

function showErrorToast(message) {

    const toast =
        document.getElementById("errorToast");

    const text =
        document.getElementById("errorToastMessage");

    text.innerText = message;

    toast.classList.remove(

        "translate-x-[120%]"

    );

    setTimeout(() => {

        toast.classList.add(

            "translate-x-[120%]"

        );

    }, 2500);

}