<?php
/* ==========================================================
   VOTIFY
   Dashboard Status API
   File : backend/admin/dashboard-status.php
========================================================== */

session_start();

header("Content-Type: application/json");


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   DEFAULT RESPONSE VALUES
========================================================== */

$total = 0;

$pending = 0;

$approved = 0;

$status = "Ready";

$startTimestamp = null;

$stopTimestamp = null;


/* ==========================================================
   TOTAL STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM students"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total = (int) $row["total"];

}


/* ==========================================================
   PENDING STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Pending'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $pending = (int) $row["total"];

}


/* ==========================================================
   APPROVED STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Approved'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $approved = (int) $row["total"];

}


/* ==========================================================
   GET CURRENT ELECTION STATUS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"
);

if (
    $result &&
    mysqli_num_rows($result) > 0
) {

    $row = mysqli_fetch_assoc($result);

    $status = $row["election_status"];

}


/* ==========================================================
   GET LATEST ELECTION START EVENT
========================================================== */

/*
   Find the most recent Election Started event.

   This represents the beginning of the
   current / latest election cycle.
*/

$startSql = "
    SELECT
        UNIX_TIMESTAMP(created_at) AS start_timestamp
    FROM admin_logs
    WHERE action = 'Election Started'
    ORDER BY id DESC
    LIMIT 1
";

$startResult = mysqli_query(
    $conn,
    $startSql
);

if (
    $startResult &&
    mysqli_num_rows($startResult) > 0
) {

    $startRow = mysqli_fetch_assoc(
        $startResult
    );

    if (
        $startRow["start_timestamp"] !== null
    ) {

        $startTimestamp =
            (int) $startRow["start_timestamp"];

    }

}


/* ==========================================================
   GET LATEST ELECTION STOP EVENT
========================================================== */

/*
   Important:

   We only need a stop timestamp if the
   current election status is Stopped.

   If the election has been started again
   after a previous stop event, the previous
   stop time must NOT be used.
*/

if ($status === "Stopped") {

    $stopSql = "
        SELECT
            UNIX_TIMESTAMP(created_at) AS stop_timestamp
        FROM admin_logs
        WHERE action = 'Election Stopped'
        ORDER BY id DESC
        LIMIT 1
    ";

    $stopResult = mysqli_query(
        $conn,
        $stopSql
    );

    if (
        $stopResult &&
        mysqli_num_rows($stopResult) > 0
    ) {

        $stopRow = mysqli_fetch_assoc(
            $stopResult
        );

        if (
            $stopRow["stop_timestamp"] !== null
        ) {

            $stopTimestamp =
                (int) $stopRow["stop_timestamp"];

        }

    }

}


/* ==========================================================
   READY STATUS
========================================================== */

/*
   When the election is Ready,
   there should be no active timer.

   So we explicitly reset both timestamps.
*/

if ($status === "Ready") {

    $startTimestamp = null;

    $stopTimestamp = null;

}


/* ==========================================================
   RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "status" => $status,

    "startTimestamp" => $startTimestamp,

    "stopTimestamp" => $stopTimestamp,

    "total" => $total,

    "pending" => $pending,

    "approved" => $approved

]);


/* ==========================================================
   CLOSE CONNECTION
========================================================== */

mysqli_close($conn);

exit;

?>