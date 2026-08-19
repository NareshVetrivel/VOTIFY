<?php
/* ==========================================================
   VOTIFY
   Registration Closed
   File : pages/student/registration_closed.php
========================================================== */

require_once "check-registration-closed.php";
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <!-- ======================================================
         META
    ====================================================== -->

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    >

    <meta
        name="theme-color"
        content="#0B1020"
    >

    <title>Registration Closed | VOTIFY</title>


    <!-- ======================================================
         TAILWIND CSS
    ====================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- ======================================================
         REMIX ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
    >


    <!-- ======================================================
         VOTIFY CSS
    ====================================================== -->

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
    class="
        bg-[#0B1020]
        text-white
        min-h-screen
        h-screen
        flex
        flex-col
        overflow-x-hidden
        antialiased

        max-md:h-auto
        max-md:min-h-screen
    "
>


    <!-- ======================================================
         LOADER
    ====================================================== -->

    <div id="loader-container"></div>


    <!-- ======================================================
         BACKGROUND GLOW
    ====================================================== -->

    <div
        class="
            fixed
            inset-0
            -z-10
            pointer-events-none
            overflow-hidden
        "
        aria-hidden="true"
    >

        <!-- Blue Glow -->

        <div
            class="
                absolute
                -top-24
                -left-24
                w-64
                h-64
                sm:w-80
                sm:h-80
                rounded-full
                bg-blue-600/15
                blur-[110px]
                sm:blur-[135px]

                max-md:w-56
                max-md:h-56
                max-md:blur-[90px]
            "
        ></div>


        <!-- Pink Glow -->

        <div
            class="
                absolute
                -bottom-24
                -right-24
                w-64
                h-64
                sm:w-80
                sm:h-80
                rounded-full
                bg-pink-600/15
                blur-[110px]
                sm:blur-[135px]

                max-md:w-56
                max-md:h-56
                max-md:blur-[90px]
            "
        ></div>


        <!-- Purple Glow -->

        <div
            class="
                absolute
                top-1/2
                left-1/2
                -translate-x-1/2
                -translate-y-1/2
                w-48
                h-48
                sm:w-64
                sm:h-64
                rounded-full
                bg-purple-600/10
                blur-[90px]

                max-md:w-48
                max-md:h-48
                max-md:blur-[75px]
            "
        ></div>

    </div>


    <!-- ======================================================
         HEADER
    ====================================================== -->

    <div
        id="header"
        class="shrink-0"
    ></div>


    <!-- ======================================================
         MAIN
    ====================================================== -->

    <main
        class="
            relative
            flex-1
            min-h-0
            w-full
            flex
            items-center
            justify-center
            overflow-hidden
            px-4
            sm:px-6
            py-3
            sm:py-4

            max-md:items-start
            max-md:justify-center
            max-md:overflow-visible
            max-md:px-4
            max-md:py-5
        "
    >


        <!-- ==================================================
             REGISTRATION CLOSED CARD
        ================================================== -->

        <section
            class="
                glass
                relative
                w-full
                max-w-[600px]
                rounded-[20px]
                sm:rounded-[24px]
                border
                border-white/10
                shadow-2xl
                overflow-hidden
                text-center
                fade-up

                max-md:max-w-none
                max-md:w-full
                max-md:rounded-[18px]
            "
        >


            <!-- ==================================================
                 TOP GLOW
            ================================================== -->

            <div
                class="
                    absolute
                    top-0
                    left-1/2
                    -translate-x-1/2
                    w-40
                    h-12
                    bg-red-500/10
                    blur-[40px]
                    pointer-events-none

                    max-md:w-32
                    max-md:h-10
                    max-md:blur-[32px]
                "
                aria-hidden="true"
            ></div>


            <!-- ==================================================
                 CARD CONTENT
            ================================================== -->

            <div
                class="
                    relative
                    px-4
                    py-4
                    sm:px-6
                    sm:py-5

                    max-md:px-5
                    max-md:py-5
                "
            >


                <!-- ==================================================
                     LOCK ICON
                ================================================== -->

                <div
                    class="
                        mx-auto
                        w-14
                        h-14
                        sm:w-16
                        sm:h-16
                        rounded-full
                        bg-red-500/15
                        border
                        border-red-500/40
                        flex
                        items-center
                        justify-center
                        shadow-[0_0_25px_rgba(248,113,113,0.22)]

                        max-md:w-14
                        max-md:h-14
                        max-md:shadow-[0_0_20px_rgba(248,113,113,0.28)]
                    "
                >

                    <i
                        class="
                            ri-lock-line
                            text-2xl
                            sm:text-3xl
                            text-red-400
                            drop-shadow-[0_0_10px_rgba(248,113,113,0.75)]

                            max-md:text-2xl
                        "
                        aria-hidden="true"
                    ></i>

                </div>


                <!-- ==================================================
                     TITLE
                ================================================== -->

                <h1
                    class="
                        mt-3
                        text-2xl
                        sm:text-3xl
                        font-extrabold
                        tracking-tight
                        text-white

                        max-md:mt-3
                        max-md:text-[23px]
                        max-md:leading-tight
                    "
                >

                    Registration Closed

                </h1>


                <!-- ==================================================
                     MAIN DESCRIPTION
                ================================================== -->

                <p
                    class="
                        mt-2
                        text-sm
                        sm:text-[15px]
                        leading-5
                        text-slate-300
                        max-w-xl
                        mx-auto

                        max-md:mt-2.5
                        max-md:text-[13px]
                        max-md:leading-[1.55]
                    "
                >

                    Student registration is temporarily unavailable
                    because the election is currently in progress.

                </p>


                <!-- ==================================================
                     SECONDARY DESCRIPTION
                ================================================== -->

                <p
                    class="
                        mt-1.5
                        text-xs
                        sm:text-[13px]
                        leading-5
                        text-slate-500
                        max-w-xl
                        mx-auto

                        max-md:mt-2
                        max-md:text-[11px]
                        max-md:leading-[1.55]
                    "
                >

                    Please wait until the administrator ends the election.
                    Registration will automatically reopen after the election
                    is completed.

                </p>


                <!-- ==================================================
                     NOTICE
                ================================================== -->

                <div
                    class="
                        mt-4
                        rounded-xl
                        border
                        border-yellow-500/20
                        bg-yellow-500/10
                        p-3
                        sm:p-3.5

                        max-md:mt-4
                        max-md:rounded-xl
                        max-md:p-3
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-2.5
                            text-left

                            max-md:gap-2.5
                        "
                    >


                        <!-- Notice Icon -->

                        <i
                            class="
                                ri-information-line
                                text-lg
                                sm:text-xl
                                text-yellow-400
                                mt-0.5
                                shrink-0

                                max-md:text-lg
                            "
                            aria-hidden="true"
                        ></i>


                        <!-- Notice Content -->

                        <div>

                            <h3
                                class="
                                    font-semibold
                                    text-xs
                                    sm:text-sm
                                    text-yellow-300

                                    max-md:text-xs
                                "
                            >

                                Why can't I register?

                            </h3>


                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    sm:text-xs
                                    text-slate-300
                                    leading-5

                                    max-md:text-[10px]
                                    max-md:leading-[1.5]
                                "
                            >

                                To ensure a secure and fair voting process,
                                new student registrations are disabled while
                                the election is active.

                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     BACK TO HOME
                ================================================== -->

                <div
                    class="
                        mt-4

                        max-md:mt-4
                    "
                >

                    <a
                        href="../../index.html"
                        class="
                            btn-primary
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            px-5
                            py-2.5
                            rounded-xl
                            text-sm
                            font-semibold

                            max-md:w-full
                            max-md:py-2.5
                            max-md:text-sm
                            max-md:rounded-xl
                        "
                    >

                        <i
                            class="
                                ri-arrow-left-line
                                text-lg

                                max-md:text-lg
                            "
                            aria-hidden="true"
                        ></i>

                        Back to Home

                    </a>

                </div>

            </div>

        </section>

    </main>


    <!-- ======================================================
         FOOTER
    ====================================================== -->

    <div
        id="footer"
        class="shrink-0"
    ></div>


    <!-- ======================================================
         APP JS
    ====================================================== -->

    <script src="../../assets/js/app.js"></script>


</body>

</html>