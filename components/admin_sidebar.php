<?php
/* ==========================================================
   VOTIFY
   Admin Sidebar
========================================================== */

$currentPage = basename($_SERVER["PHP_SELF"]);
?>

<!-- ==========================================================
ADMIN SIDEBAR
========================================================== -->

<aside

id="adminSidebar"

class="
fixed
top-0
left-0
z-50

w-72
h-screen

glass

border-r
border-white/10

transform
transition-transform
duration-300
ease-in-out

-translate-x-full

lg:sticky
lg:top-0
lg:translate-x-0

flex
flex-col">

<!-- ==========================================
LOGO
========================================== -->

<div class="relative px-8 py-8 border-b border-white/10">

    <!-- Mobile Close -->

    <div
    class="flex justify-end lg:hidden mb-4">

<button
    id="closeSidebar"
    type="button"
    aria-label="Close Sidebar"
    class="
    absolute
    top-5
    right-5
    z-[9999]

    flex
    items-center
    justify-center

    w-11
    h-11

    rounded-xl

    bg-white/10

    border
    border-white/20

    text-white

    hover:bg-red-500/30

    transition-all">

    <i class="ri-close-line text-2xl pointer-events-none"></i>

</button>

    </div>

    <h2
    class="text-4xl font-bold gradient-text">

        VOTIFY

    </h2>

    <p
    class="text-slate-400 mt-2">

        Administration Panel

    </p>

</div>

<!-- ==========================================
MENU
========================================== -->

<nav

class="

flex-1

px-5

py-6

space-y-3

overflow-y-auto">

<!-- Dashboard -->

<a

href="dashboard.php"

class="sidebar-link <?php echo ($currentPage=="dashboard.php") ? "active" : ""; ?>">

<i class="ri-dashboard-line"></i>

<span>

Dashboard

</span>

</a>

<!-- Requests -->

<a

href="requests.php"

class="sidebar-link <?php echo ($currentPage=="requests.php") ? "active" : ""; ?>">

<i class="ri-user-follow-line"></i>

<span>

Voters Requests

</span>

</a>

<!-- Voters -->

<a

href="voters.php"

class="sidebar-link <?php echo ($currentPage=="voters.php") ? "active" : ""; ?>">

<i class="ri-team-line"></i>

<span>

Voters Management

</span>

</a>

<!-- Candidates -->

<a

href="candidates.php"

class="sidebar-link <?php echo ($currentPage=="candidates.php") ? "active" : ""; ?>">

<i class="ri-award-line"></i>

<span>

Candidate Management

</span>

</a>

<!-- Canvassing -->

<a

href="canvassing.php"

class="sidebar-link <?php echo ($currentPage=="canvassing.php") ? "active" : ""; ?>">

<i class="ri-megaphone-line"></i>

<span>

Canvassing Reports

</span>

</a>

<!-- History -->

<a

href="history.php"

class="sidebar-link <?php echo ($currentPage=="history.php") ? "active" : ""; ?>">

<i class="ri-history-line"></i>

<span>

History Log

</span>

</a>

<!-- About -->

<a

href="about.php"

class="sidebar-link <?php echo ($currentPage=="about.php") ? "active" : ""; ?>">

<i class="ri-information-line"></i>

<span>

About Us

</span>

</a>

</nav>

<!-- ==========================================
MOBILE LOGOUT
========================================== -->

<div
class="p-5 border-t border-white/10">

    <a

    href="../../backend/admin/logout.php"

    class="

    lg:hidden

    flex

    items-center

    justify-center

    gap-3

    w-full

    py-4

    rounded-2xl

    font-semibold

    text-white

    bg-gradient-to-r

    from-red-500

    via-red-600

    to-pink-600

    hover:scale-105

    transition-all">

        <i class="ri-logout-box-r-line"></i>

        Logout

    </a>

</div>

</aside>