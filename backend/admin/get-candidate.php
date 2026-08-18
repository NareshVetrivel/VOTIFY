<?php
/* ==========================================================
   VOTIFY
   Get Candidate Details
   File : backend/admin/get-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================================
   SESSION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

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
   VALIDATION
========================================================== */

$id = intval(
    $_GET["id"] ?? 0
);

if ($id <= 0) {

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

    id,
    student_id,
    admission_no,
    full_name,
    department,
    year,
    manifesto,
    status

FROM candidates

WHERE id = ?

LIMIT 1

";

$stmt = mysqli_prepare(
    $conn,
    $query
);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit();

}

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $id

);

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch candidate."
    ]);

    exit();

}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch candidate."
    ]);

    exit();

}

if (mysqli_num_rows($result) === 0) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Candidate not found."
    ]);

    exit();

}

$candidate = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "candidate" => $candidate

]);

exit();

?>