<?php
/* ==========================================================
   VOTIFY
   Check Election Not Started Access
   File : pages/student/check-election-not-started.php
========================================================== */

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */

/* ==========================================================
   GET ELECTION STATUS
========================================================== */

$query = "

SELECT

    election_status,

    updated_at

FROM election_settings

LIMIT 1

";

$result = mysqli_query(

    $conn,

    $query

);

/* ==========================================================
   DATABASE ERROR
========================================================== */

if(!$result){

    exit("Unable to verify election status.");

}

/* ==========================================================
   NO CONFIGURATION
========================================================== */

if(mysqli_num_rows($result) == 0){

    return true;

}

/* ==========================================================
   FETCH STATUS
========================================================== */

$row = mysqli_fetch_assoc($result);

$status = strtolower(

    trim($row["election_status"])

);

$updatedAt = strtotime(

    $row["updated_at"]

);

$currentTime = time();

/* ==========================================================
   READY
========================================================== */

if($status === "ready"){

    return true;

}

/* ==========================================================
   STARTED
========================================================== */

if($status === "started"){

    header("Location: student_login.php");

    exit();

}

/* ==========================================================
   STOPPED
========================================================== */

if($status === "stopped"){

    $difference =

        $currentTime - $updatedAt;

    /* --------------------------------------
       Less Than One Hour
    --------------------------------------- */

    if($difference < 3600){

        header(

            "Location: election_has_ended.php"

        );

        exit();

    }

    /* --------------------------------------
       After One Hour
    --------------------------------------- */

    return true;

}

/* ==========================================================
   INVALID STATUS
========================================================== */

exit("Invalid election status.");

?>