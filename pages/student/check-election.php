<?php
/* ==========================================================
   VOTIFY
   Check Election Status
   File : pages/student/check-election.php
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
   NO RECORD
========================================================== */

if(mysqli_num_rows($result)==0){

$_SESSION["allow_status_page"] = true;

header(
    "Location: election_not_started.php"
);

exit();

}

/* ==========================================================
   FETCH STATUS
========================================================== */

$election = mysqli_fetch_assoc($result);

$status = trim($election["election_status"]);

$updatedAt = strtotime(

    $election["updated_at"]

);

$currentTime = time();

/* ==========================================================
   READY
========================================================== */

if($status === "Ready"){

    $_SESSION["allow_status_page"] = true;

    header(
        "Location: election_not_started.php"
    );

    exit();

}
/* ==========================================================
   STARTED
========================================================== */

if($status === "Started"){

    return true;

}

/* ==========================================================
   STOPPED
========================================================== */

if($status === "Stopped"){

    $difference =

    $currentTime - $updatedAt;

    /* ==========================================
       LESS THAN 1 HOUR
    ========================================== */

if($difference < 3600){

    $_SESSION["allow_status_page"] = true;

    header(
        "Location: election_has_ended.php"
    );

    exit();

}

    /* ==========================================
       AFTER 1 HOUR
    ========================================== */

$_SESSION["allow_status_page"] = true;

header(
    "Location: election_not_started.php"
);

exit();

}

/* ==========================================================
   INVALID STATUS
========================================================== */

exit("Invalid election status.");