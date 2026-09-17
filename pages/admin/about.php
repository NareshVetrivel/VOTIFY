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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>About Us | VOTIFY</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Remix Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
        rel="stylesheet">

    <!-- EXISTING VOTIFY CSS - DO NOT CHANGE -->
    <link
        rel="stylesheet"
        href="../../assets/css/custom.css">

    <link
        rel="stylesheet"
        href="../../assets/css/animations.css">

    <style>

        /* ==================================================
           ABOUT CONTENT ONLY
           HEADER + SIDEBAR ARE FROM EXISTING COMPONENTS
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

            border: 1px solid rgba(99, 102, 241, 0.15);

        }


        /* VOTIFY gradient text */

        .votify-gradient {

            background: linear-gradient(
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


        /* About section */

        .about-section {

            background:
                linear-gradient(
                    145deg,
                    rgba(18, 28, 51, 0.92),
                    rgba(11, 17, 35, 0.92)
                );

            border: 1px solid rgba(99, 102, 241, 0.18);

            border-radius: 24px;

            padding: 30px;

            transition: all 0.3s ease;

        }


        .about-section:hover {

            border-color:
                rgba(139, 92, 246, 0.35);

        }


        /* Feature cards */

        .about-feature {

            background:
                rgba(18, 28, 50, 0.75);

            border:
                1px solid rgba(99, 102, 241, 0.14);

            border-radius: 20px;

            padding: 24px;

            transition: all 0.3s ease;

        }


        .about-feature:hover {

            transform: translateY(-5px);

            border-color:
                rgba(168, 85, 247, 0.45);

            box-shadow:
                0 15px 35px
                rgba(79, 70, 229, 0.12);

        }


        /* Technology */

        .about-tech {

            background:
                rgba(23, 35, 57, 0.85);

            border:
                1px solid rgba(99, 102, 241, 0.14);

            border-radius: 16px;

            padding: 22px 15px;

            text-align: center;

            transition: all 0.3s ease;

        }


        .about-tech:hover {

            transform: translateY(-4px);

            border-color:
                rgba(139, 92, 246, 0.40);

        }

        /* Technology icon/card hover effect */
        .tech-hover i {
            display: inline-block;
            transition: transform .3s ease, filter .3s ease;
        }

        .tech-hover:hover i {
            transform: translateY(-5px) scale(1.18);
            filter:
                drop-shadow(0 0 8px rgba(99, 102, 241, .75))
                drop-shadow(0 0 18px rgba(217, 70, 239, .35));
        }

        .tech-hover:hover {
            transform: translateY(-5px);
        }


        /* Team cards */

        .team-card {

            height: 360px;

            perspective: 1000px;

            cursor: pointer;

        }


        .team-card-inner {

            position: relative;

            width: 100%;

            height: 100%;

            transform-style: preserve-3d;

            transition:
                transform 0.75s
                cubic-bezier(.2,.75,.25,1);

        }


        .team-card.flipped
        .team-card-inner {

            transform:
                rotateY(180deg);

        }


        .team-front,
        .team-back {

            position: absolute;

            inset: 0;

            width: 100%;

            height: 100%;

            padding: 25px;

            border-radius: 22px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;

            backface-visibility: hidden;

            -webkit-backface-visibility:
                hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(18, 29, 54, 0.96),
                    rgba(9, 15, 34, 0.96)
                );

            border:
                1px solid
                rgba(99, 102, 241, 0.20);

        }


        .team-back {

            transform:
                rotateY(180deg);

        }


        .team-image {

            width: 125px;

            height: 125px;

            border-radius: 50%;

            object-fit: cover;

            border: 4px solid transparent;

            background:
                linear-gradient(
                    #10182d,
                    #10182d
                ) padding-box,
                linear-gradient(
                    135deg,
                    #2563eb,
                    #9333ea,
                    #ec4899
                ) border-box;

            margin-bottom: 18px;

        }


        .team-placeholder {

            width: 125px;

            height: 125px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #818cf8;

            background: #10182d;

            border: 4px solid #6366f1;

            margin-bottom: 18px;

        }


        /* Responsive */

        @media (max-width: 768px) {

            .about-hero {

                padding: 25px;

            }

            .about-section {

                padding: 22px;

            }

            .team-card {

                height: 350px;

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
         SAME AS OTHER ADMIN PAGES
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
         SAME STRUCTURE AS candidates.php
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


            <!-- ==========================================
                 EXISTING ADMIN SIDEBAR
                 DO NOT CREATE A NEW SIDEBAR
            =========================================== -->

            <?php
                include "../../components/admin_sidebar.php";
            ?>


            <!-- ==========================================
                 ABOUT CONTENT
                 ONLY THIS AREA CHANGES
            =========================================== -->

            <section class="min-w-0">


                <!-- ======================================
                     EXISTING ADMIN TOPBAR
                ======================================= -->

                <?php

                    $pageTitle = "About Us";

                    include "../../components/admin_topbar.php";

                ?>


                <!-- ======================================
                     ABOUT PAGE
                ======================================= -->

                <div class="space-y-8">


                    <!-- ==================================
                         ABOUT PROJECT
                    =================================== -->

                    <div class="about-section">

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
                                ">

                                <i
                                    class="
                                        ri-shield-check-line
                                        text-3xl
                                        text-blue-400
                                    ">
                                </i>

                            </div>


                            <div>

                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                    ">

                                    About VOTIFY

                                </h2>

                                <p
                                    class="
                                        text-slate-500
                                        mt-1
                                    ">

                                    Online Student Voting System

                                </p>

                            </div>

                        </div>


                        <p
                            class="
                                text-slate-400
                                leading-7
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
                            ">

                            The system also provides administrators with
                            tools to manage elections, approve student
                            registrations, manage candidates and monitor
                            election activities.

                        </p>

                    </div>


                    <!-- ==================================
                         IMPORTANT FEATURES
                    =================================== -->

                    <div class="about-section">

                        <div class="mb-7">

                            <p
                                class="
                                    text-blue-400
                                    font-semibold
                                    text-sm
                                ">

                                WHAT WE PROVIDE

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-user-add-line
                                            text-3xl
                                            text-blue-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-user-settings-line
                                            text-3xl
                                            text-purple-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-lock-password-line
                                            text-3xl
                                            text-pink-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-calendar-check-line
                                            text-3xl
                                            text-blue-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-team-line
                                            text-3xl
                                            text-purple-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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

                            <div class="about-feature">

                                <div class="icon-box mb-5">

                                    <i
                                        class="
                                            ri-checkbox-circle-line
                                            text-3xl
                                            text-pink-400
                                        ">
                                    </i>

                                </div>

                                <h3
                                    class="
                                        text-lg
                                        font-bold
                                        mb-2
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


                    <!-- ==================================
                         TECHNOLOGY STACK
                    =================================== -->

                    <div class="about-section">

                        <div class="mb-7">

                            <p
                                class="
                                    text-purple-400
                                    font-semibold
                                    text-sm
                                ">

                                DEVELOPMENT

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
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
                            <div class="about-tech tech-hover">
                                <i class="ri-html5-line text-4xl text-orange-400"></i>
                                <p class="mt-3 font-semibold">HTML</p>
                            </div>

                            <!-- Tailwind CSS -->
                            <div class="about-tech tech-hover">
                                <i class="ri-tailwind-css-line text-4xl text-cyan-400"></i>
                                <p class="mt-3 font-semibold">Tailwind CSS</p>
                            </div>

                            <!-- JavaScript -->
                            <div class="about-tech tech-hover">
                                <i class="ri-javascript-line text-4xl text-yellow-300"></i>
                                <p class="mt-3 font-semibold">JavaScript</p>
                            </div>

                            <!-- PHP -->
                            <div class="about-tech tech-hover">
                                <i class="ri-code-s-slash-line text-4xl text-indigo-400"></i>
                                <p class="mt-3 font-semibold">PHP</p>
                            </div>

                            <!-- MySQL -->
                            <div class="about-tech tech-hover">
                                <i class="ri-database-2-line text-4xl text-blue-300"></i>
                                <p class="mt-3 font-semibold">MySQL</p>
                            </div>

                            <!-- PHPMailer -->
                            <div class="about-tech tech-hover">
                                <i class="ri-mail-send-line text-4xl text-pink-400"></i>
                                <p class="mt-3 font-semibold">PHPMailer</p>
                            </div>

                            <!-- GitHub -->
                            <div class="about-tech tech-hover">
                                <i class="ri-github-line text-4xl text-slate-200"></i>
                                <p class="mt-3 font-semibold">GitHub</p>
                            </div>

                        </div>

                        </div>

                    </div>


                    <!-- ==================================
                         TEAM
                    =================================== -->

                    <div class="about-section">

                        <div class="text-center mb-8">

                            <p
                                class="
                                    text-pink-400
                                    font-semibold
                                    text-sm
                                ">

                                OUR TEAM

                            </p>


                            <h2
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
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


                        <div
                            class="
                                grid
                                grid-cols-1
                                sm:grid-cols-2
                                lg:grid-cols-2
                                gap-6
                            ">


                            <!-- MEMBER 1 -->

                            <div
                                class="team-card"
                                onclick="flipCard(this)">

                                <div class="team-card-inner">

                                    <div class="team-front">

                                        <div class="team-placeholder">

                                            <i
                                                class="
                                                    ri-user-line
                                                    text-5xl
                                                ">
                                            </i>

                                        </div>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-indigo-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-500
                                                text-xs
                                                mt-5
                                            ">

                                            Click to view details

                                        </p>

                                    </div>


                                    <div class="team-back">

                                        <i
                                            class="
                                                ri-user-line
                                                text-5xl
                                                text-indigo-400
                                                mb-5
                                            ">
                                        </i>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-purple-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-5
                                            ">

                                            MCA Student

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-2
                                            ">

                                            VOTIFY Project Team

                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- MEMBER 2 -->

                            <div
                                class="team-card"
                                onclick="flipCard(this)">

                                <div class="team-card-inner">

                                    <div class="team-front">

                                        <div class="team-placeholder">

                                            <i
                                                class="
                                                    ri-user-line
                                                    text-5xl
                                                ">
                                            </i>

                                        </div>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-indigo-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-500
                                                text-xs
                                                mt-5
                                            ">

                                            Click to view details

                                        </p>

                                    </div>


                                    <div class="team-back">

                                        <i
                                            class="
                                                ri-user-line
                                                text-5xl
                                                text-purple-400
                                                mb-5
                                            ">
                                        </i>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-purple-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-5
                                            ">

                                            MCA Student

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-2
                                            ">

                                            VOTIFY Project Team

                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- MEMBER 3 -->

                            <div
                                class="team-card"
                                onclick="flipCard(this)">

                                <div class="team-card-inner">

                                    <div class="team-front">

                                        <div class="team-placeholder">

                                            <i
                                                class="
                                                    ri-user-line
                                                    text-5xl
                                                ">
                                            </i>

                                        </div>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-indigo-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-500
                                                text-xs
                                                mt-5
                                            ">

                                            Click to view details

                                        </p>

                                    </div>


                                    <div class="team-back">

                                        <i
                                            class="
                                                ri-user-line
                                                text-5xl
                                                text-pink-400
                                                mb-5
                                            ">
                                        </i>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-pink-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-5
                                            ">

                                            MCA Student

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-2
                                            ">

                                            VOTIFY Project Team

                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- MEMBER 4 -->

                            <div
                                class="team-card"
                                onclick="flipCard(this)">

                                <div class="team-card-inner">

                                    <div class="team-front">

                                        <div class="team-placeholder">

                                            <i
                                                class="
                                                    ri-user-line
                                                    text-5xl
                                                ">
                                            </i>

                                        </div>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-indigo-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-500
                                                text-xs
                                                mt-5
                                            ">

                                            Click to view details

                                        </p>

                                    </div>


                                    <div class="team-back">

                                        <i
                                            class="
                                                ri-user-line
                                                text-5xl
                                                text-indigo-400
                                                mb-5
                                            ">
                                        </i>

                                        <h3
                                            class="
                                                text-xl
                                                font-bold
                                            ">

                                            Member Name

                                        </h3>

                                        <p
                                            class="
                                                text-indigo-400
                                                mt-2
                                            ">

                                            Team Member

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-5
                                            ">

                                            MCA Student

                                        </p>

                                        <p
                                            class="
                                                text-slate-400
                                                text-sm
                                                mt-2
                                            ">

                                            VOTIFY Project Team

                                        </p>

                                    </div>

                                </div>

                            </div>

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
