<?php
/* ==========================================================
   VOTIFY
   Admin About Us Page
   File : pages/admin/about.php
========================================================== */

session_start();

/* ==========================================================
   ADMIN SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.html");
    exit();
}


/* ==========================================================
   TEAM MEMBER DATA
   Static data - No database required
========================================================== */

$teamMembers = [
    [
        "name"        => "Naresh S",
        "teamRole"    => "Team Leader + Super Admin",
        "role"        => "Full Stack Developer",
        "course"      => "MCA",
        "college"     => "Sona College of Technology",
        "projectRole" => "Full Stack Developer",
        "email"       => "naresh.25cap@sonatech.ac.in",
        "image"       => "../../assets/images/team/naresh.jpg",
        "instagram"   => null,
        "whatsapp"    => "https://wa.me/918760750750",
        "github"      => "https://github.com/NareshVetrivel",
        "linkedin"    => "https://www.linkedin.com/in/naresh-sns"
    ],

    [
        "name"        => "Ragavendhiran R",
        "teamRole"    => "Team Member + Super Admin",
        "role"        => "Backend Developer",
        "course"      => "MCA",
        "college"     => "Sona College of Technology",
        "projectRole" => "Backend Development + Database + Software Testing",
        "email"       => "ragavendhiran.25cap@sonatech.ac.in",
        "image"       => "../../assets/images/team/ragavendhiran.png",
        "instagram"   => "https://www.instagram.com/ragavendhiran._04/",
        "whatsapp"    => "https://wa.me/919597276320",
        "github"      => "https://github.com/Ragavendhiran95",
        "linkedin"    => "https://www.linkedin.com/in/ragavan2004"
    ],

    [
        "name"        => "Naveenkumar M",
        "teamRole"    => "Team Member",
        "role"        => "Backend Developer",
        "course"      => "MCA",
        "college"     => "Sona College of Technology",
        "projectRole" => "Backend Development + Database",
        "email"       => "naveenkumar.25cap@sonatech.ac.in",
        "image"       => "../../assets/images/team/naveenkumar.jpeg",
        "instagram"   => "https://www.instagram.com/its_naveen_vortex/",
        "whatsapp"    => "https://wa.me/919384570256",
        "github"      => "https://github.com/msnaveenkumar4455-web",
        "linkedin"    => "https://www.linkedin.com/in/naveen-kumar-m-5268b02a5/"
    ],

    [
        "name"        => "Sridhar D",
        "teamRole"    => "Team Member",
        "role"        => "Backend Developer",
        "course"      => "MCA",
        "college"     => "Sona College of Technology",
        "projectRole" => "Backend Development + Database",
        "email"       => "sridhar.25cap@sonatech.ac.in",
        "image"       => "../../assets/images/team/sridhar.jpeg",
        "instagram"   => "https://www.instagram.com/___.sr7dhar/",
        "whatsapp"    => "https://wa.me/916369730251",
        "github"      => "https://github.com/sridhard3107",
        "linkedin"    => "https://www.linkedin.com/in/sridhar-d-056940300/"
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>About Us | VOTIFY</title>


    <!-- ==================================================
         TAILWIND CSS
    ================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- ==================================================
         REMIX ICONS
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
        rel="stylesheet">


    <!-- ==================================================
         EXISTING VOTIFY CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/custom.css">

    <link
        rel="stylesheet"
        href="../../assets/css/animations.css">


    <!-- ==================================================
         EXISTING FLIP CARD CSS
         Only functional 3D card CSS is retained here.
    ================================================== -->

    <style>

        /* ==================================================
           ABOUT CONTENT
        ================================================== */

        .about-hero {

            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(37, 99, 235, 0.13),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 85% 25%,
                    rgba(168, 85, 247, 0.13),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 50% 90%,
                    rgba(236, 72, 153, 0.10),
                    transparent 35%
                );

            border-radius: 24px;

            padding: 40px;

            border:
                1px solid
                rgba(99, 102, 241, 0.15);

        }


        /* ==================================================
           VOTIFY GRADIENT
        ================================================== */

        .votify-gradient {

            background:
                linear-gradient(
                    90deg,
                    #2563eb 0%,
                    #4f46e5 25%,
                    #9333ea 50%,
                    #d946ef 75%,
                    #ec4899 100%
                );

            -webkit-background-clip: text;

            background-clip: text;

            -webkit-text-fill-color: transparent;

            color: transparent;

        }


        /* ==================================================
           ABOUT SECTION
        ================================================== */

        .about-section {

            background:
                linear-gradient(
                    145deg,
                    rgba(18, 28, 51, 0.92),
                    rgba(11, 17, 35, 0.92)
                );

            border:
                1px solid
                rgba(99, 102, 241, 0.18);

            border-radius: 24px;

            padding: 30px;

            transition:
                border-color .4s ease,
                box-shadow .4s ease,
                transform .4s ease;

        }


        /* ==================================================
           FEATURE CARDS
        ================================================== */

        .about-feature {

            background:
                rgba(18, 28, 50, 0.75);

            border:
                1px solid
                rgba(99, 102, 241, 0.14);

            border-radius: 20px;

            padding: 24px;

            transition:
                transform .35s ease,
                border-color .35s ease,
                box-shadow .35s ease;

        }


        /* ==================================================
           TECHNOLOGY
        ================================================== */

        .about-tech {

            background:
                rgba(23, 35, 57, 0.85);

            border:
                1px solid
                rgba(99, 102, 241, 0.14);

            border-radius: 16px;

            padding: 22px 15px;

            text-align: center;

            transition:
                transform .35s ease,
                border-color .35s ease,
                box-shadow .35s ease;

        }


        /* ==================================================
           TEAM CARD
        ================================================== */

        .team-card {

            width: 100%;

            height: 520px;

            perspective: 1000px;

            cursor: pointer;

        }


        .team-card-inner {

            position: relative;

            width: 100%;

            height: 100%;

            transform-style: preserve-3d;

            transition:
                transform .75s
                cubic-bezier(.2,.75,.25,1);

        }


        /* ==================================================
           FLIP STATE
        ================================================== */

        .team-card.flipped .team-card-inner {

            transform:
                rotateY(180deg);

        }


        /* ==================================================
           FRONT + BACK
        ================================================== */

        .team-front,
        .team-back {

            position: absolute;

            inset: 0;

            width: 100%;

            height: 100%;

            border-radius: 22px;

            overflow: hidden;

            backface-visibility: hidden;

            -webkit-backface-visibility: hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(18, 29, 54, 0.98),
                    rgba(9, 15, 34, 0.98)
                );

            border:
                1px solid
                rgba(99, 102, 241, 0.22);

            box-shadow:
                0 20px 50px
                rgba(0, 0, 0, 0.18);

        }


        /* ==================================================
           FRONT CARD
        ================================================== */

        .team-front {

            display: flex;

            flex-direction: column;

            align-items: stretch;

            justify-content: flex-start;

            text-align: center;

        }


        /* ==================================================
           IMAGE WRAPPER
        ================================================== */

        .team-image-wrapper {

            width: 100%;

            height: 80%;

            min-height: 0;

            overflow: hidden;

            position: relative;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                #10182d;

        }


        /* ==================================================
           IMAGE
        ================================================== */

        .team-image {

            width: 100%;

            height: 100%;

            object-fit: contain;

            object-position: center;

            display: block;

            border: 0;

            border-radius: 0;

            background:
                #10182d;

        }


        /* ==================================================
           IMAGE GRADIENT
        ================================================== */

        .team-image-wrapper::after {

            content: "";

            position: absolute;

            left: 0;

            right: 0;

            bottom: 0;

            height: 25%;

            background:
                linear-gradient(
                    to top,
                    rgba(7, 12, 28, 0.75),
                    transparent
                );

            pointer-events: none;

        }


        /* ==================================================
           FRONT INFO
        ================================================== */

        .team-front-info {

            height: 20%;

            min-height: 0;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            padding:
                12px 20px;

            background:
                linear-gradient(
                    180deg,
                    rgba(14, 23, 45, 0.98),
                    rgba(9, 15, 34, 0.98)
                );

        }


        .team-front-name {

            font-size:
                1.35rem;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #ffffff;

        }


        .team-front-role {

            margin-top:
                7px;

            font-size:
                .95rem;

            line-height:
                1.2;

            font-weight:
                600;

            color:
                #818cf8;

        }


        /* ==================================================
           BACK CARD
        ================================================== */

        .team-back {

            transform:
                rotateY(180deg);

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: flex-start;

            text-align: center;

            padding:
                30px 25px;

            overflow-y: auto;

        }


        /* ==================================================
           BACK HEADER
        ================================================== */

        .team-back-header {

            width: 100%;

            padding-bottom:
                18px;

            margin-bottom:
                18px;

            border-bottom:
                1px solid
                rgba(99, 102, 241, 0.14);

        }


        .team-back-name {

            font-size:
                1.5rem;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #ffffff;

        }


        .team-back-team-role {

            margin-top:
                6px;

            font-size:
                .95rem;

            font-weight:
                600;

            color:
                #c084fc;

        }


        .team-back-professional-role {

            margin-top:
                5px;

            font-size:
                .85rem;

            color:
                #818cf8;

        }


        /* ==================================================
           DETAILS
        ================================================== */

        .team-details {

            width: 100%;

            display: flex;

            flex-direction: column;

            gap: 10px;

            margin-bottom:
                18px;

        }


        .team-detail-row {

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: flex-start;

            gap: 12px;

            padding:
                9px 12px;

            border-radius:
                10px;

            background:
                rgba(255,255,255,.025);

            border:
                1px solid
                rgba(255,255,255,.05);

            text-align:
                left;

            transition:
                transform .25s ease,
                border-color .25s ease,
                background .25s ease;

        }


        .team-detail-icon {

            width:
                22px;

            flex-shrink:
                0;

            text-align:
                center;

            font-size:
                18px;

        }


        .team-detail-content {

            min-width:
                0;

            display:
                flex;

            flex-direction:
                column;

            gap:
                2px;

        }


        .team-detail-label {

            font-size:
                10px;

            text-transform:
                uppercase;

            letter-spacing:
                .08em;

            color:
                #64748b;

        }


        .team-detail-value {

            font-size:
                13px;

            line-height:
                1.4;

            color:
                #cbd5e1;

            word-break:
                break-word;

        }


        /* ==================================================
           SOCIAL
        ================================================== */

        .team-social-list {

            width: 100%;

            display: flex;

            flex-direction: column;

            gap: 8px;

        }


        .team-social-link {

            display: flex;

            align-items: center;

            justify-content: flex-start;

            gap: 12px;

            width: 100%;

            padding:
                9px 12px;

            border-radius:
                10px;

            background:
                rgba(255,255,255,.04);

            border:
                1px solid
                rgba(255,255,255,.06);

            color:
                #cbd5e1;

            font-size:
                13px;

            text-align:
                left;

            transition:
                transform .3s ease,
                background .3s ease,
                border-color .3s ease,
                color .3s ease,
                box-shadow .3s ease;

        }


        .team-social-link i {

            width:
                20px;

            text-align:
                center;

            font-size:
                18px;

            flex-shrink:
                0;

            transition:
                transform .3s ease;

        }


        .team-social-na {

            display: flex;

            align-items: center;

            justify-content: flex-start;

            gap: 12px;

            width: 100%;

            padding:
                9px 12px;

            border-radius:
                10px;

            background:
                rgba(255,255,255,.025);

            border:
                1px solid
                rgba(255,255,255,.05);

            color:
                #64748b;

            font-size:
                13px;

            text-align:
                left;

        }


        .team-social-na i {

            width:
                20px;

            text-align:
                center;

            font-size:
                18px;

        }


        /* ==================================================
           FLIP HINT
        ================================================== */

        .team-flip-hint {

            margin-top:
                14px;

            font-size:
                10px;

            color:
                #475569;

        }


        /* ==================================================
           REDUCED MOTION
        ================================================== */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                scroll-behavior: auto !important;

                transition-duration: .01ms !important;

                animation-duration: .01ms !important;

                animation-iteration-count: 1 !important;

            }

        }


        /* ==================================================
           RESPONSIVE
        ================================================== */

        @media (max-width: 768px) {

            .about-hero {

                padding:
                    25px;

            }


            .about-section {

                padding:
                    22px;

            }


            .team-card {

                height:
                    480px;

            }


            .team-front-info {

                padding:
                    10px 16px;

            }


            .team-front-name {

                font-size:
                    1.2rem;

            }


            .team-front-role {

                font-size:
                    .85rem;

            }


            .team-back {

                padding:
                    24px 18px;

            }

        }

    </style>

</head>


<body
    class="
        bg-[#0B1020]
        text-white
        min-h-screen
        overflow-x-hidden
        flex
        flex-col
    ">


    <!-- ==================================================
         LOADER
    ================================================== -->

    <div id="loader-container"></div>


    <!-- ==================================================
         BACKGROUND
    ================================================== -->

    <div
        class="
            fixed
            inset-0
            -z-10
            overflow-hidden
            pointer-events-none
        ">

        <div
            class="
                absolute
                top-0
                left-0
                w-96
                h-96
                bg-blue-600/20
                blur-[150px]
                rounded-full
                transition-transform
                duration-[3000ms]
                ease-in-out
            ">
        </div>


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
                transition-transform
                duration-[3000ms]
                ease-in-out
            ">
        </div>


        <div
            class="
                absolute
                top-1/2
                left-1/2
                w-80
                h-80
                bg-purple-600/20
                blur-[130px]
                rounded-full
                -translate-x-1/2
                -translate-y-1/2
            ">
        </div>

    </div>


    <!-- ==================================================
         EXISTING HEADER
    ================================================== -->

    <div id="header"></div>


    <!-- ==================================================
         MOBILE SIDEBAR OVERLAY
    ================================================== -->

    <div
        id="sidebarOverlay"
        class="
            fixed
            inset-0
            bg-black/60
            hidden
            z-40
            lg:hidden
        ">
    </div>


    <!-- ==================================================
         MAIN ADMIN LAYOUT
    ================================================== -->

    <main
        class="
            flex-1
            max-w-7xl
            w-full
            mx-auto
            px-4
            sm:px-6
            lg:px-8
            py-8
        ">

        <div
            class="
                grid
                grid-cols-1
                lg:grid-cols-[280px_1fr]
                gap-8
                items-start
            ">


            <!-- ==================================================
                 EXISTING ADMIN SIDEBAR
            ================================================== -->

            <?php
                include "../../components/admin_sidebar.php";
            ?>


            <!-- ==================================================
                 ABOUT CONTENT
            ================================================== -->

            <section class="min-w-0">


                <!-- ==================================================
                     EXISTING ADMIN TOPBAR
                ================================================== -->

                <?php

                    $pageTitle = "About Us";

                    include "../../components/admin_topbar.php";

                ?>


                <div class="space-y-8">


                    <!-- ==================================================
                         ABOUT PROJECT
                    ================================================== -->

                    <div
                        class="
                            about-section
                            group
                            transition-all
                            duration-500
                            ease-out
                            hover:-translate-y-1
                            hover:border-indigo-400/30
                            hover:shadow-2xl
                            hover:shadow-indigo-950/30
                        ">

                        <div
                            class="
                                flex
                                items-center
                                gap-4
                                mb-6
                            ">

                            <div
                                class="
                                    w-14
                                    h-14
                                    rounded-2xl
                                    bg-blue-500/10
                                    border
                                    border-blue-500/20
                                    flex
                                    items-center
                                    justify-center
                                    transition-all
                                    duration-500
                                    group-hover:scale-110
                                    group-hover:rotate-3
                                    group-hover:bg-blue-500/20
                                    group-hover:shadow-lg
                                    group-hover:shadow-blue-500/20
                                ">

                                <i
                                    class="
                                        ri-shield-check-line
                                        text-3xl
                                        text-blue-400
                                        transition-transform
                                        duration-500
                                        group-hover:scale-110
                                    ">
                                </i>

                            </div>


                            <div>

                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        transition-all
                                        duration-300
                                        group-hover:text-blue-300
                                    ">

                                    About VOTIFY

                                </h2>

                                <p
                                    class="
                                        text-slate-500
                                        mt-1
                                        transition-colors
                                        duration-300
                                        group-hover:text-slate-400
                                    ">

                                    Online Student Voting System

                                </p>

                            </div>

                        </div>


                        <p
                            class="
                                text-slate-400
                                leading-7
                                transition-colors
                                duration-300
                                group-hover:text-slate-300
                            ">

                            VOTIFY provides a modern digital platform
                            for conducting student elections. Instead of
                            traditional paper-based voting methods,
                            students can register, get verified, view
                            candidates and cast their vote through a
                            secure online system.

                        </p>


                        <p
                            class="
                                text-slate-400
                                leading-7
                                mt-4
                                transition-colors
                                duration-300
                                group-hover:text-slate-300
                            ">

                            The system also provides administrators with
                            tools to manage elections, approve student
                            registrations, manage candidates and monitor
                            election activities.

                        </p>

                    </div>


                    <!-- ==================================================
                         IMPORTANT FEATURES
                    ================================================== -->

                    <div
                        class="
                            about-section
                            group
                            transition-all
                            duration-500
                            hover:-translate-y-1
                            hover:border-purple-400/30
                            hover:shadow-2xl
                            hover:shadow-purple-950/20
                        ">

                        <div class="mb-7">

                            <p
                                class="
                                    text-blue-400
                                    font-semibold
                                    text-sm
                                    transition-all
                                    duration-300
                                    group-hover:tracking-widest
                                ">

                                WHAT WE PROVIDE

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
                                    transition-colors
                                    duration-300
                                    group-hover:text-indigo-200
                                ">

                                Important Features

                            </h2>

                        </div>


                        <div
                            class="
                                grid
                                grid-cols-1
                                md:grid-cols-2
                                xl:grid-cols-3
                                gap-5
                            ">


                            <!-- Feature 1 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-blue-400/40
                                    hover:shadow-xl
                                    hover:shadow-blue-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-user-add-line
                                            text-3xl
                                            text-blue-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                            group-hover/feature:-rotate-6
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-blue-300
                                    ">

                                    Student Authentication

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Students can register using college
                                    details and verify their college
                                    email through OTP.

                                </p>

                            </div>


                            <!-- Feature 2 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-purple-400/40
                                    hover:shadow-xl
                                    hover:shadow-purple-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-user-settings-line
                                            text-3xl
                                            text-purple-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                            group-hover/feature:rotate-6
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-purple-300
                                    ">

                                    Admin Approval

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Administrators can review and approve
                                    student registrations before voting.

                                </p>

                            </div>


                            <!-- Feature 3 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-pink-400/40
                                    hover:shadow-xl
                                    hover:shadow-pink-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-lock-password-line
                                            text-3xl
                                            text-pink-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                            group-hover/feature:-rotate-6
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-pink-300
                                    ">

                                    Secure Login

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Password authentication with OTP
                                    provides an additional layer of
                                    login security.

                                </p>

                            </div>


                            <!-- Feature 4 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-blue-400/40
                                    hover:shadow-xl
                                    hover:shadow-blue-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-calendar-check-line
                                            text-3xl
                                            text-blue-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-blue-300
                                    ">

                                    Election Management

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Administrators can manage election
                                    activities and control the election
                                    status.

                                </p>

                            </div>


                            <!-- Feature 5 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-purple-400/40
                                    hover:shadow-xl
                                    hover:shadow-purple-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-team-line
                                            text-3xl
                                            text-purple-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                            group-hover/feature:rotate-6
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-purple-300
                                    ">

                                    Candidate Management

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Candidate information can be added
                                    and managed by the administrator.

                                </p>

                            </div>


                            <!-- Feature 6 -->

                            <div
                                class="
                                    about-feature
                                    group/feature
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.02]
                                    hover:border-pink-400/40
                                    hover:shadow-xl
                                    hover:shadow-pink-950/30
                                ">

                                <div
                                    class="
                                        icon-box
                                        mb-5
                                        transition-all
                                        duration-500
                                        group-hover/feature:translate-x-1
                                    ">

                                    <i
                                        class="
                                            ri-checkbox-circle-line
                                            text-3xl
                                            text-pink-400
                                            transition-all
                                            duration-500
                                            group-hover/feature:scale-125
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
                                        transition-colors
                                        duration-300
                                        group-hover/feature:text-pink-300
                                    ">

                                    One Student – One Vote

                                </h3>

                                <p
                                    class="
                                        text-slate-400
                                        text-sm
                                        leading-6
                                    ">

                                    Each approved student can cast only
                                    one vote, helping maintain election
                                    integrity.

                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         TECHNOLOGY STACK
                    ================================================== -->

                    <div
                        class="
                            about-section
                            group
                            transition-all
                            duration-500
                            hover:-translate-y-1
                            hover:border-cyan-400/30
                            hover:shadow-2xl
                            hover:shadow-cyan-950/20
                        ">

                        <div class="mb-7">

                            <p
                                class="
                                    text-purple-400
                                    font-semibold
                                    text-sm
                                    transition-all
                                    duration-300
                                    group-hover:tracking-widest
                                ">

                                DEVELOPMENT

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
                                    transition-colors
                                    duration-300
                                    group-hover:text-purple-200
                                ">

                                Technology Stack

                            </h2>

                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                md:grid-cols-4
                                gap-4
                            ">


                            <!-- HTML -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-orange-400/40
                                    hover:shadow-xl
                                    hover:shadow-orange-950/20
                                ">

                                <i
                                    class="
                                        ri-html5-line
                                        text-4xl
                                        text-orange-400
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:-rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-orange-300
                                    ">
                                    HTML
                                </p>

                            </div>


                            <!-- Tailwind -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-cyan-400/40
                                    hover:shadow-xl
                                    hover:shadow-cyan-950/20
                                ">

                                <i
                                    class="
                                        ri-tailwind-css-line
                                        text-4xl
                                        text-cyan-400
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-cyan-300
                                    ">
                                    Tailwind CSS
                                </p>

                            </div>


                            <!-- JavaScript -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-yellow-400/40
                                    hover:shadow-xl
                                    hover:shadow-yellow-950/20
                                ">

                                <i
                                    class="
                                        ri-javascript-line
                                        text-4xl
                                        text-yellow-300
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:-rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-yellow-200
                                    ">
                                    JavaScript
                                </p>

                            </div>


                            <!-- PHP -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-indigo-400/40
                                    hover:shadow-xl
                                    hover:shadow-indigo-950/20
                                ">

                                <i
                                    class="
                                        ri-code-s-slash-line
                                        text-4xl
                                        text-indigo-400
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-indigo-300
                                    ">
                                    PHP
                                </p>

                            </div>


                            <!-- MySQL -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-blue-400/40
                                    hover:shadow-xl
                                    hover:shadow-blue-950/20
                                ">

                                <i
                                    class="
                                        ri-database-2-line
                                        text-4xl
                                        text-blue-300
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-blue-200
                                    ">
                                    MySQL
                                </p>

                            </div>


                            <!-- PHPMailer -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-pink-400/40
                                    hover:shadow-xl
                                    hover:shadow-pink-950/20
                                ">

                                <i
                                    class="
                                        ri-mail-send-line
                                        text-4xl
                                        text-pink-400
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:-rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-pink-300
                                    ">
                                    PHPMailer
                                </p>

                            </div>


                            <!-- GitHub -->

                            <div
                                class="
                                    about-tech
                                    group/tech
                                    transition-all
                                    duration-500
                                    hover:-translate-y-2
                                    hover:scale-[1.03]
                                    hover:border-slate-400/40
                                    hover:shadow-xl
                                    hover:shadow-slate-950/20
                                ">

                                <i
                                    class="
                                        ri-github-line
                                        text-4xl
                                        text-slate-200
                                        inline-block
                                        transition-all
                                        duration-500
                                        group-hover/tech:scale-125
                                        group-hover/tech:rotate-6
                                    ">
                                </i>

                                <p
                                    class="
                                        mt-3
                                        font-semibold
                                        transition-colors
                                        duration-300
                                        group-hover/tech:text-slate-200
                                    ">
                                    GitHub
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         TEAM
                    ================================================== -->

                    <div
                        class="
                            about-section
                            group/teamsection
                            transition-all
                            duration-500
                            hover:border-pink-400/25
                            hover:shadow-2xl
                            hover:shadow-pink-950/20
                        ">

                        <div class="text-center mb-8">

                            <p
                                class="
                                    text-pink-400
                                    font-semibold
                                    text-sm
                                    transition-all
                                    duration-500
                                    group-hover/teamsection:tracking-[0.25em]
                                ">

                                OUR TEAM

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
                                    transition-all
                                    duration-500
                                    group-hover/teamsection:text-pink-100
                                ">

                                Meet Our Team

                            </h2>


                            <p
                                class="
                                    text-slate-400
                                    mt-3
                                    text-sm
                                ">

                                Click a member to view details

                            </p>

                        </div>


                        <!-- ==================================================
                             TEAM CARD GRID
                        ================================================== -->

                        <div
                            class="
                                grid
                                grid-cols-1
                                md:grid-cols-2
                                gap-6
                                items-stretch
                            ">


                            <?php foreach ($teamMembers as $index => $member): ?>


                                <!-- ==================================================
                                     TEAM MEMBER CARD
                                ================================================== -->

                                <div
                                    class="
                                        team-card
                                        group/teamcard
                                        transition-all
                                        duration-500
                                        ease-out
                                        hover:-translate-y-2
                                    "
                                    onclick="flipCard(this)">

                                    <div
                                        class="
                                            team-card-inner
                                            transition-all
                                            duration-700
                                        ">


                                        <!-- ==================================================
                                             FRONT
                                        ================================================== -->

                                        <div
                                            class="
                                                team-front
                                                transition-all
                                                duration-500
                                                group-hover/teamcard:border-indigo-400/50
                                            ">

                                            <div
                                                class="
                                                    team-image-wrapper
                                                    transition-all
                                                    duration-700
                                                    group-hover/teamcard:bg-slate-900
                                                ">

                                                <img
                                                    src="<?= htmlspecialchars($member["image"], ENT_QUOTES, 'UTF-8') ?>"
                                                    alt="<?= htmlspecialchars($member["name"], ENT_QUOTES, 'UTF-8') ?>"
                                                    class="
                                                        team-image
                                                        transition-transform
                                                        duration-700
                                                        ease-out
                                                        group-hover/teamcard:scale-[1.035]
                                                    "
                                                    loading="lazy">

                                            </div>


                                            <div
                                                class="
                                                    team-front-info
                                                    transition-all
                                                    duration-500
                                                    group-hover/teamcard:bg-slate-950
                                                ">

                                                <h3
                                                    class="
                                                        team-front-name
                                                        transition-all
                                                        duration-300
                                                        group-hover/teamcard:text-indigo-200
                                                    ">

                                                    <?= htmlspecialchars($member["name"], ENT_QUOTES, 'UTF-8') ?>

                                                </h3>


                                                <p
                                                    class="
                                                        team-front-role
                                                        transition-all
                                                        duration-300
                                                        group-hover/teamcard:text-purple-300
                                                    ">

                                                    <?= htmlspecialchars($member["teamRole"], ENT_QUOTES, 'UTF-8') ?>

                                                </p>

                                            </div>

                                        </div>


                                        <!-- ==================================================
                                             BACK
                                        ================================================== -->

                                        <div
                                            class="
                                                team-back
                                                transition-all
                                                duration-500
                                            ">


                                            <!-- MEMBER HEADER -->

                                            <div
                                                class="team-back-header">

                                                <h3
                                                    class="
                                                        team-back-name
                                                        transition-colors
                                                        duration-300
                                                        group-hover/teamcard:text-indigo-200
                                                    ">

                                                    <?= htmlspecialchars($member["name"], ENT_QUOTES, 'UTF-8') ?>

                                                </h3>


                                                <p
                                                    class="team-back-team-role">

                                                    <?= htmlspecialchars($member["teamRole"], ENT_QUOTES, 'UTF-8') ?>

                                                </p>


                                                <p
                                                    class="team-back-professional-role">

                                                    <?= htmlspecialchars($member["role"], ENT_QUOTES, 'UTF-8') ?>

                                                </p>

                                            </div>


                                            <!-- BASIC DETAILS -->

                                            <div
                                                class="team-details">


                                                <!-- COURSE -->

                                                <div
                                                    class="
                                                        team-detail-row
                                                        transition-all
                                                        duration-300
                                                        hover:-translate-y-0.5
                                                        hover:border-indigo-400/30
                                                        hover:bg-indigo-500/10
                                                    ">

                                                    <i
                                                        class="
                                                            ri-graduation-cap-line
                                                            team-detail-icon
                                                            text-indigo-400
                                                            transition-transform
                                                            duration-300
                                                            hover:scale-110
                                                        ">
                                                    </i>

                                                    <div
                                                        class="team-detail-content">

                                                        <span
                                                            class="team-detail-label">
                                                            Course
                                                        </span>

                                                        <span
                                                            class="team-detail-value">

                                                            <?= htmlspecialchars($member["course"], ENT_QUOTES, 'UTF-8') ?>

                                                        </span>

                                                    </div>

                                                </div>


                                                <!-- COLLEGE -->

                                                <div
                                                    class="
                                                        team-detail-row
                                                        transition-all
                                                        duration-300
                                                        hover:-translate-y-0.5
                                                        hover:border-purple-400/30
                                                        hover:bg-purple-500/10
                                                    ">

                                                    <i
                                                        class="
                                                            ri-building-2-line
                                                            team-detail-icon
                                                            text-purple-400
                                                        ">
                                                    </i>

                                                    <div
                                                        class="team-detail-content">

                                                        <span
                                                            class="team-detail-label">
                                                            College
                                                        </span>

                                                        <span
                                                            class="team-detail-value">

                                                            <?= htmlspecialchars($member["college"], ENT_QUOTES, 'UTF-8') ?>

                                                        </span>

                                                    </div>

                                                </div>


                                                <!-- PROJECT ROLE -->

                                                <div
                                                    class="
                                                        team-detail-row
                                                        transition-all
                                                        duration-300
                                                        hover:-translate-y-0.5
                                                        hover:border-pink-400/30
                                                        hover:bg-pink-500/10
                                                    ">

                                                    <i
                                                        class="
                                                            ri-code-box-line
                                                            team-detail-icon
                                                            text-pink-400
                                                        ">
                                                    </i>

                                                    <div
                                                        class="team-detail-content">

                                                        <span
                                                            class="team-detail-label">
                                                            Project Role
                                                        </span>

                                                        <span
                                                            class="team-detail-value">

                                                            <?= htmlspecialchars($member["projectRole"], ENT_QUOTES, 'UTF-8') ?>

                                                        </span>

                                                    </div>

                                                </div>

                                            </div>


                                            <!-- ==================================================
                                                 EMAIL + SOCIAL
                                            ================================================== -->

                                            <div
                                                class="team-social-list">


                                                <!-- EMAIL -->

                                                <?php if (!empty($member["email"])): ?>

                                                    <a
                                                        href="mailto:<?= htmlspecialchars($member["email"], ENT_QUOTES, 'UTF-8') ?>"
                                                        class="
                                                            team-social-link
                                                            transition-all
                                                            duration-300
                                                            hover:-translate-y-0.5
                                                            hover:translate-x-1
                                                            hover:border-cyan-400/30
                                                            hover:bg-cyan-500/10
                                                            hover:shadow-lg
                                                            hover:shadow-cyan-950/20
                                                        "
                                                        onclick="event.stopPropagation();">

                                                        <i
                                                            class="
                                                                ri-mail-line
                                                                text-cyan-400
                                                                transition-transform
                                                                duration-300
                                                                group-hover/teamcard:scale-110
                                                            ">
                                                        </i>

                                                        <span
                                                            class="
                                                                min-w-0
                                                                overflow-wrap-anywhere
                                                            ">

                                                            <?= htmlspecialchars($member["email"], ENT_QUOTES, 'UTF-8') ?>

                                                        </span>

                                                    </a>

                                                <?php else: ?>

                                                    <span
                                                        class="team-social-na">

                                                        <i class="ri-mail-line"></i>

                                                        <span>
                                                            Email — N/A
                                                        </span>

                                                    </span>

                                                <?php endif; ?>


                                                <!-- INSTAGRAM -->

                                                <?php if (!empty($member["instagram"])): ?>

                                                    <a
                                                        href="<?= htmlspecialchars($member["instagram"], ENT_QUOTES, 'UTF-8') ?>"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="
                                                            team-social-link
                                                            transition-all
                                                            duration-300
                                                            hover:-translate-y-0.5
                                                            hover:translate-x-1
                                                            hover:border-pink-400/30
                                                            hover:bg-pink-500/10
                                                            hover:shadow-lg
                                                            hover:shadow-pink-950/20
                                                        "
                                                        onclick="event.stopPropagation();">

                                                        <i
                                                            class="
                                                                ri-instagram-line
                                                                text-pink-400
                                                                transition-transform
                                                                duration-300
                                                                group-hover/teamcard:scale-110
                                                            ">
                                                        </i>

                                                        <span>
                                                            Instagram
                                                        </span>

                                                    </a>

                                                <?php else: ?>

                                                    <span
                                                        class="team-social-na">

                                                        <i class="ri-instagram-line"></i>

                                                        <span>
                                                            Instagram — N/A
                                                        </span>

                                                    </span>

                                                <?php endif; ?>


                                                <!-- WHATSAPP -->

                                                <?php if (!empty($member["whatsapp"])): ?>

                                                    <a
                                                        href="<?= htmlspecialchars($member["whatsapp"], ENT_QUOTES, 'UTF-8') ?>"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="
                                                            team-social-link
                                                            transition-all
                                                            duration-300
                                                            hover:-translate-y-0.5
                                                            hover:translate-x-1
                                                            hover:border-green-400/30
                                                            hover:bg-green-500/10
                                                            hover:shadow-lg
                                                            hover:shadow-green-950/20
                                                        "
                                                        onclick="event.stopPropagation();">

                                                        <i
                                                            class="
                                                                ri-whatsapp-line
                                                                text-green-400
                                                                transition-transform
                                                                duration-300
                                                                group-hover/teamcard:scale-110
                                                            ">
                                                        </i>

                                                        <span>
                                                            WhatsApp
                                                        </span>

                                                    </a>

                                                <?php else: ?>

                                                    <span
                                                        class="team-social-na">

                                                        <i class="ri-whatsapp-line"></i>

                                                        <span>
                                                            WhatsApp — N/A
                                                        </span>

                                                    </span>

                                                <?php endif; ?>


                                                <!-- GITHUB -->

                                                <?php if (!empty($member["github"])): ?>

                                                    <a
                                                        href="<?= htmlspecialchars($member["github"], ENT_QUOTES, 'UTF-8') ?>"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="
                                                            team-social-link
                                                            transition-all
                                                            duration-300
                                                            hover:-translate-y-0.5
                                                            hover:translate-x-1
                                                            hover:border-slate-400/30
                                                            hover:bg-slate-500/10
                                                            hover:shadow-lg
                                                            hover:shadow-slate-950/20
                                                        "
                                                        onclick="event.stopPropagation();">

                                                        <i
                                                            class="
                                                                ri-github-line
                                                                text-slate-200
                                                                transition-transform
                                                                duration-300
                                                                group-hover/teamcard:scale-110
                                                            ">
                                                        </i>

                                                        <span>
                                                            GitHub
                                                        </span>

                                                    </a>

                                                <?php else: ?>

                                                    <span
                                                        class="team-social-na">

                                                        <i class="ri-github-line"></i>

                                                        <span>
                                                            GitHub — N/A
                                                        </span>

                                                    </span>

                                                <?php endif; ?>


                                                <!-- LINKEDIN -->

                                                <?php if (!empty($member["linkedin"])): ?>

                                                    <a
                                                        href="<?= htmlspecialchars($member["linkedin"], ENT_QUOTES, 'UTF-8') ?>"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="
                                                            team-social-link
                                                            transition-all
                                                            duration-300
                                                            hover:-translate-y-0.5
                                                            hover:translate-x-1
                                                            hover:border-blue-400/30
                                                            hover:bg-blue-500/10
                                                            hover:shadow-lg
                                                            hover:shadow-blue-950/20
                                                        "
                                                        onclick="event.stopPropagation();">

                                                        <i
                                                            class="
                                                                ri-linkedin-box-line
                                                                text-blue-400
                                                                transition-transform
                                                                duration-300
                                                                group-hover/teamcard:scale-110
                                                            ">
                                                        </i>

                                                        <span>
                                                            LinkedIn
                                                        </span>

                                                    </a>

                                                <?php else: ?>

                                                    <span
                                                        class="team-social-na">

                                                        <i class="ri-linkedin-box-line"></i>

                                                        <span>
                                                            LinkedIn — N/A
                                                        </span>

                                                    </span>

                                                <?php endif; ?>


                                            </div>


                                            <!-- FLIP HINT -->

                                            <p
                                                class="
                                                    team-flip-hint
                                                    transition-all
                                                    duration-300
                                                    group-hover/teamcard:text-slate-400
                                                ">

                                                Click card to flip back

                                            </p>

                                        </div>

                                    </div>

                                </div>


                            <?php endforeach; ?>

                        </div>

                    </div>


                </div>

            </section>

        </div>

    </main>


    <!-- ==================================================
         EXISTING FOOTER
    ================================================== -->

    <div id="footer"></div>


    <!-- ==================================================
         EXISTING APP JS
    ================================================== -->

    <script src="../../assets/js/app.js"></script>


    <!-- ==================================================
         TEAM FLIP
         ONLY ONE CARD OPEN AT A TIME
    ================================================== -->

    <script>

        function flipCard(card) {

            const allCards =
                document.querySelectorAll(".team-card");


            allCards.forEach(function(otherCard) {

                if (otherCard !== card) {

                    otherCard.classList.remove(
                        "flipped"
                    );

                }

            });


            card.classList.toggle("flipped");

        }

    </script>

</body>

</html>