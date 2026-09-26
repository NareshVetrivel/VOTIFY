<?php
/* ==========================================================
   VOTIFY
   Voters Management
   File : pages/admin/voters.php
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
   FETCH APPROVED VOTERS
========================================================== */

$voters = [];

$query = "
    SELECT *
    FROM students
    WHERE status='Approved'
    ORDER BY full_name ASC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $voters[] = $row;

    }

}


/* ==========================================================
   DASHBOARD COUNTS
========================================================== */

$totalApproved = 0;
$totalVoted = 0;
$totalUnvoted = 0;


/* ==========================================
   APPROVED STUDENTS
========================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) total
    FROM students
    WHERE status='Approved'
    "
);

if ($result) {

    $totalApproved =
        mysqli_fetch_assoc($result)["total"];

}


/* ==========================================
   VOTED STUDENTS
========================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) total
    FROM students
    WHERE
        status='Approved'
        AND
        vote_status='Voted'
    "
);

if ($result) {

    $totalVoted =
        mysqli_fetch_assoc($result)["total"];

}


/* ==========================================
   UNVOTED STUDENTS
========================================== */

$result = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) total
    FROM students
    WHERE
        status='Approved'
        AND
        vote_status='Unvoted'
    "
);

if ($result) {

    $totalUnvoted =
        mysqli_fetch_assoc($result)["total"];

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
    Voters Management | VOTIFY
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
    ">


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

$pageTitle = "Voters Management";

include "../../components/admin_topbar.php";

?>


<div
    id="votersContent"
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


<!-- =====================================================
     TOTAL APPROVED
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">
Approved Students
</p>

<h2
    id="approvedStudents"
    class="text-5xl font-bold mt-4"
>

<?= $totalApproved; ?>

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
    class="w-9 h-9 text-blue-400"
    viewBox="0 0 24 24"
    fill="currentColor"
>

<path
    d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5z"
/>

</svg>

</div>

</div>

</div>


<!-- =====================================================
     VOTED STUDENTS
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">
Voted Students
</p>

<h2
    id="votedStudents"
    class="text-5xl font-bold mt-4"
>

<?= $totalVoted; ?>

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

<svg
    xmlns="http://www.w3.org/2000/svg"
    class="w-8 h-8 text-green-400"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="2"
>

<path
    stroke-linecap="round"
    stroke-linejoin="round"
    d="M5 13l4 4L19 7"
/>

</svg>

</div>

</div>

</div>


<!-- =====================================================
     UNVOTED STUDENTS
===================================================== -->

<div class="glass rounded-3xl p-6 dashboard-card">

<div class="flex items-center justify-between">

<div>

<p class="text-slate-400">
Unvoted Students
</p>

<h2
    id="unvotedStudents"
    class="text-5xl font-bold mt-4"
>

<?= $totalUnvoted; ?>

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

<svg
    xmlns="http://www.w3.org/2000/svg"
    class="w-8 h-8 text-yellow-400"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="2"
>

<circle
    cx="12"
    cy="12"
    r="9"
/>

<path
    d="M12 7v5l3 3"
/>

</svg>

</div>

</div>

</div>

</div>


<!-- =====================================================
     VOTERS TABLE SECTION
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
    mb-8
    "
>

<div>

<h2 class="text-3xl font-bold">
Voters Management
</h2>

<p class="text-slate-400 mt-2">
Manage approved student voter accounts.
</p>

</div>


<div class="flex flex-wrap gap-3">

<button
    id="exportExcel"
    class="
    btn-primary
    flex
    items-center
    gap-2
    "
>

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
    mb-8
    "
>

<div
    class="
    flex
    flex-wrap
    items-center
    gap-4
    "
>


<!-- =====================================================
     CUSTOM ENTRIES DROPDOWN
     Native select is kept hidden for voters.js
     compatibility. Custom UI dispatches change events.
===================================================== -->

<div class="flex items-center gap-3">

<label
    for="entriesDropdownTrigger"
    class="text-slate-400"
>
    Show
</label>


<div
    id="entriesDropdown"
    class="relative w-36 shrink-0"
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


<!-- Hidden native select retained for voters.js -->

<select
    id="entriesSelect"
    class="hidden"
    aria-hidden="true"
    tabindex="-1"
>

<option value="10">10 Entries</option>
<option value="25">25 Entries</option>
<option value="50">50 Entries</option>
<option value="100">100 Entries</option>

</select>

</div>


<!-- =====================================================
     FILTERS
===================================================== -->

<div class="flex gap-2">

<button
    id="filterAll"
    class="filterButton btn-primary"
>

All

</button>


<button
    id="filterVoted"
    class="filterButton btn-outline"
>

Voted

</button>


<button
    id="filterUnvoted"
    class="filterButton btn-outline"
>

Unvoted

</button>

</div>

</div>


<!-- =====================================================
     SEARCH
===================================================== -->

<div
    class="
    flex
    items-center
    gap-4
    "
>

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
    pointer-events-none
    "
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="2"
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
    id="voterSearch"
    placeholder="Search voters..."
    class="w-full pl-12"
>

</div>

</div>

</div>


<!-- =====================================================
     VOTERS TABLE
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


<thead class="bg-white/5">

<tr>

<th class="px-6 py-4 text-left">
Student
</th>

<th class="px-6 py-4 text-left">
Admission No
</th>

<th class="px-6 py-4 text-left">
Department
</th>

<th class="px-6 py-4 text-left">
Year
</th>

<th class="px-6 py-4 text-center">
Vote Status
</th>

<th class="px-6 py-4 text-left">
Registered
</th>

<th class="px-6 py-4 text-center">
Actions
</th>

</tr>

</thead>


<tbody id="votersTableBody">


<?php

foreach ($voters as $student) {

?>

<tr
    class="
    border-b
    border-white/5
    hover:bg-white/5
    transition
    "
    data-id="<?= htmlspecialchars($student["id"]); ?>"
    data-status="<?= htmlspecialchars($student["vote_status"]); ?>"
    data-search="<?= htmlspecialchars(
        strtolower(
            $student["full_name"] . " " .
            $student["college_email"] . " " .
            $student["admission_no"] . " " .
            $student["department"] . " " .
            $student["year"]
        )
    ); ?>"
>


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


<!-- Vote Status -->

<td class="px-6 py-5 text-center">

<?php

if ($student["vote_status"] == "Voted") {

?>

<span
    class="
    px-4
    py-2
    rounded-full
    bg-green-500/20
    text-green-400
    "
>

Voted

</span>

<?php

} else {

?>

<span
    class="
    px-4
    py-2
    rounded-full
    bg-yellow-500/20
    text-yellow-400
    "
>

Unvoted

</span>

<?php

}

?>

</td>


<!-- Registered -->

<td class="px-6 py-5">

<?= date(
    "d M Y",
    strtotime(
        $student["created_at"]
    )
); ?>

</td>


<!-- Actions -->

<td class="px-6 py-5">

<div class="flex justify-center gap-3">


<button
    type="button"
    class="
    viewVoter
    w-11
    h-11
    rounded-xl
    bg-indigo-500/20
    text-indigo-400
    hover:bg-indigo-500/30
    transition
    "
    data-id="<?= htmlspecialchars($student["id"]); ?>"
    title="View"
>

<i class="ri-eye-line"></i>

</button>


<button
    type="button"
    class="
    editVoter
    w-11
    h-11
    rounded-xl
    bg-blue-500/20
    text-blue-400
    hover:bg-blue-500/30
    transition
    "
    data-id="<?= htmlspecialchars($student["id"]); ?>"
    title="Edit"
>

<i class="ri-edit-2-line"></i>

</button>


<button
    type="button"
    class="
    deleteVoter
    w-11
    h-11
    rounded-xl
    bg-red-500/20
    text-red-400
    hover:bg-red-500/30
    transition
    "
    data-id="<?= htmlspecialchars($student["id"]); ?>"
    title="Delete"
>

<i class="ri-delete-bin-6-line"></i>

</button>


</div>

</td>

</tr>


<?php

}

?>


<!-- =====================================================
     EMPTY STATE
===================================================== -->

<tr
    id="votersEmptyState"
    class="<?= count($voters) > 0 ? 'hidden' : ''; ?>"
>

<td
    colspan="7"
    class="px-6 py-16 text-center"
>

<div
    class="
    flex
    flex-col
    items-center
    justify-center
    max-w-xl
    mx-auto
    "
>


<!-- Icon -->

<div
    class="
    w-20
    h-20
    rounded-3xl
    bg-indigo-500/10
    border
    border-indigo-400/10
    flex
    items-center
    justify-center
    mb-6
    "
>

<i
    id="votersEmptyStateIcon"
    class="
    ri-user-search-line
    text-5xl
    text-indigo-400
    "
></i>

</div>


<!-- Title -->

<h3
    id="votersEmptyStateTitle"
    class="
    text-2xl
    font-bold
    text-white
    "
>

No Approved Voters

</h3>


<!-- Message -->

<p
    id="votersEmptyStateMessage"
    class="
    mt-3
    text-slate-400
    text-sm
    sm:text-base
    leading-relaxed
    "
>

No approved students are available yet.

</p>


<!-- Small visual hint -->

<div
    class="
    mt-6
    inline-flex
    items-center
    gap-2
    px-4
    py-2
    rounded-full
    bg-white/5
    border
    border-white/10
    text-xs
    text-slate-500
    "
>

<i class="ri-information-line"></i>

<span>

This section will update automatically when voters are available.

</span>

</div>

</div>

</td>

</tr>


</tbody>

</table>

</div>


<!-- =====================================================
     PAGINATION
===================================================== -->

<div
    class="
    flex
    flex-col
    md:flex-row
    items-center
    justify-between
    gap-5
    mt-8
    "
>


<p class="text-slate-400 text-sm">

Showing

<span id="showingStart">
0
</span>

to

<span id="showingEnd">
0
</span>

of

<span id="totalRecords">
<?= count($voters); ?>
</span>

entries

</p>


<div
    id="pagination"
    class="
    flex
    items-center
    gap-2
    "
>

<button
    id="prevPage"
    type="button"
    class="btn-outline"
>

<i class="ri-arrow-left-s-line"></i>

</button>


<div
    id="paginationNumbers"
    class="flex gap-2"
>

<!-- JavaScript -->

</div>


<button
    id="nextPage"
    type="button"
    class="btn-outline"
>

<i class="ri-arrow-right-s-line"></i>

</button>

</div>

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

include "../../components/voter_modal.php";

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

<script src="../../assets/js/voters.js"></script>


<!-- =====================================================
     CUSTOM TAILWIND DROPDOWN

     Important:
     - No normal CSS is used.
     - The native #entriesSelect remains hidden so the
       existing voters.js logic continues to work.
     - Selecting a custom option updates the native select
       and dispatches a normal change event.
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
           UPDATE UI
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
                        "text-white",
                        "bg-blue-500/15"
                    );

                    option.classList.add(
                        "text-slate-300"
                    );


                    if (isSelected) {

                        option.classList.remove(
                            "text-slate-300",
                            "bg-blue-500/15"
                        );

                        option.classList.add(
                            "bg-gradient-to-r",
                            "from-blue-600",
                            "to-purple-500",
                            "text-white"
                        );

                        option.classList.add(
                            "selected"
                        );

                    } else {

                        option.classList.remove(
                            "selected"
                        );

                    }

                }
            );

        }


        /* ==================================================
           OPTION HOVER

           Selected option does NOT receive hover styling.
        ================================================== */

        const hoverClasses = [
            "bg-blue-500/15",
            "text-white"
        ];

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


                option.addEventListener(
                    "click",
                    function () {

                        const selectedValue =
                            option.dataset.value;

                        if (!selectedValue) {
                            return;
                        }


                        /*
                         * Update the hidden native select.
                         */
                        nativeSelect.value =
                            selectedValue;


                        /*
                         * Trigger the existing voters.js
                         * change handler.
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
