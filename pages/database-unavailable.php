<?php
/* ==========================================================
   VOTIFY
   Database Unavailable
   File : pages/database-unavailable.php
========================================================== */
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Service Temporarily Unavailable | VOTIFY
    </title>


    <!-- ======================================================
         TAILWIND CSS
    ======================================================= -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- ======================================================
         REMIX ICONS
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet">


    <!-- ======================================================
         VOTIFY CUSTOM CSS
    ======================================================= -->

    <link
        rel="stylesheet"
        href="../assets/css/custom.css">

    <link
        rel="stylesheet"
        href="../assets/css/animations.css">


    <!-- ======================================================
         PAGE SPECIFIC STYLE
    ======================================================= -->

    <style>

        /* ==================================================
           PAGE SCROLL CONTROL
        =================================================== */

        html,
        body {

            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;

        }


        /* ==================================================
           SERVICE UNAVAILABLE SVG ICON
        =================================================== */

        .service-error-icon {

            color: #fb923c;

            box-shadow:
                0 0 14px rgba(251, 146, 60, 0.14),
                0 0 28px rgba(251, 146, 60, 0.07);

            animation:
                serviceErrorGlow 2.5s ease-in-out infinite;

        }


        .service-error-icon svg {

            width: 28px;
            height: 28px;

            filter:
                drop-shadow(
                    0 0 6px rgba(251, 146, 60, 0.45)
                );

        }


        @keyframes serviceErrorGlow {

            0%,
            100% {

                box-shadow:
                    0 0 14px rgba(251, 146, 60, 0.14),
                    0 0 28px rgba(251, 146, 60, 0.07);

            }


            50% {

                box-shadow:
                    0 0 20px rgba(251, 146, 60, 0.25),
                    0 0 42px rgba(251, 146, 60, 0.12);

            }

        }


        /* ==================================================
           RETRY BUTTON
        =================================================== */

        .database-retry-button {

            box-shadow:
                0 6px 18px rgba(37, 99, 235, 0.18);

        }


        .database-retry-button:hover:not(:disabled) {

            transform:
                translateY(-2px);

            box-shadow:
                0 9px 24px rgba(37, 99, 235, 0.25);

        }


        .database-retry-button:active:not(:disabled) {

            transform:
                translateY(0)
                scale(0.98);

        }


        .database-retry-button:disabled {

            cursor:
                not-allowed !important;

        }


        /* ==================================================
           HOME BUTTON
        =================================================== */

        .database-home-button:hover {

            transform:
                translateY(-2px);

        }


        .database-home-button:active {

            transform:
                translateY(0)
                scale(0.98);

        }


        /* ==================================================
           MAIN LAYOUT
        =================================================== */

        .database-main {

            min-height: 0;
            overflow: hidden;

        }


        /* ==================================================
           MOBILE SAFETY
        =================================================== */

        @media (max-width: 640px) {

            html,
            body {

                overflow-y: auto;

            }


            .database-page {

                min-height: 100dvh;
                height: auto;

            }


            .database-card {

                border-radius:
                    1.25rem;

            }

        }

    </style>

</head>


<!-- ==========================================================
     BODY
========================================================== -->

<body
    class="
        bg-[#0B1020]
        text-white
        overflow-hidden
    "
>


<!-- ==========================================================
     PAGE WRAPPER
========================================================== -->

<div
    class="
        database-page
        relative
        w-full
        h-screen
        flex
        flex-col
        overflow-hidden
    "
>


    <!-- ======================================================
         LOADER
    ======================================================= -->

    <div id="loader-container"></div>


    <!-- ======================================================
         BACKGROUND
    ======================================================= -->

    <div
        class="
            fixed
            inset-0
            -z-10
            overflow-hidden
            pointer-events-none
        "
    >

        <!-- BLUE GLOW -->

        <div
            class="
                absolute
                top-0
                left-0
                w-72
                h-72
                bg-blue-600/20
                blur-[130px]
                rounded-full
            "
        ></div>


        <!-- PINK GLOW -->

        <div
            class="
                absolute
                bottom-0
                right-0
                w-72
                h-72
                bg-pink-600/20
                blur-[130px]
                rounded-full
            "
        ></div>


        <!-- PURPLE CENTER GLOW -->

        <div
            class="
                absolute
                top-1/2
                left-1/2
                -translate-x-1/2
                -translate-y-1/2
                w-64
                h-64
                bg-purple-600/10
                blur-[110px]
                rounded-full
            "
        ></div>

    </div>


    <!-- ======================================================
         HEADER COMPONENT
    ======================================================= -->

    <div
        id="header"
        class="shrink-0"
    ></div>


    <!-- ======================================================
         MAIN
    ======================================================= -->

    <main
        class="
            database-main
            flex-1
            flex
            items-center
            justify-center
            px-4
            py-2
            sm:px-6
            sm:py-2
        "
    >


        <!-- ==================================================
             DATABASE UNAVAILABLE CARD
        =================================================== -->

        <section
            class="
                database-card
                w-full
                max-w-[480px]
                glass
                border
                border-slate-700/60
                rounded-2xl
                px-5
                py-4
                sm:px-6
                sm:py-4
                text-center
                shadow-2xl
                animate-[fadeUp_.35s_ease]
            "
        >


            <!-- ==============================================
                 SERVICE UNAVAILABLE SVG ICON
            =============================================== -->

            <div
                class="
                    service-error-icon
                    mx-auto
                    w-12
                    h-12
                    sm:w-14
                    sm:h-14
                    rounded-full
                    bg-orange-500/10
                    border
                    border-orange-500/25
                    flex
                    items-center
                    justify-center
                "
                aria-hidden="true"
            >

                <svg
                    viewBox="0 0 64 64"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >

                    <!-- SERVER BODY -->

                    <rect
                        x="9"
                        y="10"
                        width="46"
                        height="32"
                        rx="6"
                        stroke="currentColor"
                        stroke-width="3"
                    />


                    <!-- SERVER DIVIDER -->

                    <path
                        d="M9 26H55"
                        stroke="currentColor"
                        stroke-width="3"
                    />


                    <!-- SERVER STATUS LIGHTS -->

                    <circle
                        cx="18"
                        cy="18"
                        r="2.2"
                        fill="currentColor"
                    />

                    <circle
                        cx="26"
                        cy="18"
                        r="2.2"
                        fill="currentColor"
                    />

                    <circle
                        cx="18"
                        cy="34"
                        r="2.2"
                        fill="currentColor"
                    />


                    <!-- BROKEN CONNECTION -->

                    <path
                        d="M43 48L55 60"
                        stroke="currentColor"
                        stroke-width="4"
                        stroke-linecap="round"
                    />

                    <path
                        d="M55 48L43 60"
                        stroke="currentColor"
                        stroke-width="4"
                        stroke-linecap="round"
                    />

                </svg>

            </div>


            <!-- ==============================================
                 TITLE
            =============================================== -->

            <h1
                class="
                    mt-3
                    text-2xl
                    sm:text-[27px]
                    font-bold
                    tracking-tight
                    bg-gradient-to-r
                    from-blue-400
                    via-purple-400
                    to-pink-400
                    bg-clip-text
                    text-transparent
                "
            >

                Service Unavailable

            </h1>


            <!-- ==============================================
                 MAIN DESCRIPTION
            =============================================== -->

            <p
                class="
                    mt-2
                    text-sm
                    sm:text-[15px]
                    leading-5
                    text-slate-300
                    max-w-sm
                    mx-auto
                "
            >

                VOTIFY is temporarily unable to connect to its
                election services.

            </p>


            <!-- ==============================================
                 SECONDARY DESCRIPTION
            =============================================== -->

            <p
                class="
                    mt-1
                    text-xs
                    sm:text-[13px]
                    leading-5
                    text-slate-500
                "
            >

                Please wait a moment and try again.

            </p>


            <!-- ==============================================
                 INFORMATION NOTICE
            =============================================== -->

            <div
                class="
                    mt-3
                    rounded-xl
                    border
                    border-yellow-500/20
                    bg-yellow-500/10
                    px-3
                    py-2.5
                    sm:px-4
                    sm:py-2.5
                    text-left
                "
            >

                <div
                    class="
                        flex
                        items-start
                        gap-3
                    "
                >


                    <!-- INFO ICON -->

                    <div
                        class="
                            w-9
                            h-9
                            shrink-0
                            rounded-lg
                            bg-yellow-500/10
                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                ri-information-line
                                text-base
                                text-yellow-400
                            "
                        ></i>

                    </div>


                    <!-- NOTICE CONTENT -->

                    <div>

                        <h3
                            class="
                                font-semibold
                                text-sm
                                text-yellow-300
                            "
                        >

                            What happened?

                        </h3>


                        <p
                            class="
                                mt-0.5
                                text-xs
                                leading-5
                                text-slate-300
                            "
                        >

                            Our database service is temporarily unreachable.
                            Your account and election data remain protected.

                        </p>

                    </div>

                </div>

            </div>


            <!-- ==============================================
                 ACTION BUTTONS
            =============================================== -->

            <div
                class="
                    mt-3
                    flex
                    flex-col
                    sm:flex-row
                    items-stretch
                    sm:items-center
                    justify-center
                    gap-3
                "
            >


                <!-- TRY AGAIN -->

                <button
                    type="button"
                    id="retryDatabaseButton"
                    onclick="retryDatabaseConnection()"
                    class="
                        database-retry-button
                        btn-primary
                        inline-flex
                        items-center
                        justify-center
                        gap-2
                        min-w-[165px]
                        px-5
                        py-2.5
                        rounded-xl
                        text-sm
                        font-semibold
                        transition-all
                        duration-200
                    "
                >

                    <i
                        id="retryDatabaseIcon"
                        class="
                            ri-restart-line
                            text-lg
                            leading-none
                        "
                    ></i>


                    <span id="retryDatabaseText">

                        Try Again

                    </span>

                </button>


                <!-- BACK TO HOME -->

                <a
                    href="../index.html"
                    class="
                        database-home-button
                        inline-flex
                        items-center
                        justify-center
                        gap-2
                        min-w-[165px]
                        px-5
                        py-2.5
                        rounded-xl
                        border
                        border-slate-700
                        bg-slate-800/40
                        text-sm
                        font-semibold
                        text-slate-300
                        transition-all
                        duration-200
                        hover:bg-slate-800/70
                        hover:border-slate-600
                    "
                >

                    <i
                        class="
                            ri-home-5-line
                            text-lg
                        "
                    ></i>

                    Back to Home

                </a>

            </div>


            <!-- ==============================================
                 FOOTNOTE
            =============================================== -->

            <div
                class="
                    mt-3
                    flex
                    items-center
                    justify-center
                    gap-2
                    text-[11px]
                    text-slate-600
                "
            >

                <i class="ri-shield-check-line"></i>

                <span>

                    Your election data remains protected.

                </span>

            </div>


        </section>

    </main>


    <!-- ======================================================
         FOOTER COMPONENT
    ======================================================= -->

    <div
        id="footer"
        class="shrink-0"
    ></div>


</div>


<!-- ==========================================================
     APP JAVASCRIPT
========================================================== -->

<script src="../assets/js/app.js"></script>


<!-- ==========================================================
     DATABASE RETRY SCRIPT
========================================================== -->

<script>

    function retryDatabaseConnection() {

        const button =
            document.getElementById(
                "retryDatabaseButton"
            );


        const icon =
            document.getElementById(
                "retryDatabaseIcon"
            );


        const text =
            document.getElementById(
                "retryDatabaseText"
            );


        if (!button) {

            window.location.reload();

            return;

        }


        button.disabled = true;


        button.classList.add(
            "opacity-75",
            "cursor-not-allowed"
        );


        if (icon) {

            icon.className =
                "ri-loader-4-line text-lg animate-spin";

        }


        if (text) {

            text.innerText =
                "Checking...";

        }


        setTimeout(() => {

            window.location.reload();

        }, 700);

    }

</script>


</body>

</html>