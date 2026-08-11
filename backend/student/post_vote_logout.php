<?php
/* ==========================================================
   VOTIFY
   Post Vote Secure Logout
   File : backend/student/post_vote_logout.php
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   PREVENT CACHING
========================================================== */

header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Cache-Control: post-check=0, pre-check=0",
    false
);

header(
    "Pragma: no-cache"
);

header(
    "Expires: Sat, 01 Jan 2000 00:00:00 GMT"
);


/* ==========================================================
   REDIRECT MODE
   ----------------------------------------------------------
   Only two destinations are allowed:

   - index  → VOTIFY landing page
   - login  → Student login page

   No user-supplied URL is ever accepted.
========================================================== */

$redirectMode = $_GET["redirect"] ?? "login";


/* ==========================================================
   ALLOWED REDIRECT
========================================================== */

if ($redirectMode === "index") {

    $redirectLocation = "../../index.html";

} else {

    $redirectLocation = "../../pages/student/student_login.php";

}


/* ==========================================================
   REMOVE VOTING SESSION VARIABLES
========================================================== */

unset(
    $_SESSION["vote_submitted"],
    $_SESSION["selected_candidate_id"],
    $_SESSION["selected_candidate_name"]
);


/* ==========================================================
   CLEAR ALL SESSION DATA
========================================================== */

$_SESSION = [];


/* ==========================================================
   DESTROY SESSION COOKIE
========================================================== */

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


/* ==========================================================
   DESTROY SESSION
========================================================== */

session_destroy();


/* ==========================================================
   REDIRECT AFTER COMPLETE LOGOUT
========================================================== */

header(
    "Location: " . $redirectLocation
);

exit();

?>