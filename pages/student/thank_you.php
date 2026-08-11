<?php
/* ==========================================================
   VOTIFY
   Thank You Page
   File : pages/student/thank_you.php
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if (
    !isset($_SESSION["student_logged_in"]) ||
    $_SESSION["student_logged_in"] !== true
) {

    header("Location: student_login.php");
    exit();

}


/* ==========================================================
   DIRECT ACCESS PROTECTION
========================================================== */

if (
    !isset($_SESSION["vote_submitted"]) ||
    $_SESSION["vote_submitted"] !== true
) {

    header("Location: candidate_selection.php");
    exit();

}


/* ==========================================================
   STUDENT
========================================================== */

$studentName = htmlspecialchars(
    $_SESSION["student_name"] ?? "Student",
    ENT_QUOTES,
    "UTF-8"
);

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

    <title>Vote Submitted | VOTIFY</title>


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
        overflow-hidden
        flex
        flex-col
        antialiased
    "
>


    <!-- ======================================================
         HEADER
    ====================================================== -->

    <div
        id="header"
        class="shrink-0"
    ></div>


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

        <!-- Blue -->

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
                blob
            "
        ></div>


        <!-- Purple -->

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
                bg-purple-600/15
                blur-[110px]
                sm:blur-[135px]
                blob
            "
        ></div>


        <!-- Pink -->

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
                bg-pink-500/10
                blur-[90px]
            "
        ></div>

    </div>


    <!-- ======================================================
         MAIN
         Safe area between header and footer
    ====================================================== -->

    <main
        class="
            relative
            flex-1
            min-h-0
            w-full
            overflow-hidden
            flex
            items-center
            justify-center
            px-3
            sm:px-5
        "
    >

        <!-- ==================================================
             CONTENT
        ================================================== -->

        <div
            id="thank-you-content"
            class="
                w-full
                max-w-[720px]
                mx-auto
            "
        >


            <!-- ==================================================
                 SUCCESS HERO CARD
            ================================================== -->

            <section
                class="
                    glass
                    relative
                    w-full
                    rounded-[22px]
                    sm:rounded-[26px]
                    border
                    border-white/10
                    overflow-hidden
                    shadow-2xl
                    fade-up
                "
            >

                <!-- Top Glow -->

                <div
                    class="
                        absolute
                        top-0
                        left-1/2
                        -translate-x-1/2
                        w-52
                        h-20
                        bg-blue-600/10
                        blur-[55px]
                        pointer-events-none
                    "
                    aria-hidden="true"
                ></div>


                <!-- ==================================================
                     HERO CONTENT
                ================================================== -->

                <div
                    class="
                        relative
                        px-5
                        py-3.5
                        sm:px-8
                        sm:py-4
                        text-center
                    "
                >


                    <!-- ==================================================
                         SUCCESS ICON
                    ================================================== -->

                    <div
                        class="
                            relative
                            w-14
                            h-14
                            sm:w-16
                            sm:h-16
                            mx-auto
                        "
                    >

                        <div
                            class="
                                absolute
                                inset-0
                                rounded-full
                                bg-green-500/25
                                blur-lg
                                pulse
                            "
                            aria-hidden="true"
                        ></div>


                        <div
                            class="
                                relative
                                w-full
                                h-full
                                rounded-full
                                flex
                                items-center
                                justify-center
                                bg-gradient-to-br
                                from-green-400
                                to-emerald-600
                                border
                                border-green-300/20
                                shadow-[0_0_28px_rgba(34,197,94,.22)]
                            "
                        >

                            <i
                                class="
                                    ri-check-line
                                    text-4xl
                                    sm:text-5xl
                                    text-white
                                    font-bold
                                "
                                aria-hidden="true"
                            ></i>

                        </div>

                    </div>


                    <!-- ==================================================
                         SUCCESS BADGE
                    ================================================== -->

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-2
                            mt-2
                            px-3
                            py-1.5
                            rounded-full
                            bg-green-500/10
                            border
                            border-green-500/20
                            text-green-400
                            text-xs
                            sm:text-sm
                            font-semibold
                        "
                    >

                        <i
                            class="ri-shield-check-line"
                            aria-hidden="true"
                        ></i>

                        Vote Successfully Recorded

                    </div>


                    <!-- ==================================================
                         HEADING
                    ================================================== -->

                    <h1
                        class="
                            mt-2
                            text-3xl
                            sm:text-4xl
                            font-extrabold
                            tracking-tight
                            text-white
                        "
                    >
                        Thank You!
                    </h1>


                    <!-- ==================================================
                         STUDENT MESSAGE
                    ================================================== -->

                    <p
                        class="
                            mt-1.5
                            text-sm
                            sm:text-base
                            leading-6
                            text-slate-300
                        "
                    >

                        Thank you,

                        <span
                            class="
                                font-bold
                                bg-gradient-to-r
                                from-blue-400
                                via-purple-400
                                to-pink-400
                                bg-clip-text
                                text-transparent
                            "
                        >
                            <?php echo $studentName; ?>
                        </span>

                        for participating in the election.

                    </p>


                    <!-- ==================================================
                         DESCRIPTION
                    ================================================== -->

                    <p
                        class="
                            max-w-2xl
                            mx-auto
                            mt-1.5
                            text-xs
                            sm:text-sm
                            leading-5
                            text-slate-400
                        "
                    >

                        Your vote has been securely recorded.
                        Thank you for taking part in the democratic
                        process of your department.

                    </p>


                    <!-- ==================================================
                         MICRO STATUS
                    ================================================== -->

                    <div
                        class="
                            mt-2
                            flex
                            items-center
                            justify-center
                            gap-4
                            text-[10px]
                            sm:text-xs
                            text-slate-500
                        "
                    >

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                            "
                        >

                            <i
                                class="
                                    ri-checkbox-circle-fill
                                    text-green-400
                                "
                                aria-hidden="true"
                            ></i>

                            Vote Recorded

                        </span>


                        <span
                            class="text-slate-700"
                            aria-hidden="true"
                        >
                            •
                        </span>


                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                            "
                        >

                            <i
                                class="
                                    ri-lock-2-fill
                                    text-blue-400
                                "
                                aria-hidden="true"
                            ></i>

                            Identity Protected

                        </span>

                    </div>

                </div>

            </section>


            <!-- ==================================================
                 THREE STATUS CARDS
            ================================================== -->

            <section
                class="
                    mt-2.5
                    grid
                    grid-cols-3
                    gap-2.5
                    sm:gap-3
                "
            >


                <!-- ==================================================
                     VOTE RECORDED
                ================================================== -->

                <div
                    class="
                        glass
                        rounded-xl
                        border
                        border-green-500/10
                        px-2.5
                        py-2
                        sm:px-4
                        sm:py-2.5
                        text-center
                    "
                >

                    <div
                        class="
                            w-8
                            h-8
                            sm:w-10
                            sm:h-10
                            mx-auto
                            rounded-xl
                            bg-green-500/10
                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                ri-checkbox-circle-fill
                                text-lg
                                sm:text-xl
                                text-green-400
                            "
                            aria-hidden="true"
                        ></i>

                    </div>


                    <h3
                        class="
                            mt-1.5
                            text-[10px]
                            sm:text-xs
                            font-bold
                            text-white
                            leading-tight
                        "
                    >
                        Vote Recorded
                    </h3>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            sm:text-[10px]
                            text-green-400
                        "
                    >
                        Successfully
                    </p>

                </div>


                <!-- ==================================================
                     PRIVACY PROTECTED
                ================================================== -->

                <div
                    class="
                        glass
                        rounded-xl
                        border
                        border-blue-500/10
                        px-2.5
                        py-2
                        sm:px-4
                        sm:py-2.5
                        text-center
                    "
                >

                    <div
                        class="
                            w-8
                            h-8
                            sm:w-10
                            sm:h-10
                            mx-auto
                            rounded-xl
                            bg-blue-500/10
                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                ri-lock-2-fill
                                text-lg
                                sm:text-xl
                                text-blue-400
                            "
                            aria-hidden="true"
                        ></i>

                    </div>


                    <h3
                        class="
                            mt-1.5
                            text-[10px]
                            sm:text-xs
                            font-bold
                            text-white
                            leading-tight
                        "
                    >
                        Privacy Protected
                    </h3>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            sm:text-[10px]
                            text-slate-500
                        "
                    >
                        Identity Secure
                    </p>

                </div>


                <!-- ==================================================
                     FINAL SUBMISSION
                ================================================== -->

                <div
                    class="
                        glass
                        rounded-xl
                        border
                        border-purple-500/10
                        px-2.5
                        py-2
                        sm:px-4
                        sm:py-2.5
                        text-center
                    "
                >

                    <div
                        class="
                            w-8
                            h-8
                            sm:w-10
                            sm:h-10
                            mx-auto
                            rounded-xl
                            bg-purple-500/10
                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                ri-shield-star-fill
                                text-lg
                                sm:text-xl
                                text-purple-400
                            "
                            aria-hidden="true"
                        ></i>

                    </div>


                    <h3
                        class="
                            mt-1.5
                            text-[10px]
                            sm:text-xs
                            font-bold
                            text-white
                            leading-tight
                        "
                    >
                        Final Submission
                    </h3>


                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            sm:text-[10px]
                            text-slate-500
                        "
                    >
                        Cannot Be Changed
                    </p>

                </div>

            </section>


        </div>

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


    <!-- ======================================================
         THANK YOU JS
    ====================================================== -->

    <script src="../../assets/js/thank_you.js"></script>


</body>

</html>