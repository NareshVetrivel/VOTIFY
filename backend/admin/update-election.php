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

if (
    !isset($_SESSION["admin_id"]) ||
    !isset($_SESSION["admin_role"])
) {

    http_response_code(401);

    echo json_encode([

        "success" => false,

        "message" => "Unauthorized access."

    ]);

    exit();

}


/* ==========================================================
   SUPER ADMIN AUTHORIZATION
========================================================== */

/*
 * Election status is a critical system-level operation.
 *
 * Only Super Admin is allowed to:
 * - Start election
 * - Stop election
 * - Change election status
 *
 * This backend validation is important because hiding
 * buttons in the frontend alone is not secure.
 */

if ($_SESSION["admin_role"] !== "Super Admin") {

    http_response_code(403);

    echo json_encode([

        "success" => false,

        "message" =>
            "Access denied. Only the Super Admin can control the election."

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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([

        "success" => false,

        "message" => "Invalid request method."

    ]);

    exit();

}


/* ==========================================================
   GET STATUS
========================================================== */

$status = trim($_POST["status"] ?? "");


/* ==========================================================
   ALLOWED STATUS
========================================================== */

$allowed = [

    "Ready",
    "Started",
    "Stopped"

];


if (!in_array($status, $allowed, true)) {

    http_response_code(400);

    echo json_encode([

        "success" => false,

        "message" => "Invalid election status."

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

    http_response_code(500);

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


$updated = mysqli_stmt_execute($stmt);


mysqli_stmt_close($stmt);


if (!$updated) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
            "Unable to update election status."

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

if ($status === "Started") {

    $logAction = "Election Started";

    $logDescription =
        "Super Administrator started the election.";

}


/* ==========================================================
   ELECTION STOPPED
========================================================== */

elseif ($status === "Stopped") {

    $logAction = "Election Stopped";

    $logDescription =
        "Super Administrator stopped the election.";

}


/* ==========================================================
   WRITE ACTIVITY LOG
========================================================== */

if ($logAction !== null) {

    logActivity(

        (int) $_SESSION["admin_id"],

        $_SESSION["admin_username"] ?? "Super Admin",

        $logAction,

        $logDescription

    );

}


/* ==========================================================
   GET EXACT EVENT TIMESTAMP
========================================================== */

$eventTimestamp = null;


/*
 * Fetch the latest matching event created by the current
 * Super Admin. This timestamp is returned to dashboard.js.
 */

if ($logAction !== null) {

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

        $adminId = (int) $_SESSION["admin_id"];

        mysqli_stmt_bind_param(

            $logStmt,

            "is",

            $adminId,

            $logAction

        );


        if (mysqli_stmt_execute($logStmt)) {

            $logResult =
                mysqli_stmt_get_result($logStmt);


            if (
                $logResult &&
                mysqli_num_rows($logResult) > 0
            ) {

                $logRow =
                    mysqli_fetch_assoc($logResult);


                $eventTimestamp =
                    isset($logRow["event_timestamp"])
                    ? (int) $logRow["event_timestamp"]
                    : null;

            }

        }


        mysqli_stmt_close($logStmt);

    }

}


/* ==========================================================
   FALLBACK SERVER TIMESTAMP
========================================================== */

if (
    $eventTimestamp === null &&
    $logAction !== null
) {

    $eventTimestamp = time();

}


/* ==========================================================
   CLOSE DATABASE
========================================================== */

$conn->close();


/* ==========================================================
   RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "status" => $status,

    "timestamp" => $eventTimestamp,

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