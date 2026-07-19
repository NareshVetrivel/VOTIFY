<?php
/* ==========================================================
   VOTIFY
   Reusable Admin Topbar
========================================================== */

if (!isset($pageTitle)) {

    $pageTitle = "Dashboard";

}
?>

<!-- ==========================================================
TOPBAR
========================================================== -->

<div

class="

glass

rounded-3xl

px-8

py-6

mb-8

flex

items-center

justify-between">

    <!-- ======================================
    LEFT
    ======================================= -->

    <div

    class="

    flex

    items-center

    gap-5">

        <!-- Mobile Menu -->

        <button

        id="menuButton"

        class="

        lg:hidden

        w-12

        h-12

        rounded-xl

        bg-white/5

        border

        border-white/10

        hover:bg-blue-500/20

        transition">

            <i

            class="

            ri-menu-3-line

            text-2xl">

            </i>

        </button>

        <!-- Page Title -->

        <div>

            <h1

            class="

            text-3xl

            md:text-4xl

            font-bold">

                <?php

                echo $pageTitle;

                ?>

            </h1>

            <p

            class="

            mt-2

            text-slate-400">

                Welcome back,

                <span

                class="

                text-blue-400

                font-semibold">

                    <?php

                    echo htmlspecialchars($_SESSION["admin_username"]);

                    ?>

                </span>

            </p>

        </div>

    </div>

    <!-- ======================================
    RIGHT
    ======================================= -->

<a

href="../../backend/admin/logout.php"

id="desktopLogout"

class="

hidden

md:flex

items-center

gap-3

px-6

py-3

rounded-2xl

font-semibold

text-white

bg-gradient-to-r

from-red-500

via-red-600

to-pink-600

hover:scale-105

transition-all

duration-300

shadow-xl

shadow-red-500/30">
        <i

        class="

        ri-logout-box-r-line

        text-xl">

        </i>

        Logout

    </a>

</div>

<!-- ==========================================================
DESKTOP LOGOUT MODAL
========================================================== -->

<div

id="logoutModal"

class="

fixed

inset-0

hidden

items-center

justify-center

bg-black/70

backdrop-blur-sm

z-[9999]">

<div

class="

glass

rounded-3xl

w-[420px]

max-w-[90%]

p-8">

<div class="text-center">

<i

class="ri-logout-box-r-line

text-6xl

text-red-400">

</i>

<h2

class="text-3xl font-bold mt-5">

Logout

</h2>

<p

class="text-slate-400 mt-3">

Are you sure you want to logout?

</p>

<div

class="flex gap-4 mt-8">

<button

id="cancelLogout"

class="

flex-1

py-4

rounded-2xl

bg-white/10

hover:bg-white/20">

Cancel

</button>

<a

href="../../backend/admin/logout.php"

class="

flex-1

text-center

py-4

rounded-2xl

font-semibold

text-white

bg-gradient-to-r

from-red-500

to-pink-600">

Logout

</a>

</div>

</div>

</div>

</div>