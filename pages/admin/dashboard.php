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

/* ==========================================================
   ELECTION STATUS
========================================================== */

$electionStatus = "Ready";

$statusQuery = mysqli_query(

    $conn,

    "SELECT election_status
     FROM election_settings
     LIMIT 1"

);

if (

    $statusQuery &&
    mysqli_num_rows($statusQuery)

) {

    $statusRow = mysqli_fetch_assoc($statusQuery);

    $electionStatus = $statusRow["election_status"];

}

/* ==========================================================
   DASHBOARD STATISTICS
========================================================== */

// Total Registered Students

$totalStudents = 0;

$query = "SELECT COUNT(*) AS total FROM students";

$result = mysqli_query($conn, $query);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $totalStudents = $row["total"];

}

// Pending Approvals

$pendingStudents = 0;

$query = "SELECT COUNT(*) AS total
          FROM students
          WHERE status='Pending'";

$result = mysqli_query($conn, $query);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $pendingStudents = $row["total"];

}

// Approved Voters

$approvedStudents = 0;

$query = "SELECT COUNT(*) AS total
          FROM students
          WHERE status='Approved'";

$result = mysqli_query($conn, $query);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $approvedStudents = $row["total"];

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

<!-- Tailwind -->

<script src="https://cdn.tailwindcss.com"></script>

<!-- Remix Icons -->

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
rel="stylesheet">

<!-- CSS -->

<link
rel="stylesheet"
href="../../assets/css/custom.css">

<link
rel="stylesheet"
href="../../assets/css/animations.css">

</head>

<body
class="bg-[#0B1020] text-white min-h-screen overflow-x-hidden flex flex-col">

<!-- =====================================================
LOADER
===================================================== -->

<div id="loader-container"></div>

<!-- =====================================================
BACKGROUND
===================================================== -->

<div
class="fixed inset-0 -z-10 overflow-hidden">

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

$pageTitle = "Admin Dashboard";

include "../../components/admin_topbar.php";

?>

<!-- ==========================================
Dashboard Content Starts Here
========================================== -->

<!-- ==========================================
DASHBOARD CONTENT
========================================== -->

<div
id="dashboardContent"

class="space-y-8">

<!-- ======================================
STATISTICS
====================================== -->

<div
class="grid
grid-cols-1
md:grid-cols-2
xl:grid-cols-3
gap-6">

    <!-- Total Registered -->

    <div
    class="glass rounded-3xl p-6 hover:-translate-y-2 transition-all duration-300">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-slate-400">

                    Total Registered Students

                </p>

                <h2

                id="totalStudents"

                class="text-5xl font-bold mt-4">

                    <?php echo $totalStudents; ?>

                </h2>

            </div>

            <div
            class="w-16 h-16 rounded-2xl bg-blue-500/20 flex items-center justify-center">

                <i
                class="ri-team-line text-3xl text-blue-400">

                </i>

            </div>

        </div>

    </div>

    <!-- Pending -->

    <div
    class="glass rounded-3xl p-6 hover:-translate-y-2 transition-all duration-300">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-slate-400">

                    Pending Approvals

                </p>

                <h2

                id="pendingStudents"

                class="text-5xl font-bold mt-4">

                    <?php echo $pendingStudents; ?>

                </h2>

            </div>

            <div
            class="w-16 h-16 rounded-2xl bg-yellow-500/20 flex items-center justify-center">

                <i
                class="ri-time-line text-3xl text-yellow-400">

                </i>

            </div>

        </div>

    </div>

    <!-- Approved -->

    <div
    class="glass rounded-3xl p-6 hover:-translate-y-2 transition-all duration-300">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-slate-400">

                    Approved Voters

                </p>

                <h2

                id="approvedStudents"

                class="text-5xl font-bold mt-4">

                    <?php echo $approvedStudents; ?>

                </h2>

            </div>

            <div
            class="w-16 h-16 rounded-2xl bg-green-500/20 flex items-center justify-center">

                <i
                class="ri-checkbox-circle-line text-3xl text-green-400">

                </i>

            </div>

        </div>

    </div>

</div>

<!-- ==========================================
ELECTION CONTROLS
========================================== -->

<div
class="grid
grid-cols-1
xl:grid-cols-2
gap-6">

    <!-- ======================================
    START ELECTION
    ======================================= -->

    <div
    class="glass
    rounded-3xl
    p-8
    hover:-translate-y-2
    transition-all
    duration-300">

        <div class="flex items-center gap-4">

            <div
            class="w-16
            h-16
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

    <h2 class="text-2xl font-bold">

        Start Election

    </h2>

    <p class="text-slate-400 mt-1">

        Begin secure online voting.

    </p>

    <span

    id="electionStatus"

    class="

    inline-flex

    mt-3

    px-4

    py-2

    rounded-full

    bg-green-500/10

    text-green-400

    border

    border-green-500/20

    text-sm

    font-semibold">

<?php

if ($electionStatus == "Started") {

    echo "🟢 Election Running";

}

elseif ($electionStatus == "Stopped") {

    echo "🔴 Election Closed";

}

else {

    echo "🟡 Ready";

}

?>

    </span>

</div>

        </div>

<button

id="startElection"

<?php

if($electionStatus=="Started"){

echo "disabled";

}

?>

class="

        btn-primary

        w-full

        mt-8

        py-4

        rounded-2xl

        text-lg">

            <i class="ri-play-fill"></i>

            Start Election

        </button>

    </div>

    <!-- ======================================
    STOP ELECTION
    ======================================= -->

    <div
    class="glass
    rounded-3xl
    p-8
    hover:-translate-y-2
    transition-all
    duration-300">

        <div class="flex items-center gap-4">

            <div
            class="w-16
            h-16
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
                class="text-2xl font-bold">

                    Stop Election

                </h2>

                <p
                class="text-slate-400 mt-1">

                    End voting securely.

                </p>

            </div>

        </div>

<button

id="stopElection"

<?php

if($electionStatus=="Stopped"){

echo "disabled";

}

?>

class="

        w-full

        mt-8

        py-4

        rounded-2xl

        text-lg

        font-semibold

        text-white

        bg-gradient-to-r

        from-red-500

        via-red-600

        to-pink-600

        hover:scale-[1.02]

        transition-all">

            <i class="ri-stop-fill"></i>

            Stop Election

        </button>

    </div>

</div>

<!-- ==========================================
ADMIN RULES
========================================== -->

<div
class="glass
rounded-3xl
p-8
hover:-translate-y-2
transition-all
duration-300">

    <!-- Heading -->

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

    <!-- Rules -->

    <div
    class="grid
    md:grid-cols-2
    gap-5">

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Approve only genuine MCA student registrations.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Verify every voter request before approval.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Never share administrator credentials.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Start elections only after candidate verification.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Stop elections only after all votes are completed.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Monitor canvassing reports regularly.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Keep election records transparent and secure.</p>

        </div>

        <div class="flex gap-3">

            <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

            <p>Review admin logs after every election.</p>

        </div>

    </div>

</div>

</div>

</section>

</div>

</main>

<!-- =====================================================
TOAST NOTIFICATION
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

    <div class="flex items-center gap-4">

        <i

        id="toastIcon"

        class="ri-checkbox-circle-fill text-3xl">

        </i>

        <div>

            <h3

            id="toastTitle"

            class="font-bold text-lg">

                Success

            </h3>

            <p

            id="toastMessage"

            class="text-sm text-white/90">

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
JAVASCRIPT
===================================================== -->

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/dashboard.js"></script>

</body>

</html>