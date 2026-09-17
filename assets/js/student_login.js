/* ==========================================================
   VOTIFY
   Student Login JavaScript
   File : assets/js/student_login.js

   FLOW:
   1. Student enters login details
   2. Backend validates details
   3. Backend sends OTP to college email
   4. OTP popup opens
   5. Student enters 6-digit OTP
   6. OTP is verified by backend
   7. Student is redirected to dashboard
========================================================== */

"use strict";

/* ==========================================================
   DOM ELEMENTS
========================================================== */

const loginForm = document.getElementById("studentLoginForm");

const admissionInput = document.getElementById("admissionNo");
const dobInput = document.getElementById("dob");
const emailInput = document.getElementById("collegeEmail");
const passwordInput = document.getElementById("password");

const togglePassword = document.getElementById("togglePassword");
const togglePasswordIcon = document.getElementById("togglePasswordIcon");

const loginButton = document.getElementById("loginButton");

/* ==========================================================
   ERROR ELEMENTS
========================================================== */

const admissionError = document.getElementById("admissionError");
const dobError = document.getElementById("dobError");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");

/* ==========================================================
   TOAST ELEMENTS
========================================================== */

const successToast = document.getElementById("successToast");
const errorToast = document.getElementById("errorToast");

const errorToastTitle =
    document.getElementById("errorToastTitle");

const errorToastMessage =
    document.getElementById("errorToastMessage");

/* ==========================================================
   BACKEND
========================================================== */

const LOGIN_API =
    "../../backend/student/login.php";

/* ==========================================================
   DASHBOARD
========================================================== */

const DASHBOARD_URL =
    "security_check.php";

/* ==========================================================
   ALREADY VOTED
========================================================== */

const ALREADY_VOTED_URL =
    "already_voted.php";

/* ==========================================================
   GLOBAL OTP STATE
========================================================== */

let otpModal = null;
let otpInput = null;
let otpMessage = null;
let otpTimerElement = null;
let verifyOtpButton = null;

let otpTimer = null;
let otpSeconds = 300;


/* ==========================================================
   CLEAN INPUT
========================================================== */

function clean(value) {

    return String(value || "").trim();

}


/* ==========================================================
   SHOW FIELD ERROR
========================================================== */

function showError(element, message) {

    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.remove("hidden");

}


/* ==========================================================
   HIDE FIELD ERROR
========================================================== */

function hideError(element) {

    if (!element) {
        return;
    }

    element.textContent = "";
    element.classList.add("hidden");

}


/* ==========================================================
   CLEAR ERRORS
========================================================== */

function clearErrors() {

    hideError(admissionError);
    hideError(dobError);
    hideError(emailError);
    hideError(passwordError);

}


/* ==========================================================
   PASSWORD TOGGLE
========================================================== */

function togglePasswordVisibility() {

    if (!passwordInput || !togglePasswordIcon) {
        return;
    }

    if (passwordInput.type === "password") {

        passwordInput.type = "text";

        togglePasswordIcon.classList.remove(
            "ri-eye-line"
        );

        togglePasswordIcon.classList.add(
            "ri-eye-off-line"
        );

    } else {

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

if (togglePassword) {

    togglePassword.addEventListener(
        "click",
        togglePasswordVisibility
    );

}


/* ==========================================================
   VALIDATE LOGIN FORM
========================================================== */

function validateForm() {

    clearErrors();

    let valid = true;

    const admissionNo =
        clean(admissionInput?.value).toUpperCase();

    const dob =
        clean(dobInput?.value);

    const email =
        clean(emailInput?.value).toLowerCase();

    const password =
        passwordInput?.value || "";


    /* ======================================================
       ADMISSION NUMBER

       Example:
       25CAPMCA092

       Allowed:
       A-Z
       0-9
       Length: 10 to 15
    ====================================================== */

    if (admissionNo === "") {

        showError(
            admissionError,
            "Admission Number is required."
        );

        valid = false;

    } else if (
        !/^[A-Z0-9]{10,15}$/.test(admissionNo)
    ) {

        showError(
            admissionError,
            "Admission Number must contain only letters and numbers (10-15 characters)."
        );

        valid = false;

    }


    /* ======================================================
       DOB
    ====================================================== */

    if (dob === "") {

        showError(
            dobError,
            "Date of Birth is required."
        );

        valid = false;

    }


    /* ======================================================
       EMAIL
    ====================================================== */

    if (email === "") {

        showError(
            emailError,
            "College Email is required."
        );

        valid = false;

    } else if (
        !/^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/i.test(email)
    ) {

        showError(
            emailError,
            "Enter a valid @sonatech.ac.in email."
        );

        valid = false;

    }


    /* ======================================================
       PASSWORD
    ====================================================== */

    if (password === "") {

        showError(
            passwordError,
            "Password is required."
        );

        valid = false;

    }

    return valid;

}


/* ==========================================================
   SUCCESS TOAST
========================================================== */

function showSuccessToast(
    message = "Success"
) {

    if (!successToast) {
        return;
    }

    const messageElement =
        successToast.querySelector(
            "p.font-semibold"
        );

    if (messageElement) {
        messageElement.textContent = message;
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

function hideSuccessToast() {

    if (!successToast) {
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
   ERROR TOAST
========================================================== */

function showErrorToast(
    title,
    message
) {

    if (
        !errorToast ||
        !errorToastTitle ||
        !errorToastMessage
    ) {
        alert(message);
        return;
    }

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

function hideErrorToast() {

    if (!errorToast) {
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
   LOGIN BUTTON
========================================================== */

function disableLoginButton() {

    if (!loginButton) {
        return;
    }

    loginButton.disabled = true;

    loginButton.innerHTML = `
        <i class="ri-loader-4-line animate-spin text-xl"></i>
        Sending OTP...
    `;

}


/* ==========================================================
   ENABLE LOGIN BUTTON
========================================================== */

function enableLoginButton() {

    if (!loginButton) {
        return;
    }

    loginButton.disabled = false;

    loginButton.innerHTML = `
        <i class="ri-login-circle-line text-xl"></i>
        Login Securely
    `;

}


/* ==========================================================
   ALREADY VOTED
========================================================== */

function isAlreadyVotedResponse(result) {

    if (!result) {
        return false;
    }

    if (
        result.already_voted === true ||
        result.already_voted === "true"
    ) {
        return true;
    }

    const message =
        String(
            result.message || ""
        )
        .trim()
        .toLowerCase();

    return (
        message.includes("already cast your vote") ||
        message.includes("already voted") ||
        message.includes("vote already cast")
    );

}


/* ==========================================================
   CREATE OTP MODAL
========================================================== */

function createOtpModal() {

    if (document.getElementById("votifyOtpModal")) {

        otpModal =
            document.getElementById("votifyOtpModal");

        otpInput =
            document.getElementById("votifyOtpInput");

        otpMessage =
            document.getElementById("votifyOtpMessage");

        otpTimerElement =
            document.getElementById("votifyOtpTimer");

        verifyOtpButton =
            document.getElementById("votifyVerifyOtp");

        return;

    }


    const modal =
        document.createElement("div");

    modal.id =
        "votifyOtpModal";

    modal.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(0,0,0,0.78);
        backdrop-filter: blur(8px);
    `;


    modal.innerHTML = `

        <div style="
            width:100%;
            max-width:470px;
            background:#111827;
            border:1px solid rgba(99,102,241,0.45);
            border-radius:24px;
            padding:32px;
            box-shadow:0 25px 80px rgba(0,0,0,0.55);
            color:white;
            text-align:center;
        ">

            <div style="
                width:64px;
                height:64px;
                margin:0 auto 18px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                background:rgba(59,130,246,0.16);
                border:1px solid rgba(59,130,246,0.35);
                font-size:30px;
            ">
                ✉️
            </div>


            <h2 style="
                font-size:26px;
                font-weight:700;
                margin-bottom:8px;
            ">
                Verify College Email
            </h2>


            <p id="votifyOtpEmail" style="
                color:#93c5fd;
                font-size:15px;
                margin-bottom:8px;
            ">
                OTP sent to your college email
            </p>


            <p style="
                color:#9ca3af;
                font-size:14px;
                margin-bottom:22px;
            ">
                Enter the 6-digit OTP sent to your email.
            </p>


            <input
                id="votifyOtpInput"
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                placeholder="Enter 6-digit OTP"
                style="
                    width:100%;
                    height:58px;
                    border-radius:14px;
                    border:1px solid #374151;
                    background:#1f2937;
                    color:white;
                    font-size:22px;
                    letter-spacing:8px;
                    text-align:center;
                    outline:none;
                    margin-bottom:12px;
                "
            >


            <div
                id="votifyOtpMessage"
                style="
                    min-height:22px;
                    color:#f87171;
                    font-size:14px;
                    margin-bottom:10px;
                "
            ></div>


            <div style="
                color:#9ca3af;
                font-size:14px;
                margin-bottom:20px;
            ">
                OTP expires in
                <strong
                    id="votifyOtpTimer"
                    style="color:#60a5fa;"
                >
                    05:00
                </strong>
            </div>


            <button
                id="votifyVerifyOtp"
                type="button"
                style="
                    width:100%;
                    height:55px;
                    border:none;
                    border-radius:14px;
                    color:white;
                    font-size:17px;
                    font-weight:700;
                    cursor:pointer;
                    background:linear-gradient(
                        90deg,
                        #2563eb,
                        #9333ea,
                        #ec4899
                    );
                "
            >
                Verify OTP
            </button>


            <button
                id="votifyCloseOtp"
                type="button"
                style="
                    width:100%;
                    margin-top:12px;
                    height:45px;
                    border:none;
                    background:transparent;
                    color:#9ca3af;
                    cursor:pointer;
                    font-size:14px;
                "
            >
                Cancel
            </button>

        </div>
    `;


    document.body.appendChild(modal);


    otpModal = modal;

    otpInput =
        document.getElementById("votifyOtpInput");

    otpMessage =
        document.getElementById("votifyOtpMessage");

    otpTimerElement =
        document.getElementById("votifyOtpTimer");

    verifyOtpButton =
        document.getElementById("votifyVerifyOtp");


    const closeButton =
        document.getElementById("votifyCloseOtp");


    verifyOtpButton.addEventListener(
        "click",
        verifyLoginOtp
    );


    closeButton.addEventListener(
        "click",
        function () {

            closeOtpModal();

        }
    );


    otpInput.addEventListener(
        "input",
        function () {

            this.value =
                this.value
                    .replace(/\D/g, "")
                    .slice(0, 6);

            if (otpMessage) {
                otpMessage.textContent = "";
            }

        }
    );


    otpInput.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Enter") {

                event.preventDefault();

                verifyLoginOtp();

            }

        }
    );

}


/* ==========================================================
   SHOW OTP MODAL
========================================================== */

function showOtpModal(email) {

    createOtpModal();


    const emailElement =
        document.getElementById(
            "votifyOtpEmail"
        );


    if (emailElement) {

        emailElement.textContent =
            email
                ? `OTP sent to ${email}`
                : "OTP sent to your college email";

    }


    if (otpInput) {

        otpInput.value = "";

    }


    if (otpMessage) {

        otpMessage.textContent = "";

    }


    otpModal.style.display =
        "flex";


    startOtpTimer();


    setTimeout(
        function () {

            if (otpInput) {
                otpInput.focus();
            }

        },
        200
    );

}


/* ==========================================================
   CLOSE OTP MODAL
========================================================== */

function closeOtpModal() {

    if (!otpModal) {
        return;
    }

    otpModal.style.display =
        "none";


    stopOtpTimer();

}


/* ==========================================================
   OTP TIMER
========================================================== */

function startOtpTimer() {

    stopOtpTimer();

    otpSeconds = 300;

    updateOtpTimer();


    otpTimer =
        setInterval(
            function () {

                otpSeconds--;

                updateOtpTimer();


                if (otpSeconds <= 0) {

                    stopOtpTimer();

                    if (otpMessage) {

                        otpMessage.textContent =
                            "OTP expired. Please login again to receive a new OTP.";

                    }

                    if (verifyOtpButton) {

                        verifyOtpButton.disabled =
                            true;

                        verifyOtpButton.textContent =
                            "OTP Expired";

                    }

                }

            },
            1000
        );

}


/* ==========================================================
   STOP TIMER
========================================================== */

function stopOtpTimer() {

    if (otpTimer) {

        clearInterval(
            otpTimer
        );

        otpTimer = null;

    }

}


/* ==========================================================
   UPDATE TIMER
========================================================== */

function updateOtpTimer() {

    if (!otpTimerElement) {
        return;
    }

    const minutes =
        Math.floor(
            otpSeconds / 60
        )
        .toString()
        .padStart(2, "0");


    const seconds =
        (
            otpSeconds % 60
        )
        .toString()
        .padStart(2, "0");


    otpTimerElement.textContent =
        `${minutes}:${seconds}`;

}


/* ==========================================================
   VERIFY LOGIN OTP
========================================================== */

async function verifyLoginOtp() {

    if (!otpInput) {
        return;
    }


    const otp =
        clean(
            otpInput.value
        );


    if (!/^\d{6}$/.test(otp)) {

        if (otpMessage) {

            otpMessage.textContent =
                "Please enter a valid 6-digit OTP.";

        }

        otpInput.focus();

        return;

    }


    if (otpSeconds <= 0) {

        if (otpMessage) {

            otpMessage.textContent =
                "OTP expired. Please login again.";

        }

        return;

    }


    if (verifyOtpButton) {

        verifyOtpButton.disabled =
            true;

        verifyOtpButton.textContent =
            "Verifying OTP...";

    }


    const formData =
        new FormData();

    formData.append(
        "action",
        "verify_otp"
    );

    formData.append(
        "otp",
        otp
    );


    try {

        const response =
            await fetch(
                LOGIN_API,
                {
                    method: "POST",
                    body: formData,
                    cache: "no-store",
                    credentials: "same-origin"
                }
            );


        const result =
            await response.json();


        console.log(
            "OTP Verification Response:",
            result
        );


        if (result.success) {

            stopOtpTimer();

            if (otpMessage) {

                otpMessage.style.color =
                    "#4ade80";

                otpMessage.textContent =
                    "OTP verified successfully.";

            }


            if (verifyOtpButton) {

                verifyOtpButton.disabled =
                    true;

                verifyOtpButton.textContent =
                    "Verified ✓";

            }


            showSuccessToast(
                result.message ||
                "Login successful."
            );


            setTimeout(
                function () {

                    window.location.replace(
                        result.redirect ||
                        DASHBOARD_URL
                    );

                },
                800
            );


            return;

        }


        if (otpMessage) {

            otpMessage.style.color =
                "#f87171";

            otpMessage.textContent =
                result.message ||
                "Incorrect OTP.";

        }


        if (verifyOtpButton) {

            verifyOtpButton.disabled =
                false;

            verifyOtpButton.textContent =
                "Verify OTP";

        }


        if (result.otp_expired) {

            stopOtpTimer();

            if (otpTimerElement) {

                otpTimerElement.textContent =
                    "Expired";

            }

        }


    } catch (error) {

        console.error(
            "VOTIFY OTP Verification Error:",
            error
        );


        if (otpMessage) {

            otpMessage.style.color =
                "#f87171";

            otpMessage.textContent =
                "Unable to verify OTP. Please try again.";

        }


        if (verifyOtpButton) {

            verifyOtpButton.disabled =
                false;

            verifyOtpButton.textContent =
                "Verify OTP";

        }

    }

}


/* ==========================================================
   LOGIN FORM SUBMIT
========================================================== */

if (loginForm) {

    loginForm.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();


            clearErrors();


            /* ==============================================
               VALIDATION
            ============================================== */

            if (!validateForm()) {

                return;

            }


            /* ==============================================
               DISABLE BUTTON
            ============================================== */

            disableLoginButton();


            /* ==============================================
               FORM DATA
            ============================================== */

            const formData =
                new FormData();


            formData.append(
                "action",
                "login"
            );


            formData.append(
                "admissionNo",
                clean(
                    admissionInput?.value
                ).toUpperCase()
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


            try {

                const response =
                    await fetch(
                        LOGIN_API,
                        {
                            method: "POST",
                            body: formData,
                            cache: "no-store",
                            credentials: "same-origin"
                        }
                    );


                const result =
                    await response.json();


                console.log(
                    "Login Response:",
                    result
                );


                /* ==========================================
                   ALREADY VOTED
                ========================================== */

                if (
                    isAlreadyVotedResponse(
                        result
                    )
                ) {

                    window.location.replace(
                        ALREADY_VOTED_URL
                    );

                    return;

                }


                /* ==========================================
                   OTP REQUIRED
                ========================================== */

                if (
                    result.success &&
                    (
                        result.otp_required === true ||
                        result.requires_otp === true
                    )
                ) {

                    enableLoginButton();

                    showOtpModal(
                        result.email ||
                        "your college email"
                    );

                    return;

                }


                /* ==========================================
                   DIRECT SUCCESS
                ========================================== */

                if (result.success) {

                    showSuccessToast(
                        result.message ||
                        "Login successful."
                    );


                    setTimeout(
                        function () {

                            window.location.replace(
                                result.redirect ||
                                DASHBOARD_URL
                            );

                        },
                        800
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


            } catch (error) {

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
   AUTO HIDE FIELD ERRORS
========================================================== */

if (admissionInput) {

    admissionInput.addEventListener(
        "input",
        function () {

            hideError(
                admissionError
            );

        }
    );

}


if (dobInput) {

    dobInput.addEventListener(
        "input",
        function () {

            hideError(
                dobError
            );

        }
    );

}


if (emailInput) {

    emailInput.addEventListener(
        "input",
        function () {

            hideError(
                emailError
            );

        }
    );

}


if (passwordInput) {

    passwordInput.addEventListener(
        "input",
        function () {

            hideError(
                passwordError
            );

        }
    );

}


/* ==========================================================
   ENTER KEY
========================================================== */

if (passwordInput) {

    passwordInput.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Enter") {

                event.preventDefault();

                if (loginForm) {
                    loginForm.requestSubmit();
                }

            }

        }
    );

}


/* ==========================================================
   INITIALIZE
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        hideSuccessToast();
        hideErrorToast();

        clearErrors();

        enableLoginButton();

        if (admissionInput) {
            admissionInput.focus();
        }

        console.log(
            "VOTIFY Student Login Initialized"
        );

    }
);