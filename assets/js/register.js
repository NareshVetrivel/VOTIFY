/*
==========================================================
 VOTIFY - Student Registration + OTP Verification
 File: assets/js/register.js
==========================================================
*/

document.addEventListener("DOMContentLoaded", () => {
    createOtpModal();

    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", registerStudent);
    }

    initializeInputs();
    initializePasswordToggle();
});


/* ======================================================
   GENERAL HELPERS
====================================================== */

function showToast(message, type = "info") {
    let toastContainer = document.getElementById("toastContainer");

    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "toastContainer";

        toastContainer.style.position = "fixed";
        toastContainer.style.top = "20px";
        toastContainer.style.right = "20px";
        toastContainer.style.zIndex = "99999";

        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement("div");

    toast.className = `toast-message ${type}`;

    toast.textContent = message;

    toast.style.padding = "14px 20px";
    toast.style.marginBottom = "10px";
    toast.style.borderRadius = "10px";
    toast.style.color = "#ffffff";
    toast.style.fontSize = "14px";
    toast.style.fontWeight = "500";
    toast.style.boxShadow = "0 10px 30px rgba(0,0,0,0.3)";
    toast.style.backdropFilter = "blur(10px)";

    if (type === "success") {
        toast.style.background = "rgba(16,185,129,0.95)";
    } else if (type === "error") {
        toast.style.background = "rgba(239,68,68,0.95)";
    } else if (type === "warning") {
        toast.style.background = "rgba(245,158,11,0.95)";
    } else {
        toast.style.background = "rgba(59,130,246,0.95)";
    }

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(20px)";
        toast.style.transition = "all 0.3s ease";

        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}


/* ======================================================
   INPUT INITIALIZATION
====================================================== */

function initializeInputs() {

    const inputs = document.querySelectorAll(
        "input, select, textarea"
    );

    inputs.forEach(input => {

        input.addEventListener("focus", () => {
            input.classList.add("input-focused");
        });

        input.addEventListener("blur", () => {
            input.classList.remove("input-focused");
        });

    });
}


/* ======================================================
   PASSWORD TOGGLE
====================================================== */

function initializePasswordToggle() {

    const toggleButtons = document.querySelectorAll(
        ".password-toggle"
    );

    toggleButtons.forEach(button => {

        button.addEventListener("click", () => {

            const targetId = button.dataset.target;

            const input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            if (input.type === "password") {

                input.type = "text";

                button.classList.add("active");

            } else {

                input.type = "password";

                button.classList.remove("active");

            }

        });

    });
}


/* ======================================================
   VALIDATION HELPERS
====================================================== */

function validateRequired(value) {

    return (
        value !== null &&
        value !== undefined &&
        String(value).trim() !== ""
    );
}


function validateEmail(email) {

    if (!email) {
        return false;
    }

    const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return emailPattern.test(email);
}


function validatePassword(password) {

    if (!password) {
        return false;
    }

    return password.length >= 6;
}


function setFieldError(field, message) {

    if (!field) {
        return;
    }

    field.classList.add("error");

    let errorElement =
        field.parentElement.querySelector(
            ".field-error"
        );

    if (!errorElement) {

        errorElement = document.createElement("small");

        errorElement.className =
            "field-error";

        errorElement.style.display = "block";
        errorElement.style.marginTop = "5px";
        errorElement.style.color = "#ef4444";

        field.parentElement.appendChild(
            errorElement
        );
    }

    errorElement.textContent = message;
}


function clearFieldError(field) {

    if (!field) {
        return;
    }

    field.classList.remove("error");

    const errorElement =
        field.parentElement.querySelector(
            ".field-error"
        );

    if (errorElement) {
        errorElement.remove();
    }
}


/* ======================================================
   REGISTER STUDENT
====================================================== */

async function registerStudent(event) {

    event.preventDefault();

    const form = event.target;

    if (!form) {
        return;
    }

    const formData = new FormData(form);

    const nameField =
        document.getElementById("name");

    const emailField =
        document.getElementById("email");

    const registerNumberField =
        document.getElementById("registerNumber");

    const departmentField =
        document.getElementById("department");

    const passwordField =
        document.getElementById("password");

    const confirmPasswordField =
        document.getElementById("confirmPassword");


    const nameValue =
        nameField
            ? nameField.value.trim()
            : "";

    const emailValue =
        emailField
            ? emailField.value.trim()
            : "";

    const registerNumberValue =
        registerNumberField
            ? registerNumberField.value.trim()
            : "";

    const departmentValue =
        departmentField
            ? departmentField.value.trim()
            : "";

    const passwordValue =
        passwordField
            ? passwordField.value
            : "";

    const confirmPasswordValue =
        confirmPasswordField
            ? confirmPasswordField.value
            : "";


    /* --------------------------------------------------
       VALIDATION
    -------------------------------------------------- */

    let isValid = true;


    if (!validateRequired(nameValue)) {

        setFieldError(
            nameField,
            "Name is required"
        );

        isValid = false;

    } else {

        clearFieldError(nameField);

    }


    if (!validateRequired(emailValue)) {

        setFieldError(
            emailField,
            "Email is required"
        );

        isValid = false;

    } else if (!validateEmail(emailValue)) {

        setFieldError(
            emailField,
            "Enter a valid email address"
        );

        isValid = false;

    } else {

        clearFieldError(emailField);

    }


    if (
        registerNumberField &&
        !validateRequired(registerNumberValue)
    ) {

        setFieldError(
            registerNumberField,
            "Register number is required"
        );

        isValid = false;

    } else {

        clearFieldError(registerNumberField);

    }


    if (
        departmentField &&
        !validateRequired(departmentValue)
    ) {

        setFieldError(
            departmentField,
            "Department is required"
        );

        isValid = false;

    } else {

        clearFieldError(departmentField);

    }


    if (!validateRequired(passwordValue)) {

        setFieldError(
            passwordField,
            "Password is required"
        );

        isValid = false;

    } else if (!validatePassword(passwordValue)) {

        setFieldError(
            passwordField,
            "Password must contain at least 6 characters"
        );

        isValid = false;

    } else {

        clearFieldError(passwordField);

    }


    if (
        passwordValue !==
        confirmPasswordValue
    ) {

        setFieldError(
            confirmPasswordField,
            "Passwords do not match"
        );

        isValid = false;

    } else {

        clearFieldError(confirmPasswordField);

    }


    if (!isValid) {
        return;
    }


    /* --------------------------------------------------
       SUBMIT BUTTON
    -------------------------------------------------- */

    const submitButton =
        form.querySelector(
            'button[type="submit"]'
        );

    const originalButtonText =
        submitButton
            ? submitButton.innerHTML
            : "";


    if (submitButton) {

        submitButton.disabled = true;

        submitButton.innerHTML =
            "Creating Account...";

    }


    try {

        const response =
            await fetch(
                "../../backend/student/register.php",
                {
                    method: "POST",
                    body: formData
                }
            );


        const result =
            await response.json();


        if (
            result.status ===
            "otp_required"
        ) {

            showOtpModal(
                result.email ||
                emailValue
            );

            return;
        }


        if (
            result.status ===
            "success"
        ) {

            showToast(
                result.message ||
                "Registration successful",
                "success"
            );


            setTimeout(() => {

                window.location.href =
                    result.redirect ||
                    "../login.html";

            }, 1200);


            return;
        }


        showToast(
            result.message ||
            "Registration failed",
            "error"
        );


    } catch (error) {

        console.error(
            "Registration Error:",
            error
        );

        showToast(
            "Unable to connect to server",
            "error"
        );

    } finally {

        if (submitButton) {

            submitButton.disabled = false;

            submitButton.innerHTML =
                originalButtonText;

        }

    }
}


/* ======================================================
   EMAIL MASKING
====================================================== */

function maskEmail(email) {

    if (
        !email ||
        !email.includes("@")
    ) {
        return email;
    }


    const parts =
        email.split("@");


    const username =
        parts[0];

    const domain =
        parts[1];


    if (
        username.length <= 4
    ) {

        return (
            username.charAt(0) +
            "*".repeat(
                Math.max(
                    username.length - 1,
                    1
                )
            ) +
            "@" +
            domain
        );

    }


    const visibleStart =
        username.slice(0, 2);


    const visibleEnd =
        username.slice(-2);


    const maskedLength =
        Math.max(
            username.length - 4,
            4
        );


    return (
        visibleStart +
        "*".repeat(maskedLength) +
        visibleEnd +
        "@" +
        domain
    );
}


/* ======================================================
   OTP VARIABLES
====================================================== */

let otpModal = null;

let otpTimerInterval = null;

let resendTimerInterval = null;

let otpRemainingSeconds = 300;

let resendRemainingSeconds = 30;


/* ======================================================
   CREATE OTP MODAL
====================================================== */

function createOtpModal() {

    if (
        document.getElementById(
            "registrationOtpModal"
        )
    ) {
        otpModal =
            document.getElementById(
                "registrationOtpModal"
            );

        return;
    }


    const modal =
        document.createElement("div");


    modal.id =
        "registrationOtpModal";


    modal.innerHTML = `

        <div class="otp-modal-overlay">

            <div class="otp-modal-card">

                <button
                    type="button"
                    class="otp-close-button"
                    id="otpCloseButton"
                    aria-label="Close"
                >
                    &times;
                </button>


                <div class="otp-mail-icon">

                    <span>✉</span>

                </div>


                <h2>
                    Verify College Email
                </h2>


                <p class="otp-description">

                    We sent a 6-digit OTP to

                </p>


                <p
                    class="otp-email"
                    id="otpEmailDisplay"
                >
                    your college email
                </p>


                <div class="otp-input-wrapper">

                    <input
                        type="text"
                        id="registrationOtp"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="Enter 6-digit OTP"
                    >

                </div>


                <button
                    type="button"
                    id="verifyOtpButton"
                    class="verify-otp-button"
                >

                    <span class="verify-icon">
                        🛡
                    </span>

                    Verify OTP

                </button>


                <div class="otp-resend-row">

                    <span>
                        Didn't receive OTP?
                    </span>

                    <button
                        type="button"
                        id="resendOtpButton"
                        class="resend-otp-button"
                    >
                        Resend OTP
                    </button>

                </div>


                <p class="otp-outlook-message">

                    Check your Outlook inbox and
                    Junk folder

                </p>


                <p
                    class="otp-expiry"
                    id="otpExpiryText"
                >
                    OTP expires in 05:00
                </p>

            </div>

        </div>

    `;


    document.body.appendChild(modal);


    otpModal = modal;


    /* ==================================================
       MODAL STYLES
    ================================================== */

    if (
        !document.getElementById(
            "registrationOtpStyles"
        )
    ) {

        const style =
            document.createElement("style");


        style.id =
            "registrationOtpStyles";


        style.textContent = `

            #registrationOtpModal {

                position: fixed;

                inset: 0;

                z-index: 999999;

                display: none;

                font-family:
                    Inter,
                    system-ui,
                    -apple-system,
                    BlinkMacSystemFont,
                    "Segoe UI",
                    sans-serif;

            }


            .otp-modal-overlay {

                width: 100%;

                height: 100%;

                display: flex;

                align-items: center;

                justify-content: center;

                padding: 20px;

                background:
                    rgba(0, 0, 0, 0.72);

                backdrop-filter:
                    blur(10px);

                -webkit-backdrop-filter:
                    blur(10px);

            }


            .otp-modal-card {

                position: relative;

                width: min(
                    440px,
                    100%
                );

                padding:
                    34px 32px 30px;

                border-radius: 24px;

                text-align: center;

                background:
                    linear-gradient(
                        145deg,
                        rgba(20, 29, 50, 0.98),
                        rgba(11, 18, 35, 0.98)
                    );

                border:
                    1px solid
                    rgba(255,255,255,0.08);

                box-shadow:
                    0 30px 80px
                    rgba(0,0,0,0.55);

                color: #ffffff;

                animation:
                    otpModalAppear
                    0.25s ease-out;

            }


            @keyframes otpModalAppear {

                from {

                    opacity: 0;

                    transform:
                        translateY(15px)
                        scale(0.97);

                }

                to {

                    opacity: 1;

                    transform:
                        translateY(0)
                        scale(1);

                }

            }


            .otp-close-button {

                position: absolute;

                top: 14px;

                right: 17px;

                width: 34px;

                height: 34px;

                border: none;

                border-radius: 50%;

                background:
                    rgba(255,255,255,0.06);

                color:
                    rgba(255,255,255,0.7);

                font-size: 24px;

                line-height: 1;

                cursor: pointer;

                transition:
                    all 0.2s ease;

            }


            .otp-close-button:hover {

                background:
                    rgba(255,255,255,0.12);

                color: #ffffff;

            }


            .otp-mail-icon {

                width: 62px;

                height: 62px;

                margin:
                    2px auto 18px;

                display: flex;

                align-items: center;

                justify-content: center;

                border-radius: 18px;

                background:
                    rgba(59,130,246,0.13);

                border:
                    1px solid
                    rgba(59,130,246,0.2);

            }


            .otp-mail-icon span {

                font-size: 30px;

                color: #60a5fa;

                filter:
                    drop-shadow(
                        0 0 12px
                        rgba(96,165,250,0.35)
                    );

            }


            .otp-modal-card h2 {

                margin: 0;

                font-size: 25px;

                line-height: 1.25;

                font-weight: 700;

                letter-spacing: -0.3px;

            }


            .otp-description {

                margin:
                    12px 0 4px;

                color:
                    rgba(255,255,255,0.68);

                font-size: 14px;

                line-height: 1.5;

            }


            .otp-email {

                margin:
                    0 0 22px;

                color: #60a5fa;

                font-size: 14px;

                font-weight: 600;

                word-break: break-all;

            }


            .otp-input-wrapper {

                width: 100%;

                margin-bottom: 14px;

            }


            #registrationOtp {

                width: 100%;

                height: 52px;

                box-sizing: border-box;

                border-radius: 12px;

                border:
                    1px solid
                    rgba(255,255,255,0.10);

                outline: none;

                background:
                    rgba(255,255,255,0.045);

                color: #ffffff;

                text-align: center;

                font-size: 17px;

                font-weight: 600;

                letter-spacing: 3px;

                transition:
                    border-color 0.2s ease,
                    box-shadow 0.2s ease,
                    background 0.2s ease;

            }


            #registrationOtp::placeholder {

                color:
                    rgba(255,255,255,0.42);

                letter-spacing: 0;

                font-weight: 400;

            }


            #registrationOtp:focus {

                border-color:
                    rgba(96,165,250,0.75);

                background:
                    rgba(255,255,255,0.065);

                box-shadow:
                    0 0 0 3px
                    rgba(59,130,246,0.12);

            }


            .verify-otp-button {

                width: 100%;

                height: 52px;

                border: none;

                border-radius: 12px;

                color: #ffffff;

                font-size: 15px;

                font-weight: 700;

                cursor: pointer;

                background:
                    linear-gradient(
                        100deg,
                        #2563eb,
                        #7c3aed,
                        #ec4899
                    );

                box-shadow:
                    0 10px 28px
                    rgba(99,102,241,0.25);

                transition:
                    transform 0.2s ease,
                    box-shadow 0.2s ease,
                    opacity 0.2s ease;

            }


            .verify-otp-button:hover {

                transform:
                    translateY(-1px);

                box-shadow:
                    0 14px 32px
                    rgba(99,102,241,0.35);

            }


            .verify-otp-button:disabled {

                cursor: not-allowed;

                opacity: 0.65;

                transform: none;

            }


            .verify-icon {

                margin-right: 7px;

            }


            .otp-resend-row {

                display: flex;

                align-items: center;

                justify-content: center;

                gap: 5px;

                margin-top: 18px;

                font-size: 13px;

                color:
                    rgba(255,255,255,0.58);

            }


            .resend-otp-button {

                padding: 0;

                border: none;

                background: transparent;

                color: #60a5fa;

                font-size: 13px;

                font-weight: 600;

                cursor: pointer;

            }


            .resend-otp-button:hover {

                text-decoration: underline;

            }


            .resend-otp-button:disabled {

                color:
                    rgba(255,255,255,0.38);

                cursor: not-allowed;

                text-decoration: none;

            }


            .otp-outlook-message {

                margin:
                    15px 0 0;

                color:
                    rgba(255,255,255,0.55);

                font-size: 12px;

                line-height: 1.5;

            }


            .otp-expiry {

                margin:
                    13px 0 0;

                color:
                    rgba(255,255,255,0.42);

                font-size: 12px;

            }


            @media (max-width: 480px) {

                .otp-modal-overlay {

                    padding: 14px;

                }


                .otp-modal-card {

                    padding:
                        30px 22px 25px;

                    border-radius: 20px;

                }


                .otp-modal-card h2 {

                    font-size: 22px;

                }


                .otp-mail-icon {

                    width: 56px;

                    height: 56px;

                }

            }

        `;


        document.head.appendChild(style);

    }


    /* ==================================================
       EVENT HANDLERS
    ================================================== */

    const closeButton =
        document.getElementById(
            "otpCloseButton"
        );


    if (closeButton) {

        closeButton.addEventListener(
            "click",
            closeOtpModal
        );

    }


    const verifyButton =
        document.getElementById(
            "verifyOtpButton"
        );


    if (verifyButton) {

        verifyButton.addEventListener(
            "click",
            verifyRegistrationOtp
        );

    }


    const resendButton =
        document.getElementById(
            "resendOtpButton"
        );


    if (resendButton) {

        resendButton.addEventListener(
            "click",
            resendRegistrationOtp
        );

    }


    const otpInput =
        document.getElementById(
            "registrationOtp"
        );


    if (otpInput) {

        otpInput.addEventListener(
            "input",
            () => {

                otpInput.value =
                    otpInput.value
                        .replace(/\D/g, "")
                        .slice(0, 6);

            }
        );


        otpInput.addEventListener(
            "keydown",
            event => {

                if (
                    event.key === "Enter"
                ) {

                    event.preventDefault();

                    verifyRegistrationOtp();

                }

            }
        );

    }


    modal
        .querySelector(
            ".otp-modal-overlay"
        )
        .addEventListener(
            "click",
            event => {

                if (
                    event.target.classList.contains(
                        "otp-modal-overlay"
                    )
                ) {

                    closeOtpModal();

                }

            }
        );
}


/* ======================================================
   SHOW OTP MODAL
====================================================== */

function showOtpModal(email) {

    if (!otpModal) {

        createOtpModal();

    }


    const modal =
        document.getElementById(
            "registrationOtpModal"
        );


    if (!modal) {
        return;
    }


    const emailDisplay =
        document.getElementById(
            "otpEmailDisplay"
        );


    if (emailDisplay) {

        emailDisplay.textContent =
            maskEmail(email) ||
            "your college email";

    }


    const otpInput =
        document.getElementById(
            "registrationOtp"
        );


    if (otpInput) {

        otpInput.value = "";

    }


    modal.style.display = "block";


    document.body.style.overflow =
        "hidden";


    otpRemainingSeconds = 300;


    resendRemainingSeconds = 30;


    updateOtpTimer();


    updateResendButton(
        resendRemainingSeconds
    );


    startOtpTimer();

    startResendTimer();


    setTimeout(() => {

        if (otpInput) {

            otpInput.focus();

        }

    }, 150);
}


/* ======================================================
   CLOSE OTP MODAL
====================================================== */

function closeOtpModal() {

    const modal =
        document.getElementById(
            "registrationOtpModal"
        );


    if (!modal) {
        return;
    }


    modal.style.display = "none";


    document.body.style.overflow = "";


    stopOtpTimer();

    stopResendTimer();

}


/* ======================================================
   OTP TIMER
====================================================== */

function startOtpTimer() {

    stopOtpTimer();


    otpTimerInterval =
        setInterval(() => {

            otpRemainingSeconds--;

            if (
                otpRemainingSeconds <= 0
            ) {

                otpRemainingSeconds = 0;

                stopOtpTimer();

                updateOtpTimer();

                const verifyButton =
                    document.getElementById(
                        "verifyOtpButton"
                    );

                if (verifyButton) {

                    verifyButton.disabled =
                        true;

                }

                return;
            }


            updateOtpTimer();

        }, 1000);
}


function stopOtpTimer() {

    if (otpTimerInterval) {

        clearInterval(
            otpTimerInterval
        );

        otpTimerInterval = null;

    }
}


function updateOtpTimer() {

    const expiryText =
        document.getElementById(
            "otpExpiryText"
        );


    if (!expiryText) {
        return;
    }


    const minutes =
        Math.floor(
            otpRemainingSeconds / 60
        );


    const seconds =
        otpRemainingSeconds % 60;


    const formattedMinutes =
        String(minutes).padStart(
            2,
            "0"
        );


    const formattedSeconds =
        String(seconds).padStart(
            2,
            "0"
        );


    expiryText.textContent =
        `OTP expires in ${formattedMinutes}:${formattedSeconds}`;
}


/* ======================================================
   RESEND TIMER
====================================================== */

function startResendTimer() {

    stopResendTimer();


    resendTimerInterval =
        setInterval(() => {

            resendRemainingSeconds--;


            if (
                resendRemainingSeconds <= 0
            ) {

                resendRemainingSeconds = 0;

                stopResendTimer();

            }


            updateResendButton(
                resendRemainingSeconds
            );

        }, 1000);
}


function stopResendTimer() {

    if (resendTimerInterval) {

        clearInterval(
            resendTimerInterval
        );

        resendTimerInterval = null;

    }
}


function updateResendButton(seconds) {

    const button =
        document.getElementById(
            "resendOtpButton"
        );


    if (!button) {
        return;
    }


    if (seconds > 0) {

        button.disabled = true;

        /*
         * Keep the requested UI text.
         * The button stays disabled during cooldown.
         */

        button.textContent =
            "Resend OTP";

    } else {

        button.disabled = false;

        button.textContent =
            "Resend OTP";

    }
}


/* ======================================================
   VERIFY REGISTRATION OTP
====================================================== */

async function verifyRegistrationOtp() {

    const otpInput =
        document.getElementById(
            "registrationOtp"
        );


    const verifyButton =
        document.getElementById(
            "verifyOtpButton"
        );


    if (!otpInput) {
        return;
    }


    const otp =
        otpInput.value.trim();


    if (!/^\d{6}$/.test(otp)) {

        showToast(
            "Please enter a valid 6-digit OTP",
            "warning"
        );

        otpInput.focus();

        return;
    }


    if (
        otpRemainingSeconds <= 0
    ) {

        showToast(
            "OTP has expired. Please request a new OTP.",
            "warning"
        );

        return;
    }


    if (verifyButton) {

        verifyButton.disabled = true;

        verifyButton.innerHTML =
            `
                <span class="verify-icon">
                    ⏳
                </span>
                Verifying...
            `;

    }


    try {

        const response =
            await fetch(
                "../../backend/student/verify-register-otp.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },

                    body:
                        new URLSearchParams({
                            otp: otp
                        })
                }
            );


        const result =
            await response.json();


        if (
            result.status ===
            "success"
        ) {

            showToast(
                result.message ||
                "Email verified successfully",
                "success"
            );


            stopOtpTimer();

            stopResendTimer();


            setTimeout(() => {

                closeOtpModal();


                if (
                    result.redirect
                ) {

                    window.location.href =
                        result.redirect;

                } else {

                    window.location.href =
                        "../login.html";

                }

            }, 1000);


            return;
        }


        showToast(
            result.message ||
            "Invalid OTP",
            "error"
        );


    } catch (error) {

        console.error(
            "OTP Verification Error:",
            error
        );


        showToast(
            "Unable to verify OTP. Please try again.",
            "error"
        );

    } finally {

        if (verifyButton) {

            verifyButton.disabled = false;

            verifyButton.innerHTML =
                `
                    <span class="verify-icon">
                        🛡
                    </span>
                    Verify OTP
                `;

        }

    }
}


/* ======================================================
   RESEND REGISTRATION OTP
====================================================== */

async function resendRegistrationOtp() {

    const resendButton =
        document.getElementById(
            "resendOtpButton"
        );


    if (
        resendButton &&
        resendButton.disabled
    ) {
        return;
    }


    if (resendButton) {

        resendButton.disabled = true;

        resendButton.textContent =
            "Sending...";

    }


    try {

        const response =
            await fetch(
                "../../backend/student/resend-register-otp.php",
                {
                    method: "POST"
                }
            );


        const result =
            await response.json();


        if (
            result.status ===
            "success"
        ) {

            const emailDisplay =
                document.getElementById(
                    "otpEmailDisplay"
                );


            if (
                emailDisplay &&
                result.email
            ) {

                emailDisplay.textContent =
                    maskEmail(
                        result.email
                    );

            }


            showToast(
                result.message ||
                "OTP sent successfully",
                "success"
            );


            otpRemainingSeconds = 300;

            resendRemainingSeconds = 30;


            const verifyButton =
                document.getElementById(
                    "verifyOtpButton"
                );


            if (verifyButton) {

                verifyButton.disabled =
                    false;

            }


            updateOtpTimer();

            updateResendButton(
                resendRemainingSeconds
            );


            startOtpTimer();

            startResendTimer();


            const otpInput =
                document.getElementById(
                    "registrationOtp"
                );


            if (otpInput) {

                otpInput.value = "";

                otpInput.focus();

            }


        } else {

            showToast(
                result.message ||
                "Unable to resend OTP",
                "error"
            );


            if (resendButton) {

                resendButton.disabled =
                    false;

                resendButton.textContent =
                    "Resend OTP";

            }

        }


    } catch (error) {

        console.error(
            "Resend OTP Error:",
            error
        );


        showToast(
            "Unable to resend OTP. Please try again.",
            "error"
        );


        if (resendButton) {

            resendButton.disabled =
                false;

            resendButton.textContent =
                "Resend OTP";

        }

    }

}


/* ======================================================
   END OF OTP SECTION
====================================================== */


/*
==========================================================
 IMPORTANT:

 The remaining original VOTIFY registration functions
 continue below this point.

 They should remain exactly as they were in your
 original register.js file.

 The OTP-related corrections above are:

 1. Mask college email
 2. Verify OTP UI
 3. Resend OTP UI
 4. Outlook + Junk folder message
 5. OTP expiry timer
 6. Dark glass modal
 7. Gradient Verify OTP button
 8. 6-digit OTP validation
 9. Resend cooldown
10. Mobile responsive modal

==========================================================
*/