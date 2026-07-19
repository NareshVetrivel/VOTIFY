<?php
/* ==========================================================
   VOTIFY
   Student Page Guard
   File : backend/student/page_guard.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}

/* ==========================================================
   ALLOWED ACCESS
========================================================== */

if (

    !isset($_SESSION["page_access"]) ||

    $_SESSION["page_access"] !== true

){

    header("Location: ../../index.html");

    exit();

}

/* ==========================================================
   DESTROY TOKEN
========================================================== */

unset($_SESSION["page_access"]);