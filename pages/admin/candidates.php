<?php
/* ==========================================================
   VOTIFY
   Candidate Management
   File : pages/admin/candidates.php
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
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   FETCH CANDIDATES
========================================================== */

$candidates = [];


$query = "
    SELECT
        id,
        student_id,
        admission_no,
        full_name,
        department,
        year,
        manifesto,
        status,
        vote_count,
        created_at,
        updated_at
    FROM candidates
    ORDER BY created_at DESC
";


$result = mysqli_query(
    $conn,
    $query
);


if ($result) {

    while (
        $row =
            mysqli_fetch_assoc(
                $result
            )
    ) {

        $candidates[] =
            $row;

    }

}


/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

$totalCandidates = 0;

$totalFirstYear = 0;

$totalSecondYear = 0;


/* ==========================================================
   TOTAL CANDIDATES
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM candidates
    "
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $totalCandidates =
        (int) (
            $row["total"] ?? 0
        );

}


/* ==========================================================
   FIRST-YEAR CANDIDATES
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM candidates
    WHERE
        year = '1st Year'
        OR
        year = 'I Year'
    "
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $totalFirstYear =
        (int) (
            $row["total"] ?? 0
        );

}


/* ==========================================================
   SECOND-YEAR CANDIDATES
========================================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM candidates
    WHERE
        year = '2nd Year'
        OR
        year = 'II Year'
    "
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $totalSecondYear =
        (int) (
            $row["total"] ?? 0
        );

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
        Candidate Management | VOTIFY
    </title>


    <!-- =====================================================
         TAILWIND
    ===================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- =====================================================
         REMIX ICONS
    ===================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         CSS
    ===================================================== -->

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
    overflow-x-hidden
    flex
    flex-col
    "
>


    <!-- =====================================================
         LOADER
    ===================================================== -->

    <div id="loader-container"></div>


    <!-- =====================================================
         BACKGROUND
    ===================================================== -->

    <div class="fixed inset-0 -z-10 overflow-hidden">

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
            blur-[130px]
            rounded-full
            -translate-x-1/2
            -translate-y-1/2
            "
        ></div>

    </div>


    <!-- =====================================================
         HEADER
    ===================================================== -->

    <div id="header"></div>


    <!-- =====================================================
         MOBILE OVERLAY
    ===================================================== -->

    <div
        id="sidebarOverlay"
        class="
        fixed
        inset-0
        bg-black/60
        hidden
        z-40
        lg:hidden
        "
    ></div>


    <!-- =====================================================
         MAIN
    ===================================================== -->

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
        "
    >

        <div
            class="
            grid
            grid-cols-1
            lg:grid-cols-[280px_1fr]
            gap-8
            items-start
            "
        >


            <!-- =====================================================
                 SIDEBAR
            ===================================================== -->

            <?php

            include "../../components/admin_sidebar.php";

            ?>


            <!-- =====================================================
                 CONTENT
            ===================================================== -->

            <section class="min-w-0">


                <?php

                $pageTitle =
                    "Candidate Management";

                include "../../components/admin_topbar.php";

                ?>


                <div
                    id="candidatesContent"
                    class="space-y-8"
                >


                    <!-- =====================================================
                         STATISTICS
                    ===================================================== -->

                    <div
                        class="
                        grid
                        grid-cols-1
                        md:grid-cols-2
                        xl:grid-cols-3
                        gap-6
                        "
                    >


                        <!-- =================================================
                             TOTAL CANDIDATES
                        ================================================= -->

                        <div
                            class="
                            glass
                            rounded-3xl
                            p-6
                            dashboard-card
                            "
                        >

                            <div
                                class="
                                flex
                                items-center
                                justify-between
                                "
                            >

                                <div>

                                    <p class="text-slate-400">
                                        Total Candidates
                                    </p>


                                    <h2
                                        id="totalCandidates"
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $totalCandidates; ?>

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
                                    justify-center
                                    "
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="
                                        w-9
                                        h-9
                                        text-blue-400
                                        "
                                        aria-hidden="true"
                                    >

                                        <path
                                            d="M12 2a5 5 0 100 10 5 5 0 000-10zm0 12c-4.97 0-9 2.24-9 5v3h18v-3c0-2.76-4.03-5-9-5z"
                                        />

                                    </svg>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             FIRST YEAR
                        ================================================= -->

                        <div
                            class="
                            glass
                            rounded-3xl
                            p-6
                            dashboard-card
                            "
                        >

                            <div
                                class="
                                flex
                                items-center
                                justify-between
                                "
                            >

                                <div>

                                    <p class="text-slate-400">
                                        1st Year Candidates
                                    </p>


                                    <h2
                                        id="firstYearCandidates"
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $totalFirstYear; ?>

                                    </h2>

                                </div>


                                <div
                                    class="
                                    w-16
                                    h-16
                                    rounded-2xl
                                    bg-emerald-500/20
                                    flex
                                    items-center
                                    justify-center
                                    "
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="
                                        w-8
                                        h-8
                                        text-emerald-400
                                        "
                                        aria-hidden="true"
                                    >

                                        <path
                                            d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"
                                        />

                                    </svg>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             SECOND YEAR
                        ================================================= -->

                        <div
                            class="
                            glass
                            rounded-3xl
                            p-6
                            dashboard-card
                            "
                        >

                            <div
                                class="
                                flex
                                items-center
                                justify-between
                                "
                            >

                                <div>

                                    <p class="text-slate-400">
                                        2nd Year Candidates
                                    </p>


                                    <h2
                                        id="secondYearCandidates"
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $totalSecondYear; ?>

                                    </h2>

                                </div>


                                <div
                                    class="
                                    w-16
                                    h-16
                                    rounded-2xl
                                    bg-violet-500/20
                                    flex
                                    items-center
                                    justify-center
                                    "
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="
                                        w-8
                                        h-8
                                        text-violet-400
                                        "
                                        aria-hidden="true"
                                    >

                                        <path
                                            d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"
                                        />

                                    </svg>

                                </div>

                            </div>

                        </div>


                    </div>


                    <!-- =====================================================
                         CANDIDATES TABLE SECTION
                    ===================================================== -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-8
                        "
                    >


                        <!-- =================================================
                             HEADER
                        ================================================= -->

                        <div
                            class="
                            flex
                            flex-col
                            xl:flex-row
                            xl:items-center
                            xl:justify-between
                            gap-6
                            mb-8
                            "
                        >


                            <!-- LEFT -->

                            <div>

                                <h2 class="text-3xl font-bold">

                                    Candidate Management

                                </h2>


                                <p class="text-slate-400 mt-2">

                                    Manage election candidates.

                                </p>

                            </div>


                            <!-- RIGHT -->

                            <div
                                class="
                                flex
                                flex-wrap
                                gap-3
                                "
                            >


                                <!-- ADD CANDIDATE -->

                                <button
                                    id="addCandidate"
                                    type="button"
                                    class="
                                    flex
                                    items-center
                                    gap-2
                                    px-8
                                    py-4
                                    rounded-2xl
                                    font-semibold
                                    text-white
                                    bg-gradient-to-r
                                    from-green-500
                                    to-emerald-600
                                    hover:scale-105
                                    transition-all
                                    shadow-lg
                                    shadow-green-500/30
                                    "
                                >

                                    <i
                                        class="ri-user-add-line"
                                        aria-hidden="true"
                                    ></i>

                                    Add Candidate

                                </button>


                                <!-- EXPORT EXCEL -->

                                <button
                                    id="exportCandidates"
                                    type="button"
                                    class="
                                    btn-primary
                                    flex
                                    items-center
                                    gap-2
                                    "
                                >

                                    <i
                                        class="ri-file-excel-2-line"
                                        aria-hidden="true"
                                    ></i>

                                    Export Excel

                                </button>


                            </div>

                        </div>


                        <!-- =====================================================
                             TOOLBAR
                        ===================================================== -->

                        <div
                            class="
                            flex
                            flex-col
                            2xl:flex-row
                            2xl:items-center
                            2xl:justify-between
                            gap-6
                            mb-8
                            "
                        >


                            <!-- LEFT -->

                            <div
                                class="
                                flex
                                flex-wrap
                                items-center
                                gap-4
                                "
                            >


                                <!-- ENTRIES -->

                                <!-- =================================================
                                     CUSTOM ENTRIES DROPDOWN
                                     Native select is hidden for candidates.js
                                     compatibility.
                                ================================================= -->

                                <div
                                    class="
                                    flex
                                    items-center
                                    gap-3
                                    "
                                >

                                    <label
                                        for="entriesDropdownTrigger"
                                        class="text-slate-400"
                                    >

                                        Show

                                    </label>


                                    <div
                                        id="entriesDropdown"
                                        class="
                                        relative
                                        w-36
                                        shrink-0
                                        "
                                    >

                                        <button
                                            id="entriesDropdownTrigger"
                                            type="button"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            class="
                                            w-full
                                            h-11
                                            px-4
                                            rounded-xl
                                            border
                                            border-white/10
                                            bg-white/5
                                            text-white
                                            text-left
                                            font-medium
                                            flex
                                            items-center
                                            justify-between
                                            gap-3
                                            transition
                                            duration-200
                                            hover:bg-white/10
                                            focus:outline-none
                                            focus:ring-2
                                            focus:ring-blue-500/60
                                            "
                                        >

                                            <span id="entriesDropdownValue">
                                                10 Entries
                                            </span>

                                            <i
                                                id="entriesDropdownIcon"
                                                class="
                                                ri-arrow-down-s-line
                                                text-lg
                                                text-slate-300
                                                transition-transform
                                                duration-200
                                                "
                                            ></i>

                                        </button>


                                        <div
                                            id="entriesDropdownMenu"
                                            role="listbox"
                                            aria-labelledby="entriesDropdownTrigger"
                                            class="
                                            hidden
                                            absolute
                                            left-0
                                            top-[calc(100%+8px)]
                                            z-[80]
                                            w-full
                                            p-1.5
                                            rounded-2xl
                                            border
                                            border-white/10
                                            bg-[#111827]
                                            shadow-2xl
                                            shadow-black/40
                                            backdrop-blur-xl
                                            "
                                        >

                                            <button
                                                type="button"
                                                role="option"
                                                data-value="10"
                                                aria-selected="true"
                                                class="
                                                entriesDropdownOption
                                                selected
                                                w-full
                                                px-3
                                                py-2.5
                                                rounded-xl
                                                text-left
                                                text-sm
                                                font-medium
                                                bg-gradient-to-r
                                                from-blue-600
                                                to-purple-500
                                                text-white
                                                transition
                                                duration-150
                                                "
                                            >
                                                10 Entries
                                            </button>

                                            <button
                                                type="button"
                                                role="option"
                                                data-value="25"
                                                aria-selected="false"
                                                class="
                                                entriesDropdownOption
                                                w-full
                                                px-3
                                                py-2.5
                                                rounded-xl
                                                text-left
                                                text-sm
                                                font-medium
                                                text-slate-300
                                                transition
                                                duration-150
                                                "
                                            >
                                                25 Entries
                                            </button>

                                            <button
                                                type="button"
                                                role="option"
                                                data-value="50"
                                                aria-selected="false"
                                                class="
                                                entriesDropdownOption
                                                w-full
                                                px-3
                                                py-2.5
                                                rounded-xl
                                                text-left
                                                text-sm
                                                font-medium
                                                text-slate-300
                                                transition
                                                duration-150
                                                "
                                            >
                                                50 Entries
                                            </button>

                                            <button
                                                type="button"
                                                role="option"
                                                data-value="100"
                                                aria-selected="false"
                                                class="
                                                entriesDropdownOption
                                                w-full
                                                px-3
                                                py-2.5
                                                rounded-xl
                                                text-left
                                                text-sm
                                                font-medium
                                                text-slate-300
                                                transition
                                                duration-150
                                                "
                                            >
                                                100 Entries
                                            </button>

                                        </div>

                                    </div>


                                    <!-- Hidden native select retained for candidates.js -->

                                    <select
                                        id="entriesSelect"
                                        class="hidden"
                                        aria-hidden="true"
                                        tabindex="-1"
                                    >

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

                                </div>


                                <!-- FILTERS -->

                                <div
                                    class="
                                    flex
                                    flex-wrap
                                    gap-2
                                    "
                                >

                                    <button
                                        id="filterAll"
                                        type="button"
                                        class="
                                        filterButton
                                        btn-primary
                                        "
                                    >

                                        All

                                    </button>


                                    <button
                                        id="filterFirstYear"
                                        type="button"
                                        class="
                                        filterButton
                                        btn-outline
                                        "
                                    >

                                        1st Year

                                    </button>


                                    <button
                                        id="filterSecondYear"
                                        type="button"
                                        class="
                                        filterButton
                                        btn-outline
                                        "
                                    >

                                        2nd Year

                                    </button>

                                </div>


                            </div>


                            <!-- RIGHT / SEARCH -->

                            <div
                                class="
                                flex
                                items-center
                                gap-4
                                "
                            >

                                <div
                                    class="
                                    relative
                                    w-full
                                    xl:w-80
                                    "
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="
                                        absolute
                                        left-4
                                        top-1/2
                                        -translate-y-1/2
                                        w-5
                                        h-5
                                        text-slate-400
                                        pointer-events-none
                                        "
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
                                    >

                                        <circle
                                            cx="11"
                                            cy="11"
                                            r="7"
                                        />

                                        <path
                                            d="M20 20L16.5 16.5"
                                        />

                                    </svg>


                                    <input
                                        type="text"
                                        id="candidateSearch"
                                        placeholder="Search candidates..."
                                        autocomplete="off"
                                        spellcheck="false"
                                        aria-label="Search candidates"
                                        class="
                                        w-full
                                        pl-12
                                        "
                                    >

                                </div>

                            </div>


                        </div>


                        <!-- =====================================================
                             CANDIDATES TABLE
                        ===================================================== -->

                        <div
                            class="
                            overflow-x-auto
                            rounded-2xl
                            border
                            border-white/10
                            "
                        >

                            <table class="w-full">


                                <!-- =================================================
                                     TABLE HEAD
                                ================================================= -->

                                <thead class="bg-white/5">

                                    <tr>

                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-center
                                            "
                                        >

                                            Photo

                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-left
                                            "
                                        >

                                            Candidate

                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-left
                                            "
                                        >

                                            Department

                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-left
                                            "
                                        >

                                            Year

                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-left
                                            "
                                        >

                                            Manifesto

                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-center
                                            "
                                        >

                                            Actions

                                        </th>

                                    </tr>

                                </thead>


                                <!-- =================================================
                                     TABLE BODY
                                ================================================= -->

                                <tbody id="candidatesTableBody">


                                    <?php if (count($candidates) > 0): ?>


                                        <?php foreach ($candidates as $candidate): ?>


                                            <?php

                                            /*
                                             * Dedicated searchable text.
                                             *
                                             * Search covers:
                                             *
                                             * - Candidate name
                                             * - Admission number
                                             * - Department
                                             * - Year
                                             * - Manifesto
                                             */

                                            $candidateSearchText =
                                                strtolower(
                                                    trim(
                                                        implode(
                                                            " ",
                                                            [
                                                                (string) (
                                                                    $candidate["full_name"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $candidate["admission_no"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $candidate["department"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $candidate["year"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $candidate["manifesto"]
                                                                    ?? ""
                                                                )
                                                            ]
                                                        )
                                                    )
                                                );


                                            $candidateYear =
                                                trim(
                                                    (string) (
                                                        $candidate["year"]
                                                        ?? ""
                                                    )
                                                );

                                            ?>


                                            <tr
                                                class="
                                                border-b
                                                border-white/5
                                                hover:bg-white/5
                                                transition
                                                "
                                                data-id="<?= (int) $candidate["id"]; ?>"
                                                data-year="<?= htmlspecialchars(
                                                    $candidateYear,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"
                                                data-search="<?= htmlspecialchars(
                                                    $candidateSearchText,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"
                                            >


                                                <!-- PHOTO -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    text-center
                                                    "
                                                >

                                                    <img
                                                        src="../../backend/candidate-photo.php?id=<?= (int) $candidate["id"]; ?>"
                                                        alt="<?= htmlspecialchars(
                                                            $candidate["full_name"],
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ); ?>"
                                                        class="
                                                        w-14
                                                        h-14
                                                        rounded-xl
                                                        object-cover
                                                        mx-auto
                                                        border
                                                        border-white/10
                                                        "
                                                        loading="lazy"
                                                    >

                                                </td>


                                                <!-- CANDIDATE -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <div>

                                                        <div
                                                            class="
                                                            font-semibold
                                                            "
                                                        >

                                                            <?= htmlspecialchars(
                                                                $candidate["full_name"]
                                                            ); ?>

                                                        </div>


                                                        <div
                                                            class="
                                                            text-xs
                                                            text-slate-400
                                                            mt-1
                                                            "
                                                        >

                                                            <?= htmlspecialchars(
                                                                $candidate["admission_no"]
                                                            ); ?>

                                                        </div>

                                                    </div>

                                                </td>


                                                <!-- DEPARTMENT -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <?= htmlspecialchars(
                                                        $candidate["department"]
                                                    ); ?>

                                                </td>


                                                <!-- YEAR -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <?= htmlspecialchars(
                                                        $candidate["year"]
                                                    ); ?>

                                                </td>


                                                <!-- MANIFESTO -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <p
                                                        class="
                                                        max-w-xs
                                                        truncate
                                                        "
                                                    >

                                                        <?= htmlspecialchars(
                                                            $candidate["manifesto"]
                                                        ); ?>

                                                    </p>

                                                </td>


                                                <!-- ACTIONS -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <div
                                                        class="
                                                        flex
                                                        justify-center
                                                        gap-3
                                                        "
                                                    >


                                                        <!-- VIEW -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            viewCandidate
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-cyan-500/20
                                                            text-cyan-400
                                                            hover:bg-cyan-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $candidate["id"]; ?>"
                                                            title="View"
                                                            aria-label="View candidate"
                                                        >

                                                            <i
                                                                class="ri-eye-line"
                                                                aria-hidden="true"
                                                            ></i>

                                                        </button>


                                                        <!-- EDIT -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            editCandidate
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-blue-500/20
                                                            text-blue-400
                                                            hover:bg-blue-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $candidate["id"]; ?>"
                                                            data-manifesto="<?= htmlspecialchars(
                                                                $candidate["manifesto"],
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ); ?>"
                                                            data-admission="<?= htmlspecialchars(
                                                                $candidate["admission_no"],
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ); ?>"
                                                            data-name="<?= htmlspecialchars(
                                                                $candidate["full_name"],
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ); ?>"
                                                            data-department="<?= htmlspecialchars(
                                                                $candidate["department"],
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ); ?>"
                                                            data-year="<?= htmlspecialchars(
                                                                $candidate["year"],
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ); ?>"
                                                            data-student="<?= (int) $candidate["student_id"]; ?>"
                                                            title="Edit"
                                                            aria-label="Edit candidate"
                                                        >

                                                            <i
                                                                class="ri-edit-2-line"
                                                                aria-hidden="true"
                                                            ></i>

                                                        </button>


                                                        <!-- DELETE -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            deleteCandidate
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-red-500/20
                                                            text-red-400
                                                            hover:bg-red-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $candidate["id"]; ?>"
                                                            title="Delete"
                                                            aria-label="Delete candidate"
                                                        >

                                                            <i
                                                                class="ri-delete-bin-6-line"
                                                                aria-hidden="true"
                                                            ></i>

                                                        </button>


                                                    </div>

                                                </td>


                                            </tr>


                                        <?php endforeach; ?>


                                    <?php endif; ?>


                                    <!-- =================================================
                                         CONTROLLED EMPTY STATE
                                    ================================================= -->

                                    <tr
                                        id="candidatesEmptyState"
                                        class="<?= count($candidates) > 0 ? 'hidden' : ''; ?>"
                                    >

                                        <td
                                            colspan="6"
                                            class="px-6 py-16"
                                        >

                                            <div
                                                class="
                                                flex
                                                flex-col
                                                items-center
                                                justify-center
                                                text-center
                                                "
                                            >


                                                <!-- ICON -->

                                                <div
                                                    class="
                                                    w-20
                                                    h-20
                                                    rounded-3xl
                                                    bg-blue-500/10
                                                    border
                                                    border-blue-500/10
                                                    flex
                                                    items-center
                                                    justify-center
                                                    mb-6
                                                    "
                                                >

                                                    <i
                                                        id="candidateEmptyIcon"
                                                        class="
                                                        ri-user-search-line
                                                        text-5xl
                                                        text-slate-400
                                                        "
                                                        aria-hidden="true"
                                                    ></i>

                                                </div>


                                                <!-- LABEL -->

                                                <span
                                                    id="candidateEmptyLabel"
                                                    class="
                                                    inline-flex
                                                    items-center
                                                    gap-2
                                                    px-3
                                                    py-1.5
                                                    rounded-full
                                                    bg-white/5
                                                    border
                                                    border-white/10
                                                    text-xs
                                                    font-semibold
                                                    uppercase
                                                    tracking-wider
                                                    text-slate-400
                                                    mb-4
                                                    "
                                                >

                                                    <i
                                                        class="ri-team-line"
                                                        aria-hidden="true"
                                                    ></i>

                                                    Candidate Directory

                                                </span>


                                                <!-- TITLE -->

                                                <h3
                                                    id="candidateEmptyTitle"
                                                    class="
                                                    text-2xl
                                                    sm:text-3xl
                                                    font-bold
                                                    text-white
                                                    "
                                                >

                                                    No Candidates Found

                                                </h3>


                                                <!-- MESSAGE -->

                                                <p
                                                    id="candidateEmptyMessage"
                                                    class="
                                                    mt-3
                                                    max-w-md
                                                    text-sm
                                                    sm:text-base
                                                    leading-7
                                                    text-slate-400
                                                    "
                                                >

                                                    No candidates have been added yet.

                                                </p>


                                                <!-- CLEAR SEARCH -->

                                                <button
                                                    type="button"
                                                    id="clearCandidateSearch"
                                                    class="
                                                    hidden
                                                    mt-6
                                                    inline-flex
                                                    items-center
                                                    gap-2
                                                    px-5
                                                    py-3
                                                    rounded-xl
                                                    bg-blue-500/10
                                                    border
                                                    border-blue-500/20
                                                    text-blue-400
                                                    hover:bg-blue-500/20
                                                    hover:border-blue-500/30
                                                    transition-all
                                                    duration-200
                                                    "
                                                >

                                                    <i
                                                        class="ri-close-circle-line"
                                                        aria-hidden="true"
                                                    ></i>

                                                    <span>
                                                        Clear Search
                                                    </span>

                                                </button>


                                            </div>

                                        </td>

                                    </tr>


                                </tbody>

                            </table>

                        </div>


                    </div>


                </div>


            </section>


        </div>

    </main>


    <!-- =====================================================
         MODALS
    ===================================================== -->

    <?php

    include "../../components/candidate_modal.php";

    ?>


    <?php

    include "../../components/candidate_view_modal.php";

    ?>


    <?php

    include "../../components/student_modal.php";

    ?>


    <?php

    include "../../components/confirmation_modal.php";

    ?>


    <?php

    include "../../components/toast.php";

    ?>


    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <div id="footer"></div>


    <!-- =====================================================
         JAVASCRIPT
    ===================================================== -->

    <script src="../../assets/js/app.js"></script>

    <script src="../../assets/js/dashboard.js"></script>

    <script src="../../assets/js/confirmation_modal.js"></script>

    <script src="../../assets/js/toast.js"></script>


    <!--
        Cache version bumped so browser loads the latest
        candidates.js instead of an older cached copy.
    -->

    <script
        src="../../assets/js/candidates.js?v=20260925-01"
    ></script>


    <!-- =====================================================
         CUSTOM TAILWIND ENTRIES DROPDOWN

         No normal CSS is used.
         The hidden #entriesSelect remains available to
         candidates.js so existing pagination logic continues
         to work without modification.
    ===================================================== -->

    <script>

    document.addEventListener(
        "DOMContentLoaded",
        function () {

            const dropdown =
                document.getElementById(
                    "entriesDropdown"
                );

            const trigger =
                document.getElementById(
                    "entriesDropdownTrigger"
                );

            const menu =
                document.getElementById(
                    "entriesDropdownMenu"
                );

            const valueDisplay =
                document.getElementById(
                    "entriesDropdownValue"
                );

            const icon =
                document.getElementById(
                    "entriesDropdownIcon"
                );

            const nativeSelect =
                document.getElementById(
                    "entriesSelect"
                );

            const options =
                Array.from(
                    document.querySelectorAll(
                        ".entriesDropdownOption"
                    )
                );

            if (
                !dropdown ||
                !trigger ||
                !menu ||
                !valueDisplay ||
                !icon ||
                !nativeSelect ||
                options.length === 0
            ) {
                return;
            }


            /* ==================================================
               STATE
            ================================================== */

            let isOpen = false;


            /* ==================================================
               OPEN
            ================================================== */

            function openDropdown() {

                if (isOpen) {
                    return;
                }

                isOpen = true;

                menu.classList.remove(
                    "hidden"
                );

                trigger.setAttribute(
                    "aria-expanded",
                    "true"
                );

                icon.classList.remove(
                    "ri-arrow-down-s-line"
                );

                icon.classList.add(
                    "ri-arrow-up-s-line"
                );

            }


            /* ==================================================
               CLOSE
            ================================================== */

            function closeDropdown() {

                if (!isOpen) {
                    return;
                }

                isOpen = false;

                menu.classList.add(
                    "hidden"
                );

                trigger.setAttribute(
                    "aria-expanded",
                    "false"
                );

                icon.classList.remove(
                    "ri-arrow-up-s-line"
                );

                icon.classList.add(
                    "ri-arrow-down-s-line"
                );

            }


            /* ==================================================
               UPDATE SELECTED UI

               IMPORTANT:
               Selected option keeps ONLY the gradient
               selected state. It does not receive hover
               styling.
            ================================================== */

            function updateDropdownUI(
                selectedValue
            ) {

                const selectedOption =
                    options.find(
                        option =>
                            option.dataset.value ===
                            String(selectedValue)
                    );

                if (!selectedOption) {
                    return;
                }


                valueDisplay.textContent =
                    selectedOption.textContent.trim();


                options.forEach(
                    option => {

                        const isSelected =
                            option ===
                            selectedOption;

                        option.setAttribute(
                            "aria-selected",
                            isSelected
                                ? "true"
                                : "false"
                        );

                        option.classList.remove(
                            "bg-gradient-to-r",
                            "from-blue-600",
                            "to-purple-500",
                            "bg-blue-500/15",
                            "text-white",
                            "selected"
                        );

                        if (isSelected) {

                            option.classList.remove(
                                "text-slate-300"
                            );

                            option.classList.add(
                                "bg-gradient-to-r",
                                "from-blue-600",
                                "to-purple-500",
                                "text-white",
                                "selected"
                            );

                        } else {

                            option.classList.add(
                                "text-slate-300"
                            );

                        }

                    }
                );

            }


            /* ==================================================
               NON-SELECTED OPTION HOVER

               Selected option intentionally ignores hover.
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
                                "bg-blue-500/15",
                                "text-white"
                            );

                        }
                    );


                    option.addEventListener(
                        "pointerleave",
                        function () {

                            if (
                                option.classList.contains(
                                    "selected"
                                )
                            ) {
                                return;
                            }

                            option.classList.remove(
                                "bg-blue-500/15",
                                "text-white"
                            );

                            option.classList.add(
                                "text-slate-300"
                            );

                        }
                    );


                    option.addEventListener(
                        "click",
                        function () {

                            const selectedValue =
                                option.dataset.value;

                            if (!selectedValue) {
                                return;
                            }


                            /*
                             * Keep candidates.js compatible.
                             */
                            nativeSelect.value =
                                selectedValue;


                            /*
                             * Existing candidates.js listens
                             * for the native select change.
                             */
                            nativeSelect.dispatchEvent(
                                new Event(
                                    "change",
                                    {
                                        bubbles: true
                                    }
                                )
                            );


                            updateDropdownUI(
                                selectedValue
                            );

                            closeDropdown();

                        }
                    );

                }
            );


            /* ==================================================
               TRIGGER
            ================================================== */

            trigger.addEventListener(
                "click",
                function (event) {

                    event.stopPropagation();

                    if (isOpen) {

                        closeDropdown();

                    } else {

                        openDropdown();

                    }

                }
            );


            /* ==================================================
               OUTSIDE CLICK
            ================================================== */

            document.addEventListener(
                "click",
                function (event) {

                    if (
                        !dropdown.contains(
                            event.target
                        )
                    ) {

                        closeDropdown();

                    }

                }
            );


            /* ==================================================
               ESCAPE
            ================================================== */

            document.addEventListener(
                "keydown",
                function (event) {

                    if (
                        event.key === "Escape" &&
                        isOpen
                    ) {

                        closeDropdown();

                        trigger.focus();

                    }

                }
            );


            /* ==================================================
               INITIAL STATE
            ================================================== */

            updateDropdownUI(
                nativeSelect.value || "10"
            );

        }

    );

    </script>


</body>

</html>