<?php
/* ==========================================================
   VOTIFY
   Admin Logout
========================================================== */

session_start();

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";
require_once "log_activity.php";

/* ==========================================================
   SAVE LOG BEFORE DESTROY SESSION
========================================================== */

if (
    isset($_SESSION["admin_id"]) &&
    isset($_SESSION["admin_username"])
) {

    logActivity(

        $_SESSION["admin_id"],

        $_SESSION["admin_username"],

        "Admin Logout",

        "Administrator logged out successfully."

    );

}

/* ==========================================================
   CLEAR SESSION
========================================================== */

$_SESSION = [];

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(

        session_name(),

        "",

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

session_destroy();

/* ==========================================================
   PREVENT CACHE
========================================================== */

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* ==========================================================
   REDIRECT
========================================================== */

header("Location: ../../pages/admin/login.html");

exit();
?>