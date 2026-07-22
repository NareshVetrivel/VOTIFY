<?php
/* ==========================================================
   VOTIFY
   Check Registration Closed Access
   File : pages/student/check-registration-closed.php
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
   NO RECORD
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
   ACCESS CONTROL
========================================================== */

/* ------------------------------------------
   Election Started
   Registration Closed page allowed
------------------------------------------ */

if($status === "started"){

    return true;

}

/* ------------------------------------------
   Election Ready
   Redirect to Register
------------------------------------------ */

if($status === "ready"){

    header("Location: student_register.php");

    exit();

}

/* ------------------------------------------
   Election Stopped
   Redirect to Register
------------------------------------------ */

if($status === "stopped"){

    header("Location: student_register.php");

    exit();

}

/* ==========================================================
   INVALID STATUS
========================================================== */

exit("Invalid election status.");