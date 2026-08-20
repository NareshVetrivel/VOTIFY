<?php
/* ==========================================================
   VOTIFY
   Admin Dashboard
   File : pages/admin/dashboard.php
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
   ELECTION STATUS
========================================================== */

$electionStatus = "Ready";

$statusQuery = mysqli_query(

    $conn,

    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"

);

if (

    $statusQuery &&
    mysqli_num_rows($statusQuery)

) {

    $statusRow =
        mysqli_fetch_assoc(
            $statusQuery
        );

    $electionStatus =
        $statusRow["election_status"];

}


/* ==========================================================
   ELECTION TIMING
   Source: admin_logs
========================================================== */

$electionStartTimestamp = null;

$electionStopTimestamp = null;


/* ==========================================================
   LATEST ELECTION START
========================================================== */

$startQuery = mysqli_query(

    $conn,

    "SELECT
        UNIX_TIMESTAMP(created_at) AS event_time
     FROM admin_logs
     WHERE action = 'Election Started'
     ORDER BY id DESC
     LIMIT 1"

);

if (

    $startQuery &&
    mysqli_num_rows($startQuery)

) {

    $startRow =
        mysqli_fetch_assoc(
            $startQuery
        );

    $electionStartTimestamp =
        !empty($startRow["event_time"])
            ? (int) $startRow["event_time"]
            : null;

}


/* ==========================================================
   LATEST ELECTION STOP
========================================================== */

$stopQuery = mysqli_query(

    $conn,

    "SELECT
        UNIX_TIMESTAMP(created_at) AS event_time
     FROM admin_logs
     WHERE action = 'Election Stopped'
     ORDER BY id DESC
     LIMIT 1"

);

if (

    $stopQuery &&
    mysqli_num_rows($stopQuery)

) {

    $stopRow =
        mysqli_fetch_assoc(
            $stopQuery
        );

    $electionStopTimestamp =
        !empty($stopRow["event_time"])
            ? (int) $stopRow["event_time"]
            : null;

}


/* ==========================================================
   DETERMINE CURRENT ELECTION CYCLE
========================================================== */

/*
 * A new Start belongs to the current election cycle
 * only when it happened after the latest Stop.
 *
 * This prevents an old Start timestamp from being
 * paired with a new Stop timestamp.
 */

if (

    $electionStartTimestamp !== null &&

    (

        $electionStopTimestamp === null ||

        $electionStartTimestamp >
        $electionStopTimestamp

    )

) {

    $electionStatus =
        "Started";

}


/*
 * If the latest Stop belongs to the latest Start,
 * the election is stopped.
 */

elseif (

    $electionStartTimestamp !== null &&

    $electionStopTimestamp !== null &&

    $electionStopTimestamp >=
    $electionStartTimestamp

) {

    $electionStatus =
        "Stopped";

}


/* ==========================================================
   RUNNING DURATION
========================================================== */

$initialDuration = 0;


/* ==========================================================
   CURRENT ELECTION IS RUNNING
========================================================== */

if (

    $electionStatus === "Started" &&

    $electionStartTimestamp !== null

) {

    $initialDuration =
        max(

            0,

            time() -
            $electionStartTimestamp

        );

}


/* ==========================================================
   CURRENT ELECTION IS STOPPED
========================================================== */

elseif (

    $electionStatus === "Stopped" &&

    $electionStartTimestamp !== null &&

    $electionStopTimestamp !== null &&

    $electionStopTimestamp >=
    $electionStartTimestamp

) {

    $initialDuration =
        max(

            0,

            $electionStopTimestamp -
            $electionStartTimestamp

        );

}


/* ==========================================================
   DASHBOARD STATISTICS
========================================================== */

/*
 * Kept for compatibility with the existing
 * VOTIFY dashboard architecture.
 */

$totalStudents = 0;

$query =

    "SELECT COUNT(*) AS total
     FROM students";


$result =
    mysqli_query(
        $conn,
        $query
    );


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );

    $totalStudents =
        (int) $row["total"];

}


$pendingStudents = 0;

$query =

    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Pending'";


$result =
    mysqli_query(
        $conn,
        $query
    );


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );

    $pendingStudents =
        (int) $row["total"];

}


$approvedStudents = 0;

$query =

    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Approved'";


$result =
    mysqli_query(
        $conn,
        $query
    );


if ($result) {

    $row =
        mysqli_fetch_assoc(
            $result
        );

    $approvedStudents =
        (int) $row["total"];

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

Admin Dashboard | VOTIFY

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
rel="stylesheet">


<!-- =====================================================
CUSTOM CSS
===================================================== -->

<link
rel="stylesheet"
href="../../assets/css/custom.css">

<link
rel="stylesheet"
href="../../assets/css/animations.css">


<!-- =====================================================
DASHBOARD CARD HOVER
===================================================== -->

<style>

.dashboard-card {

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease;

}

.dashboard-card:hover {

    transform:
        translateY(-6px);

    box-shadow:
        0 0 30px
        rgba(59, 130, 246, 0.28),

        0 20px 45px
        rgba(0, 0, 0, 0.25);

}

</style>

</head>


<body
class="bg-[#0B1020]
text-white
min-h-screen
overflow-x-hidden
flex
flex-col">


<!-- =====================================================
LOADER
===================================================== -->

<div id="loader-container"></div>


<!-- =====================================================
BACKGROUND
===================================================== -->

<div
class="fixed
inset-0
-z-10
overflow-hidden">

    <div
    class="absolute
    top-0
    left-0
    w-96
    h-96
    bg-blue-600/20
    blur-[150px]
    rounded-full">
    </div>


    <div
    class="absolute
    bottom-0
    right-0
    w-96
    h-96
    bg-pink-600/20
    blur-[150px]
    rounded-full">
    </div>


    <div
    class="absolute
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


<!-- =====================================================
HEADER
===================================================== -->

<div id="header"></div>


<!-- =====================================================
MOBILE OVERLAY
===================================================== -->

<div
id="sidebarOverlay"
class="fixed
inset-0
bg-black/60
hidden
z-40
lg:hidden">
</div>


<!-- =====================================================
MAIN LAYOUT
===================================================== -->

<main
class="flex-1
max-w-7xl
w-full
mx-auto
px-4
sm:px-6
lg:px-8
py-8">

<div
class="grid
grid-cols-1
lg:grid-cols-[280px_1fr]
gap-8
items-start">


<!-- =====================================================
SIDEBAR
===================================================== -->

<?php

include "../../components/admin_sidebar.php";

?>


<!-- =====================================================
CONTENT
===================================================== -->

<section
class="min-w-0">


<?php

$pageTitle =
    "Admin Dashboard";

include "../../components/admin_topbar.php";

?>


<!-- =====================================================
DASHBOARD CONTENT
===================================================== -->

<div
id="dashboardContent"
class="space-y-8">


<!-- =====================================================
ELECTION INFORMATION CARDS
===================================================== -->

<div
class="grid
grid-cols-1
md:grid-cols-2
xl:grid-cols-3
gap-6">


<!-- =====================================================
CARD 1 — ELECTION TIMING
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-6">

    <div
    class="flex
    items-center
    justify-between">

        <div>

            <p
            class="text-slate-400">

                Election Timing

            </p>


            <div
            class="mt-4
            space-y-2">

                <!-- START -->

                <div
                class="flex
                items-center
                gap-2
                text-sm">

                    <i
                    class="ri-play-circle-line
                    text-blue-400">
                    </i>

                    <span
                    class="text-slate-400">

                        Start

                    </span>

                    <span
                    id="electionStartTime"
                    class="font-semibold text-white">

                        <?php

                        echo

                        $electionStartTimestamp !== null

                            ? date(
                                "h:i A",
                                $electionStartTimestamp
                            )

                            : "--:--";

                        ?>

                    </span>

                </div>


                <!-- STOP -->

                <div
                class="flex
                items-center
                gap-2
                text-sm">

                    <i
                    class="ri-stop-circle-line
                    text-red-400">
                    </i>

                    <span
                    class="text-slate-400">

                        Stop

                    </span>

                    <span
                    id="electionStopTime"
                    class="font-semibold text-white">

                        <?php

                        if (

                            $electionStatus ===
                            "Started"

                        ) {

                            echo "Running...";

                        }

                        elseif (

                            $electionStopTimestamp !==
                            null

                        ) {

                            echo date(

                                "h:i A",

                                $electionStopTimestamp

                            );

                        }

                        else {

                            echo "--:--";

                        }

                        ?>

                    </span>

                </div>

            </div>

        </div>


        <div
        class="w-16
        h-16
        rounded-2xl
        bg-blue-500/20
        flex
        items-center
        justify-center
        shrink-0">

            <i
            class="ri-calendar-schedule-line
            text-3xl
            text-blue-400">
            </i>

        </div>

    </div>

</div>


<!-- =====================================================
CARD 2 — RUNNING DURATION
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-6">

    <div
    class="flex
    items-center
    justify-between">

        <div>

            <p
            class="text-slate-400">

                Running Duration

            </p>


            <h2
            id="electionRunningDuration"
            class="text-4xl
            font-bold
            mt-4
            tracking-wide">

                <?php

                $hours =
                    floor(
                        $initialDuration /
                        3600
                    );

                $minutes =
                    floor(
                        (
                            $initialDuration %
                            3600
                        ) / 60
                    );

                $seconds =
                    $initialDuration %
                    60;


                echo sprintf(

                    "%02d:%02d:%02d",

                    $hours,

                    $minutes,

                    $seconds

                );

                ?>

            </h2>


            <p
            id="durationStatus"
            class="text-sm
            mt-2
            <?php

            echo

                $electionStatus ===
                "Started"

                ? "text-green-400"

                : "text-slate-500";

            ?>">

                <?php

                if (

                    $electionStatus ===
                    "Started"

                ) {

                    echo
                        "Election is running";

                }

                elseif (

                    $initialDuration >
                    0

                ) {

                    echo
                        "Election completed";

                }

                else {

                    echo
                        "Election not started";

                }

                ?>

            </p>

        </div>


        <div
        class="w-16
        h-16
        rounded-2xl
        bg-yellow-500/20
        flex
        items-center
        justify-center
        shrink-0">

            <i
            class="ri-timer-line
            text-3xl
            text-yellow-400">
            </i>

        </div>

    </div>

</div>


<!-- =====================================================
CARD 3 — ELECTION STATUS
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-6">

    <div
    class="flex
    items-center
    justify-between">

        <div>

            <p
            class="text-slate-400">

                Election Status

            </p>


            <h2
            id="electionStatusText"
            class="text-3xl
            font-bold
            mt-4">

                <?php

                if (

                    $electionStatus ===
                    "Started"

                ) {

                    echo "Running";

                }

                elseif (

                    $electionStatus ===
                    "Stopped"

                ) {

                    echo "Stopped";

                }

                else {

                    echo "Ready";

                }

                ?>

            </h2>


            <p
            id="electionStatusDescription"
            class="text-sm
            text-slate-500
            mt-2">

                <?php

                if (

                    $electionStatus ===
                    "Started"

                ) {

                    echo
                        "Election is currently active";

                }

                elseif (

                    $electionStatus ===
                    "Stopped"

                ) {

                    echo
                        "Election is not active";

                }

                else {

                    echo
                        "Election is ready to start";

                }

                ?>

            </p>

        </div>


        <div
        id="electionStatusIcon"
        class="w-16
        h-16
        rounded-2xl
        flex
        items-center
        justify-center
        shrink-0
        <?php

        if (
            $electionStatus ===
            "Started"
        ) {

            echo "bg-green-500/20";

        }

        elseif (
            $electionStatus ===
            "Stopped"
        ) {

            echo "bg-red-500/20";

        }

        else {

            echo "bg-yellow-500/20";

        }

        ?>">

            <?php

            if (
                $electionStatus ===
                "Started"
            ) {

                ?>

                <i
                class="ri-checkbox-circle-line
                text-3xl
                text-green-400">
                </i>

                <?php

            }

            elseif (
                $electionStatus ===
                "Stopped"
            ) {

                ?>

                <i
                class="ri-close-circle-line
                text-3xl
                text-red-400">
                </i>

                <?php

            }

            else {

                ?>

                <i
                class="ri-time-line
                text-3xl
                text-yellow-400">
                </i>

                <?php

            }

            ?>

        </div>

    </div>

</div>

</div>


<!-- =====================================================
HIDDEN COMPATIBILITY STATUS
===================================================== -->

<span
id="electionStatus"
class="hidden"
aria-hidden="true">

<?php

echo $electionStatus;

?>

</span>


<!-- =====================================================
ELECTION CONTROLS
===================================================== -->

<div
class="grid
grid-cols-1
xl:grid-cols-2
gap-6">


<!-- =====================================================
START ELECTION
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-8
flex
flex-col">

    <div
    class="flex
    items-start
    gap-4
    min-h-[96px]">

        <div
        class="w-16
        h-16
        shrink-0
        rounded-2xl
        bg-blue-500/20
        flex
        items-center
        justify-center">

            <i
            class="ri-play-circle-line
            text-4xl
            text-blue-400">
            </i>

        </div>


        <div>

            <h2
            class="text-2xl
            font-bold">

                Start Election

            </h2>


            <p
            class="text-slate-400
            mt-1">

                Begin secure online voting.

            </p>

        </div>

    </div>


    <button
    id="startElection"

    <?php

    if (
        $electionStatus ===
        "Started"
    ) {

        echo "disabled";

    }

    ?>

    class="
    btn-primary
    w-full
    h-20
    mt-8
    rounded-2xl
    text-lg
    font-semibold
    flex
    items-center
    justify-center
    gap-3
    transition-all
    duration-300">

        <i
        class="ri-play-fill">
        </i>

        Start Election

    </button>

</div>


<!-- =====================================================
STOP ELECTION
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-8
flex
flex-col">

    <div
    class="flex
    items-start
    gap-4
    min-h-[96px]">

        <div
        class="w-16
        h-16
        shrink-0
        rounded-2xl
        bg-red-500/20
        flex
        items-center
        justify-center">

            <i
            class="ri-stop-circle-line
            text-4xl
            text-red-400">
            </i>

        </div>


        <div>

            <h2
            class="text-2xl
            font-bold">

                Stop Election

            </h2>


            <p
            class="text-slate-400
            mt-1">

                End voting securely.

            </p>

        </div>

    </div>


    <button
    id="stopElection"

    <?php

    if (

        $electionStatus ===
        "Stopped"

        ||

        $electionStatus ===
        "Ready"

    ) {

        echo "disabled";

    }

    ?>

    class="
    w-full
    h-20
    mt-8
    rounded-2xl
    text-lg
    font-semibold
    text-white
    bg-gradient-to-r
    from-red-500
    via-red-600
    to-pink-600
    hover:scale-[1.02]
    transition-all
    duration-300
    flex
    items-center
    justify-center
    gap-3">

        <i
        class="ri-stop-fill">
        </i>

        Stop Election

    </button>

</div>

</div>


<!-- =====================================================
ADMIN RULES
===================================================== -->

<div
class="dashboard-card
glass
rounded-3xl
p-8">


    <div
    class="flex
    items-center
    gap-4
    mb-8">


        <div
        class="w-16
        h-16
        rounded-2xl
        bg-blue-500/20
        flex
        items-center
        justify-center">

            <i
            class="ri-shield-check-line
            text-4xl
            text-blue-400">
            </i>

        </div>


        <div>

            <h2
            class="text-2xl
            font-bold">

                Administrator Rules

            </h2>


            <p
            class="text-slate-400
            mt-1">

                Follow these rules to maintain a secure and fair election process.

            </p>

        </div>

    </div>


    <div
    class="grid
    md:grid-cols-2
    gap-5">


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Approve only genuine MCA student registrations.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Verify every voter request before approval.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Never share administrator credentials.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Start elections only after candidate verification.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Stop elections only after all votes are completed.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Monitor canvassing reports regularly.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Keep election records transparent and secure.
            </p>

        </div>


        <div class="flex gap-3">

            <i
            class="ri-checkbox-circle-fill
            text-green-400
            mt-1">
            </i>

            <p>
                Review admin logs after every election.
            </p>

        </div>

    </div>

</div>

</div>

</section>

</div>

</main>


<!-- =====================================================
TOAST
===================================================== -->

<div
id="dashboardToast"
class="
fixed
top-6
right-6
translate-x-[120%]
transition-all
duration-500
z-[9999]
rounded-2xl
shadow-2xl
px-6
py-4
text-white
min-w-[320px]">

    <div
    class="flex
    items-center
    gap-4">

        <i
        id="toastIcon"
        class="ri-checkbox-circle-fill
        text-3xl">
        </i>


        <div>

            <h3
            id="toastTitle"
            class="font-bold
            text-lg">

                Success

            </h3>


            <p
            id="toastMessage"
            class="text-sm
            text-white/90">

                Operation Completed

            </p>

        </div>

    </div>

</div>


<!-- =====================================================
FOOTER
===================================================== -->

<div id="footer"></div>


<!-- =====================================================
ELECTION TIMER DATA
===================================================== -->

<script>

window.VOTIFY_ELECTION_DATA = {

    status:
        <?php

        echo json_encode(
            $electionStatus
        );

        ?>,

    startTimestamp:
        <?php

        echo
            $electionStartTimestamp !== null
                ? (int)
                    $electionStartTimestamp
                : "null";

        ?>,

    stopTimestamp:
        <?php

        echo
            $electionStopTimestamp !== null
                ? (int)
                    $electionStopTimestamp
                : "null";

        ?>,

    initialDuration:
        <?php

        echo (int)
            $initialDuration;

        ?>

};

</script>


<!-- =====================================================
JAVASCRIPT
===================================================== -->

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/dashboard.js"></script>


</body>

</html>