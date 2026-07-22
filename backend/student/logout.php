<?php
/* ==========================================================
   VOTIFY
   Student Logout
   File : backend/student/logout.php
========================================================== */

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   DESTROY SESSION
========================================================== */

$_SESSION = [];

session_unset();

session_destroy();

/* ==========================================================
   PREVENT CACHE
========================================================== */

header("Cache-Control: no-cache, no-store, must-revalidate");

header("Pragma: no-cache");

header("Expires: 0");

/* ==========================================================
   REDIRECT
========================================================== */

header(

    "Location: ../../pages/student/student_login.php"

);

exit();

?>