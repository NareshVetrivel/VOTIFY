<?php
/* ==========================================================
   VOTIFY
   Voting
   File : pages/student/voting.php
========================================================== */

/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if (
    !isset($_SESSION["student_logged_in"]) ||
    $_SESSION["student_logged_in"] !== true
) {
    header("Location: student_login.php");
    exit();
}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/* ==========================================================
   GET ACTIVE CANDIDATES
========================================================== */

$query = "
SELECT
    id,
    student_id,
    admission_no,
    full_name,
    department,
    year,
    manifesto,
    photo
FROM candidates
WHERE status = 'Active'
ORDER BY id ASC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Unable to load candidates.");
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
        Voting | VOTIFY
    </title>

    <!-- ==========================================================
    TAILWIND
    ========================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ==========================================================
    REMIX ICON
    ========================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
        rel="stylesheet">

    <!-- ==========================================================
    CUSTOM CSS
    ========================================================== -->

    <link
        rel="stylesheet"
        href="../../assets/css/custom.css">

    <link
        rel="stylesheet"
        href="../../assets/css/animations.css">

</head>

<body
    id="votingPage"
    class="bg-[#0B1020] text-white min-h-screen overflow-x-hidden">

    <!-- ==========================================================
    LOADER
    ========================================================== -->

    <div id="loader-container"></div>

    <!-- ==========================================================
    BACKGROUND
    ========================================================== -->

    <div class="fixed inset-0 -z-10 overflow-hidden">

        <div
            class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 blur-[150px] rounded-full">
        </div>

        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-pink-600/20 blur-[150px] rounded-full">
        </div>

        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-600/20 blur-[130px] rounded-full">
        </div>

    </div>

    <!-- ==========================================================
    HEADER
    ========================================================== -->

    <?php require "../../components/header.html"; ?>

    <!-- ==========================================================
    MAIN
    ========================================================== -->

    <main>

        <?php
        require "../../components/candidate_selection_section.php";
        ?>

        <?php
        require "../../components/candidate_confirmation_section.php";
        ?>

    </main>

    <!-- ==========================================================
    SECURITY ENTRY MODAL
    ========================================================== -->

    <?php require "../../components/security_entry_modal.php"; ?>

    <!-- ==========================================================
    SECURITY MODAL
    ========================================================== -->

    <?php require "../../components/security_modal.php"; ?>

    <!-- ==========================================================
    FOOTER
    ========================================================== -->

    <?php require "../../components/footer.html"; ?>

    <!-- ==========================================================
    APP JS
    ========================================================== -->

    <script src="../../assets/js/app.js"></script>

    <!-- ==========================================================
    SECURITY GUARD
    ========================================================== -->

    <script src="../../assets/js/security_guard.js"></script>

<!-- ==========================================================
VOTING JS
========================================================== -->

<script src="../../assets/js/voting.js"></script>

<!-- ==========================================================
CANDIDATE CONFIRMATION JS
========================================================== -->

<script src="../../assets/js/candidate_confirmation.js"></script>

</body>

</html>