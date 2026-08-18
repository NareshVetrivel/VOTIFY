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
   FETCH PENDING REQUESTS
========================================================== */

$requests = [];

$query = "

SELECT *

FROM students

WHERE status='Pending'

ORDER BY created_at DESC

";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $requests[] = $row;

    }

}

/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

$totalRequests = 0;

$pendingRequests = 0;

$approvedRequests = 0;

$rejectedRequests = 0;

/* Total */

$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) total FROM students"

);

if($result){

    $totalRequests =
    mysqli_fetch_assoc($result)["total"];

}

/* Pending */

$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) total
     FROM students
     WHERE status='Pending'"

);

if($result){

    $pendingRequests =
    mysqli_fetch_assoc($result)["total"];

}

/* Approved */

$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) total
     FROM students
     WHERE status='Approved'"

);

if($result){

    $approvedRequests =
    mysqli_fetch_assoc($result)["total"];

}

/* Rejected */

$result = mysqli_query(

    $conn,

    "SELECT COUNT(*) total
     FROM students
     WHERE status='Rejected'"

);

if($result){

    $rejectedRequests =
    mysqli_fetch_assoc($result)["total"];

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

Voters Requests | VOTIFY

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

class="

bg-[#0B1020]

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

<!-- Header -->

<div id="header"></div>

<!-- Mobile Overlay -->

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

py-8">

<div

class="

grid

grid-cols-1

lg:grid-cols-[280px_1fr]

gap-8

items-start">

<!-- Sidebar -->

<?php

include "../../components/admin_sidebar.php";

?>

<!-- =====================================================
CONTENT
===================================================== -->

<section class="min-w-0">

<?php

$pageTitle = "Voters Requests";

include "../../components/admin_topbar.php";

?>

<div

id="requestsContent"

class="space-y-8">

<!-- =====================================================
STATISTICS
===================================================== -->

<div

class="

grid

grid-cols-1

md:grid-cols-2

xl:grid-cols-4

gap-6">

<!-- Total -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

Total Requests

</p>

<h2 class="text-5xl font-bold mt-4">

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

justify-center">

<i class="ri-user-line text-3xl text-blue-400"></i>

</div>

</div>

</div>

<!-- Pending -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

Pending Requests

</p>

<h2 class="text-5xl font-bold mt-4">

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

justify-center">

<i class="ri-time-line text-3xl text-yellow-400"></i>

</div>

</div>

</div>

<!-- Approved -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

Approved Requests

</p>

<h2 class="text-5xl font-bold mt-4">

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

justify-center">

<i class="ri-check-line text-3xl text-green-400"></i>

</div>

</div>

</div>

<!-- Rejected -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

Rejected Requests

</p>

<h2 class="text-5xl font-bold mt-4">

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

justify-center">

<i class="ri-close-line text-3xl text-red-400"></i>

</div>

</div>

</div>

</div>

<!-- =====================================================
REQUESTS TABLE START
===================================================== -->

<div class="glass rounded-3xl p-8">

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

<h2 class="text-3xl font-bold">

Pending Registration Requests

</h2>

<p class="text-slate-400 mt-2">

Review and verify student registration requests.

</p>

</div>

<div class="relative w-full lg:w-80">

<i
class="ri-search-2-line absolute left-4 top-1/2 -translate-y-1/2 text-lg text-slate-400 pointer-events-none">
</i>

<input
type="text"
id="requestSearch"
placeholder="Search students..."
class="w-full h-14 pl-12 pr-5 rounded-2xl bg-white/5 border border-white/10 focus:border-blue-500 outline-none transition">

</div>

</div>

<div class="overflow-x-auto rounded-2xl border border-white/10">

<table class="w-full">

<thead class="bg-white/5">

<tr>

<th class="px-6 py-4 text-left">Student</th>

<th class="px-6 py-4 text-left">Admission No</th>

<th class="px-6 py-4 text-left">Department</th>

<th class="px-6 py-4 text-left">Year</th>

<th class="px-6 py-4 text-left">Registered</th>

<th class="px-6 py-4 text-center">Status</th>

<th class="px-6 py-4 text-center">Actions</th>

</tr>

</thead>

<tbody id="requestsTableBody">

<?php

if (count($requests) == 0) {

?>

<tr>

<td
colspan="7"
class="py-12 text-center text-slate-400">

<i class="ri-inbox-line text-5xl block mb-4"></i>

No Pending Registration Requests

</td>

</tr>

<?php

}

else {

foreach ($requests as $student) {

?>

<tr
data-id="<?= $student["id"]; ?>"
class="border-b border-white/5 hover:bg-white/5 transition">

<!-- Student -->

<td class="px-6 py-5">

<div>

<div class="font-semibold">

<?= htmlspecialchars($student["full_name"]); ?>

</div>

<div class="text-xs text-slate-400 mt-1">

<?= htmlspecialchars($student["college_email"]); ?>

</div>

</div>

</td>

<!-- Admission -->

<td class="px-6 py-5">

<?= htmlspecialchars($student["admission_no"]); ?>

</td>

<!-- Department -->

<td class="px-6 py-5">

<?= htmlspecialchars($student["department"]); ?>

</td>

<!-- Year -->

<td class="px-6 py-5">

<?= htmlspecialchars($student["year"]); ?>

</td>

<!-- Registered -->

<td class="px-6 py-5">

<?= date(

"d M Y",

strtotime($student["created_at"])

); ?>

</td>

<!-- Status -->

<td class="px-6 py-5 text-center">

<span
class="px-4 py-2 rounded-full bg-yellow-500/20 text-yellow-400">

Pending

</span>

</td>

<!-- Actions -->

<td class="px-6 py-5">

<div
class="flex justify-center gap-3"
data-actions="<?= $student["id"]; ?>">

<button

class="viewRequest

w-11

h-11

rounded-xl

bg-blue-500/20

text-blue-400

hover:bg-blue-500/30

transition"

data-id="<?= $student["id"]; ?>"

title="View">

<i class="ri-eye-line"></i>

</button>

<button

class="approveRequest

w-11

h-11

rounded-xl

bg-green-500/20

text-green-400

hover:bg-green-500/30

transition"

data-id="<?= $student["id"]; ?>"

title="Approve">

<i class="ri-check-line"></i>

</button>

<button

class="rejectRequest

w-11

h-11

rounded-xl

bg-red-500/20

text-red-400

hover:bg-red-500/30

transition"

data-id="<?= $student["id"]; ?>"

title="Reject">

<i class="ri-close-line"></i>

</button>

</div>

</td>

</tr>

<?php

}

}

?>

</tbody>

</table>

</div>

</div>

</div>

</section>

</div>

</main>

<!-- =====================================================
ACTION MODAL
===================================================== -->

<?php include "../../components/confirmation_modal.php"; ?>

<?php include "../../components/student_modal.php"; ?>

<?php include "../../components/toast.php"; ?>

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

<script src="../../assets/js/student_modal.js"></script>

<script src="../../assets/js/requests.js"></script>
</body>

</html>