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
   6-Digit OTP Modal
       ↓
   Verify OTP
       ↓
   Secure Identity Verification
========================================================== */

"use strict";


/* ==========================================================
   CONFIGURATION
========================================================== */

const LOGIN_API =
    "../../backend/student/login.php";


/*
 * Backend normally returns:
 * security_check.php
 */
const SECURITY_CHECK_URL =
    "security_check.php";


/*
 * VOTIFY Student Admission Number Format
 *
 * Example:
 * 25CAPMCA080
 *
 * Format:
 * 2 digits + CAPMCA + 3 digits
 */
const ADMISSION_NUMBER_REGEX =
    /^[0-9]{2}CAPMCA[0-9]{3}$/;


/*
 * Only official Sona College email domain.
 */
const COLLEGE_EMAIL_REGEX =
    /^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/i;


/* ==========================================================
   GLOBAL STATE
========================================================== */

let otpTimer = null;


/*
 * Prevent duplicate login requests.
 *
 * Once the login button is clicked and the request
 * starts, another click/submit is ignored.
 */
let loginProcessing = false;


/*
 * Prevent duplicate OTP verification requests.
 */
let otpProcessing = false;


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
   VOTIFY COMMON TOAST
========================================================== */

/*
 * IMPORTANT:
 *
 * Login uses the existing VOTIFY toast system.
 *
 * components/toast.php
 * assets/js/toast.js
 *
 * Existing function:
 *
 * showToast(type, title, message)
 */

function showLoginToast(
    type,
    title,
    message
) {

    if (
        typeof window.showToast ===
        "function"
    ) {

        window.showToast(
            type,
            title,
            message
        );

        return;
    }


    console.error(
        "VOTIFY: toast.js is not loaded."
    );

}


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


    initializeLoginInputs(
        form
    );


    initializePasswordToggle(
        form
    );

}


/* ==========================================================
   LOGIN INPUT INITIALIZATION
========================================================== */

function initializeLoginInputs(
    form
) {

    const admissionInput =
        form.querySelector(
            '[name="admissionNo"]'
        ) ||
        document.querySelector(
            "#admissionNo"
        );


    const emailInput =
        form.querySelector(
            '[name="collegeEmail"]'
        ) ||
        document.querySelector(
            "#collegeEmail"
        );


    /* ------------------------------------------------------
       ADMISSION NUMBER
    ------------------------------------------------------ */

    if (admissionInput) {

        admissionInput.addEventListener(
            "input",
            function () {

                this.value =
                    this.value
                        .toUpperCase()
                        .replace(
                            /[^A-Z0-9]/g,
                            ""
                        )
                        .slice(
                            0,
                            11
                        );

            }
        );

    }


    /* ------------------------------------------------------
       COLLEGE EMAIL
    ------------------------------------------------------ */

    if (emailInput) {

        emailInput.addEventListener(
            "input",
            function () {

                this.value =
                    this.value
                        .replace(
                            /\s/g,
                            ""
                        )
                        .toLowerCase();

            }
        );

    }

}


/* ==========================================================
   PASSWORD EYE TOGGLE
========================================================== */

function initializePasswordToggle(
    form
) {

    const passwordInput =
        form.querySelector(
            '[name="password"]'
        ) ||
        document.querySelector(
            "#password"
        );


    if (!passwordInput) {

        console.warn(
            "VOTIFY: Password input not found."
        );

        return;

    }


    const toggleButton =
        form.querySelector(
            "#togglePassword"
        ) ||
        document.querySelector(
            "#togglePassword"
        );


    if (!toggleButton) {

        console.warn(
            "VOTIFY: Password toggle button not found."
        );

        return;

    }


    /*
     * Prevent duplicate initialization.
     */
    if (
        toggleButton.dataset.initialized ===
        "true"
    ) {

        return;

    }


    toggleButton.dataset.initialized =
        "true";


    toggleButton.addEventListener(
        "click",
        function (event) {

            event.preventDefault();


            const isPassword =
                passwordInput.type ===
                "password";


            passwordInput.type =
                isPassword
                    ? "text"
                    : "password";


            const icon =
                document.querySelector(
                    "#togglePasswordIcon"
                ) ||
                toggleButton.querySelector(
                    "i"
                );


            if (icon) {

                icon.classList.toggle(
                    "ri-eye-line",
                    !isPassword
                );

                icon.classList.toggle(
                    "ri-eye-off-line",
                    isPassword
                );

            }


            toggleButton.setAttribute(
                "aria-label",
                isPassword
                    ? "Hide password"
                    : "Show password"
            );


            passwordInput.focus();

        }
    );

}


/* ==========================================================
   LOGIN
========================================================== */

async function handleLogin(
    event
) {

    event.preventDefault();


    /*
     * FIRST CLICK LOCK
     *
     * Prevents double-click and repeated submit.
     */
    if (loginProcessing) {

        return;

    }


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

        showLoginToast(
            "error",
            "Login Error",
            "Login form fields are missing. Please check student_login.php."
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


    /* ======================================================
       VALIDATION
    ====================================================== */

    /* ------------------------------------------------------
       ADMISSION NUMBER
    ------------------------------------------------------ */

    if (!admissionNo) {

        showLoginToast(
            "error",
            "Admission Number Required",
            "Please enter your Admission Number."
        );

        admissionInput.focus();

        return;

    }


    if (
        !ADMISSION_NUMBER_REGEX.test(
            admissionNo
        )
    ) {

        showLoginToast(
            "error",
            "Invalid Admission Number",
            "Use the format 25CAPMCA080."
        );

        admissionInput.focus();

        return;

    }


    /* ------------------------------------------------------
       DATE OF BIRTH
    ------------------------------------------------------ */

    if (!dob) {

        showLoginToast(
            "error",
            "Date of Birth Required",
            "Please enter your Date of Birth."
        );

        dobInput.focus();

        return;

    }


    /* ------------------------------------------------------
       COLLEGE EMAIL
    ------------------------------------------------------ */

    if (!collegeEmail) {

        showLoginToast(
            "error",
            "College Email Required",
            "Please enter your College Email."
        );

        emailInput.focus();

        return;

    }


    if (
        !COLLEGE_EMAIL_REGEX.test(
            collegeEmail
        )
    ) {

        showLoginToast(
            "error",
            "Invalid College Email",
            "Use only your @sonatech.ac.in college email."
        );

        emailInput.focus();

        return;

    }


    /* ------------------------------------------------------
       PASSWORD
    ------------------------------------------------------ */

    if (!password) {

        showLoginToast(
            "error",
            "Password Required",
            "Please enter your Password."
        );

        passwordInput.focus();

        return;

    }


    /* ======================================================
       LOCK LOGIN PROCESS
    ====================================================== */

    loginProcessing =
        true;


    setButtonLoading(
        button,
        true,
        "Logging in..."
    );


    /* ======================================================
       FORM DATA
    ====================================================== */

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


    /* ======================================================
       SEND LOGIN REQUEST
    ====================================================== */

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

        }

        catch (jsonError) {

            console.error(
                "Invalid JSON response:",
                text
            );


            showLoginToast(
                "error",
                "Server Error",
                "Server returned an invalid response. Please try again."
            );


            return;

        }


        /* ==================================================
           LOGIN FAILED
        ================================================== */

        if (!data.success) {

            showLoginToast(
                "error",
                "Login Failed",
                data.message ||
                "Login failed. Please check your details."
            );


            return;

        }


        /* ==================================================
           OTP REQUIRED
        ================================================== */

        if (
            data.otp_required === true ||
            data.requires_otp === true
        ) {

            /*
             * Keep login button locked while
             * OTP verification is active.
             */
            setButtonLoading(
                button,
                true,
                "OTP Verification Required"
            );


            showOtpModal(
                data.email ||
                collegeEmail,
                data.otp_expires_in ||
                300
            );


            return;

        }


        /* ==================================================
           DIRECT LOGIN FALLBACK
        ================================================== */

        window.location.href =
            data.redirect ||
            SECURITY_CHECK_URL;

    }

    catch (error) {

        console.error(
            "LOGIN FETCH ERROR:",
            error
        );


        showLoginToast(
            "error",
            "Connection Error",
            "Unable to connect to login service. Please try again."
        );

    }

    finally {

        /*
         * If OTP modal exists, keep the login button locked.
         *
         * If request failed before OTP modal was created,
         * restore the login button.
         */
        const otpModal =
            document.querySelector(
                "#votifyLoginOtpModal"
            );


        if (!otpModal) {

            loginProcessing =
                false;


            setButtonLoading(
                button,
                false,
                "Login Securely"
            );

        }

    }

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


        button.setAttribute(
            "aria-disabled",
            "true"
        );


        button.classList.add(
            "cursor-not-allowed"
        );


        button.classList.remove(
            "hover:scale-[1.02]"
        );


        /*
         * Save original HTML only once.
         */
        if (
            !button.dataset.originalContent
        ) {

            button.dataset.originalContent =
                button.innerHTML;

        }


        button.innerHTML = `

            <span
                class="
                inline-flex
                items-center
                justify-center
                gap-2
                "
            >

                <i
                    class="
                    ri-loader-4-line
                    animate-spin
                    text-xl
                    "
                    aria-hidden="true"
                ></i>

                <span>
                    ${escapeHtml(text)}
                </span>

            </span>

        `;


        return;

    }


    button.disabled =
        false;


    button.removeAttribute(
        "aria-disabled"
    );


    button.classList.remove(
        "cursor-not-allowed"
    );


    button.classList.add(
        "hover:scale-[1.02]"
    );


    if (
        button.dataset.originalContent
    ) {

        button.innerHTML =
            button.dataset.originalContent;

    }

    else {

        button.textContent =
            text;

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


    otpProcessing =
        false;


    const modal =
        document.createElement(
            "div"
        );


    modal.id =
        "votifyLoginOtpModal";


    modal.innerHTML = `

        <div class="votify-otp-overlay">

            <div
                class="votify-otp-box"
                role="dialog"
                aria-modal="true"
                aria-labelledby="votifyOtpTitle"
            >

                <button
                    type="button"
                    class="votify-otp-close"
                    id="votifyOtpClose"
                    aria-label="Close OTP verification"
                >
                    &times;
                </button>


                <div
                    class="votify-otp-icon"
                    aria-hidden="true"
                >
                    <i class="ri-mail-line"></i>
                </div>


                <h2 id="votifyOtpTitle">
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

                    <!-- ==================================================
                         SIX OTP BOXES
                    ================================================== -->

                    <div
                        id="votifyOtpBoxes"
                        class="votify-otp-boxes"
                        role="group"
                        aria-label="Enter 6-digit OTP"
                    >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp1"
                            autocomplete="one-time-code"
                            aria-label="OTP digit 1"
                        >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp2"
                            aria-label="OTP digit 2"
                        >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp3"
                            aria-label="OTP digit 3"
                        >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp4"
                            aria-label="OTP digit 4"
                        >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp5"
                            aria-label="OTP digit 5"
                        >

                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="votify-otp-digit"
                            id="votifyOtp6"
                            aria-label="OTP digit 6"
                        >

                    </div>


                    <!-- ==================================================
                         OTP MESSAGE
                    ================================================== -->

                    <div
                        id="votifyOtpMessage"
                        class="votify-otp-message"
                        aria-live="polite"
                    ></div>


                    <!-- ==================================================
                         VERIFY BUTTON
                    ================================================== -->

                    <button
                        type="submit"
                        id="votifyVerifyOtpButton"
                        class="votify-otp-button"
                    >
                        <span
                            class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            "
                        >

                            <i
                                class="ri-shield-check-line"
                                aria-hidden="true"
                            ></i>

                            <span>
                                Verify OTP
                            </span>

                        </span>
                    </button>


                    <!-- ==================================================
                         OTP TIMER
                    ================================================== -->

                    <div
                        id="votifyOtpTimer"
                        class="votify-otp-timer"
                        aria-live="polite"
                    ></div>


                    <!-- ==================================================
                         RESEND OTP
                    ================================================== -->

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


    /* ======================================================
       OTP ELEMENTS
    ====================================================== */

    const otpInputs =
        Array.from(
            document.querySelectorAll(
                ".votify-otp-digit"
            )
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


    /* ======================================================
       INITIALIZE OTP INPUTS
    ====================================================== */

    initializeOtpInputs(
        otpInputs
    );


    /* ======================================================
       SUBMIT OTP
    ====================================================== */

    if (otpForm) {

        otpForm.addEventListener(
            "submit",
            verifyLoginOtp
        );

    }


    /* ======================================================
       CLOSE OTP
    ====================================================== */

    if (closeButton) {

        closeButton.addEventListener(
            "click",
            function () {

                clearOtpTimer();

                removeExistingOtpModal();

                /*
                 * Allow login again only after
                 * user closes the OTP modal.
                 */
                loginProcessing =
                    false;


                const form =
                    document.querySelector(
                        "#studentLoginForm"
                    );


                if (form) {

                    const button =
                        form.querySelector(
                            'button[type="submit"]'
                        );


                    setButtonLoading(
                        button,
                        false,
                        "Login Securely"
                    );

                }

            }
        );

    }


    /* ======================================================
       RESEND OTP
    ====================================================== */

    if (resendButton) {

        resendButton.addEventListener(
            "click",
            resendLoginOtp
        );

    }


    /* ======================================================
       TIMER
    ====================================================== */

    startOtpTimer(
        seconds
    );


    /* ======================================================
       FOCUS FIRST OTP BOX
    ====================================================== */

    setTimeout(
        function () {

            if (otpInputs.length > 0) {

                otpInputs[0].focus();

            }

        },
        100
    );

}


/* ==========================================================
   INITIALIZE SIX OTP INPUTS
========================================================== */

function initializeOtpInputs(
    inputs
) {

    if (!inputs.length) {

        return;

    }


    inputs.forEach(
        function (input, index) {

            /* ------------------------------------------------
               INPUT
            ------------------------------------------------ */

            input.addEventListener(
                "input",
                function () {

                    /*
                     * Numbers only.
                     */
                    this.value =
                        this.value
                            .replace(
                                /[^0-9]/g,
                                ""
                            )
                            .slice(
                                0,
                                1
                            );


                    /*
                     * Remove error/success state
                     * when user edits OTP.
                     */
                    clearOtpInputState();


                    /*
                     * Auto move to next box.
                     */
                    if (
                        this.value &&
                        index <
                        inputs.length - 1
                    ) {

                        inputs[
                            index + 1
                        ].focus();

                    }


                    /*
                     * When all six digits are filled,
                     * keep focus on final box.
                     */
                    if (
                        index ===
                        inputs.length - 1
                    ) {

                        this.blur();

                    }

                }
            );


            /* ------------------------------------------------
               KEYDOWN
            ------------------------------------------------ */

            input.addEventListener(
                "keydown",
                function (event) {

                    /*
                     * Backspace:
                     *
                     * If current box is empty,
                     * move to previous box.
                     */
                    if (
                        event.key ===
                        "Backspace"
                    ) {

                        if (
                            !this.value &&
                            index > 0
                        ) {

                            event.preventDefault();

                            inputs[
                                index - 1
                            ].focus();

                            inputs[
                                index - 1
                            ].select();

                        }

                    }


                    /*
                     * Left arrow.
                     */
                    if (
                        event.key ===
                        "ArrowLeft" &&
                        index > 0
                    ) {

                        event.preventDefault();

                        inputs[
                            index - 1
                        ].focus();

                    }


                    /*
                     * Right arrow.
                     */
                    if (
                        event.key ===
                        "ArrowRight" &&
                        index <
                        inputs.length - 1
                    ) {

                        event.preventDefault();

                        inputs[
                            index + 1
                        ].focus();

                    }

                }
            );


            /* ------------------------------------------------
               PASTE
            ------------------------------------------------ */

            input.addEventListener(
                "paste",
                function (event) {

                    event.preventDefault();


                    const pasted =
                        (
                            event.clipboardData ||
                            window.clipboardData
                        )
                        .getData("text")
                        .replace(
                            /[^0-9]/g,
                            ""
                        )
                        .slice(
                            0,
                            6
                        );


                    if (!pasted) {

                        return;

                    }


                    /*
                     * Fill all six boxes.
                     */
                    pasted
                        .split("")
                        .forEach(
                            function (
                                digit,
                                digitIndex
                            ) {

                                if (
                                    inputs[
                                        digitIndex
                                    ]
                                ) {

                                    inputs[
                                        digitIndex
                                    ].value =
                                        digit;

                                }

                            }
                        );


                    /*
                     * Focus the next empty box,
                     * otherwise final box.
                     */
                    const nextEmpty =
                        inputs.findIndex(
                            function (
                                otpInput
                            ) {

                                return !otpInput.value;

                            }
                        );


                    if (
                        nextEmpty !== -1
                    ) {

                        inputs[
                            nextEmpty
                        ].focus();

                    }

                    else {

                        inputs[
                            inputs.length - 1
                        ].focus();

                    }

                }
            );


            /* ------------------------------------------------
               FOCUS
            ------------------------------------------------ */

            input.addEventListener(
                "focus",
                function () {

                    this.classList.add(
                        "active"
                    );

                }
            );


            /* ------------------------------------------------
               BLUR
            ------------------------------------------------ */

            input.addEventListener(
                "blur",
                function () {

                    this.classList.remove(
                        "active"
                    );

                }
            );

        }
    );

}


/* ==========================================================
   GET COMBINED OTP
========================================================== */

function getLoginOtp() {

    const inputs =
        Array.from(
            document.querySelectorAll(
                ".votify-otp-digit"
            )
        );


    if (
        inputs.length !== 6
    ) {

        return "";

    }


    return inputs
        .map(
            function (input) {

                return input.value;

            }
        )
        .join("");

}


/* ==========================================================
   CLEAR OTP INPUT STATE
========================================================== */

function clearOtpInputState() {

    const inputs =
        document.querySelectorAll(
            ".votify-otp-digit"
        );


    inputs.forEach(
        function (input) {

            input.classList.remove(
                "otp-error",
                "otp-success"
            );

        }
    );

}


/* ==========================================================
   SHOW OTP ERROR STATE
========================================================== */

function showOtpErrorState() {

    const inputs =
        document.querySelectorAll(
            ".votify-otp-digit"
        );


    inputs.forEach(
        function (input) {

            input.classList.add(
                "otp-error"
            );

        }
    );

}


/* ==========================================================
   SHOW OTP SUCCESS STATE
========================================================== */

function showOtpSuccessState() {

    const inputs =
        document.querySelectorAll(
            ".votify-otp-digit"
        );


    inputs.forEach(
        function (input) {

            input.classList.add(
                "otp-success"
            );

        }
    );

}


/* ==========================================================
   VERIFY OTP
========================================================== */

async function verifyLoginOtp(
    event
) {

    event.preventDefault();


    /*
     * Prevent duplicate OTP requests.
     */
    if (otpProcessing) {

        return;

    }


    const otp =
        getLoginOtp();


    /* ------------------------------------------------------
       VALIDATE OTP
    ------------------------------------------------------ */

    if (
        !/^[0-9]{6}$/.test(
            otp
        )
    ) {

        showLoginToast(
            "error",
            "Invalid OTP",
            "Please enter the complete 6-digit OTP."
        );


        showOtpMessage(
            "Please enter the complete 6-digit OTP.",
            "error"
        );


        showOtpErrorState();


        const firstEmpty =
            Array.from(
                document.querySelectorAll(
                    ".votify-otp-digit"
                )
            ).find(
                function (input) {

                    return !input.value;

                }
            );


        if (firstEmpty) {

            firstEmpty.focus();

        }


        return;

    }


    /* ======================================================
       LOCK OTP VERIFICATION
    ====================================================== */

    otpProcessing =
        true;


    const button =
        document.querySelector(
            "#votifyVerifyOtpButton"
        );


    const inputs =
        document.querySelectorAll(
            ".votify-otp-digit"
        );


    inputs.forEach(
        function (input) {

            input.disabled =
                true;

        }
    );


    if (button) {

        button.disabled =
            true;


        button.classList.add(
            "cursor-not-allowed"
        );


        button.innerHTML = `

            <span
                class="
                inline-flex
                items-center
                justify-center
                gap-2
                "
            >

                <i
                    class="
                    ri-loader-4-line
                    animate-spin
                    text-xl
                    "
                    aria-hidden="true"
                ></i>

                <span>
                    Verifying...
                </span>

            </span>

        `;

    }


    showOtpMessage(
        "",
        ""
    );


    /* ======================================================
       REQUEST
    ====================================================== */

    const formData =
        new FormData();


    formData.append(
        "action",
        "verify_otp"
    );


    /*
     * Backend still receives ONE combined OTP.
     *
     * Example:
     * 123456
     */
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

        }

        catch (error) {

            showLoginToast(
                "error",
                "Server Error",
                "Invalid server response."
            );


            showOtpMessage(
                "Invalid server response.",
                "error"
            );


            showOtpErrorState();


            return;

        }


        /* ==================================================
           OTP FAILED
        ================================================== */

        if (!data.success) {

            otpProcessing =
                false;


            showLoginToast(
                "error",
                "OTP Verification Failed",
                data.message ||
                "OTP verification failed."
            );


            showOtpMessage(
                data.message ||
                "OTP verification failed.",
                "error"
            );


            showOtpErrorState();


            /*
             * Re-enable OTP boxes.
             */
            inputs.forEach(
                function (input) {

                    input.disabled =
                        false;

                }
            );


            if (button) {

                button.disabled =
                    false;

                button.classList.remove(
                    "cursor-not-allowed"
                );

                button.innerHTML = `

                    <span
                        class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2
                        "
                    >

                        <i
                            class="ri-shield-check-line"
                            aria-hidden="true"
                        ></i>

                        <span>
                            Verify OTP
                        </span>

                    </span>

                `;

            }


            /*
             * If OTP expired, allow resend.
             */
            if (
                data.otp_expired === true
            ) {

                const resend =
                    document.querySelector(
                        "#votifyResendOtp"
                    );


                if (resend) {

                    resend.style.display =
                        "inline-block";

                }

            }


            return;

        }


        /* ==================================================
           OTP SUCCESS
        ================================================== */

        showOtpSuccessState();


        showLoginToast(
            "success",
            "Login Successful",
            "OTP verified successfully. Logging you in..."
        );


        showOtpMessage(
            "OTP verified successfully. Logging you in...",
            "success"
        );


        if (button) {

            button.innerHTML = `

                <span
                    class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    "
                >

                    <i
                        class="
                        ri-checkbox-circle-line
                        text-xl
                        "
                        aria-hidden="true"
                    ></i>

                    <span>
                        Verified
                    </span>

                </span>

            `;

        }


        /*
         * Keep login permanently locked
         * until redirect.
         */
        loginProcessing =
            true;


        otpProcessing =
            true;


        /* ==================================================
           REDIRECT
        ================================================== */

        setTimeout(
            function () {

                window.location.href =
                    data.redirect ||
                    SECURITY_CHECK_URL;

            },
            800
        );

    }

    catch (error) {

        console.error(
            "OTP VERIFY ERROR:",
            error
        );


        otpProcessing =
            false;


        showLoginToast(
            "error",
            "Verification Error",
            "Unable to verify OTP. Please try again."
        );


        showOtpMessage(
            "Unable to verify OTP. Please try again.",
            "error"
        );


        showOtpErrorState();


        inputs.forEach(
            function (input) {

                input.disabled =
                    false;

            }
        );


        if (button) {

            button.disabled =
                false;


            button.classList.remove(
                "cursor-not-allowed"
            );


            button.innerHTML = `

                <span
                    class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    "
                >

                    <i
                        class="ri-shield-check-line"
                        aria-hidden="true"
                    ></i>

                    <span>
                        Verify OTP
                    </span>

                </span>

            `;

        }

    }

}


/* ==========================================================
   RESEND OTP
========================================================== */

async function resendLoginOtp() {

    /*
     * Do not allow resend while OTP verification
     * is currently processing.
     */
    if (otpProcessing) {

        return;

    }


    const form =
        document.querySelector(
            "#studentLoginForm"
        );


    if (!form) {

        return;

    }


    const admissionInput =
        form.querySelector(
            '[name="admissionNo"]'
        );


    const dobInput =
        form.querySelector(
            '[name="dob"]'
        );


    const emailInput =
        form.querySelector(
            '[name="collegeEmail"]'
        );


    const passwordInput =
        form.querySelector(
            '[name="password"]'
        );


    if (
        !admissionInput ||
        !dobInput ||
        !emailInput ||
        !passwordInput
    ) {

        showLoginToast(
            "error",
            "Login Details Missing",
            "Login details are missing."
        );


        showOtpMessage(
            "Login details are missing.",
            "error"
        );


        return;

    }


    const admissionNo =
        admissionInput.value
            .trim()
            .toUpperCase();


    const collegeEmail =
        emailInput.value
            .trim()
            .toLowerCase();


    if (
        !ADMISSION_NUMBER_REGEX.test(
            admissionNo
        )
    ) {

        showLoginToast(
            "error",
            "Invalid Admission Number",
            "Use the format 25CAPMCA080."
        );

        return;

    }


    if (
        !COLLEGE_EMAIL_REGEX.test(
            collegeEmail
        )
    ) {

        showLoginToast(
            "error",
            "Invalid College Email",
            "Use only your @sonatech.ac.in college email."
        );

        return;

    }


    const button =
        document.querySelector(
            "#votifyResendOtp"
        );


    if (!button) {

        return;

    }


    button.disabled =
        true;


    button.classList.add(
        "cursor-not-allowed"
    );


    button.innerHTML = `

        <span
            class="
            inline-flex
            items-center
            justify-center
            gap-2
            "
        >

            <i
                class="
                ri-loader-4-line
                animate-spin
                "
                aria-hidden="true"
            ></i>

            <span>
                Sending...
            </span>

        </span>

    `;


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
        dobInput.value.trim()
    );


    formData.append(
        "collegeEmail",
        collegeEmail
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

        }

        catch (error) {

            showLoginToast(
                "error",
                "Server Error",
                "Invalid server response."
            );


            showOtpMessage(
                "Invalid server response.",
                "error"
            );


            return;

        }


        if (!data.success) {

            showLoginToast(
                "error",
                "OTP Resend Failed",
                data.message ||
                "Unable to resend OTP."
            );


            showOtpMessage(
                data.message ||
                "Unable to resend OTP.",
                "error"
            );


            return;

        }


        showLoginToast(
            "success",
            "OTP Sent",
            "New OTP sent to your college email."
        );


        showOtpMessage(
            "New OTP sent to your college email.",
            "success"
        );


        clearOtpInputState();


        /*
         * Clear all OTP boxes.
         */
        const inputs =
            document.querySelectorAll(
                ".votify-otp-digit"
            );


        inputs.forEach(
            function (input) {

                input.value =
                    "";

                input.disabled =
                    false;

            }
        );


        startOtpTimer(
            data.otp_expires_in ||
            300
        );


        if (inputs.length > 0) {

            inputs[0].focus();

        }

    }

    catch (error) {

        console.error(
            "RESEND OTP ERROR:",
            error
        );


        showLoginToast(
            "error",
            "Connection Error",
            "Unable to send OTP. Please try again."
        );


        showOtpMessage(
            "Unable to send OTP. Please try again.",
            "error"
        );

    }

    finally {

        button.disabled =
            false;


        button.classList.remove(
            "cursor-not-allowed"
        );


        button.innerHTML = `

            <span>
                Resend OTP
            </span>

        `;

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


    if (
        !Number.isFinite(
            remaining
        ) ||
        remaining <= 0
    ) {

        remaining =
            300;

    }


    const timer =
        document.querySelector(
            "#votifyOtpTimer"
        );


    if (!timer) {

        return;

    }


    timer.classList.remove(
        "expired"
    );


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
   CLEAR OTP TIMER
========================================================== */

function clearOtpTimer() {

    if (otpTimer) {

        clearInterval(
            otpTimer
        );

        otpTimer =
            null;

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

        /* ==================================================
           OTP OVERLAY
        ================================================== */

        .votify-otp-overlay {

            position: fixed;

            inset: 0;

            z-index: 99999;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                rgba(0, 0, 0, 0.78);

            backdrop-filter:
                blur(8px);

            padding: 20px;

            overflow-y: auto;

        }


        /* ==================================================
           OTP BOX
        ================================================== */

        .votify-otp-box {

            position: relative;

            width: 100%;

            max-width: 500px;

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


        /* ==================================================
           CLOSE BUTTON
        ================================================== */

        .votify-otp-close {

            position: absolute;

            right: 18px;

            top: 14px;

            width: 35px;

            height: 35px;

            border: none;

            background:
                transparent;

            color: #aaa;

            font-size: 28px;

            cursor: pointer;

            transition:
                color 0.2s ease;

        }


        .votify-otp-close:hover {

            color: white;

        }


        /* ==================================================
           OTP ICON
        ================================================== */

        .votify-otp-icon {

            width: 70px;

            height: 70px;

            margin:
                0 auto 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background:
                rgba(59,130,246,0.15);

            border:
                1px solid
                rgba(59,130,246,0.4);

            color:
                #ffffff;

            font-size: 32px;

        }


        /* ==================================================
           OTP HEADING
        ================================================== */

        .votify-otp-box h2 {

            margin:
                0 0 12px;

            font-size:
                27px;

            font-weight:
                700;

        }


        /* ==================================================
           DESCRIPTION
        ================================================== */

        .votify-otp-description {

            margin:
                0 0 8px;

            color:
                #aab4c8;

        }


        /* ==================================================
           EMAIL
        ================================================== */

        .votify-otp-email {

            margin-bottom:
                24px;

            color:
                #60a5fa;

            font-weight:
                600;

            word-break:
                break-all;

        }


        /* ==================================================
           SIX OTP BOX CONTAINER
        ================================================== */

        .votify-otp-boxes {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                10px;

            width:
                100%;

            margin:
                0 auto;

        }


        /* ==================================================
           INDIVIDUAL OTP BOX
        ================================================== */

        .votify-otp-digit {

            width:
                58px;

            height:
                62px;

            box-sizing:
                border-box;

            border:
                1px solid
                #334155;

            border-radius:
                12px;

            background:
                #1e293b;

            color:
                white;

            font-size:
                26px;

            font-weight:
                700;

            text-align:
                center;

            outline:
                none;

            transition:
                all 0.2s ease;

            caret-color:
                #60a5fa;

        }


        /* ==================================================
           OTP ACTIVE BOX
        ================================================== */

        .votify-otp-digit.active {

            border-color:
                #3b82f6;

            box-shadow:
                0 0 0 3px
                rgba(59,130,246,0.20),
                0 0 20px
                rgba(59,130,246,0.25);

            transform:
                translateY(-1px);

        }


        /* ==================================================
           OTP ERROR
        ================================================== */

        .votify-otp-digit.otp-error {

            border-color:
                #ef4444;

            box-shadow:
                0 0 0 3px
                rgba(239,68,68,0.15),
                0 0 18px
                rgba(239,68,68,0.20);

        }


        /* ==================================================
           OTP SUCCESS
        ================================================== */

        .votify-otp-digit.otp-success {

            border-color:
                #22c55e;

            box-shadow:
                0 0 0 3px
                rgba(34,197,94,0.15),
                0 0 18px
                rgba(34,197,94,0.20);

        }


        /* ==================================================
           OTP MESSAGE
        ================================================== */

        .votify-otp-message {

            min-height:
                22px;

            margin-top:
                12px;

            font-size:
                14px;

        }


        .votify-otp-message.error {

            color:
                #f87171;

        }


        .votify-otp-message.success {

            color:
                #4ade80;

        }


        /* ==================================================
           VERIFY BUTTON
        ================================================== */

        .votify-otp-button {

            width:
                100%;

            margin-top:
                16px;

            padding:
                14px;

            border:
                none;

            border-radius:
                10px;

            background:
                linear-gradient(
                    90deg,
                    #2563eb,
                    #d946ef
                );

            color:
                white;

            font-size:
                16px;

            font-weight:
                700;

            cursor:
                pointer;

            transition:
                opacity 0.2s ease;

        }


        .votify-otp-button:disabled {

            opacity:
                0.65;

            cursor:
                not-allowed;

        }


        /* ==================================================
           TIMER
        ================================================== */

        .votify-otp-timer {

            margin-top:
                15px;

            color:
                #94a3b8;

            font-size:
                14px;

        }


        .votify-otp-timer.expired {

            color:
                #f87171;

        }


        /* ==================================================
           RESEND
        ================================================== */

        .votify-resend-button {

            margin-top:
                12px;

            padding:
                8px 15px;

            border:
                none;

            background:
                transparent;

            color:
                #60a5fa;

            cursor:
                pointer;

            font-size:
                14px;

        }


        .votify-resend-button:disabled {

            opacity:
                0.5;

            cursor:
                not-allowed;

        }


        /* ==================================================
           MOBILE RESPONSIVE
        ================================================== */

        @media (
            max-width: 520px
        ) {

            .votify-otp-box {

                max-width:
                    100%;

                padding:
                    30px 18px;

            }


            .votify-otp-box h2 {

                font-size:
                    24px;

            }


            .votify-otp-boxes {

                gap:
                    7px;

            }


            .votify-otp-digit {

                width:
                    46px;

                height:
                    54px;

                font-size:
                    23px;

                border-radius:
                    10px;

            }

        }


        /* ==================================================
           SMALL MOBILE
        ================================================== */

        @media (
            max-width: 360px
        ) {

            .votify-otp-boxes {

                gap:
                    5px;

            }


            .votify-otp-digit {

                width:
                    40px;

                height:
                    50px;

                font-size:
                    21px;

            }

        }

    `;


    document.head.appendChild(
        style
    );

}


/* ==========================================================
   END OF LOGIN.JS
========================================================== */