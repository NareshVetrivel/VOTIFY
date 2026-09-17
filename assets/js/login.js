/* ==========================================================
   VOTIFY
   Student Login JavaScript
   File : assets/js/login.js

   FLOW:

   Login Form
       ↓
   backend/student/login.php
       ↓
   OTP sent
       ↓
   OTP modal
       ↓
   Verify OTP
       ↓
   Student Dashboard
========================================================== */

"use strict";


/* ==========================================================
   CONFIGURATION
========================================================== */

const LOGIN_API =
    "../../backend/student/login.php";


const DASHBOARD_URL =
    "/VOTIFY/pages/student/student_dashboard.php";


let otpTimer = null;


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "VOTIFY login.js loaded successfully."
        );


        initializeLoginForm();

    }
);


/* ==========================================================
   FIND LOGIN FORM
========================================================== */

function initializeLoginForm() {

    const form =
        document.querySelector(
            "#studentLoginForm"
        ) ||
        document.querySelector(
            'form[data-login-form]'
        ) ||
        document.querySelector(
            "form"
        );


    if (!form) {

        console.error(
            "VOTIFY: Student login form not found."
        );

        return;
    }


    form.addEventListener(
        "submit",
        handleLogin
    );
}


/* ==========================================================
   LOGIN
========================================================== */

async function handleLogin(event) {

    event.preventDefault();


    const form =
        event.currentTarget;


    const button =
        form.querySelector(
            'button[type="submit"]'
        );


    /* ------------------------------------------------------
       GET INPUTS
    ------------------------------------------------------ */

    const admissionInput =
        form.querySelector(
            '[name="admissionNo"]'
        ) ||
        document.querySelector(
            "#admissionNo"
        );


    const dobInput =
        form.querySelector(
            '[name="dob"]'
        ) ||
        document.querySelector(
            "#dob"
        );


    const emailInput =
        form.querySelector(
            '[name="collegeEmail"]'
        ) ||
        document.querySelector(
            "#collegeEmail"
        );


    const passwordInput =
        form.querySelector(
            '[name="password"]'
        ) ||
        document.querySelector(
            "#password"
        );


    if (
        !admissionInput ||
        !dobInput ||
        !emailInput ||
        !passwordInput
    ) {

        showMessage(
            "Login form fields are missing. Please check student_login.php.",
            "error"
        );

        return;
    }


    /* ------------------------------------------------------
       VALUES
    ------------------------------------------------------ */

    const admissionNo =
        admissionInput.value
            .trim()
            .toUpperCase();


    const dob =
        dobInput.value.trim();


    const collegeEmail =
        emailInput.value
            .trim()
            .toLowerCase();


    const password =
        passwordInput.value;


    /* ------------------------------------------------------
       VALIDATION
    ------------------------------------------------------ */

    if (!admissionNo) {

        showMessage(
            "Please enter your Admission Number.",
            "error"
        );

        admissionInput.focus();

        return;
    }


    if (!dob) {

        showMessage(
            "Please enter your Date of Birth.",
            "error"
        );

        dobInput.focus();

        return;
    }


    if (!collegeEmail) {

        showMessage(
            "Please enter your College Email.",
            "error"
        );

        emailInput.focus();

        return;
    }


    if (!password) {

        showMessage(
            "Please enter your Password.",
            "error"
        );

        passwordInput.focus();

        return;
    }


    /* ------------------------------------------------------
       BUTTON LOADING
    ------------------------------------------------------ */

    setButtonLoading(
        button,
        true,
        "Logging in..."
    );


    /* ------------------------------------------------------
       FORM DATA
    ------------------------------------------------------ */

    const formData =
        new FormData();


    formData.append(
        "action",
        "login"
    );


    formData.append(
        "admissionNo",
        admissionNo
    );


    formData.append(
        "dob",
        dob
    );


    formData.append(
        "collegeEmail",
        collegeEmail
    );


    formData.append(
        "password",
        password
    );


    /* ------------------------------------------------------
       SEND REQUEST
    ------------------------------------------------------ */

    try {

        const response =
            await fetch(
                LOGIN_API,
                {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin",
                    headers: {
                        "Accept":
                            "application/json"
                    }
                }
            );


        const text =
            await response.text();


        console.log(
            "LOGIN RESPONSE:",
            text
        );


        let data;


        try {

            data =
                JSON.parse(text);

        } catch (jsonError) {

            console.error(
                "Invalid JSON response:",
                text
            );


            showMessage(
                "Server returned an invalid response. Check PHP error log.",
                "error"
            );


            return;
        }


        /* --------------------------------------------------
           LOGIN FAILED
        -------------------------------------------------- */

        if (!data.success) {

            showMessage(
                data.message ||
                "Login failed.",
                "error"
            );


            return;
        }


        /* --------------------------------------------------
           OTP REQUIRED
        -------------------------------------------------- */

        if (
            data.otp_required === true ||
            data.requires_otp === true
        ) {

            showOtpModal(
                data.email ||
                collegeEmail,
                data.otp_expires_in ||
                300
            );


            return;
        }


        /* --------------------------------------------------
           DIRECT LOGIN
           (Fallback)
        -------------------------------------------------- */

        if (data.redirect) {

            window.location.href =
                data.redirect;

        } else {

            window.location.href =
                DASHBOARD_URL;
        }


    } catch (error) {

        console.error(
            "LOGIN FETCH ERROR:",
            error
        );


        showMessage(
            "Unable to connect to login service. Please try again.",
            "error"
        );


    } finally {

        setButtonLoading(
            button,
            false,
            "Login"
        );
    }
}


/* ==========================================================
   OTP MODAL
========================================================== */

function showOtpModal(
    email,
    seconds
) {

    removeExistingOtpModal();


    const modal =
        document.createElement(
            "div"
        );


    modal.id =
        "votifyLoginOtpModal";


    modal.innerHTML = `

        <div class="votify-otp-overlay">

            <div class="votify-otp-box">

                <button
                    type="button"
                    class="votify-otp-close"
                    id="votifyOtpClose"
                >
                    &times;
                </button>


                <div class="votify-otp-icon">
                    ✉
                </div>


                <h2>
                    Verify College Email
                </h2>


                <p class="votify-otp-description">

                    We sent a 6-digit OTP to

                </p>


                <div class="votify-otp-email">

                    ${escapeHtml(email)}

                </div>


                <form
                    id="votifyOtpForm"
                    autocomplete="off"
                >

                    <input
                        type="text"
                        id="votifyLoginOtp"
                        name="otp"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        placeholder="Enter 6-digit OTP"
                        autocomplete="one-time-code"
                        class="votify-otp-input"
                    >


                    <div
                        id="votifyOtpMessage"
                        class="votify-otp-message"
                    ></div>


                    <button
                        type="submit"
                        id="votifyVerifyOtpButton"
                        class="votify-otp-button"
                    >
                        Verify OTP
                    </button>


                    <div
                        id="votifyOtpTimer"
                        class="votify-otp-timer"
                    ></div>


                    <button
                        type="button"
                        id="votifyResendOtp"
                        class="votify-resend-button"
                    >
                        Resend OTP
                    </button>

                </form>

            </div>

        </div>
    `;


    document.body.appendChild(
        modal
    );


    addOtpStyles();


    /* ------------------------------------------------------
       ELEMENTS
    ------------------------------------------------------ */

    const otpInput =
        document.querySelector(
            "#votifyLoginOtp"
        );


    const otpForm =
        document.querySelector(
            "#votifyOtpForm"
        );


    const closeButton =
        document.querySelector(
            "#votifyOtpClose"
        );


    const resendButton =
        document.querySelector(
            "#votifyResendOtp"
        );


    /* ------------------------------------------------------
       OTP INPUT
    ------------------------------------------------------ */

    otpInput.addEventListener(
        "input",
        function () {

            this.value =
                this.value
                    .replace(
                        /[^0-9]/g,
                        ""
                    )
                    .slice(
                        0,
                        6
                    );
        }
    );


    /* ------------------------------------------------------
       SUBMIT OTP
    ------------------------------------------------------ */

    otpForm.addEventListener(
        "submit",
        verifyLoginOtp
    );


    /* ------------------------------------------------------
       CLOSE
    ------------------------------------------------------ */

    closeButton.addEventListener(
        "click",
        function () {

            clearOtpTimer();

            removeExistingOtpModal();

        }
    );


    /* ------------------------------------------------------
       RESEND
    ------------------------------------------------------ */

    resendButton.addEventListener(
        "click",
        resendLoginOtp
    );


    /* ------------------------------------------------------
       START TIMER
    ------------------------------------------------------ */

    startOtpTimer(
        seconds
    );


    /* ------------------------------------------------------
       FOCUS
    ------------------------------------------------------ */

    setTimeout(
        function () {

            otpInput.focus();

        },
        100
    );
}


/* ==========================================================
   VERIFY OTP
========================================================== */

async function verifyLoginOtp(event) {

    event.preventDefault();


    const otpInput =
        document.querySelector(
            "#votifyLoginOtp"
        );


    const button =
        document.querySelector(
            "#votifyVerifyOtpButton"
        );


    const message =
        document.querySelector(
            "#votifyOtpMessage"
        );


    if (!otpInput) {

        return;
    }


    const otp =
        otpInput.value.trim();


    /* ------------------------------------------------------
       VALIDATE OTP
    ------------------------------------------------------ */

    if (
        !/^[0-9]{6}$/.test(
            otp
        )
    ) {

        showOtpMessage(
            "Please enter the 6-digit OTP.",
            "error"
        );


        otpInput.focus();

        return;
    }


    /* ------------------------------------------------------
       LOADING
    ------------------------------------------------------ */

    button.disabled = true;

    button.textContent =
        "Verifying...";


    message.textContent =
        "";


    /* ------------------------------------------------------
       REQUEST
    ------------------------------------------------------ */

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
                    credentials: "same-origin",
                    headers: {
                        "Accept":
                            "application/json"
                    }
                }
            );


        const text =
            await response.text();


        console.log(
            "OTP RESPONSE:",
            text
        );


        let data;


        try {

            data =
                JSON.parse(text);

        } catch (error) {

            showOtpMessage(
                "Invalid server response.",
                "error"
            );


            return;
        }


        /* --------------------------------------------------
           OTP FAILED
        -------------------------------------------------- */

        if (!data.success) {

            showOtpMessage(
                data.message ||
                "OTP verification failed.",
                "error"
            );


            if (
                data.otp_expired === true
            ) {

                const resend =
                    document.querySelector(
                        "#votifyResendOtp"
                    );


                if (resend) {

                    resend.style.display =
                        "block";
                }
            }


            return;
        }


        /* --------------------------------------------------
           OTP SUCCESS
        -------------------------------------------------- */

        showOtpMessage(
            "OTP verified successfully. Logging you in...",
            "success"
        );


        button.textContent =
            "Verified ✓";


        /* --------------------------------------------------
           REDIRECT
        -------------------------------------------------- */

        setTimeout(
            function () {

                window.location.href =
                    data.redirect ||
                    DASHBOARD_URL;

            },
            500
        );


    } catch (error) {

        console.error(
            "OTP VERIFY ERROR:",
            error
        );


        showOtpMessage(
            "Unable to verify OTP. Please try again.",
            "error"
        );


    } finally {

        button.disabled = false;

        if (
            button.textContent ===
            "Verifying..."
        ) {

            button.textContent =
                "Verify OTP";
        }
    }
}


/* ==========================================================
   RESEND OTP
========================================================== */

async function resendLoginOtp() {

    /*
     * Resend uses the same login details
     * from the original form.
     */

    const form =
        document.querySelector(
            "#studentLoginForm"
        ) ||
        document.querySelector(
            "form"
        );


    if (!form) {

        return;
    }


    const admissionInput =
        form.querySelector(
            '[name="admissionNo"]'
        ) ||
        document.querySelector(
            "#admissionNo"
        );


    const dobInput =
        form.querySelector(
            '[name="dob"]'
        ) ||
        document.querySelector(
            "#dob"
        );


    const emailInput =
        form.querySelector(
            '[name="collegeEmail"]'
        ) ||
        document.querySelector(
            "#collegeEmail"
        );


    const passwordInput =
        form.querySelector(
            '[name="password"]'
        ) ||
        document.querySelector(
            "#password"
        );


    if (
        !admissionInput ||
        !dobInput ||
        !emailInput ||
        !passwordInput
    ) {

        showOtpMessage(
            "Login details are missing.",
            "error"
        );

        return;
    }


    const button =
        document.querySelector(
            "#votifyResendOtp"
        );


    button.disabled = true;

    button.textContent =
        "Sending...";


    const formData =
        new FormData();


    formData.append(
        "action",
        "login"
    );


    formData.append(
        "admissionNo",
        admissionInput.value
            .trim()
            .toUpperCase()
    );


    formData.append(
        "dob",
        dobInput.value.trim()
    );


    formData.append(
        "collegeEmail",
        emailInput.value
            .trim()
            .toLowerCase()
    );


    formData.append(
        "password",
        passwordInput.value
    );


    try {

        const response =
            await fetch(
                LOGIN_API,
                {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin",
                    headers: {
                        "Accept":
                            "application/json"
                    }
                }
            );


        const text =
            await response.text();


        let data;


        try {

            data =
                JSON.parse(text);

        } catch (error) {

            showOtpMessage(
                "Invalid server response.",
                "error"
            );

            return;
        }


        if (!data.success) {

            showOtpMessage(
                data.message ||
                "Unable to resend OTP.",
                "error"
            );


            return;
        }


        showOtpMessage(
            "New OTP sent to your college email.",
            "success"
        );


        startOtpTimer(
            data.otp_expires_in ||
            300
        );


        const otpInput =
            document.querySelector(
                "#votifyLoginOtp"
            );


        if (otpInput) {

            otpInput.value =
                "";

            otpInput.focus();
        }


    } catch (error) {

        console.error(
            "RESEND OTP ERROR:",
            error
        );


        showOtpMessage(
            "Unable to send OTP. Please try again.",
            "error"
        );


    } finally {

        button.disabled = false;

        button.textContent =
            "Resend OTP";
    }
}


/* ==========================================================
   OTP TIMER
========================================================== */

function startOtpTimer(
    seconds
) {

    clearOtpTimer();


    let remaining =
        parseInt(
            seconds,
            10
        );


    const timer =
        document.querySelector(
            "#votifyOtpTimer"
        );


    if (!timer) {

        return;
    }


    otpTimer =
        setInterval(
            function () {

                if (
                    remaining <= 0
                ) {

                    clearOtpTimer();


                    timer.textContent =
                        "OTP expired. Please resend OTP.";


                    timer.classList.add(
                        "expired"
                    );


                    return;
                }


                const minutes =
                    Math.floor(
                        remaining / 60
                    );


                const secs =
                    remaining % 60;


                timer.textContent =
                    "OTP expires in " +
                    minutes +
                    ":" +
                    String(secs)
                        .padStart(
                            2,
                            "0"
                        );


                remaining--;

            },
            1000
        );
}


/* ==========================================================
   CLEAR TIMER
========================================================== */

function clearOtpTimer() {

    if (otpTimer) {

        clearInterval(
            otpTimer
        );

        otpTimer = null;
    }
}


/* ==========================================================
   OTP MESSAGE
========================================================== */

function showOtpMessage(
    message,
    type
) {

    const element =
        document.querySelector(
            "#votifyOtpMessage"
        );


    if (!element) {

        return;
    }


    element.textContent =
        message;


    element.className =
        "votify-otp-message " +
        (type || "");
}


/* ==========================================================
   NORMAL PAGE MESSAGE
========================================================== */

function showMessage(
    message,
    type
) {

    let element =
        document.querySelector(
            "#loginMessage"
        );


    if (!element) {

        element =
            document.createElement(
                "div"
            );


        element.id =
            "loginMessage";


        const form =
            document.querySelector(
                "#studentLoginForm"
            ) ||
            document.querySelector(
                "form"
            );


        if (form) {

            form.prepend(
                element
            );

        } else {

            document.body.prepend(
                element
            );
        }
    }


    element.textContent =
        message;


    element.className =
        "login-message " +
        (type || "");
}


/* ==========================================================
   BUTTON LOADING
========================================================== */

function setButtonLoading(
    button,
    loading,
    text
) {

    if (!button) {

        return;
    }


    if (loading) {

        button.disabled =
            true;


        button.dataset.originalText =
            button.textContent;


        button.textContent =
            text;

    } else {

        button.disabled =
            false;


        button.textContent =
            button.dataset.originalText ||
            text;
    }
}


/* ==========================================================
   REMOVE OTP MODAL
========================================================== */

function removeExistingOtpModal() {

    const existing =
        document.querySelector(
            "#votifyLoginOtpModal"
        );


    if (existing) {

        existing.remove();
    }
}


/* ==========================================================
   ESCAPE HTML
========================================================== */

function escapeHtml(
    value
) {

    return String(value)
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );
}


/* ==========================================================
   OTP CSS
========================================================== */

function addOtpStyles() {

    if (
        document.querySelector(
            "#votifyOtpStyles"
        )
    ) {

        return;
    }


    const style =
        document.createElement(
            "style"
        );


    style.id =
        "votifyOtpStyles";


    style.textContent = `

        .votify-otp-overlay {

            position: fixed;

            inset: 0;

            z-index: 99999;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                rgba(0, 0, 0, 0.75);

            backdrop-filter:
                blur(8px);

            padding: 20px;
        }


        .votify-otp-box {

            position: relative;

            width: 100%;

            max-width: 470px;

            padding: 35px;

            border-radius: 20px;

            background:
                #111827;

            border:
                1px solid
                rgba(255,255,255,0.12);

            box-shadow:
                0 25px 80px
                rgba(0,0,0,0.45);

            text-align: center;

            color: white;
        }


        .votify-otp-close {

            position: absolute;

            right: 18px;

            top: 14px;

            width: 35px;

            height: 35px;

            border: none;

            background: transparent;

            color: #aaa;

            font-size: 28px;

            cursor: pointer;
        }


        .votify-otp-icon {

            width: 70px;

            height: 70px;

            margin: 0 auto 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background:
                rgba(59,130,246,0.15);

            border:
                1px solid
                rgba(59,130,246,0.4);

            font-size: 32px;
        }


        .votify-otp-box h2 {

            margin: 0 0 12px;

            font-size: 27px;
        }


        .votify-otp-description {

            margin: 0 0 8px;

            color: #aab4c8;

        }


        .votify-otp-email {

            margin-bottom: 22px;

            color: #60a5fa;

            font-weight: 600;

            word-break: break-all;
        }


        .votify-otp-input {

            width: 100%;

            box-sizing: border-box;

            padding: 16px;

            border-radius: 12px;

            border:
                1px solid #334155;

            background:
                #1e293b;

            color: white;

            font-size: 24px;

            letter-spacing: 8px;

            text-align: center;

            outline: none;
        }


        .votify-otp-input:focus {

            border-color:
                #3b82f6;

            box-shadow:
                0 0 0 3px
                rgba(59,130,246,0.2);
        }


        .votify-otp-button {

            width: 100%;

            margin-top: 16px;

            padding: 14px;

            border: none;

            border-radius: 10px;

            background:
                linear-gradient(
                    90deg,
                    #2563eb,
                    #d946ef
                );

            color: white;

            font-size: 16px;

            font-weight: 700;

            cursor: pointer;
        }


        .votify-otp-button:disabled {

            opacity: 0.65;

            cursor: not-allowed;
        }


        .votify-otp-timer {

            margin-top: 15px;

            color: #94a3b8;

            font-size: 14px;
        }


        .votify-otp-timer.expired {

            color: #f87171;
        }


        .votify-resend-button {

            margin-top: 12px;

            padding: 8px 15px;

            border: none;

            background: transparent;

            color: #60a5fa;

            cursor: pointer;

            font-size: 14px;
        }


        .votify-resend-button:disabled {

            opacity: 0.5;

            cursor: not-allowed;
        }


        .votify-otp-message {

            min-height: 22px;

            margin-top: 12px;

            font-size: 14px;

        }


        .votify-otp-message.error {

            color: #f87171;
        }


        .votify-otp-message.success {

            color: #4ade80;
        }


        .login-message {

            margin-bottom: 15px;

            padding: 12px;

            border-radius: 8px;

            font-size: 14px;

        }


        .login-message.error {

            color: #fecaca;

            background:
                rgba(239,68,68,0.12);

            border:
                1px solid
                rgba(239,68,68,0.25);
        }

    `;


    document.head.appendChild(
        style
    );
}