<?php
/* ==========================================================
   VOTIFY
   Admin History Logs
   File : pages/admin/history.php
========================================================== */

session_start();


/* ==========================================================
   SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.html");

    exit();

}


/* ==========================================================
   SUPER ADMIN ACCESS PROTECTION
========================================================== */

if (
    !isset($_SESSION["admin_role"]) ||
    $_SESSION["admin_role"] !== "Super Admin"
) {

    header("Location: dashboard.php");

    exit();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   FETCH ADMIN LOGS
========================================================== */

$logs = [];

$query = "
    SELECT *
    FROM admin_logs
    ORDER BY created_at DESC
";

$result = mysqli_query(
    $conn,
    $query
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $logs[] = $row;

    }

}


/* ==========================================================
   HISTORY STATISTICS
========================================================== */

$totalLogs = 0;
$todayLogs = 0;
$adminActions = 0;
$securityEvents = 0;


/* ==========================================================
   TOTAL LOGS
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM admin_logs
    "
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $totalLogs = (int) ($row["total"] ?? 0);

}


/* ==========================================================
   TODAY'S LOGS
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM admin_logs
    WHERE DATE(created_at) = CURDATE()
    "
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $todayLogs = (int) ($row["total"] ?? 0);

}


/* ==========================================================
   ADMIN ACTIONS
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM admin_logs
    WHERE action NOT LIKE '%Login%'
    AND action NOT LIKE '%Logout%'
    "
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $adminActions = (int) ($row["total"] ?? 0);

}


/* ==========================================================
   SECURITY EVENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM admin_logs
    WHERE action LIKE '%Login%'
    OR action LIKE '%Logout%'
    "
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $securityEvents = (int) ($row["total"] ?? 0);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    Admin History Logs | VOTIFY
</title>


<!-- ======================================================
     TAILWIND CSS
====================================================== -->

<script src="https://cdn.tailwindcss.com"></script>


<!-- ======================================================
     REMIX ICONS
====================================================== -->

<link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet">


<!-- ======================================================
     CUSTOM CSS
====================================================== -->

<link
    rel="stylesheet"
    href="../../assets/css/custom.css">

<link
    rel="stylesheet"
    href="../../assets/css/animations.css">
</head>


<body
    class="
    bg-[#0B1020]
    text-white
    min-h-screen
    overflow-x-hidden
    flex
    flex-col">


<!-- ======================================================
     LOADER
====================================================== -->

<div id="loader-container"></div>


<!-- ======================================================
     ANIMATED BACKGROUND
====================================================== -->

<div
    class="
    fixed
    inset-0
    -z-10
    overflow-hidden">

    <div
        class="
        absolute
        top-0
        left-0
        w-96
        h-96
        bg-blue-600/20
        blur-[150px]
        rounded-full">
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
        rounded-full">
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
        -translate-y-1/2">
    </div>

</div>


<!-- ======================================================
     HEADER
====================================================== -->

<div id="header"></div>


<!-- ======================================================
     MOBILE SIDEBAR OVERLAY
====================================================== -->

<div
    id="sidebarOverlay"
    class="
    fixed
    inset-0
    bg-black/60
    hidden
    z-40
    lg:hidden">
</div>


<!-- ======================================================
     MAIN CONTENT
====================================================== -->

<main
    class="
    flex-1
    max-w-7xl
    w-full
    mx-auto
    px-4
    sm:px-6
    lg:px-8
    py-8">

    <div
        class="
        grid
        grid-cols-1
        lg:grid-cols-[280px_1fr]
        gap-8
        items-start">


        <!-- ==================================================
             ADMIN SIDEBAR
        ================================================== -->

        <?php include "../../components/admin_sidebar.php"; ?>


        <!-- ==================================================
             PAGE CONTENT
        ================================================== -->

        <section class="min-w-0">

            <?php

            $pageTitle =
                "Admin History Logs";

            include
                "../../components/admin_topbar.php";

            ?>


            <!-- ==================================================
                 HISTORY CONTENT
            ================================================== -->

            <div
                id="historyContent"
                class="space-y-8">


                <!-- ==================================================
                     STATISTICS
                ================================================== -->

                <div
                    class="
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    xl:grid-cols-4
                    gap-6">


                    <!-- TOTAL LOGS -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-6
                        dashboard-card">

                        <div
                            class="
                            flex
                            items-center
                            justify-between">

                            <div>

                                <p class="text-slate-400">
                                    Total Logs
                                </p>

                                <h2
                                    class="
                                    text-5xl
                                    font-bold
                                    mt-4">

                                    <?php
                                    echo $totalLogs;
                                    ?>

                                </h2>

                            </div>


                            <div
                                class="
                                w-16
                                h-16
                                rounded-2xl
                                bg-blue-500/20
                                flex
                                items-center
                                justify-center">

                                <i
                                    class="
                                    ri-file-list-3-line
                                    text-3xl
                                    text-blue-400">
                                </i>

                            </div>

                        </div>

                    </div>


                    <!-- TODAY'S LOGS -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-6
                        dashboard-card">

                        <div
                            class="
                            flex
                            items-center
                            justify-between">

                            <div>

                                <p class="text-slate-400">
                                    Today's Logs
                                </p>

                                <h2
                                    class="
                                    text-5xl
                                    font-bold
                                    mt-4">

                                    <?php
                                    echo $todayLogs;
                                    ?>

                                </h2>

                            </div>


                            <div
                                class="
                                w-16
                                h-16
                                rounded-2xl
                                bg-yellow-500/20
                                flex
                                items-center
                                justify-center">

                                <i
                                    class="
                                    ri-calendar-check-line
                                    text-3xl
                                    text-yellow-400">
                                </i>

                            </div>

                        </div>

                    </div>


                    <!-- ADMIN ACTIONS -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-6
                        dashboard-card">

                        <div
                            class="
                            flex
                            items-center
                            justify-between">

                            <div>

                                <p class="text-slate-400">
                                    Admin Actions
                                </p>

                                <h2
                                    class="
                                    text-5xl
                                    font-bold
                                    mt-4">

                                    <?php
                                    echo $adminActions;
                                    ?>

                                </h2>

                            </div>


                            <div
                                class="
                                w-16
                                h-16
                                rounded-2xl
                                bg-green-500/20
                                flex
                                items-center
                                justify-center">

                                <i
                                    class="
                                    ri-user-settings-line
                                    text-3xl
                                    text-green-400">
                                </i>

                            </div>

                        </div>

                    </div>


                    <!-- SECURITY EVENTS -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-6
                        dashboard-card">

                        <div
                            class="
                            flex
                            items-center
                            justify-between">

                            <div>

                                <p class="text-slate-400">
                                    Security Events
                                </p>

                                <h2
                                    class="
                                    text-5xl
                                    font-bold
                                    mt-4">

                                    <?php
                                    echo $securityEvents;
                                    ?>

                                </h2>

                            </div>


                            <div
                                class="
                                w-16
                                h-16
                                rounded-2xl
                                bg-red-500/20
                                flex
                                items-center
                                justify-center">

                                <i
                                    class="
                                    ri-shield-check-line
                                    text-3xl
                                    text-red-400">
                                </i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     ADMIN HISTORY TABLE
                ================================================== -->

                <div
                    class="
                    glass
                    rounded-3xl
                    p-8">


                    <!-- ==================================================
                         TABLE HEADER
                    ================================================== -->

                    <div
                        class="
                        flex
                        flex-col
                        lg:flex-row
                        lg:items-center
                        lg:justify-between
                        gap-6
                        mb-8">


                        <div>

                            <h2
                                class="
                                text-3xl
                                font-bold">

                                Admin Activity History

                            </h2>


                            <p
                                class="
                                text-slate-400
                                mt-2">

                                Complete audit trail of administrator actions.

                            </p>

                        </div>


                        <!-- SEARCH -->

                        <div
                            class="
                            relative
                            w-full
                            lg:w-80">

                            <i
                                class="
                                ri-search-line
                                absolute
                                left-4
                                top-1/2
                                -translate-y-1/2
                                text-slate-400">
                            </i>


                            <input
                                type="text"
                                id="historySearch"
                                placeholder="Search logs..."
                                class="pl-12">

                        </div>

                    </div>


                    <!-- ==================================================
                         FILTERS
                    ================================================== -->

                    <div
                        class="
                        flex
                        flex-col
                        md:flex-row
                        justify-between
                        gap-4
                        mb-6">


                        <!-- ==================================================
                             ENTRIES CUSTOM DROPDOWN
                        ================================================== -->

                        <div
                            id="entriesDropdown"
                            class="relative w-40 shrink-0">


                            <!-- Hidden native select -->

                            <select
                                id="entriesSelect"
                                class="sr-only"
                                aria-hidden="true"
                                tabindex="-1">

                                <option value="10">
                                    10 Entries
                                </option>

                                <option value="25">
                                    25 Entries
                                </option>

                                <option value="50">
                                    50 Entries
                                </option>

                                <option value="100">
                                    100 Entries
                                </option>

                            </select>


                            <!-- Custom trigger -->

                            <button
                                id="entriesDropdownButton"
                                type="button"
                                class="
                                w-full
                                min-h-11
                                inline-flex
                                items-center
                                justify-between
                                gap-2.5
                                px-3.5
                                py-2.5
                                rounded-xl
                                border
                                border-white/10
                                bg-white/5
                                text-slate-200
                                text-sm
                                font-medium
                                text-left
                                cursor-pointer
                                transition-all
                                duration-200
                                hover:bg-white/10
                                hover:border-blue-400/40
                                focus:outline-none
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/10"
                                aria-haspopup="listbox"
                                aria-expanded="false">

                                <span
                                    id="entriesDropdownLabel">

                                    10 Entries

                                </span>

                                <i
                                    class="
                                    ri-arrow-down-s-line
                                    text-lg
                                    text-slate-300
                                    shrink-0
                                    transition-transform
                                    duration-200">
                                </i>

                            </button>


                            <!-- Custom menu -->

                            <div
                                id="entriesDropdownMenu"
                                class="
                                absolute
                                top-[calc(100%+7px)]
                                left-0
                                w-full
                                z-[100]
                                p-1
                                rounded-[13px]
                                border
                                border-white/10
                                bg-slate-900/95
                                shadow-[0_20px_50px_rgba(0,0,0,0.45)]
                                backdrop-blur-xl
                                opacity-0
                                invisible
                                -translate-y-1
                                scale-[0.98]
                                origin-top
                                transition-all
                                duration-150"
                                role="listbox"
                                aria-label="Entries per page">


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-gradient-to-r
                                    from-blue-600
                                    to-purple-500
                                    text-white
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150
                                    selected"
                                    data-value="10"
                                    role="option"
                                    aria-selected="true">

                                    <span>
                                        10 Entries
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="25"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        25 Entries
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="50"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        50 Entries
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="100"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        100 Entries
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>

                            </div>

                        </div>


                        <!-- ==================================================
                             ACTION CUSTOM DROPDOWN
                        ================================================== -->

                        <div
                            id="actionDropdown"
                            class="
                                relative
                                w-56
                                shrink-0">


                            <!-- Hidden native select -->

                            <select
                                id="actionFilter"
                                class="sr-only"
                                aria-hidden="true"
                                tabindex="-1">

                                <option value="">
                                    All Activities
                                </option>

                                <option value="Login">
                                    Login
                                </option>

                                <option value="Logout">
                                    Logout
                                </option>

                                <option value="Election Started">
                                    Election Started
                                </option>

                                <option value="Election Stopped">
                                    Election Stopped
                                </option>

                                <option value="Student Approved">
                                    Student Approved
                                </option>

                                <option value="Candidate Added">
                                    Candidate Added
                                </option>

                            </select>


                            <!-- Custom trigger -->

                            <button
                                id="actionDropdownButton"
                                type="button"
                                class="
                                w-full
                                min-h-11
                                inline-flex
                                items-center
                                justify-between
                                gap-2.5
                                px-3.5
                                py-2.5
                                rounded-xl
                                border
                                border-white/10
                                bg-white/5
                                text-slate-200
                                text-sm
                                font-medium
                                text-left
                                cursor-pointer
                                transition-all
                                duration-200
                                hover:bg-white/10
                                hover:border-blue-400/40
                                focus:outline-none
                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-500/10"
                                aria-haspopup="listbox"
                                aria-expanded="false">

                                <span
                                    id="actionDropdownLabel">

                                    All Activities

                                </span>

                                <i
                                    class="
                                    ri-arrow-down-s-line
                                    text-lg
                                    text-slate-300
                                    shrink-0
                                    transition-transform
                                    duration-200">
                                </i>

                            </button>


                            <!-- Custom menu -->

                            <div
                                id="actionDropdownMenu"
                                class="
                                absolute
                                top-[calc(100%+7px)]
                                left-0
                                w-full
                                z-[100]
                                p-1
                                rounded-[13px]
                                border
                                border-white/10
                                bg-slate-900/95
                                shadow-[0_20px_50px_rgba(0,0,0,0.45)]
                                backdrop-blur-xl
                                opacity-0
                                invisible
                                -translate-y-1
                                scale-[0.98]
                                origin-top
                                transition-all
                                duration-150"
                                role="listbox"
                                aria-label="Activity filter">


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-gradient-to-r
                                    from-blue-600
                                    to-purple-500
                                    text-white
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150
                                    selected"
                                    data-value=""
                                    role="option"
                                    aria-selected="true">

                                    <span>
                                        All Activities
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Login"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Login
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Logout"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Logout
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Election Started"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Election Started
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Election Stopped"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Election Stopped
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Student Approved"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Student Approved
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>


                                <button
                                    type="button"
                                    class="
                                    w-full
                                    min-h-10
                                    inline-flex
                                    items-center
                                    justify-between
                                    gap-2.5
                                    px-2.5
                                    py-2
                                    rounded-[9px]
                                    border-0
                                    bg-transparent
                                    text-slate-300
                                    text-sm
                                    font-medium
                                    text-left
                                    cursor-pointer
                                    transition-all
                                    duration-150"
                                    data-value="Candidate Added"
                                    role="option"
                                    aria-selected="false">

                                    <span>
                                        Candidate Added
                                    </span>

                                    <i
                                        class="
                                        ri-check-line
                                        text-base
                                        opacity-0
                                        transition-opacity
                                        duration-150">
                                    </i>

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         TABLE
                    ================================================== -->

                    <div
                        class="
                        overflow-x-auto
                        rounded-2xl
                        border
                        border-white/10">

                        <table
                            id="historyTable"
                            class="w-full">

                            <thead
                                class="bg-white/5">

                                <tr>

                                    <th
                                        class="
                                        px-6
                                        py-4
                                        text-left">

                                        Date & Time

                                    </th>

                                    <th
                                        class="
                                        px-6
                                        py-4
                                        text-left">

                                        Action

                                    </th>

                                    <th
                                        class="
                                        px-6
                                        py-4
                                        text-left">

                                        Description

                                    </th>

                                    <th
                                        class="
                                        px-6
                                        py-4
                                        text-left">

                                        Admin

                                    </th>

                                    <th
                                        class="
                                        px-6
                                        py-4
                                        text-left">

                                        IP Address

                                    </th>

                                </tr>

                            </thead>


                            <tbody id="historyTableBody">

                                <?php if (count($logs) === 0) { ?>

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="
                                            text-center
                                            py-10
                                            text-slate-400">

                                            No Logs Available

                                        </td>

                                    </tr>

                                <?php } else { ?>


                                    <?php foreach ($logs as $log) { ?>

                                        <tr
                                            class="
                                            border-b
                                            border-white/5
                                            hover:bg-white/5
                                            transition">

                                            <!-- DATE & TIME -->

                                            <td
                                                class="
                                                px-6
                                                py-4">

                                                <?php

                                                echo date(
                                                    "d M Y",
                                                    strtotime(
                                                        $log["created_at"]
                                                    )
                                                );

                                                ?>

                                                <br>

                                                <span
                                                    class="
                                                    text-xs
                                                    text-slate-500">

                                                    <?php

                                                    echo date(
                                                        "h:i A",
                                                        strtotime(
                                                            $log["created_at"]
                                                        )
                                                    );

                                                    ?>

                                                </span>

                                            </td>


                                            <!-- ACTION -->

                                            <td
                                                class="
                                                px-6
                                                py-4
                                                font-medium">

                                                <?php

                                                echo htmlspecialchars(
                                                    $log["action"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                                ?>

                                            </td>


                                            <!-- DESCRIPTION -->

                                            <td
                                                class="
                                                px-6
                                                py-4">

                                                <?php

                                                echo htmlspecialchars(
                                                    $log["description"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                                ?>

                                            </td>


                                            <!-- ADMIN -->

                                            <td
                                                class="
                                                px-6
                                                py-4
                                                text-blue-400">

                                                <?php

                                                echo htmlspecialchars(
                                                    $log["admin_username"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                                ?>

                                            </td>


                                            <!-- IP ADDRESS -->

                                            <td
                                                class="
                                                px-6
                                                py-4
                                                text-slate-400">

                                                <?php

                                                echo htmlspecialchars(
                                                    $log["ip_address"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                                ?>

                                            </td>

                                        </tr>

                                    <?php } ?>


                                <?php } ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- ==================================================
                         PAGINATION
                    ================================================== -->

                    <div
                        class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-4
                        mt-6
                        pt-5
                        border-t
                        border-white/10">


                        <!-- INFO -->

                        <p
                            id="historyInfo"
                            class="
                            text-sm
                            text-slate-400
                            text-center
                            sm:text-left">

                            Showing

                            <strong class="text-slate-200">
                                0
                            </strong>

                            to

                            <strong class="text-slate-200">
                                0
                            </strong>

                            of

                            <strong class="text-slate-200">
                                0
                            </strong>

                            entries

                        </p>


                        <!-- PAGINATION -->

                        <div
                            id="historyPagination"
                            class="
                            flex
                            items-center
                            justify-center
                            gap-1.5">


                            <!-- PREVIOUS -->

                            <button
                                id="prevPage"
                                type="button"
                                aria-label="Previous page"
                                class="
                                inline-flex
                                items-center
                                justify-center
                                h-9
                                min-w-9
                                px-2.5
                                rounded-lg
                                border
                                border-white/10
                                bg-white/5
                                text-slate-300
                                text-sm
                                font-medium
                                transition
                                hover:bg-white/10
                                hover:text-white
                                disabled:opacity-30
                                disabled:cursor-not-allowed">

                                <i
                                    class="
                                    ri-arrow-left-s-line
                                    text-lg">
                                </i>

                            </button>


                            <!-- PAGE NUMBERS -->

                            <div
                                id="historyPageButtons"
                                class="
                                flex
                                items-center
                                gap-1.5">
                            </div>


                            <span
                                id="currentPage"
                                class="hidden">

                                1

                            </span>


                            <!-- NEXT -->

                            <button
                                id="nextPage"
                                type="button"
                                aria-label="Next page"
                                class="
                                inline-flex
                                items-center
                                justify-center
                                h-9
                                min-w-9
                                px-2.5
                                rounded-lg
                                border
                                border-white/10
                                bg-white/5
                                text-slate-300
                                text-sm
                                font-medium
                                transition
                                hover:bg-white/10
                                hover:text-white
                                disabled:opacity-30
                                disabled:cursor-not-allowed">

                                <i
                                    class="
                                    ri-arrow-right-s-line
                                    text-lg">
                                </i>

                            </button>

                        </div>

                    </div>


                </div>

            </div>

        </section>

    </div>

</main>


<!-- ======================================================
     FOOTER
====================================================== -->

<div id="footer"></div>


<!-- ======================================================
     JAVASCRIPT
====================================================== -->

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/dashboard.js"></script>

<script src="../../assets/js/history.js"></script>


<!-- ======================================================
     CUSTOM DROPDOWN JAVASCRIPT
====================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        /* ==================================================
           TAILWIND CUSTOM DROPDOWN
        ================================================== */

        function initializeCustomDropdown(config) {

            const wrapper =
                document.getElementById(config.wrapper);

            const trigger =
                document.getElementById(config.trigger);

            const menu =
                document.getElementById(config.menu);

            const label =
                document.getElementById(config.label);

            const nativeSelect =
                document.getElementById(config.select);

            if (
                !wrapper ||
                !trigger ||
                !menu ||
                !label ||
                !nativeSelect
            ) {
                return;
            }

            const options =
                Array.from(
                    menu.querySelectorAll(
                        "[data-value]"
                    )
                );

            const arrow =
                trigger.querySelector(
                    ".ri-arrow-down-s-line"
                );


            /* ==================================================
               OPTION HOVER
               Only non-selected options receive hover styling.
               The selected option is intentionally protected.
            ================================================== */

            options.forEach(
                option => {

                    option.addEventListener(
                        "pointerenter",
                        function () {

                            if (
                                option.classList.contains(
                                    "selected"
                                )
                            ) {
                                return;
                            }

                            option.classList.add(
                                ...hoverClasses
                            );

                        }
                    );


                    option.addEventListener(
                        "pointerleave",
                        function () {

                            option.classList.remove(
                                ...hoverClasses
                            );

                        }
                    );

                }
            );


            const menuClosed = [
                "opacity-0",
                "invisible",
                "-translate-y-1",
                "scale-[0.98]"
            ];

            const menuOpen = [
                "opacity-100",
                "visible",
                "translate-y-0",
                "scale-100"
            ];

            const triggerOpen = [
                "bg-slate-800/95",
                "border-blue-500",
                "ring-4",
                "ring-blue-500/10"
            ];

            const triggerClosed = [
                "bg-white/5",
                "border-white/10"
            ];

            const selectedClasses = [
                "bg-gradient-to-r",
                "from-blue-600",
                "to-purple-500",
                "text-white"
            ];

            const unselectedClasses = [
                "bg-transparent",
                "text-slate-300"
            ];

            const hoverClasses = [
                "bg-blue-500/15",
                "text-white"
            ];


            function closeDropdown() {

                menu.classList.remove(...menuOpen);
                menu.classList.add(...menuClosed);

                trigger.classList.remove(...triggerOpen);
                trigger.classList.add(...triggerClosed);

                if (arrow) {
                    arrow.classList.remove("rotate-180");
                }

                trigger.setAttribute(
                    "aria-expanded",
                    "false"
                );
            }


            function openDropdown() {

                menu.classList.remove(...menuClosed);
                menu.classList.add(...menuOpen);

                trigger.classList.remove(...triggerClosed);
                trigger.classList.add(...triggerOpen);

                if (arrow) {
                    arrow.classList.add("rotate-180");
                }

                trigger.setAttribute(
                    "aria-expanded",
                    "true"
                );
            }


            function toggleDropdown() {

                if (
                    menu.classList.contains("visible")
                ) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            }


            function updateUI(value) {

                const selected =
                    options.find(
                        option =>
                            option.dataset.value ===
                            String(value)
                    );

                if (!selected) {
                    return;
                }

                const textElement =
                    selected.querySelector("span");

                if (textElement) {
                    label.textContent =
                        textElement.textContent.trim();
                }


                options.forEach(
                    option => {

                        const isSelected =
                            option.dataset.value ===
                            String(value);

                        option.classList.toggle(
                            "selected",
                            isSelected
                        );

                        option.setAttribute(
                            "aria-selected",
                            isSelected
                                ? "true"
                                : "false"
                        );

                        const check =
                            option.querySelector(
                                ".ri-check-line"
                            );


                        if (isSelected) {

                            /*
                             * Selected option is protected
                             * from hover styling.
                             */
                            option.classList.remove(
                                ...unselectedClasses
                            );

                            option.classList.remove(
                                ...hoverClasses
                            );

                            option.classList.add(
                                ...selectedClasses
                            );

                            if (check) {

                                check.classList.remove(
                                    "opacity-0"
                                );

                                check.classList.add(
                                    "opacity-100"
                                );
                            }


                        } else {

                            option.classList.remove(
                                ...selectedClasses
                            );

                            option.classList.add(
                                ...unselectedClasses
                            );

                            if (check) {

                                check.classList.remove(
                                    "opacity-100"
                                );

                                check.classList.add(
                                    "opacity-0"
                                );
                            }
                        }

                    }
                );
            }


            closeDropdown();

            updateUI(
                nativeSelect.value
            );


            trigger.addEventListener(
                "click",
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    toggleDropdown();

                }
            );


            options.forEach(
                option => {

                    option.addEventListener(
                        "click",
                        function (event) {

                            event.preventDefault();
                            event.stopPropagation();

                            const value =
                                option.dataset.value;

                            if (
                                value === undefined
                            ) {
                                return;
                            }

                            nativeSelect.value =
                                value;

                            updateUI(value);

                            nativeSelect.dispatchEvent(
                                new Event(
                                    "change",
                                    {
                                        bubbles: true
                                    }
                                )
                            );

                            closeDropdown();

                        }
                    );

                }
            );


            document.addEventListener(
                "click",
                function (event) {

                    if (
                        !wrapper.contains(
                            event.target
                        )
                    ) {
                        closeDropdown();
                    }

                }
            );


            document.addEventListener(
                "keydown",
                function (event) {

                    if (
                        event.key === "Escape"
                    ) {
                        closeDropdown();
                    }

                }
            );


            nativeSelect.addEventListener(
                "change",
                function () {

                    updateUI(
                        nativeSelect.value
                    );

                }
            );

        }


        initializeCustomDropdown({

            wrapper: "entriesDropdown",
            trigger: "entriesDropdownButton",
            menu: "entriesDropdownMenu",
            label: "entriesDropdownLabel",
            select: "entriesSelect"

        });


        initializeCustomDropdown({

            wrapper: "actionDropdown",
            trigger: "actionDropdownButton",
            menu: "actionDropdownMenu",
            label: "actionDropdownLabel",
            select: "actionFilter"

        });

    }

);

</script>

</body>

</html>