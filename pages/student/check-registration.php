<?php
/* ==========================================================
   VOTIFY
   Check Registration Access
   File : pages/student/check-registration.php
========================================================== */

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/* ==========================================================
   GET ELECTION STATUS
========================================================== */

$query = "

SELECT

    election_status

FROM election_settings

LIMIT 1

";

$result = mysqli_query($conn, $query);

/* ==========================================================
   DATABASE ERROR
========================================================== */

if(!$result){

    exit("Unable to verify election status.");

}

/* ==========================================================
   NO RECORD FOUND
========================================================== */

if(mysqli_num_rows($result) == 0){

    exit("Election configuration not found.");

}

/* ==========================================================
   FETCH STATUS
========================================================== */

$row = mysqli_fetch_assoc($result);

$status = strtolower(trim($row["election_status"]));

/* ==========================================================
   REGISTRATION ACCESS
========================================================== */

/* --------------------------
   READY
   Registration Allowed
--------------------------- */

if($status === "ready"){

    return true;

}

/* --------------------------
   STARTED
   Registration Blocked
--------------------------- */

if($status === "started"){

    header(

        "Location: registration_closed.php"

    );

    exit();

}

/* --------------------------
   STOPPED
   Registration Allowed
--------------------------- */

if($status === "stopped"){

    return true;

}

/* ==========================================================
   INVALID STATUS
========================================================== */

exit("Invalid election status.");

?>