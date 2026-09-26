<?php
/* ==========================================================
   VOTIFY
   Delete Candidate
   File : backend/admin/delete-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit();
}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


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
   ELECTION STATUS CHECK
   Candidate deletion is NOT allowed while election is running.
========================================================== */

$statusQuery = "
    SELECT election_status
    FROM election_settings
    WHERE id = 1
    LIMIT 1
";


$statusStmt = mysqli_prepare(
    $conn,
    $statusQuery
);


if (!$statusStmt) {

    error_log(
        "VOTIFY delete-candidate.php: Unable to prepare election status query."
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify election status."
    ]);

    exit();
}


if (!mysqli_stmt_execute($statusStmt)) {

    mysqli_stmt_close($statusStmt);

    error_log(
        "VOTIFY delete-candidate.php: Election status query failed."
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify election status."
    ]);

    exit();
}


mysqli_stmt_bind_result(
    $statusStmt,
    $electionStatus
);


$statusFound = mysqli_stmt_fetch(
    $statusStmt
);


mysqli_stmt_close(
    $statusStmt
);


/* ==========================================================
   DEFAULT STATUS
========================================================== */

if (!$statusFound || !$electionStatus) {

    $electionStatus = "Ready";
}


/* ==========================================================
   BLOCK DELETE WHILE ELECTION IS RUNNING
========================================================== */

if ($electionStatus === "Started") {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Deleting candidates is disabled while the election is running."
    ]);

    exit();
}


/* ==========================================================
   REQUEST VALIDATION
========================================================== */

$candidateId = intval(
    $_POST["candidateId"] ?? 0
);


if ($candidateId <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate."
    ]);

    exit();
}


/* ==========================================================
   FETCH CANDIDATE
========================================================== */

$query = "
    SELECT
        full_name,
        admission_no
    FROM candidates
    WHERE id = ?
    LIMIT 1
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


if (!$stmt) {

    error_log(
        "VOTIFY delete-candidate.php: Candidate lookup prepare failed."
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $candidateId
);


if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to find candidate."
    ]);

    exit();
}


/* ==========================================================
   FETCH RESULT USING BIND RESULT
   Better compatibility with shared hosting.
========================================================== */

mysqli_stmt_bind_result(
    $stmt,
    $candidateName,
    $candidateAdmissionNo
);


if (!mysqli_stmt_fetch($stmt)) {

    mysqli_stmt_close($stmt);

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Candidate not found."
    ]);

    exit();
}


mysqli_stmt_close($stmt);


/* ==========================================================
   DELETE DATABASE RECORD
========================================================== */

$query = "
    DELETE FROM candidates
    WHERE id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


if (!$stmt) {

    error_log(
        "VOTIFY delete-candidate.php: Delete prepare failed."
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare delete."
    ]);

    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $candidateId
);


if (!mysqli_stmt_execute($stmt)) {

    error_log(
        "VOTIFY delete-candidate.php: Delete failed - "
        . mysqli_stmt_error($stmt)
    );

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to delete candidate."
    ]);

    exit();
}


/* ==========================================================
   VERIFY DELETE
========================================================== */

$affectedRows = mysqli_stmt_affected_rows(
    $stmt
);


mysqli_stmt_close(
    $stmt
);


if ($affectedRows !== 1) {

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Candidate was not deleted."
    ]);

    exit();
}


/* ==========================================================
   NOTE
   Candidate photo is stored inside the candidates table
   as MEDIUMBLOB.

   Deleting the candidate row also removes the stored photo.
   No local file deletion is required.
========================================================== */


/* ==========================================================
   ADMIN LOG
========================================================== */

$adminId = intval(
    $_SESSION["admin_id"]
);


$admin = trim(
    $_SESSION["admin_username"] ?? "Admin"
);


$ip = $_SERVER["REMOTE_ADDR"] ?? "Unknown";


$admin = substr(
    $admin,
    0,
    100
);


$ip = substr(
    $ip,
    0,
    45
);


$action = "Candidate Deleted";


$description =
    "Deleted candidate : "
    . $candidateName
    . " ("
    . $candidateAdmissionNo
    . ")";


$logQuery = "
    INSERT INTO admin_logs (
        admin_id,
        admin_username,
        action,
        description,
        ip_address
    )
    VALUES (
        ?,
        ?,
        ?,
        ?,
        ?
    )
";


$logStmt = mysqli_prepare(
    $conn,
    $logQuery
);


if ($logStmt) {

    mysqli_stmt_bind_param(
        $logStmt,
        "issss",
        $adminId,
        $admin,
        $action,
        $description,
        $ip
    );


    if (!mysqli_stmt_execute($logStmt)) {

        error_log(
            "VOTIFY delete-candidate.php: Admin log failed - "
            . mysqli_stmt_error($logStmt)
        );
    }


    mysqli_stmt_close(
        $logStmt
    );
}


/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([
    "success" => true,
    "message" => "Candidate deleted successfully."
]);

exit();

?>