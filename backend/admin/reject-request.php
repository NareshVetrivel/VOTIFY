<?php
/* ==========================================================
   VOTIFY
   Reject Student Request
   Election Running Lock
========================================================== */

session_start();

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   ADMIN AUTHENTICATION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    http_response_code(401);

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

require_once "log_activity.php";


/* ==========================================================
   GET STUDENT ID
========================================================== */

$id = intval(
    $_POST["id"] ?? 0
);


if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Student"
    ]);

    exit();

}


/* ==========================================================
   ELECTION STATUS CHECK
   Started = Election Running
   Rejection is NOT allowed while election is running.
========================================================== */

$statusResult = mysqli_query(

    $conn,

    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"

);


if (!$statusResult) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check election status."
    ]);

    exit();

}


$statusRow = mysqli_fetch_assoc(
    $statusResult
);


$electionStatus = $statusRow["election_status"] ?? "Ready";


/* ==========================================================
   BLOCK REJECTION DURING RUNNING ELECTION
========================================================== */

if ($electionStatus === "Started") {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Student rejection is disabled while the election is running."
    ]);

    exit();

}


/* ==========================================================
   GET STUDENT DETAILS
========================================================== */

$result = mysqli_query(

    $conn,

    "SELECT full_name
     FROM students
     WHERE id=$id
     LIMIT 1"

);


if (
    !$result ||
    mysqli_num_rows($result) == 0
) {

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit();

}


$student = mysqli_fetch_assoc(
    $result
);


/* ==========================================================
   DELETE REJECTED STUDENT
========================================================== */

/*
 * IMPORTANT:
 *
 * Rejected student registrations are completely
 * removed from the students table.
 *
 * This releases UNIQUE fields such as:
 *
 * - admission_no
 * - phone
 * - college_email
 *
 * Therefore the student can register again later.
 */

$delete = mysqli_query(

    $conn,

    "DELETE FROM students
     WHERE id=$id"

);


if (!$delete) {

    error_log(
        "VOTIFY Reject Student Delete Error: " .
        mysqli_error($conn)
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to reject."
    ]);

    exit();

}


/* ==========================================================
   LOG ACTIVITY
========================================================== */

logActivity(

    $_SESSION["admin_id"],

    $_SESSION["admin_username"],

    "Student Rejected",

    $student["full_name"] .
    " registration rejected."

);


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Student Rejected Successfully."

]);

exit();

?>