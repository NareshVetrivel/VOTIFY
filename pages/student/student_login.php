<?php
/* ==========================================================
   VOTIFY
   Student Login
   File : pages/student/student_login.php
========================================================== */

require_once __DIR__ . "/check-login.php";


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   ALREADY LOGGED IN
========================================================== */

if (
    isset($_SESSION["student_id"]) &&
    (int) $_SESSION["student_id"] > 0 &&
    !empty($_SESSION["student_logged_in"])
) {

    header(
        "Location: security_check.php"
    );

    exit();

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Student Login | VOTIFY
    </title>


    <!-- ==========================================================
         TAILWIND CSS
    ========================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- ==========================================================
         REMIX ICONS
    ========================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >


    <!-- ==========================================================
         CUSTOM CSS
    ========================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/custom.css"
    >


    <link
        rel="stylesheet"
        href="../../assets/css/animations.css"
    >

</head>


<body
    class="bg-[#0B1020] text-white min-h-screen flex flex-col overflow-x-hidden"
>


    <!-- ==========================================================
         LOADER
    ========================================================== -->

    <div id="loader-container"></div>


    <!-- ==========================================================
         ANIMATED BACKGROUND
    ========================================================== -->

    <div class="fixed inset-0 -z-10 overflow-hidden">


        <div
            class="
            absolute
            top-0
            left-0
            w-96
            h-96
            bg-blue-600/20
            blur-[140px]
            rounded-full
            "
        ></div>


        <div
            class="
            absolute
            bottom-0
            right-0
            w-96
            h-96
            bg-pink-600/20
            blur-[150px]
            rounded-full
            "
        ></div>


        <div
            class="
            absolute
            top-1/2
            left-1/2
            w-80
            h-80
            bg-purple-600/20
            blur-[120px]
            rounded-full
            transform
            -translate-x-1/2
            -translate-y-1/2
            "
        ></div>

    </div>


    <!-- ==========================================================
         HEADER
    ========================================================== -->

    <div id="header"></div>


    <!-- ==========================================================
         LOGIN SECTION
    ========================================================== -->

    <section
        class="flex-1 py-14"
    >

        <div
            class="
            max-w-5xl
            mx-auto
            px-6
            "
        >


            <!-- ==================================================
                 PAGE HEADING
            ================================================== -->

            <div
                class="
                text-center
                mb-10
                "
            >

                <h1
                    class="
                    mt-6
                    text-4xl
                    md:text-5xl
                    font-bold
                    "
                >

                    <span class="gradient-text">

                        Student Login

                    </span>

                </h1>


                <p
                    class="
                    mt-4
                    text-slate-400
                    "
                >

                    Secure Student Authentication

                </p>

            </div>


            <!-- ==================================================
                 LOGIN CARD
            ================================================== -->

            <div
                class="
                glass
                rounded-3xl
                p-8
                md:p-10
                shadow-2xl
                "
            >


                <!-- ==================================================
                     SECURE IDENTITY VERIFICATION
                ================================================== -->

                <div
                    class="
                    rounded-2xl
                    border
                    border-blue-500/20
                    bg-blue-500/10
                    p-6
                    mb-8
                    "
                >

                    <div
                        class="
                        flex
                        items-start
                        gap-4
                        "
                    >

                        <div
                            class="
                            w-14
                            h-14
                            rounded-2xl
                            bg-blue-500/20
                            flex
                            items-center
                            justify-center
                            shrink-0
                            "
                        >

                            <i
                                class="
                                ri-shield-check-line
                                text-3xl
                                text-blue-400
                                "
                            ></i>

                        </div>


                        <div>

                            <h2
                                class="
                                text-xl
                                font-bold
                                text-blue-300
                                "
                            >

                                Secure Identity Verification

                            </h2>


                            <p
                                class="
                                mt-2
                                text-slate-300
                                leading-7
                                "
                            >

                                Complete all required verification steps
                                to authenticate your identity and securely
                                participate in the online voting process.

                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     LOGIN FORM START
                ================================================== -->

                <form
                    id="studentLoginForm"
                    class="
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-x-6
                    gap-y-6
                    "
                    novalidate
                >


                    <!-- ==================================================
                         ADMISSION NUMBER
                    ================================================== -->

                    <div>

                        <label
                            for="admissionNo"
                            class="
                            block
                            mb-2
                            font-medium
                            text-slate-300
                            "
                        >

                            Admission Number

                            <span class="text-pink-400">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <i
                                class="
                                ri-user-3-line
                                absolute
                                left-4
                                top-1/2
                                transform
                                -translate-y-1/2
                                text-slate-400
                                text-xl
                                "
                            ></i>


                            <input
                                type="text"
                                id="admissionNo"
                                name="admissionNo"
                                maxlength="11"
                                autocomplete="off"
                                placeholder="25CAPMCA080"
                                pattern="[0-9]{2}CAPMCA[0-9]{3}"
                                title="Admission Number must follow the format 25CAPMCA080"
                                class="
                                w-full
                                h-14
                                pl-12
                                pr-5
                                rounded-xl
                                bg-white/5
                                border
                                border-white/10
                                text-white
                                placeholder:text-slate-500
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/20
                                outline-none
                                transition-all
                                "
                                required
                            >

                        </div>

                    </div>


                    <!-- ==================================================
                         DATE OF BIRTH
                    ================================================== -->

                    <div>

                        <label
                            for="dob"
                            class="
                            block
                            mb-2
                            font-medium
                            text-slate-300
                            "
                        >

                            Date of Birth

                            <span class="text-pink-400">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <i
                                class="
                                ri-calendar-line
                                absolute
                                left-4
                                top-1/2
                                transform
                                -translate-y-1/2
                                text-slate-400
                                text-xl
                                "
                            ></i>


                            <input
                                type="date"
                                id="dob"
                                name="dob"
                                class="
                                w-full
                                h-14
                                pl-12
                                pr-5
                                rounded-xl
                                bg-white/5
                                border
                                border-white/10
                                text-white
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/20
                                outline-none
                                transition-all
                                "
                                required
                            >

                        </div>

                    </div>


                    <!-- ==================================================
                         COLLEGE EMAIL
                    ================================================== -->

                    <div>

                        <label
                            for="collegeEmail"
                            class="
                            block
                            mb-2
                            font-medium
                            text-slate-300
                            "
                        >

                            College Email

                            <span class="text-pink-400">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <i
                                class="
                                ri-mail-line
                                absolute
                                left-4
                                top-1/2
                                transform
                                -translate-y-1/2
                                text-slate-400
                                text-xl
                                "
                            ></i>


                            <input
                                type="email"
                                id="collegeEmail"
                                name="collegeEmail"
                                autocomplete="off"
                                placeholder="yourname@sonatech.ac.in"
                                pattern="[a-zA-Z0-9._%+-]+@sonatech\.ac\.in"
                                title="Only @sonatech.ac.in college email addresses are allowed"
                                class="
                                w-full
                                h-14
                                pl-12
                                pr-5
                                rounded-xl
                                bg-white/5
                                border
                                border-white/10
                                text-white
                                placeholder:text-slate-500
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/20
                                outline-none
                                transition-all
                                "
                                required
                            >

                        </div>

                    </div>


                    <!-- ==================================================
                         PASSWORD
                    ================================================== -->

                    <div>

                        <label
                            for="password"
                            class="
                            block
                            mb-2
                            font-medium
                            text-slate-300
                            "
                        >

                            Password

                            <span class="text-pink-400">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <!-- Password Icon -->

                            <i
                                class="
                                ri-lock-password-line
                                absolute
                                left-4
                                top-1/2
                                transform
                                -translate-y-1/2
                                text-slate-400
                                text-xl
                                "
                                aria-hidden="true"
                            ></i>


                            <!-- Password Input -->

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter Password"
                                autocomplete="current-password"
                                class="
                                w-full
                                h-14
                                pl-12
                                pr-14
                                rounded-xl
                                bg-white/5
                                border
                                border-white/10
                                text-white
                                placeholder:text-slate-500
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/20
                                outline-none
                                transition-all
                                "
                                required
                            >


                            <!-- Password Eye Toggle -->

                            <button
                                type="button"
                                id="togglePassword"
                                aria-label="Show password"
                                aria-controls="password"
                                class="
                                absolute
                                right-5
                                top-1/2
                                transform
                                -translate-y-1/2
                                text-slate-400
                                hover:text-blue-400
                                transition
                                "
                            >

                                <i
                                    id="togglePasswordIcon"
                                    class="
                                    ri-eye-line
                                    text-xl
                                    "
                                    aria-hidden="true"
                                ></i>

                            </button>

                        </div>

                    </div>


                    <!-- ==================================================
                         AUTHENTICATION REQUIREMENTS
                    ================================================== -->

                    <div
                        class="
                        md:col-span-2
                        rounded-2xl
                        border
                        border-emerald-500/20
                        bg-emerald-500/10
                        p-6
                        "
                    >

                        <div
                            class="
                            flex
                            items-center
                            gap-3
                            mb-5
                            "
                        >

                            <div
                                class="
                                w-12
                                h-12
                                rounded-xl
                                bg-emerald-500/20
                                flex
                                items-center
                                justify-center
                                "
                            >

                                <i
                                    class="
                                    ri-shield-check-line
                                    text-2xl
                                    text-emerald-400
                                    "
                                ></i>

                            </div>


                            <div>

                                <h2
                                    class="
                                    text-xl
                                    font-bold
                                    text-emerald-300
                                    "
                                >

                                    Authentication Requirements

                                </h2>


                                <p
                                    class="
                                    text-sm
                                    text-slate-400
                                    mt-1
                                    "
                                >

                                    Access is granted only after successful verification.

                                </p>

                            </div>

                        </div>


                        <div
                            class="
                            grid
                            grid-cols-1
                            sm:grid-cols-2
                            gap-4
                            "
                        >

                            <div
                                class="
                                flex
                                items-center
                                gap-3
                                "
                            >

                                <i
                                    class="
                                    ri-checkbox-circle-fill
                                    text-green-400
                                    text-lg
                                    "
                                ></i>

                                <span
                                    class="text-slate-300"
                                >

                                    Approved Registration Required

                                </span>

                            </div>


                            <div
                                class="
                                flex
                                items-center
                                gap-3
                                "
                            >

                                <i
                                    class="
                                    ri-checkbox-circle-fill
                                    text-green-400
                                    text-lg
                                    "
                                ></i>

                                <span
                                    class="text-slate-300"
                                >

                                    Unvoted Status Required

                                </span>

                            </div>


                            <div
                                class="
                                flex
                                items-center
                                gap-3
                                "
                            >

                                <i
                                    class="
                                    ri-checkbox-circle-fill
                                    text-green-400
                                    text-lg
                                    "
                                ></i>

                                <span
                                    class="text-slate-300
                                    "
                                >

                                    Four-Step Identity Verification

                                </span>

                            </div>


                            <div
                                class="
                                flex
                                items-center
                                gap-3
                                "
                            >

                                <i
                                    class="
                                    ri-mail-check-line
                                    text-green-400
                                    text-lg
                                    "
                                ></i>

                                <span
                                    class="text-slate-300"
                                >

                                    Email OTP Verification

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         LOGIN BUTTON
                    ================================================== -->

                    <div
                        class="
                        md:col-span-2
                        mt-2
                        "
                    >

                        <button
                            type="submit"
                            id="loginButton"
                            class="
                            btn-primary
                            w-full
                            h-14
                            rounded-xl
                            text-lg
                            font-semibold
                            flex
                            items-center
                            justify-center
                            gap-3
                            hover:scale-[1.02]
                            transition-all
                            duration-300
                            "
                        >

                            <i
                                class="
                                ri-login-circle-line
                                text-xl
                                "
                                aria-hidden="true"
                            ></i>

                            Login Securely

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </section>


    <!-- ==========================================================
         VOTIFY COMMON TOAST

         IMPORTANT:
         Existing VOTIFY toast design is preserved.

         Position:
         TOP-RIGHT

         No new toast UI is created here.
    ========================================================== -->

    <?php

    $toastComponent =
        __DIR__ .
        "/../../components/toast.php";


    if (file_exists($toastComponent)) {

        require_once $toastComponent;

    } else {

        error_log(
            "VOTIFY: components/toast.php not found."
        );

    }

    ?>


    <!-- ==========================================================
         FOOTER
    ========================================================== -->

    <div id="footer"></div>


    <!-- ==========================================================
         APP JS
    ========================================================== -->

    <script
        src="../../assets/js/app.js"
    ></script>


    <!-- ==========================================================
         COMMON TOAST JS

         MUST LOAD BEFORE login.js
    ========================================================== -->

    <script
        src="../../assets/js/toast.js"
    ></script>


    <!-- ==========================================================
         STUDENT LOGIN JS
    ========================================================== -->

    <script
        src="../../assets/js/login.js"
    ></script>


</body>

</html>