<?php
/* ==========================================================
   VOTIFY
   Voters Requests
   File : pages/admin/requests.php
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
   FETCH CURRENT ELECTION STATUS
========================================================== */

$currentElectionStatus = "Ready";


$statusStatement = mysqli_prepare(
    $conn,
    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"
);


if ($statusStatement) {

    if (
        mysqli_stmt_execute(
            $statusStatement
        )
    ) {

        $statusResult =
            mysqli_stmt_get_result(
                $statusStatement
            );


        if (
            $statusResult &&
            mysqli_num_rows($statusResult) > 0
        ) {

            $statusRow =
                mysqli_fetch_assoc(
                    $statusResult
                );


            $databaseStatus =
                trim(
                    (string)
                    ($statusRow["election_status"] ?? "")
                );


            if (
                in_array(
                    $databaseStatus,
                    [
                        "Ready",
                        "Started",
                        "Stopped"
                    ],
                    true
                )
            ) {

                $currentElectionStatus =
                    $databaseStatus;

            }

        }

    }


    mysqli_stmt_close(
        $statusStatement
    );

}


/* ==========================================================
   FETCH PENDING REQUESTS
========================================================== */

$requests = [];


$query = "
    SELECT *
    FROM students
    WHERE status = 'Pending'
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

        $requests[] =
            $row;

    }

}


/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

$totalRequests = 0;

$pendingRequests = 0;

$approvedRequests = 0;

$rejectedRequests = 0;


/* ==========================================================
   TOTAL
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students"
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $totalRequests =
        (int) (
            $row["total"] ?? 0
        );

}


/* ==========================================================
   PENDING
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Pending'"
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $pendingRequests =
        (int) (
            $row["total"] ?? 0
        );

}


/* ==========================================================
   APPROVED
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Approved'"
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $approvedRequests =
        (int) (
            $row["total"] ?? 0
        );

}


/* ==========================================================
   REJECTED
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Rejected'"
);


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );


    $rejectedRequests =
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
        Voters Requests | VOTIFY
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
                    "Voters Requests";

                include "../../components/admin_topbar.php";

                ?>


                <div
                    id="requestsContent"
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
                        xl:grid-cols-4
                        gap-6
                        "
                    >


                        <!-- TOTAL -->

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
                                        Total Requests
                                    </p>


                                    <h2
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $totalRequests; ?>

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

                                    <i
                                        class="
                                        ri-user-line
                                        text-3xl
                                        text-blue-400
                                        "
                                    ></i>

                                </div>

                            </div>

                        </div>


                        <!-- PENDING -->

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
                                        Pending Requests
                                    </p>


                                    <h2
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $pendingRequests; ?>

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
                                    justify-center
                                    "
                                >

                                    <i
                                        class="
                                        ri-time-line
                                        text-3xl
                                        text-yellow-400
                                        "
                                    ></i>

                                </div>

                            </div>

                        </div>


                        <!-- APPROVED -->

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
                                        Approved Requests
                                    </p>


                                    <h2
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $approvedRequests; ?>

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
                                    justify-center
                                    "
                                >

                                    <i
                                        class="
                                        ri-check-line
                                        text-3xl
                                        text-green-400
                                        "
                                    ></i>

                                </div>

                            </div>

                        </div>


                        <!-- REJECTED -->

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
                                        Rejected Requests
                                    </p>


                                    <h2
                                        class="
                                        text-5xl
                                        font-bold
                                        mt-4
                                        "
                                    >

                                        <?= $rejectedRequests; ?>

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
                                    justify-center
                                    "
                                >

                                    <i
                                        class="
                                        ri-close-line
                                        text-3xl
                                        text-red-400
                                        "
                                    ></i>

                                </div>

                            </div>

                        </div>


                    </div>


                    <!-- =====================================================
                         REQUESTS TABLE
                    ===================================================== -->

                    <div
                        class="
                        glass
                        rounded-3xl
                        p-8
                        "
                    >


                        <div
                            class="
                            flex
                            flex-col
                            lg:flex-row
                            lg:items-center
                            lg:justify-between
                            gap-6
                            mb-8
                            "
                        >


                            <div>

                                <h2 class="text-3xl font-bold">

                                    Pending Registration Requests

                                </h2>


                                <p class="text-slate-400 mt-2">

                                    Review and verify student registration requests.

                                </p>

                            </div>


                            <!-- =================================================
                                 SEARCH
                            ================================================= -->

                            <div
                                class="
                                relative
                                w-full
                                lg:w-80
                                "
                            >

                                <i
                                    class="
                                    ri-search-2-line
                                    absolute
                                    left-4
                                    top-1/2
                                    -translate-y-1/2
                                    text-lg
                                    text-slate-400
                                    pointer-events-none
                                    "
                                    aria-hidden="true"
                                ></i>


                                <input
                                    type="text"
                                    id="requestSearch"
                                    placeholder="Search students..."
                                    autocomplete="off"
                                    spellcheck="false"
                                    class="
                                    w-full
                                    h-14
                                    pl-12
                                    pr-5
                                    rounded-2xl
                                    bg-white/5
                                    border
                                    border-white/10
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-500/20
                                    outline-none
                                    transition
                                    "
                                    aria-label="Search student requests"
                                >

                            </div>


                        </div>


                        <!-- =====================================================
                             TABLE
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
                                            text-left
                                            "
                                        >
                                            Student
                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-left
                                            "
                                        >
                                            Admission No
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
                                            Registered
                                        </th>


                                        <th
                                            class="
                                            px-6
                                            py-4
                                            text-center
                                            "
                                        >
                                            Status
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

                                <tbody id="requestsTableBody">


                                    <?php if (count($requests) > 0): ?>


                                        <?php foreach ($requests as $student): ?>


                                            <?php

                                            /*
                                             * Build a dedicated searchable string.
                                             *
                                             * Search covers:
                                             *
                                             * - Full name
                                             * - College email
                                             * - Admission number
                                             * - Department
                                             * - Year
                                             */

                                            $requestSearchText =
                                                strtolower(
                                                    trim(
                                                        implode(
                                                            " ",
                                                            [
                                                                (string) (
                                                                    $student["full_name"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $student["college_email"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $student["admission_no"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $student["department"]
                                                                    ?? ""
                                                                ),

                                                                (string) (
                                                                    $student["year"]
                                                                    ?? ""
                                                                )
                                                            ]
                                                        )
                                                    )
                                                );

                                            ?>


                                            <tr
                                                data-id="<?= (int) $student["id"]; ?>"
                                                data-search="<?= htmlspecialchars(
                                                    $requestSearchText,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>"
                                                class="
                                                border-b
                                                border-white/5
                                                hover:bg-white/5
                                                transition
                                                "
                                            >


                                                <!-- STUDENT -->

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
                                                                $student["full_name"]
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
                                                                $student["college_email"]
                                                            ); ?>

                                                        </div>

                                                    </div>

                                                </td>


                                                <!-- ADMISSION -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <?= htmlspecialchars(
                                                        $student["admission_no"]
                                                    ); ?>

                                                </td>


                                                <!-- DEPARTMENT -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <?= htmlspecialchars(
                                                        $student["department"]
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
                                                        $student["year"]
                                                    ); ?>

                                                </td>


                                                <!-- REGISTERED -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    "
                                                >

                                                    <?= date(
                                                        "d M Y",
                                                        strtotime(
                                                            $student["created_at"]
                                                        )
                                                    ); ?>

                                                </td>


                                                <!-- STATUS -->

                                                <td
                                                    class="
                                                    px-6
                                                    py-5
                                                    text-center
                                                    "
                                                >

                                                    <span
                                                        class="
                                                        px-4
                                                        py-2
                                                        rounded-full
                                                        bg-yellow-500/20
                                                        text-yellow-400
                                                        "
                                                    >

                                                        Pending

                                                    </span>

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
                                                        data-actions="<?= (int) $student["id"]; ?>"
                                                    >


                                                        <!-- VIEW -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            viewRequest
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-blue-500/20
                                                            text-blue-400
                                                            hover:bg-blue-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $student["id"]; ?>"
                                                            title="View Student"
                                                            aria-label="View student"
                                                        >

                                                            <i
                                                                class="ri-eye-line"
                                                                aria-hidden="true"
                                                            ></i>

                                                        </button>


                                                        <!-- APPROVE -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            approveRequest
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-green-500/20
                                                            text-green-400
                                                            hover:bg-green-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $student["id"]; ?>"
                                                            title="Approve Student"
                                                            aria-label="Approve student"
                                                        >

                                                            <i
                                                                class="ri-check-line"
                                                                aria-hidden="true"
                                                            ></i>

                                                        </button>


                                                        <!-- REJECT -->

                                                        <button
                                                            type="button"
                                                            class="
                                                            rejectRequest
                                                            w-11
                                                            h-11
                                                            rounded-xl
                                                            bg-red-500/20
                                                            text-red-400
                                                            hover:bg-red-500/30
                                                            transition
                                                            "
                                                            data-id="<?= (int) $student["id"]; ?>"
                                                            title="Reject Student"
                                                            aria-label="Reject student"
                                                        >

                                                            <i
                                                                class="ri-close-line"
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
                                        id="requestsEmptyState"
                                        class="<?= count($requests) > 0 ? 'hidden' : ''; ?>"
                                    >

                                        <td
                                            colspan="7"
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
                                                        id="requestEmptyIcon"
                                                        class="
                                                        ri-inbox-archive-line
                                                        text-5xl
                                                        text-slate-400
                                                        "
                                                        aria-hidden="true"
                                                    ></i>

                                                </div>


                                                <!-- LABEL -->

                                                <span
                                                    id="requestEmptyLabel"
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
                                                        class="ri-inbox-line"
                                                        aria-hidden="true"
                                                    ></i>

                                                    Request Queue

                                                </span>


                                                <!-- TITLE -->

                                                <h3
                                                    id="requestEmptyTitle"
                                                    class="
                                                    text-2xl
                                                    sm:text-3xl
                                                    font-bold
                                                    text-white
                                                    "
                                                >

                                                    No Pending Registration Requests

                                                </h3>


                                                <!-- MESSAGE -->

                                                <p
                                                    id="requestEmptyMessage"
                                                    class="
                                                    mt-3
                                                    max-w-md
                                                    text-sm
                                                    sm:text-base
                                                    leading-7
                                                    text-slate-400
                                                    "
                                                >

                                                    There are currently no registration requests waiting for review.

                                                </p>


                                                <!-- CLEAR SEARCH -->

                                                <button
                                                    type="button"
                                                    id="clearRequestSearch"
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
         CONFIRMATION MODAL
    ===================================================== -->

    <?php

    include "../../components/confirmation_modal.php";

    ?>


    <!-- =====================================================
         STUDENT MODAL
    ===================================================== -->

    <?php

    include "../../components/student_modal.php";

    ?>


    <!-- =====================================================
         TOAST
    ===================================================== -->

    <?php

    include "../../components/toast.php";

    ?>


    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <div id="footer"></div>


    <!-- =====================================================
         INITIAL ELECTION STATUS
    ===================================================== -->

    <script>

        /*
         * Use a page-specific initial status name.
         *
         * This avoids conflicts with any other VOTIFY
         * JavaScript global variable.
         */

        window.VOTIFY_ELECTION_STATUS =
            <?= json_encode(
                $currentElectionStatus,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            ); ?>;


        console.log(
            "VOTIFY Initial Election Status:",
            window.VOTIFY_ELECTION_STATUS
        );

    </script>


    <!-- =====================================================
         JAVASCRIPT
    ===================================================== -->

    <script src="../../assets/js/app.js"></script>

    <script src="../../assets/js/toast.js"></script>

    <script src="../../assets/js/dashboard.js"></script>

    <script src="../../assets/js/confirmation_modal.js"></script>

    <script src="../../assets/js/student_modal.js"></script>


    <!--
        IMPORTANT:
        Cache version intentionally bumped.

        Previous:
        20260919-02

        Current:
        20260925-01

        This forces the browser to load the latest
        requests.js instead of an older cached copy.
    -->

    <script
        src="../../assets/js/requests.js?v=20260925-01"
    ></script>


</body>

</html>