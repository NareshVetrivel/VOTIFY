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

SELECT *

FROM candidates

ORDER BY created_at DESC

";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $candidates[] = $row;

    }

}

/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

$totalCandidates = 0;

$totalFirstYear = 0;

$totalSecondYear = 0;

/* ==========================================
   TOTAL CANDIDATES
========================================== */

$result = mysqli_query(

    $conn,

    "

    SELECT COUNT(*) total

    FROM candidates

    "

);

if ($result) {

    $totalCandidates =

    mysqli_fetch_assoc($result)["total"];

}

/* ==========================================
   FIRST-YEAR CANDIDATES
========================================== */

$result = mysqli_query(

    $conn,

    "

    SELECT COUNT(*) AS total

    FROM candidates

    WHERE

        year='1st Year'

        OR

        year='I Year'

    "

);

if($result){

    $totalFirstYear =

    mysqli_fetch_assoc($result)["total"];

}

/* ==========================================
   SECOND-YEAR CANDIDATES
========================================== */

$result = mysqli_query(

    $conn,

    "

    SELECT COUNT(*) AS total

    FROM candidates

    WHERE

        year='2nd Year'

        OR

        year='II Year'

    "

);

if($result){

    $totalSecondYear =

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

Candidate Management | VOTIFY

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

$pageTitle = "Candidate Management";

include "../../components/admin_topbar.php";

?>

<div

id="candidatesContent"

class="space-y-8">

<!-- =====================================================
STATISTICS
===================================================== -->

<div

class="

grid

grid-cols-1

md:grid-cols-2

xl:grid-cols-3

gap-6">

<!-- =====================================================
TOTAL CANDIDATES
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

Total Candidates

</p>

<h2

id="totalCandidates"

class="text-5xl font-bold mt-4">

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

justify-center">

<svg

xmlns="http://www.w3.org/2000/svg"

viewBox="0 0 24 24"

fill="currentColor"

class="w-9 h-9 text-blue-400">

<path d="M12 2a5 5 0 100 10 5 5 0 000-10zm0 12c-4.97 0-9 2.24-9 5v3h18v-3c0-2.76-4.03-5-9-5z"/>

</svg>

</div>

</div>

</div>

<!-- =====================================================
ACTIVE CANDIDATES
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

1st Year Candidates

</p>

<h2

id="firstYearCandidates"

class="text-5xl font-bold mt-4">

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
justify-center">

<svg
xmlns="http://www.w3.org/2000/svg"
viewBox="0 0 24 24"
fill="currentColor"
class="w-8 h-8 text-emerald-400">

<path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"/>

</svg>

</div>

</div>

</div>

<!-- =====================================================
INACTIVE CANDIDATES
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">

2nd Year Candidates

</p>

<h2

id="secondYearCandidates"

class="text-5xl font-bold mt-4">

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
justify-center">

<svg
xmlns="http://www.w3.org/2000/svg"
viewBox="0 0 24 24"
fill="currentColor"
class="w-8 h-8 text-violet-400">

<path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"/>

</svg>

</div>

</div>

</div>

</div>

<!-- =====================================================
CANDIDATES TABLE SECTION
===================================================== -->

<div class="glass rounded-3xl p-8">

<!-- =====================================================
HEADER
===================================================== -->

<div

class="

flex

flex-col

xl:flex-row

xl:items-center

xl:justify-between

gap-6

mb-8">

<!-- Left -->

<div>

<h2 class="text-3xl font-bold">

Candidate Management

</h2>

<p class="text-slate-400 mt-2">

Manage election candidates.

</p>

</div>

<!-- Right -->

<div class="flex flex-wrap gap-3">

<!-- Add Candidate -->

<button

id="addCandidate"

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

shadow-green-500/30">

<i class="ri-user-add-line"></i>

Add Candidate

</button>

<!-- Export Excel -->

<button

id="exportCandidates"

class="

btn-primary

flex

items-center

gap-2">

<i class="ri-file-excel-2-line"></i>

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

mb-8">

<!-- LEFT -->

<div

class="

flex

flex-wrap

items-center

gap-4">

<!-- Entries -->

<div class="flex items-center gap-3">

<label class="text-slate-400">

Show

</label>

<select

id="entriesSelect"

class="w-24">

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

<!-- Filters -->

<div class="flex gap-2">

<button
id="filterAll"
class="filterButton btn-primary">

All

</button>

<button
id="filterFirstYear"
class="filterButton btn-outline">

1st Year

</button>

<button
id="filterSecondYear"
class="filterButton btn-outline">

2nd Year

</button>

</div>

</div>

<!-- RIGHT -->

<div

class="

flex

items-center

gap-4">

<div class="relative w-full xl:w-80">

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

pointer-events-none"

fill="none"

viewBox="0 0 24 24"

stroke="currentColor"

stroke-width="2">

<circle cx="11" cy="11" r="7"/>

<path d="M20 20L16.5 16.5"/>

</svg>

<input

type="text"

id="candidateSearch"

placeholder="Search candidates..."

class="w-full pl-12">

</div>

</div>

</div>

<!-- =====================================================
CANDIDATES TABLE
===================================================== -->

<div class="overflow-x-auto rounded-2xl border border-white/10">

<table class="w-full">

<thead class="bg-white/5">

<tr>

<th class="px-6 py-4 text-center">

Photo

</th>

<th class="px-6 py-4 text-left">

Candidate

</th>

<th class="px-6 py-4 text-left">

Department

</th>

<th class="px-6 py-4 text-left">

Year

</th>

<th class="px-6 py-4 text-left">

Manifesto

</th>

<th class="px-6 py-4 text-center">

Actions

</th>

</tr>

</thead>

<tbody id="candidatesTableBody">

<?php

if(count($candidates)==0){

?>

<tr>

<td

colspan="6"

class="py-20 text-center text-slate-400">

<div class="flex justify-center mb-6">

<svg
xmlns="http://www.w3.org/2000/svg"
viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="1.8"
class="w-16 h-16 text-slate-500">

<circle cx="12" cy="8" r="3"/>

<path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/>

</svg>

</div>

<h3 class="text-3xl font-bold text-white">

No Candidates Found

</h3>

<p class="mt-3 text-slate-400">

No candidates have been added yet.

</p>

</td>

</tr>

<?php

}

else{

foreach($candidates as $candidate){

?>

<tr
class="
border-b
border-white/5
hover:bg-white/5
transition"
data-id="<?= $candidate["id"]; ?>"
data-year="<?= $candidate["year"]; ?>"
data-search="<?= strtolower(
$candidate["full_name"]." ".
$candidate["admission_no"]." ".
$candidate["department"]." ".
$candidate["manifesto"]
); ?>">

<!-- PHOTO -->

<td class="px-6 py-5 text-center">

<img

src="../../uploads/candidates/<?= htmlspecialchars($candidate["photo"]); ?>"

alt="Candidate"

class="

w-14

h-14

rounded-xl

object-cover

mx-auto

border

border-white/10">

</td>

<!-- NAME -->

<td class="px-6 py-5">

<div>

<div class="font-semibold">

<?= htmlspecialchars($candidate["full_name"]); ?>

</div>

<div class="text-xs text-slate-400 mt-1">

<?= htmlspecialchars($candidate["admission_no"]); ?>

</div>

</div>

</td>

<!-- DEPARTMENT -->

<td class="px-6 py-5">

<?= htmlspecialchars($candidate["department"]); ?>

</td>

<!-- YEAR -->

<td class="px-6 py-5">

<?= htmlspecialchars($candidate["year"]); ?>

</td>

<!-- MANIFESTO -->

<td class="px-6 py-5">

<p class="max-w-xs truncate">

<?= htmlspecialchars($candidate["manifesto"]); ?>

</p>

</td>

<!-- ACTIONS -->

<td class="px-6 py-5">

<div

class="

flex

justify-center

gap-3">

<!-- VIEW -->

<button

class="

viewCandidate

w-11

h-11

rounded-xl

bg-cyan-500/20

text-cyan-400

hover:bg-cyan-500/30

transition"

data-id="<?= $candidate["id"]; ?>"

title="View">

<i class="ri-eye-line"></i>

</button>

<!-- EDIT -->

<button
class="
editCandidate
w-11
h-11
rounded-xl
bg-blue-500/20
text-blue-400
hover:bg-blue-500/30
transition"

data-id="<?= $candidate["id"]; ?>"

data-photo="<?= htmlspecialchars($candidate["photo"]); ?>"

data-manifesto="<?= htmlspecialchars($candidate["manifesto"]); ?>"

data-admission="<?= htmlspecialchars($candidate["admission_no"]); ?>"

data-name="<?= htmlspecialchars($candidate["full_name"]); ?>"

data-department="<?= htmlspecialchars($candidate["department"]); ?>"

data-year="<?= htmlspecialchars($candidate["year"]); ?>"

data-student="<?= $candidate["student_id"]; ?>"

title="Edit">

<i class="ri-edit-2-line"></i>

</button>

<!-- DELETE -->

<button

class="

deleteCandidate

w-11

h-11

rounded-xl

bg-red-500/20

text-red-400

hover:bg-red-500/30

transition"

data-id="<?= $candidate["id"]; ?>"

title="Delete">

<i class="ri-delete-bin-6-line"></i>

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

</section>

</div>

</main>

<!-- =====================================================
MODALS
===================================================== -->

<?php include "../../components/candidate_modal.php"; ?>

<?php include "../../components/candidate_view_modal.php"; ?>

<?php include "../../components/student_modal.php"; ?>

<?php include "../../components/confirmation_modal.php"; ?>

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

<script src="../../assets/js/toast.js"></script>

<script src="../../assets/js/candidates.js"></script>

</body>

</html>