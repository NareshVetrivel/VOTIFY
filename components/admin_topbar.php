<?php
/* ==========================================================
   VOTIFY
   Reusable Admin Topbar
========================================================== */

if (!isset($pageTitle)) {

    $pageTitle = "Dashboard";

}


/* ==========================================================
   ADMIN SESSION DATA
========================================================== */

$adminUsername =
    $_SESSION["admin_username"] ?? "Administrator";

$adminRole =
    $_SESSION["admin_role"] ?? "Admin";


/* ==========================================================
   ROLE DISPLAY LABEL
========================================================== */

$roleLabel =
    ($adminRole === "Super Admin")
        ? "Super Admin"
        : "Admin";

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

        <!-- ==================================
        MOBILE MENU
        =================================== -->

        <button

        id="menuButton"

        type="button"

        aria-label="Open Sidebar"

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


        <!-- ==================================
        PAGE TITLE
        =================================== -->

        <div>

            <h1

            class="

            text-3xl

            md:text-4xl

            font-bold">

                <?php

                echo htmlspecialchars(
                    $pageTitle,
                    ENT_QUOTES,
                    "UTF-8"
                );

                ?>

            </h1>


            <!-- ==================================
            WELCOME MESSAGE
            =================================== -->

            <div

            class="

            mt-2

            flex

            flex-wrap

            items-center

            gap-2">

                <p

                class="

                text-slate-400">

                    Welcome back,

                </p>


                <!-- Admin Username -->

                <span

                class="

                text-blue-400

                font-semibold">

                    <?php

                    echo htmlspecialchars(
                        $adminUsername,
                        ENT_QUOTES,
                        "UTF-8"
                    );

                    ?>

                </span>


                <!-- Role Badge -->

                <span

                class="

                inline-flex

                items-center

                gap-1.5

                px-3

                py-1

                rounded-full

                text-xs

                font-semibold

                border

                <?php

                if ($adminRole === "Super Admin") {

                    echo "
                    bg-purple-500/10
                    border-purple-500/30
                    text-purple-300
                    ";

                }

                else {

                    echo "
                    bg-blue-500/10
                    border-blue-500/30
                    text-blue-300
                    ";

                }

                ?>">

                    <i

                    class="<?php

                    echo ($adminRole === "Super Admin")

                        ? "ri-shield-star-line"

                        : "ri-shield-user-line";

                    ?>">

                    </i>

                    <?php

                    echo htmlspecialchars(
                        $roleLabel,
                        ENT_QUOTES,
                        "UTF-8"
                    );

                    ?>

                </span>

            </div>

        </div>

    </div>


    <!-- ======================================
    RIGHT - LOGOUT
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

        <div

        class="text-center">

            <!-- Logout Icon -->

            <i

            class="

            ri-logout-box-r-line

            text-6xl

            text-red-400">

            </i>


            <!-- Title -->

            <h2

            class="

            text-3xl

            font-bold

            mt-5">

                Logout

            </h2>


            <!-- Description -->

            <p

            class="

            text-slate-400

            mt-3">

                Are you sure you want to logout?

            </p>


            <!-- ==================================
            ACTION BUTTONS
            =================================== -->

            <div

            class="

            flex

            gap-4

            mt-8">

                <!-- Cancel -->

                <button

                id="cancelLogout"

                type="button"

                class="

                flex-1

                py-4

                rounded-2xl

                bg-white/10

                hover:bg-white/20

                transition-all">

                    Cancel

                </button>


                <!-- Confirm Logout -->

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

                to-pink-600

                hover:scale-[1.02]

                transition-all">

                    Logout

                </a>

            </div>

        </div>

    </div>

</div>