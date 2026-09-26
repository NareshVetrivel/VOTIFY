<?php
/* ==========================================================
   VOTIFY
   Approve Student Request
   File : backend/admin/approve-request.php

   Election Rule:
   Ready   = Approval Allowed
   Started = Approval Disabled
   Stopped = Approval Allowed
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
   REQUEST METHOD
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method Not Allowed"
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
   INPUT
========================================================== */

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id || $id <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Student"
    ]);

    exit();
}


/* ==========================================================
   ELECTION STATUS CHECK
========================================================== */

/*
 * Election workflow:
 *
 * Ready   -> Approval allowed
 * Started -> Approval disabled
 * Stopped -> Approval allowed
 *
 * This is a BACKEND security check.
 *
 * Even if frontend JavaScript is bypassed,
 * approval remains blocked while election is running.
 */

$statusStatement = mysqli_prepare(
    $conn,
    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"
);


if (!$statusStatement) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare election status check."
    ]);

    exit();
}


if (!mysqli_stmt_execute($statusStatement)) {

    mysqli_stmt_close($statusStatement);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check election status."
    ]);

    exit();
}


mysqli_stmt_bind_result(
    $statusStatement,
    $electionStatus
);


$hasStatus =
    mysqli_stmt_fetch(
        $statusStatement
    );


mysqli_stmt_close(
    $statusStatement
);


if (!$hasStatus) {

    /*
     * If election settings are unavailable,
     * fail safely instead of allowing approval.
     */

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Election status is unavailable."
    ]);

    exit();
}


$electionStatus =
    trim(
        (string) $electionStatus
    );


/* ==========================================================
   BLOCK APPROVAL DURING RUNNING ELECTION
========================================================== */

if ($electionStatus === "Started") {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Student approval is disabled while the election is running."
    ]);

    exit();
}


/* ==========================================================
   VALIDATE ELECTION STATUS
========================================================== */

$allowedStatuses = [
    "Ready",
    "Started",
    "Stopped"
];


if (
    !in_array(
        $electionStatus,
        $allowedStatuses,
        true
    )
) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Invalid election status."
    ]);

    exit();
}


/* ==========================================================
   FIND STUDENT
========================================================== */

$studentStatement = mysqli_prepare(
    $conn,
    "SELECT full_name, status
     FROM students
     WHERE id = ?
     LIMIT 1"
);


if (!$studentStatement) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare student lookup."
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $studentStatement,
    "i",
    $id
);


if (!mysqli_stmt_execute($studentStatement)) {

    mysqli_stmt_close($studentStatement);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to find student."
    ]);

    exit();
}


mysqli_stmt_bind_result(
    $studentStatement,
    $studentName,
    $studentStatus
);


$studentFound =
    mysqli_stmt_fetch(
        $studentStatement
    );


mysqli_stmt_close(
    $studentStatement
);


if (!$studentFound) {

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit();
}


/* ==========================================================
   STUDENT STATUS CHECK
========================================================== */

/*
 * Only Pending requests should be approved.
 *
 * This prevents accidentally changing an already
 * processed student back to Approved.
 */

if ($studentStatus !== "Pending") {

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => "This student request has already been processed."
    ]);

    exit();
}


/* ==========================================================
   UPDATE STUDENT STATUS
========================================================== */

$updateStatement = mysqli_prepare(
    $conn,
    "UPDATE students
     SET status = 'Approved'
     WHERE id = ?
       AND status = 'Pending'"
);


if (!$updateStatement) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare approval."
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $updateStatement,
    "i",
    $id
);


if (
    !mysqli_stmt_execute(
        $updateStatement
    )
) {

    mysqli_stmt_close(
        $updateStatement
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to approve student."
    ]);

    exit();
}


$affectedRows =
    mysqli_stmt_affected_rows(
        $updateStatement
    );


mysqli_stmt_close(
    $updateStatement
);


/* ==========================================================
   VERIFY UPDATE
========================================================== */

if ($affectedRows !== 1) {

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => "Student request could not be approved."
    ]);

    exit();
}


/* ==========================================================
   ACTIVITY LOG
========================================================== */

$adminId =
    $_SESSION["admin_id"] ?? 0;


$adminUsername =
    $_SESSION["admin_username"] ?? "Admin";


logActivity(
    $adminId,
    $adminUsername,
    "Student Approved",
    $studentName . " registration approved."
);


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

http_response_code(200);

echo json_encode([
    "success" => true,
    "message" => "Student Approved Successfully."
]);

exit();