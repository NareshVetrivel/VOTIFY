<?php
/* ==========================================================
   VOTIFY
   Check Login Access
   File : pages/student/check-login.php
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   DATABASE CONNECTION CHECK
========================================================== */

if (
    !isset($conn) ||
    !($conn instanceof mysqli)
) {

    exit(
        "Unable to verify election status."
    );

}


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


$result =
    mysqli_query(
        $conn,
        $query
    );


/* ==========================================================
   DATABASE ERROR
========================================================== */

if (!$result) {

    error_log(
        "VOTIFY CHECK LOGIN ERROR: " .
        mysqli_error($conn)
    );


    exit(
        "Unable to verify election status."
    );

}


/* ==========================================================
   NO ELECTION CONFIGURATION
========================================================== */

if (
    mysqli_num_rows($result) === 0
) {

    mysqli_free_result($result);


    header(
        "Location: election_not_started.php"
    );

    exit();

}


/* ==========================================================
   FETCH ELECTION STATUS
========================================================== */

$row =
    mysqli_fetch_assoc(
        $result
    );


mysqli_free_result(
    $result
);


$status =
    strtolower(
        trim(
            (string)(
                $row["election_status"] ?? ""
            )
        )
    );


$updatedAt =
    strtotime(
        (string)(
            $row["updated_at"] ?? ""
        )
    );


$currentTime =
    time();


/* ==========================================================
   READY
========================================================== */

/*
 * Election is configured but voting
 * has not started yet.
 */

if (
    $status === "ready"
) {

    header(
        "Location: election_not_started.php"
    );

    exit();

}


/* ==========================================================
   STARTED
========================================================== */

/*
 * Election is currently active.
 *
 * Allow student_login.php to continue.
 */

if (
    $status === "started"
) {

    return true;

}


/* ==========================================================
   STOPPED
========================================================== */

/*
 * Election has been stopped.
 */

if (
    $status === "stopped"
) {


    /* ------------------------------------------------------
       INVALID / MISSING UPDATED TIME
    ------------------------------------------------------ */

    if (
        $updatedAt === false
    ) {

        header(
            "Location: election_not_started.php"
        );

        exit();

    }


    $difference =
        $currentTime -
        $updatedAt;


    /* ------------------------------------------------------
       LESS THAN ONE HOUR
    ------------------------------------------------------ */

    if (
        $difference < 3600
    ) {

        header(
            "Location: election_has_ended.php"
        );

        exit();

    }


    /* ------------------------------------------------------
       AFTER ONE HOUR
    ------------------------------------------------------ */

    header(
        "Location: election_not_started.php"
    );

    exit();

}


/* ==========================================================
   INVALID ELECTION STATUS
========================================================== */

error_log(
    "VOTIFY CHECK LOGIN: Invalid election status - " .
    $status
);


exit(
    "Invalid election status."
);

?>