<?php
/* ==========================================================
   VOTIFY
   Update Election Status
   File : backend/admin/update-election.php
========================================================== */

session_start();

header("Content-Type: application/json");


/* ==========================================================
   SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    echo json_encode([

        "success" => false,

        "message" => "Unauthorized"

    ]);

    exit();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   ACTIVITY LOG
========================================================== */

require_once "log_activity.php";


/* ==========================================================
   REQUEST METHOD
========================================================== */

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    echo json_encode([

        "success" => false,

        "message" => "Invalid Request"

    ]);

    exit();

}


/* ==========================================================
   GET STATUS
========================================================== */

$status =
    $_POST["status"] ?? "";


/* ==========================================================
   ALLOWED STATUS
========================================================== */

$allowed = [

    "Ready",
    "Started",
    "Stopped"

];


if (
    !in_array(
        $status,
        $allowed,
        true
    )
) {

    echo json_encode([

        "success" => false,

        "message" => "Invalid Status"

    ]);

    exit();

}


/* ==========================================================
   UPDATE ELECTION STATUS
========================================================== */

$stmt = mysqli_prepare(

    $conn,

    "UPDATE election_settings
     SET election_status = ?
     WHERE id = 1"

);


if (!$stmt) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Unable to prepare election update."

    ]);

    exit();

}


mysqli_stmt_bind_param(

    $stmt,

    "s",

    $status

);


$updated =
    mysqli_stmt_execute($stmt);


mysqli_stmt_close($stmt);


if (!$updated) {

    echo json_encode([

        "success" => false,

        "message" =>
            mysqli_error($conn)

    ]);

    exit();

}


/* ==========================================================
   EVENT LOGGING
========================================================== */

$logAction = null;

$logDescription = null;


/* ==========================================================
   ELECTION STARTED
========================================================== */

if (
    $status === "Started"
) {

    $logAction =
        "Election Started";

    $logDescription =
        "Administrator started the election.";

}


/* ==========================================================
   ELECTION STOPPED
========================================================== */

elseif (
    $status === "Stopped"
) {

    $logAction =
        "Election Stopped";

    $logDescription =
        "Administrator stopped the election.";

}


/* ==========================================================
   WRITE ACTIVITY LOG
========================================================== */

if (
    $logAction !== null
) {

    logActivity(

        $_SESSION["admin_id"],

        $_SESSION["admin_username"],

        $logAction,

        $logDescription

    );

}


/* ==========================================================
   GET EXACT EVENT TIMESTAMP
========================================================== */

$eventTimestamp = null;


/*
 * We fetch the latest log for the exact action
 * created by the current administrator.
 *
 * This prevents an older election event from being
 * accidentally returned to the dashboard.
 */

if (
    $logAction !== null
) {

    $logStmt = mysqli_prepare(

        $conn,

        "SELECT
            UNIX_TIMESTAMP(created_at) AS event_timestamp
         FROM admin_logs
         WHERE admin_id = ?
           AND action = ?
         ORDER BY id DESC
         LIMIT 1"

    );


    if ($logStmt) {

        mysqli_stmt_bind_param(

            $logStmt,

            "is",

            $_SESSION["admin_id"],

            $logAction

        );


        if (
            mysqli_stmt_execute($logStmt)
        ) {

            $logResult =
                mysqli_stmt_get_result(
                    $logStmt
                );


            if (
                $logResult &&
                mysqli_num_rows($logResult) > 0
            ) {

                $logRow =
                    mysqli_fetch_assoc(
                        $logResult
                    );


                $eventTimestamp =
                    isset(
                        $logRow["event_timestamp"]
                    )
                        ? (int)
                            $logRow["event_timestamp"]
                        : null;

            }

        }


        mysqli_stmt_close($logStmt);

    }

}


/* ==========================================================
   FALLBACK SERVER TIMESTAMP
========================================================== */

/*
 * Normally the timestamp comes from admin_logs.
 * If the log lookup unexpectedly fails, use the
 * current server timestamp as a safe fallback.
 */

if (
    $eventTimestamp === null &&
    $logAction !== null
) {

    $eventTimestamp =
        time();

}


/* ==========================================================
   RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "status" => $status,

    "timestamp" =>
        $eventTimestamp,

    "serverTime" =>
        $eventTimestamp !== null
            ? date(
                "Y-m-d H:i:s",
                $eventTimestamp
            )
            : null

]);

exit();

?>