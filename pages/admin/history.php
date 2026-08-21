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

/*
 * History logs contain sensitive administrator
 * activity and IP address information.
 *
 * Only Super Admin is allowed to access this page.
 */

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

    $totalLogs =
        mysqli_fetch_assoc(
            $result
        )["total"];

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

    $todayLogs =
        mysqli_fetch_assoc(
            $result
        )["total"];

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

    $adminActions =
        mysqli_fetch_assoc(
            $result
        )["total"];

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

    $securityEvents =
        mysqli_fetch_assoc(
            $result
        )["total"];

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


<!-- ==========================================
TAILWIND CSS
========================================== -->

<script src="https://cdn.tailwindcss.com"></script>


<!-- ==========================================
REMIX ICONS
========================================== -->

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
rel="stylesheet">


<!-- ==========================================
CUSTOM CSS
========================================== -->

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


<!-- ==========================================
LOADER
========================================== -->

<div id="loader-container"></div>


<!-- ==========================================
ANIMATED BACKGROUND
========================================== -->

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


<!-- ==========================================
HEADER
========================================== -->

<div id="header"></div>


<!-- ==========================================
MOBILE SIDEBAR OVERLAY
========================================== -->

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


<!-- ==========================================
MAIN CONTENT
========================================== -->

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


<!-- ==========================================
ADMIN SIDEBAR
========================================== -->

<?php include "../../components/admin_sidebar.php"; ?>


<!-- ==========================================
PAGE CONTENT
========================================== -->

<section class="min-w-0">


<?php

$pageTitle =
    "Admin History Logs";

include
    "../../components/admin_topbar.php";

?>


<!-- ==========================================
HISTORY CONTENT
========================================== -->

<div

id="historyContent"

class="space-y-8">


<!-- ======================================================
STATISTICS
====================================================== -->

<div

class="
grid
grid-cols-1
md:grid-cols-2
xl:grid-cols-4
gap-6">


<!-- ==========================================
TOTAL LOGS
========================================== -->

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


<!-- ==========================================
TODAY'S LOGS
========================================== -->

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


<!-- ==========================================
ADMIN ACTIONS
========================================== -->

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


<!-- ==========================================
SECURITY EVENTS
========================================== -->

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


<!-- ======================================================
ADMIN HISTORY TABLE
====================================================== -->

<div
class="
glass
rounded-3xl
p-8">


<!-- ==========================================
TABLE HEADER
========================================== -->

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


<!-- Search -->

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


<!-- ==========================================
FILTERS
========================================== -->

<div
class="
flex
flex-col
md:flex-row
justify-between
gap-4
mb-6">


<select
id="entriesSelect"
class="w-40">

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


<select
id="actionFilter"
class="w-56">

    <option value="">

        All Activities

    </option>

    <option>

        Login

    </option>

    <option>

        Logout

    </option>

    <option>

        Election Started

    </option>

    <option>

        Election Stopped

    </option>

    <option>

        Student Approved

    </option>

    <option>

        Candidate Added

    </option>

</select>

</div>


<!-- ==========================================
TABLE
========================================== -->

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


<tbody
id="historyTableBody">


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


<!-- Date & Time -->

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


<!-- Action -->

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


<!-- Description -->

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


<!-- Admin -->

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


<!-- IP Address -->

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


<!-- ==========================================
PAGINATION
========================================== -->

<div
class="
flex
flex-col
md:flex-row
justify-between
items-center
gap-4
mt-8">


<p
id="historyInfo"
class="text-slate-400">

    Showing

    <strong>0</strong>

    to

    <strong>0</strong>

    of

    <strong>0</strong>

    entries

</p>


<div
class="
flex
items-center
gap-3">


<button
id="prevPage"
type="button"
class="btn-outline">

    Previous

</button>


<span
id="currentPage"
class="px-4">

    1

</span>


<button
id="nextPage"
type="button"
class="btn-primary">

    Next

</button>

</div>

</div>


</div>


</div>


</section>


</div>

</main>


<!-- ==========================================
FOOTER
========================================== -->

<div id="footer"></div>


<!-- ==========================================
JAVASCRIPT
========================================== -->

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/dashboard.js"></script>

<script src="../../assets/js/history.js"></script>


</body>

</html>