/*
==========================================================
 VOTIFY - Student Registration + OTP Verification
 File: assets/js/register.js

 Features:
 - Registration validation
 - Password visibility toggle
 - Password strength meter
 - Single-click registration lock
 - OTP verification
 - OTP resend
 - 6-digit OTP box input
==========================================================
*/

document.addEventListener("DOMContentLoaded", () => {

    createOtpModal();

    const registerForm =
        document.getElementById("registerForm");

    if (registerForm) {

        registerForm.addEventListener(
            "submit",
            registerStudent
        );

    }

    initializeInputs();
    initializeRegistrationValidation();
    initializePasswordToggle();
    initializePasswordStrength();

});


/* ======================================================
   GENERAL TOAST
====================================================== */

function showToast(message, type = "info") {

    let toastContainer =
        document.getElementById("toastContainer");

    if (!toastContainer) {

        toastContainer =
            document.createElement("div");

        toastContainer.id =
            "toastContainer";

        toastContainer.style.position = "fixed";
        toastContainer.style.top = "20px";
        toastContainer.style.right = "20px";
        toastContainer.style.zIndex = "2147483647";
        toastContainer.style.maxWidth =
            "calc(100vw - 40px)";
        toastContainer.style.pointerEvents =
            "none";

        document.body.appendChild(
            toastContainer
        );
    }

    const toast =
        document.createElement("div");

    toast.className =
        `toast-message ${type}`;

    toast.textContent =
        message;

    toast.style.position =
        "relative";

    toast.style.zIndex =
        "2147483647";

    toast.style.padding =
        "14px 20px";

    toast.style.marginBottom =
        "10px";

    toast.style.borderRadius =
        "12px";

    toast.style.color =
        "#ffffff";

    toast.style.fontSize =
        "14px";

    toast.style.fontWeight =
        "500";

    toast.style.boxShadow =
        "0 15px 40px rgba(0,0,0,0.45)";

    toast.style.backdropFilter =
        "none";

    toast.style.webkitBackdropFilter =
        "none";

    toast.style.opacity =
        "1";

    toast.style.transform =
        "translateX(0)";

    toast.style.transition =
        "opacity 0.3s ease, transform 0.3s ease";

    toast.style.pointerEvents =
        "auto";

    if (type === "success") {

        toast.style.background =
            "#16A34A";

    } else if (type === "error") {

        toast.style.background =
            "#DC2626";

    } else if (type === "warning") {

        toast.style.background =
            "#D97706";

    } else {

        toast.style.background =
            "#2563EB";
    }

    toastContainer.appendChild(
        toast
    );

    setTimeout(() => {

        toast.style.opacity =
            "0";

        toast.style.transform =
            "translateX(20px)";

        setTimeout(() => {

            toast.remove();

        }, 300);

    }, 3000);

}


/* ======================================================
   INPUT INITIALIZATION
====================================================== */

function initializeInputs() {

    const inputs =
        document.querySelectorAll(
            "input, select, textarea"
        );

    inputs.forEach(input => {

        input.addEventListener(
            "focus",
            () => {

                input.classList.add(
                    "input-focused"
                );

            }
        );

        input.addEventListener(
            "blur",
            () => {

                input.classList.remove(
                    "input-focused"
                );

            }
        );

    });

}


/* ======================================================
   REGISTRATION INPUT VALIDATION
====================================================== */

function initializeRegistrationValidation() {

    const fullName =
        document.getElementById("fullName");

    const admissionNo =
        document.getElementById("admissionNo");

    const phone =
        document.getElementById("phone");

    const email =
        document.getElementById("email");


    /* --------------------------------------------------
       FULL NAME
    -------------------------------------------------- */

    if (fullName) {

        fullName.addEventListener(
            "input",
            () => {

                fullName.value =
                    fullName.value
                        .replace(/[^A-Za-z ]/g, "")
                        .replace(/\s+/g, " ")
                        .toUpperCase();

            }
        );

    }


    /* --------------------------------------------------
       ADMISSION NUMBER
       Format: 25CAPMCA080
    -------------------------------------------------- */

    if (admissionNo) {

        admissionNo.addEventListener(
            "input",
            () => {

                admissionNo.value =
                    admissionNo.value
                        .toUpperCase()
                        .replace(/[^A-Z0-9]/g, "")
                        .slice(0, 11);

            }
        );

    }


    /* --------------------------------------------------
       PHONE NUMBER
    -------------------------------------------------- */

    if (phone) {

        phone.addEventListener(
            "input",
            () => {

                phone.value =
                    phone.value
                        .replace(/\D/g, "")
                        .slice(0, 10);

            }
        );

    }


    /* --------------------------------------------------
       EMAIL
    -------------------------------------------------- */

    if (email) {

        email.addEventListener(
            "input",
            () => {

                email.value =
                    email.value.replace(
                        /\s/g,
                        ""
                    );

            }
        );

    }

}


/* ======================================================
   PASSWORD TOGGLE
====================================================== */

function initializePasswordToggle() {

    const passwordToggle =
        document.getElementById(
            "togglePassword"
        );

    const confirmPasswordToggle =
        document.getElementById(
            "toggleConfirmPassword"
        );


    if (passwordToggle) {

        passwordToggle.addEventListener(
            "click",
            () => {

                togglePasswordVisibility(
                    "password",
                    passwordToggle
                );

            }
        );

    }


    if (confirmPasswordToggle) {

        confirmPasswordToggle.addEventListener(
            "click",
            () => {

                togglePasswordVisibility(
                    "confirmPassword",
                    confirmPasswordToggle
                );

            }
        );

    }


    const genericButtons =
        document.querySelectorAll(
            ".password-toggle"
        );


    genericButtons.forEach(button => {

        if (
            button === passwordToggle ||
            button === confirmPasswordToggle
        ) {

            return;

        }

        button.addEventListener(
            "click",
            () => {

                const targetId =
                    button.dataset.target;

                if (!targetId) {
                    return;
                }

                togglePasswordVisibility(
                    targetId,
                    button
                );

            }
        );

    });

}


/* ======================================================
   PASSWORD VISIBILITY
====================================================== */

function togglePasswordVisibility(
    targetId,
    button
) {

    const input =
        document.getElementById(
            targetId
        );

    if (!input || !button) {
        return;
    }


    const icon =
        button.querySelector("i");


    if (input.type === "password") {

        input.type = "text";

        button.classList.add(
            "active"
        );

        button.setAttribute(
            "aria-label",
            "Hide password"
        );

        if (icon) {

            icon.className =
                "ri-eye-off-line text-xl";

        }

    } else {

        input.type = "password";

        button.classList.remove(
            "active"
        );

        button.setAttribute(
            "aria-label",
            "Show password"
        );

        if (icon) {

            icon.className =
                "ri-eye-line text-xl";

        }

    }

}


/* ======================================================
   PASSWORD STRENGTH INITIALIZATION
====================================================== */

function initializePasswordStrength() {

    const passwordField =
        document.getElementById(
            "password"
        );

    if (!passwordField) {
        return;
    }


    passwordField.addEventListener(
        "input",
        () => {

            updatePasswordStrength(
                passwordField.value
            );

        }
    );


    updatePasswordStrength("");

}


/* ======================================================
   UPDATE PASSWORD STRENGTH
====================================================== */

function updatePasswordStrength(
    password
) {

    const strengthContainer =
        document.getElementById(
            "passwordStrength"
        );

    const strengthBar =
        document.getElementById(
            "strengthBar"
        );

    const strengthText =
        document.getElementById(
            "strengthText"
        );

    const requirementsText =
        document.getElementById(
            "strengthRequirements"
        );


    if (
        !strengthContainer ||
        !strengthBar ||
        !strengthText
    ) {

        console.warn(
            "VOTIFY: Password strength elements not found."
        );

        return;

    }


    const lengthValid =
        password.length >= 8;

    const uppercaseValid =
        /[A-Z]/.test(password);

    const numberValid =
        /[0-9]/.test(password);

    const specialValid =
        /[^A-Za-z0-9]/.test(password);


    let strength = 0;


    if (lengthValid) {
        strength++;
    }

    if (uppercaseValid) {
        strength++;
    }

    if (numberValid) {
        strength++;
    }

    if (specialValid) {
        strength++;
    }


    /* ==================================================
       EMPTY PASSWORD
    ================================================== */

    if (!password) {

        strengthContainer.classList.add(
            "hidden"
        );

        strengthBar.style.width =
            "0%";

        strengthBar.style.height =
            "100%";

        strengthBar.style.background =
            "transparent";

        strengthBar.style.boxShadow =
            "none";

        strengthText.textContent =
            "Password Strength";

        strengthText.style.color =
            "#94A3B8";

        if (requirementsText) {

            requirementsText.textContent =
                "Min 8 characters";

        }

        return;

    }


    /* ==================================================
       SHOW METER
    ================================================== */

    strengthContainer.classList.remove(
        "hidden"
    );

    strengthContainer.style.display =
        "block";


    const percentage =
        (strength / 4) * 100;


    strengthBar.classList.remove(
        "hidden"
    );

    strengthBar.style.display =
        "block";

    strengthBar.style.visibility =
        "visible";

    strengthBar.style.opacity =
        "1";

    strengthBar.style.height =
        "100%";

    strengthBar.style.minWidth =
        "0";

    strengthBar.style.maxWidth =
        "100%";

    strengthBar.style.width =
        `${percentage}%`;

    strengthBar.style.borderRadius =
        "999px";

    strengthBar.style.transition =
        "width 0.35s ease, background 0.25s ease, box-shadow 0.25s ease";


    /* ==================================================
       PASSWORD STRENGTH COLORS

       1 = RED
       2 = ORANGE
       3 = ORANGE
       4 = GREEN
    ================================================== */

    if (strength === 1) {

        strengthBar.style.background =
            "#EF4444";

        strengthBar.style.boxShadow =
            "0 0 12px rgba(239,68,68,0.45)";

        strengthText.textContent =
            "Weak";

        strengthText.style.color =
            "#F87171";

        if (requirementsText) {

            requirementsText.textContent =
                "Add uppercase, number & special character";

        }

    }

    else if (strength === 2) {

        strengthBar.style.background =
            "#F59E0B";

        strengthBar.style.boxShadow =
            "0 0 12px rgba(245,158,11,0.45)";

        strengthText.textContent =
            "Fair";

        strengthText.style.color =
            "#FBBF24";

        if (requirementsText) {

            requirementsText.textContent =
                "Password can be stronger";

        }

    }

    else if (strength === 3) {

        strengthBar.style.background =
            "#F59E0B";

        strengthBar.style.boxShadow =
            "0 0 14px rgba(245,158,11,0.50)";

        strengthText.textContent =
            "Good";

        strengthText.style.color =
            "#FBBF24";

        if (requirementsText) {

            requirementsText.textContent =
                "Almost there";

        }

    }

    else {

        strengthBar.style.background =
            "#22C55E";

        strengthBar.style.boxShadow =
            "0 0 16px rgba(34,197,94,0.50)";

        strengthText.textContent =
            "Strong";

        strengthText.style.color =
            "#34D399";

        if (requirementsText) {

            requirementsText.textContent =
                "Strong password";

        }

    }

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

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
        email
    );

}


function validateAdmissionNumber(
    admissionNumber
) {

    return /^[0-9]{2}CAPMCA[0-9]{3}$/.test(
        admissionNumber
    );

}


function validatePhoneNumber(phone) {

    return /^[6-9][0-9]{9}$/.test(
        phone
    );

}


function validatePassword(password) {

    if (!password) {
        return false;
    }

    return (
        password.length >= 8 &&
        /[A-Z]/.test(password) &&
        /[0-9]/.test(password) &&
        /[^A-Za-z0-9]/.test(password)
    );

}


/* ======================================================
   FIELD ERROR
====================================================== */

function setFieldError(
    field,
    message
) {

    if (!field) {
        return;
    }


    field.classList.add(
        "error"
    );


    let errorElement = null;


    if (field.id) {

        errorElement =
            document.getElementById(
                `${field.id}Error`
            );

    }


    if (!errorElement) {

        errorElement =
            field.parentElement
                ? field.parentElement.querySelector(
                    ".field-error"
                )
                : null;

    }


    if (!errorElement) {

        errorElement =
            document.createElement(
                "p"
            );

        errorElement.className =
            "field-error mt-2 text-sm text-red-400 font-medium";


        if (field.parentElement) {

            field.parentElement.appendChild(
                errorElement
            );

        }

    }


    errorElement.textContent =
        message;

    errorElement.classList.remove(
        "hidden"
    );

}


/* ======================================================
   CLEAR FIELD ERROR
====================================================== */

function clearFieldError(
    field
) {

    if (!field) {
        return;
    }


    field.classList.remove(
        "error"
    );


    let errorElement = null;


    if (field.id) {

        errorElement =
            document.getElementById(
                `${field.id}Error`
            );

    }


    if (!errorElement) {

        errorElement =
            field.parentElement
                ? field.parentElement.querySelector(
                    ".field-error"
                )
                : null;

    }


    if (errorElement) {

        errorElement.textContent =
            "";

        errorElement.classList.add(
            "hidden"
        );

    }

}


/* ======================================================
   REGISTER STUDENT
====================================================== */

async function registerStudent(event) {

    event.preventDefault();


    const form =
        event.target;


    if (!form) {
        return;
    }


    /* ==================================================
       HARD DOUBLE-SUBMISSION PROTECTION
    ================================================== */

    if (
        form.dataset.submitting ===
        "true"
    ) {

        return;

    }


    /* ==================================================
       GET FIELDS
    ================================================== */

    const nameField =
        document.getElementById(
            "fullName"
        );

    const dobField =
        document.getElementById(
            "dob"
        );

    const emailField =
        document.getElementById(
            "email"
        );

    const admissionField =
        document.getElementById(
            "admissionNo"
        );

    const phoneField =
        document.getElementById(
            "phone"
        );

    const departmentField =
        document.getElementById(
            "department"
        );

    const yearField =
        document.getElementById(
            "year"
        );

    const passwordField =
        document.getElementById(
            "password"
        );

    const confirmPasswordField =
        document.getElementById(
            "confirmPassword"
        );


    const selectedGender =
        document.querySelector(
            'input[name="gender"]:checked'
        );


    /* ==================================================
       READ VALUES
    ================================================== */

    const nameValue =
        nameField
            ? nameField.value.trim()
            : "";

    const dobValue =
        dobField
            ? dobField.value.trim()
            : "";

    const emailValue =
        emailField
            ? emailField.value.trim()
            : "";

    const admissionValue =
        admissionField
            ? admissionField.value
                .trim()
                .toUpperCase()
            : "";

    const phoneValue =
        phoneField
            ? phoneField.value.trim()
            : "";

    const departmentValue =
        departmentField
            ? departmentField.value.trim()
            : "";

    const yearValue =
        yearField
            ? yearField.value.trim()
            : "";

    const genderValue =
        selectedGender
            ? selectedGender.value
            : "";

    const passwordValue =
        passwordField
            ? passwordField.value
            : "";

    const confirmPasswordValue =
        confirmPasswordField
            ? confirmPasswordField.value
            : "";


    /* ==================================================
       VALIDATION
    ================================================== */

    let isValid = true;


    if (!validateRequired(nameValue)) {

        setFieldError(
            nameField,
            "Full Name is required."
        );

        isValid = false;

    } else if (
        !/^[A-Za-z ]+$/.test(
            nameValue
        )
    ) {

        setFieldError(
            nameField,
            "Full Name can contain only letters and spaces."
        );

        isValid = false;

    } else {

        clearFieldError(
            nameField
        );

    }


    if (!validateRequired(dobValue)) {

        setFieldError(
            dobField,
            "Date of Birth is required."
        );

        isValid = false;

    } else {

        clearFieldError(
            dobField
        );

    }


    if (!validateRequired(admissionValue)) {

        setFieldError(
            admissionField,
            "Admission Number is required."
        );

        isValid = false;

    } else if (
        !validateAdmissionNumber(
            admissionValue
        )
    ) {

        setFieldError(
            admissionField,
            "Use the format 25CAPMCA080."
        );

        isValid = false;

    } else {

        clearFieldError(
            admissionField
        );

    }


    if (!validateRequired(phoneValue)) {

        setFieldError(
            phoneField,
            "Phone Number is required."
        );

        isValid = false;

    } else if (
        !validatePhoneNumber(
            phoneValue
        )
    ) {

        setFieldError(
            phoneField,
            "Enter a valid 10-digit Indian mobile number."
        );

        isValid = false;

    } else {

        clearFieldError(
            phoneField
        );

    }


    if (!validateRequired(emailValue)) {

        setFieldError(
            emailField,
            "College Email is required."
        );

        isValid = false;

    } else if (
        !validateEmail(emailValue)
    ) {

        setFieldError(
            emailField,
            "Enter a valid email address."
        );

        isValid = false;

    } else if (
        !/@sonatech\.ac\.in$/i.test(
            emailValue
        )
    ) {

        setFieldError(
            emailField,
            "Use only your @sonatech.ac.in email."
        );

        isValid = false;

    } else {

        clearFieldError(
            emailField
        );

    }


    if (!validateRequired(departmentValue)) {

        setFieldError(
            departmentField,
            "Department is required."
        );

        isValid = false;

    } else if (
        departmentValue !== "MCA"
    ) {

        setFieldError(
            departmentField,
            "Please select MCA."
        );

        isValid = false;

    } else {

        clearFieldError(
            departmentField
        );

    }


    if (!validateRequired(yearValue)) {

        setFieldError(
            yearField,
            "Year is required."
        );

        isValid = false;

    } else if (
        !["I Year", "II Year"].includes(
            yearValue
        )
    ) {

        setFieldError(
            yearField,
            "Please select a valid Year."
        );

        isValid = false;

    } else {

        clearFieldError(
            yearField
        );

    }


    const genderError =
        document.getElementById(
            "genderError"
        );


    if (!selectedGender) {

        if (genderError) {

            genderError.textContent =
                "Please select your gender.";

            genderError.classList.remove(
                "hidden"
            );

        }

        isValid = false;

    } else {

        if (genderError) {

            genderError.textContent =
                "";

            genderError.classList.add(
                "hidden"
            );

        }

    }


    if (!validateRequired(passwordValue)) {

        setFieldError(
            passwordField,
            "Password is required."
        );

        isValid = false;

    } else if (
        passwordValue.length < 8
    ) {

        setFieldError(
            passwordField,
            "Password must contain at least 8 characters."
        );

        isValid = false;

    } else if (
        !/[A-Z]/.test(passwordValue)
    ) {

        setFieldError(
            passwordField,
            "Password must contain at least one uppercase letter."
        );

        isValid = false;

    } else if (
        !/[0-9]/.test(passwordValue)
    ) {

        setFieldError(
            passwordField,
            "Password must contain at least one number."
        );

        isValid = false;

    } else if (
        !/[^A-Za-z0-9]/.test(
            passwordValue
        )
    ) {

        setFieldError(
            passwordField,
            "Password must contain at least one special character."
        );

        isValid = false;

    } else {

        clearFieldError(
            passwordField
        );

    }


    if (
        !validateRequired(
            confirmPasswordValue
        )
    ) {

        setFieldError(
            confirmPasswordField,
            "Please confirm your password."
        );

        isValid = false;

    } else if (
        passwordValue !==
        confirmPasswordValue
    ) {

        setFieldError(
            confirmPasswordField,
            "Passwords do not match."
        );

        isValid = false;

    } else {

        clearFieldError(
            confirmPasswordField
        );

    }


    if (!isValid) {

        const firstInvalid =
            form.querySelector(
                ".error"
            );

        if (firstInvalid) {

            firstInvalid.focus();

        }

        return;

    }


    const formData =
        new FormData(form);


    formData.set(
        "admissionNo",
        admissionValue
    );

    formData.set(
        "gender",
        genderValue
    );


    const submitButton =
        document.getElementById(
            "registerButton"
        ) ||
        form.querySelector(
            'button[type="submit"]'
        );


    const originalButtonText =
        submitButton
            ? submitButton.innerHTML
            : "";


    /* ==================================================
       SINGLE CLICK LOCK
    ================================================== */

    form.dataset.submitting =
        "true";


    if (submitButton) {

        submitButton.disabled =
            true;

        submitButton.setAttribute(
            "aria-disabled",
            "true"
        );

        submitButton.style.cursor =
            "not-allowed";

        submitButton.style.opacity =
            "0.85";

        submitButton.innerHTML =
            `
                <span
                    style="
                        display:inline-block;
                        width:18px;
                        height:18px;
                        border:2px solid rgba(255,255,255,0.35);
                        border-top-color:#ffffff;
                        border-radius:50%;
                        animation:votifyRegisterSpin 0.7s linear infinite;
                        margin-right:9px;
                        vertical-align:-3px;
                    "
                ></span>
                Creating Account...
            `;

    }


    if (
        !document.getElementById(
            "votifyRegisterSpinnerStyle"
        )
    ) {

        const spinnerStyle =
            document.createElement(
                "style"
            );

        spinnerStyle.id =
            "votifyRegisterSpinnerStyle";

        spinnerStyle.textContent = `
            @keyframes votifyRegisterSpin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            #registerButton:disabled {
                cursor: not-allowed !important;
            }
        `;

        document.head.appendChild(
            spinnerStyle
        );

    }


    /* ==================================================
       SEND REGISTRATION REQUEST
    ================================================== */

    try {

        const response =
            await fetch(
                "../../backend/student/register.php",
                {
                    method: "POST",
                    body: formData
                }
            );


        const responseText =
            await response.text();


        let result;


        try {

            result =
                JSON.parse(
                    responseText
                );

        } catch (jsonError) {

            console.error(
                "Invalid server response:",
                responseText
            );

            throw new Error(
                "Invalid server response."
            );

        }


        /* ==================================================
           OTP REQUIRED
        ================================================== */

        if (
            result.status ===
            "otp_required"
        ) {

            showOtpModal(
                result.email ||
                emailValue
            );

            /*
             * IMPORTANT:
             *
             * Registration button remains locked.
             * User must complete OTP verification.
             */

            return;

        }


        /* ==================================================
           SUCCESS
        ================================================== */

        if (
            result.status ===
            "success"
        ) {

            showToast(
                result.message ||
                "Registration successful.",
                "success"
            );


            setTimeout(() => {

                window.location.href =
                    result.redirect ||
                    "../../index.html";

            }, 1200);

            return;

        }


        /* ==================================================
           SERVER ERROR
        ================================================== */

        showToast(
            result.message ||
            "Registration failed.",
            "error"
        );


        unlockRegistrationForm(
            form,
            submitButton,
            originalButtonText
        );


        if (result.field) {

            const errorField =
                document.getElementById(
                    result.field
                );

            if (errorField) {

                setFieldError(
                    errorField,
                    result.message ||
                    "Please check this field."
                );

                if (
                    typeof errorField.focus ===
                    "function"
                ) {

                    errorField.focus();

                }

            }

        }


    } catch (error) {

        console.error(
            "Registration Error:",
            error
        );


        showToast(
            "Unable to connect to server. Please try again.",
            "error"
        );


        unlockRegistrationForm(
            form,
            submitButton,
            originalButtonText
        );

    }

}


/* ======================================================
   UNLOCK REGISTRATION FORM
====================================================== */

function unlockRegistrationForm(
    form,
    submitButton,
    originalButtonText
) {

    if (!form) {
        return;
    }


    form.dataset.submitting =
        "false";


    if (submitButton) {

        submitButton.disabled =
            false;

        submitButton.removeAttribute(
            "aria-disabled"
        );

        submitButton.style.cursor =
            "";

        submitButton.style.opacity =
            "";

        if (originalButtonText) {

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
        "*".repeat(
            maskedLength
        ) +
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
        document.createElement(
            "div"
        );


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


                <!-- =======================================
                     SIX OTP BOXES
                ======================================== -->

                <div
                    class="otp-boxes"
                    id="otpBoxes"
                    role="group"
                    aria-label="Enter 6-digit OTP"
                >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox1"
                        maxlength="1"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        aria-label="OTP digit 1"
                    >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox2"
                        maxlength="1"
                        inputmode="numeric"
                        aria-label="OTP digit 2"
                    >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox3"
                        maxlength="1"
                        inputmode="numeric"
                        aria-label="OTP digit 3"
                    >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox4"
                        maxlength="1"
                        inputmode="numeric"
                        aria-label="OTP digit 4"
                    >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox5"
                        maxlength="1"
                        inputmode="numeric"
                        aria-label="OTP digit 5"
                    >

                    <input
                        type="text"
                        class="otp-box"
                        id="otpBox6"
                        maxlength="1"
                        inputmode="numeric"
                        aria-label="OTP digit 6"
                    >

                </div>


                <!-- Hidden combined OTP value -->

                <input
                    type="hidden"
                    id="registrationOtp"
                    value=""
                >


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


    document.body.appendChild(
        modal
    );


    otpModal =
        modal;


    /* ==================================================
       OTP MODAL STYLES
    ================================================== */

    if (
        !document.getElementById(
            "registrationOtpStyles"
        )
    ) {

        const style =
            document.createElement(
                "style"
            );


        style.id =
            "registrationOtpStyles";


        style.textContent = `

            #registrationOtpModal {
                position: fixed;
                inset: 0;
                z-index: 2147483000;
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
                width: min(440px, 100%);
                padding: 34px 32px 30px;
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
                color:
                    #ffffff;
            }


            .otp-mail-icon {
                width: 62px;
                height: 62px;
                margin: 2px auto 18px;
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


            /* ==================================================
               SIX OTP BOXES
            ================================================== */

            .otp-boxes {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                margin:
                    2px 0 18px;
            }


            .otp-box {
                width: 48px;
                height: 54px;
                flex: 0 0 48px;
                box-sizing: border-box;

                border:
                    1px solid
                    rgba(255,255,255,0.10);

                border-radius: 12px;

                outline: none;

                background:
                    rgba(255,255,255,0.045);

                color:
                    #ffffff;

                text-align:
                    center;

                font-size:
                    22px;

                font-weight:
                    700;

                caret-color:
                    #60a5fa;

                transition:
                    border-color 0.2s ease,
                    background 0.2s ease,
                    box-shadow 0.2s ease,
                    transform 0.15s ease;
            }


            .otp-box::selection {
                background:
                    rgba(96,165,250,0.35);
            }


            .otp-box:focus {
                border-color:
                    rgba(96,165,250,0.85);

                background:
                    rgba(59,130,246,0.09);

                box-shadow:
                    0 0 0 3px
                    rgba(59,130,246,0.12),
                    0 0 18px
                    rgba(96,165,250,0.12);

                transform:
                    translateY(-1px);
            }


            .otp-box.otp-filled {
                border-color:
                    rgba(96,165,250,0.45);

                background:
                    rgba(59,130,246,0.08);
            }


            .otp-box.otp-error {
                border-color:
                    rgba(239,68,68,0.85);

                background:
                    rgba(239,68,68,0.08);

                box-shadow:
                    0 0 0 3px
                    rgba(239,68,68,0.10);
            }


            .otp-box.otp-success {
                border-color:
                    rgba(34,197,94,0.75);

                background:
                    rgba(34,197,94,0.08);

                box-shadow:
                    0 0 14px
                    rgba(34,197,94,0.14);
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
            }


            .verify-otp-button:disabled {
                cursor:
                    not-allowed;
                opacity:
                    0.65;
                transform:
                    none;
            }


            .verify-icon {
                margin-right:
                    7px;
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
                background:
                    transparent;
                color: #60a5fa;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
            }


            .resend-otp-button:disabled {
                color:
                    rgba(255,255,255,0.38);
                cursor:
                    not-allowed;
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
                        30px 18px 25px;
                    border-radius: 20px;
                }

                .otp-modal-card h2 {
                    font-size: 22px;
                }

                .otp-boxes {
                    gap: 6px;
                }

                .otp-box {
                    width: 43px;
                    height: 50px;
                    flex-basis: 43px;
                    font-size: 20px;
                    border-radius: 10px;
                }

            }


            @media (max-width: 360px) {

                .otp-boxes {
                    gap: 5px;
                }

                .otp-box {
                    width: 39px;
                    height: 48px;
                    flex-basis: 39px;
                    font-size: 19px;
                }

            }

        `;


        document.head.appendChild(
            style
        );

    }


    /* ==================================================
       OTP EVENT HANDLERS
    ================================================== */

    initializeOtpBoxes();


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


    const overlay =
        modal.querySelector(
            ".otp-modal-overlay"
        );


    if (overlay) {

        overlay.addEventListener(
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

}


/* ======================================================
   INITIALIZE OTP BOXES
====================================================== */

function initializeOtpBoxes() {

    const boxes =
        getOtpBoxes();


    if (boxes.length !== 6) {

        console.warn(
            "VOTIFY: OTP boxes not found."
        );

        return;

    }


    boxes.forEach(
        (box, index) => {

            /* ------------------------------------------
               INPUT
            ------------------------------------------ */

            box.addEventListener(
                "input",
                event => {

                    handleOtpInput(
                        event,
                        index
                    );

                }
            );


            /* ------------------------------------------
               KEYBOARD
            ------------------------------------------ */

            box.addEventListener(
                "keydown",
                event => {

                    handleOtpKeydown(
                        event,
                        index
                    );

                }
            );


            /* ------------------------------------------
               PASTE
            ------------------------------------------ */

            box.addEventListener(
                "paste",
                event => {

                    handleOtpPaste(
                        event
                    );

                }
            );


            /* ------------------------------------------
               FOCUS
            ------------------------------------------ */

            box.addEventListener(
                "focus",
                () => {

                    clearOtpBoxErrorState();

                }
            );

        }
    );

}


/* ======================================================
   GET OTP BOXES
====================================================== */

function getOtpBoxes() {

    return Array.from(
        document.querySelectorAll(
            ".otp-box"
        )
    );

}


/* ======================================================
   HANDLE OTP INPUT
====================================================== */

function handleOtpInput(
    event,
    index
) {

    const box =
        event.target;


    /*
     * Allow digits only.
     */

    let value =
        box.value
            .replace(/\D/g, "");


    /*
     * If more than one digit somehow
     * reaches the input, use the last
     * digit here.
     *
     * Full paste is handled separately.
     */

    if (value.length > 1) {

        value =
            value.charAt(
                value.length - 1
            );

    }


    box.value =
        value;


    if (value) {

        box.classList.add(
            "otp-filled"
        );


        box.classList.remove(
            "otp-error"
        );


        /*
         * Move to next box.
         */

        if (
            index <
            5
        ) {

            const nextBox =
                document.getElementById(
                    `otpBox${index + 2}`
                );

            if (nextBox) {

                nextBox.focus();

                nextBox.select();

            }

        }

    } else {

        box.classList.remove(
            "otp-filled"
        );

    }


    updateCombinedOtp();

}


/* ======================================================
   HANDLE OTP KEYDOWN
====================================================== */

function handleOtpKeydown(
    event,
    index
) {

    const box =
        event.target;


    /* ==================================================
       BACKSPACE
    ================================================== */

    if (
        event.key ===
        "Backspace"
    ) {

        if (
            box.value === "" &&
            index > 0
        ) {

            const previousBox =
                document.getElementById(
                    `otpBox${index}`
                );

            if (previousBox) {

                previousBox.value =
                    "";

                previousBox.classList.remove(
                    "otp-filled"
                );

                previousBox.focus();

            }

            updateCombinedOtp();

        }

        return;

    }


    /* ==================================================
       LEFT ARROW
    ================================================== */

    if (
        event.key ===
        "ArrowLeft"
    ) {

        if (index > 0) {

            const previousBox =
                document.getElementById(
                    `otpBox${index}`
                );

            if (previousBox) {

                previousBox.focus();

            }

        }

        return;

    }


    /* ==================================================
       RIGHT ARROW
    ================================================== */

    if (
        event.key ===
        "ArrowRight"
    ) {

        if (
            index < 5
        ) {

            const nextBox =
                document.getElementById(
                    `otpBox${index + 2}`
                );

            if (nextBox) {

                nextBox.focus();

            }

        }

        return;

    }


    /* ==================================================
       ENTER
    ================================================== */

    if (
        event.key ===
        "Enter"
    ) {

        event.preventDefault();

        verifyRegistrationOtp();

        return;

    }


    /*
     * Block non-numeric keys except
     * normal control/navigation keys.
     */

    if (
        event.key.length === 1 &&
        !/[0-9]/.test(event.key)
    ) {

        event.preventDefault();

    }

}


/* ======================================================
   HANDLE OTP PASTE
====================================================== */

function handleOtpPaste(
    event
) {

    event.preventDefault();


    const clipboardText =
        event.clipboardData
            ? event.clipboardData.getData(
                "text"
            )
            : "";


    const digits =
        clipboardText
            .replace(/\D/g, "")
            .slice(0, 6);


    if (!digits) {
        return;
    }


    const boxes =
        getOtpBoxes();


    digits
        .split("")
        .forEach(
            (digit, index) => {

                if (!boxes[index]) {
                    return;
                }


                boxes[index].value =
                    digit;


                boxes[index].classList.add(
                    "otp-filled"
                );


                boxes[index].classList.remove(
                    "otp-error"
                );

            }
        );


    /*
     * Clear remaining boxes if
     * pasted value has less than 6 digits.
     */

    for (
        let i = digits.length;
        i < boxes.length;
        i++
    ) {

        boxes[i].value =
            "";

        boxes[i].classList.remove(
            "otp-filled"
        );

    }


    updateCombinedOtp();


    /*
     * Focus:
     *
     * 6 digits -> last box
     * Less than 6 -> next empty box
     */

    if (
        digits.length >= 6
    ) {

        boxes[5].focus();

    } else {

        boxes[digits.length].focus();

    }

}


/* ======================================================
   UPDATE COMBINED OTP
====================================================== */

function updateCombinedOtp() {

    const boxes =
        getOtpBoxes();


    const otp =
        boxes
            .map(
                box =>
                    box.value
                        .replace(/\D/g, "")
            )
            .join("");


    const hiddenOtp =
        document.getElementById(
            "registrationOtp"
        );


    if (hiddenOtp) {

        hiddenOtp.value =
            otp;

    }


    return otp;

}


/* ======================================================
   CLEAR OTP BOXES
====================================================== */

function clearOtpBoxes() {

    const boxes =
        getOtpBoxes();


    boxes.forEach(
        box => {

            box.value =
                "";

            box.classList.remove(
                "otp-filled",
                "otp-error",
                "otp-success"
            );

        }
    );


    updateCombinedOtp();

}


/* ======================================================
   OTP ERROR STATE
====================================================== */

function showOtpBoxError() {

    const boxes =
        getOtpBoxes();


    boxes.forEach(
        box => {

            box.classList.add(
                "otp-error"
            );

        }
    );

}


/* ======================================================
   CLEAR OTP ERROR STATE
====================================================== */

function clearOtpBoxErrorState() {

    const boxes =
        getOtpBoxes();


    boxes.forEach(
        box => {

            box.classList.remove(
                "otp-error"
            );

        }
    );

}


/* ======================================================
   OTP SUCCESS STATE
====================================================== */

function showOtpBoxSuccess() {

    const boxes =
        getOtpBoxes();


    boxes.forEach(
        box => {

            box.classList.remove(
                "otp-error"
            );

            box.classList.add(
                "otp-success"
            );

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


    /*
     * Clear all OTP boxes
     * every time modal opens.
     */

    clearOtpBoxes();


    modal.style.display =
        "block";


    document.body.style.overflow =
        "hidden";


    otpRemainingSeconds =
        300;

    resendRemainingSeconds =
        30;


    updateOtpTimer();

    updateResendButton(
        resendRemainingSeconds
    );


    startOtpTimer();

    startResendTimer();


    const verifyButton =
        document.getElementById(
            "verifyOtpButton"
        );


    if (verifyButton) {

        verifyButton.disabled =
            false;

        verifyButton.innerHTML =
            `
                <span class="verify-icon">
                    🛡
                </span>
                Verify OTP
            `;

    }


    setTimeout(() => {

        const firstBox =
            document.getElementById(
                "otpBox1"
            );


        if (firstBox) {

            firstBox.focus();

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


    modal.style.display =
        "none";


    document.body.style.overflow =
        "";


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

                otpRemainingSeconds =
                    0;

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

        otpTimerInterval =
            null;

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


    expiryText.textContent =
        `OTP expires in ${
            String(minutes).padStart(2, "0")
        }:${
            String(seconds).padStart(2, "0")
        }`;

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

                resendRemainingSeconds =
                    0;

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

        resendTimerInterval =
            null;

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

        button.disabled =
            true;

        button.textContent =
            `Resend OTP (${seconds}s)`;

    } else {

        button.disabled =
            false;

        button.textContent =
            "Resend OTP";

    }

}


/* ======================================================
   VERIFY REGISTRATION OTP
====================================================== */

async function verifyRegistrationOtp() {

    const verifyButton =
        document.getElementById(
            "verifyOtpButton"
        );


    /*
     * Build final OTP from the six boxes.
     */

    const otp =
        updateCombinedOtp();


    /* ==================================================
       OTP VALIDATION
    ================================================== */

    if (!/^\d{6}$/.test(otp)) {

        showToast(
            "Please enter a valid 6-digit OTP.",
            "warning"
        );


        showOtpBoxError();


        const boxes =
            getOtpBoxes();


        const firstEmpty =
            boxes.find(
                box =>
                    box.value === ""
            );


        if (firstEmpty) {

            firstEmpty.focus();

        }


        return;

    }


    clearOtpBoxErrorState();


    if (
        otpRemainingSeconds <= 0
    ) {

        showToast(
            "OTP has expired. Please request a new OTP.",
            "warning"
        );


        showOtpBoxError();


        return;

    }


    if (verifyButton) {

        verifyButton.disabled =
            true;

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


        const responseText =
            await response.text();


        let result;


        try {

            result =
                JSON.parse(
                    responseText
                );

        } catch (jsonError) {

            console.error(
                "Invalid OTP server response:",
                responseText
            );

            throw new Error(
                "Invalid server response."
            );

        }


        /* ==================================================
           OTP SUCCESS
        ================================================== */

        if (
            result.status ===
            "success"
        ) {

            showOtpBoxSuccess();


            showToast(
                result.message ||
                "Email verified successfully.",
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
                        "../../index.html";

                }

            }, 1000);


            return;

        }


        /* ==================================================
           OTP ERROR
        ================================================== */

        showOtpBoxError();


        showToast(
            result.message ||
            "Invalid OTP.",
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

            verifyButton.disabled =
                false;

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

        resendButton.disabled =
            true;

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


        const responseText =
            await response.text();


        let result;


        try {

            result =
                JSON.parse(
                    responseText
                );

        } catch (jsonError) {

            console.error(
                "Invalid resend server response:",
                responseText
            );

            throw new Error(
                "Invalid server response."
            );

        }


        /* ==================================================
           RESEND SUCCESS
        ================================================== */

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
                "OTP sent successfully.",
                "success"
            );


            otpRemainingSeconds =
                300;

            resendRemainingSeconds =
                30;


            /*
             * Clear old OTP boxes after
             * receiving a new OTP.
             */

            clearOtpBoxes();


            const verifyButton =
                document.getElementById(
                    "verifyOtpButton"
                );


            if (verifyButton) {

                verifyButton.disabled =
                    false;

                verifyButton.innerHTML =
                    `
                        <span class="verify-icon">
                            🛡
                        </span>
                        Verify OTP
                    `;

            }


            updateOtpTimer();

            updateResendButton(
                resendRemainingSeconds
            );


            startOtpTimer();

            startResendTimer();


            const firstBox =
                document.getElementById(
                    "otpBox1"
                );


            if (firstBox) {

                firstBox.focus();

            }


        } else {

            showToast(
                result.message ||
                "Unable to resend OTP.",
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
   END OF REGISTER.JS
====================================================== */