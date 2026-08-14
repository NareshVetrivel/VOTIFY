<?php
/* ==========================================================
   VOTIFY
   Already Voted Page
   File : pages/student/already_voted.php
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   PREVENT CACHING
========================================================== */

header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Cache-Control: post-check=0, pre-check=0",
    false
);

header(
    "Pragma: no-cache"
);

header(
    "Expires: Sat, 01 Jan 2000 00:00:00 GMT"
);


/* ==========================================================
   STUDENT NAME
========================================================== */

$studentName = "";

if (isset($_SESSION["student_name"])) {

    $studentName = htmlspecialchars(
        $_SESSION["student_name"],
        ENT_QUOTES,
        "UTF-8"
    );

}

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

    <title>
        Vote Already Submitted | VOTIFY
    </title>


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
            overflow-hidden
            pointer-events-none
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
                blob
            "
        ></div>


        <!-- Purple Glow -->

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


        <!-- Pink Glow -->

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
            py-3
            sm:py-4
        "
    >

        <!-- ==================================================
             CONTENT WRAPPER
        ================================================== -->

        <div
            class="
                w-full
                max-w-[800px]
                mx-auto
            "
        >


            <!-- ==================================================
                 ALREADY VOTED CARD
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

                <!-- ==================================================
                     TOP GLOW
                ================================================== -->

                <div
                    class="
                        absolute
                        top-0
                        left-1/2
                        -translate-x-1/2
                        w-52
                        h-20
                        bg-blue-600/10
                        blur-[50px]
                        pointer-events-none
                    "
                    aria-hidden="true"
                ></div>


                <!-- ==================================================
                     CARD CONTENT
                ================================================== -->

                <div
                    class="
                        relative
                        px-5
                        py-5
                        sm:px-8
                        sm:py-6
                        text-center
                    "
                >


                    <!-- ==================================================
                         ICON
                    ================================================== -->

                    <div
                        class="
                            relative
                            w-16
                            h-16
                            sm:w-[72px]
                            sm:h-[72px]
                            mx-auto
                        "
                    >

                        <div
                            class="
                                absolute
                                inset-0
                                rounded-full
                                bg-blue-500/20
                                blur-lg
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
                                from-blue-500
                                to-indigo-600
                                border
                                border-blue-300/20
                                shadow-[0_0_28px_rgba(59,130,246,.22)]
                            "
                        >

                            <i
                                class="
                                    ri-checkbox-circle-line
                                    text-4xl
                                    sm:text-[42px]
                                    text-white
                                "
                                aria-hidden="true"
                            ></i>

                        </div>

                    </div>


                    <!-- ==================================================
                         STATUS BADGE
                    ================================================== -->

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-1.5
                            mt-3
                            px-3.5
                            py-1.5
                            rounded-full
                            bg-blue-500/10
                            border
                            border-blue-500/20
                            text-blue-400
                            text-xs
                            sm:text-sm
                            font-semibold
                        "
                    >

                        <i
                            class="ri-information-line"
                            aria-hidden="true"
                        ></i>

                        Vote Already Submitted

                    </div>


                    <!-- ==================================================
                         HEADING
                    ================================================== -->

                    <h1
                        class="
                            mt-2.5
                            text-3xl
                            sm:text-4xl
                            font-extrabold
                            tracking-tight
                            text-white
                        "
                    >
                        Already Voted
                    </h1>


                    <!-- ==================================================
                         STUDENT NAME
                    ================================================== -->

                    <?php if ($studentName !== "") { ?>

                        <p
                            class="
                                mt-2
                                text-base
                                sm:text-lg
                                text-slate-300
                            "
                        >

                            Hello,

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

                        </p>

                    <?php } ?>


                    <!-- ==================================================
                         DESCRIPTION
                    ================================================== -->

                    <p
                        class="
                            max-w-2xl
                            mx-auto
                            mt-2
                            text-sm
                            sm:text-base
                            leading-6
                            text-slate-400
                        "
                    >

                        Our records show that you have already cast
                        your vote in this election.

                        Each student is allowed to vote only once.

                    </p>


                    <!-- ==================================================
                         THREE STATUS CARDS
                    ================================================== -->

                    <div
                        class="
                            mt-4
                            grid
                            grid-cols-3
                            gap-2.5
                            sm:gap-4
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
                                px-3
                                py-3
                                sm:px-4
                                sm:py-4
                                text-center
                            "
                        >

                            <div
                                class="
                                    w-9
                                    h-9
                                    sm:w-11
                                    sm:h-11
                                    mx-auto
                                    rounded-lg
                                    bg-green-500/10
                                    flex
                                    items-center
                                    justify-center
                                "
                            >

                                <i
                                    class="
                                        ri-checkbox-circle-fill
                                        text-xl
                                        sm:text-2xl
                                        text-green-400
                                    "
                                    aria-hidden="true"
                                ></i>

                            </div>


                            <h3
                                class="
                                    mt-2
                                    text-sm
                                    sm:text-base
                                    font-bold
                                    text-white
                                    leading-tight
                                "
                            >
                                Vote Recorded
                            </h3>


                            <p
                                class="
                                    mt-1
                                    text-xs
                                    sm:text-sm
                                    text-green-400
                                "
                            >
                                Completed
                            </p>

                        </div>


                        <!-- ==================================================
                             ONE VOTE
                        ================================================== -->

                        <div
                            class="
                                glass
                                rounded-xl
                                border
                                border-blue-500/10
                                px-3
                                py-3
                                sm:px-4
                                sm:py-4
                                text-center
                            "
                        >

                            <div
                                class="
                                    w-9
                                    h-9
                                    sm:w-11
                                    sm:h-11
                                    mx-auto
                                    rounded-lg
                                    bg-blue-500/10
                                    flex
                                    items-center
                                    justify-center
                                "
                            >

                                <i
                                    class="
                                        ri-user-line
                                        text-xl
                                        sm:text-2xl
                                        text-blue-400
                                    "
                                    aria-hidden="true"
                                ></i>

                            </div>


                            <h3
                                class="
                                    mt-2
                                    text-sm
                                    sm:text-base
                                    font-bold
                                    text-white
                                    leading-tight
                                "
                            >
                                One Vote
                            </h3>


                            <p
                                class="
                                    mt-1
                                    text-xs
                                    sm:text-sm
                                    text-slate-400
                                "
                            >
                                Per Student
                            </p>

                        </div>


                        <!-- ==================================================
                             VOTE LOCKED
                        ================================================== -->

                        <div
                            class="
                                glass
                                rounded-xl
                                border
                                border-purple-500/10
                                px-3
                                py-3
                                sm:px-4
                                sm:py-4
                                text-center
                            "
                        >

                            <div
                                class="
                                    w-9
                                    h-9
                                    sm:w-11
                                    sm:h-11
                                    mx-auto
                                    rounded-lg
                                    bg-purple-500/10
                                    flex
                                    items-center
                                    justify-center
                                "
                            >

                                <i
                                    class="
                                        ri-lock-2-fill
                                        text-xl
                                        sm:text-2xl
                                        text-purple-400
                                    "
                                    aria-hidden="true"
                                ></i>

                            </div>


                            <h3
                                class="
                                    mt-2
                                    text-sm
                                    sm:text-base
                                    font-bold
                                    text-white
                                    leading-tight
                                "
                            >
                                Vote Locked
                            </h3>


                            <p
                                class="
                                    mt-1
                                    text-xs
                                    sm:text-sm
                                    text-slate-400
                                "
                            >
                                Cannot Repeat
                            </p>

                        </div>

                    </div>


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
         ALREADY VOTED JS
    ====================================================== -->

    <script src="../../assets/js/already_voted.js"></script>


</body>

</html>