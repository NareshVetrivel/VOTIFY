<?php
/* ==========================================================
   VOTIFY
   Get Candidate Details
   File : backend/admin/get-candidate.php
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
   REQUEST VALIDATION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit();

}


/* ==========================================================
   CANDIDATE ID
========================================================== */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($id <= 0) {

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

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit();

}


/* ==========================================================
   BIND CANDIDATE ID
========================================================== */

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


/* ==========================================================
   EXECUTE
========================================================== */

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch candidate."
    ]);

    exit();

}


/* ==========================================================
   GET RESULT
========================================================== */

$result = mysqli_stmt_get_result($stmt);


if (!$result) {

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to read candidate data."
    ]);

    exit();

}


/* ==========================================================
   CHECK CANDIDATE
========================================================== */

if (mysqli_num_rows($result) === 0) {

    mysqli_stmt_close($stmt);

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Candidate not found."
    ]);

    exit();

}


/* ==========================================================
   FETCH CANDIDATE
========================================================== */

$candidate = mysqli_fetch_assoc($result);


/* ==========================================================
   CLOSE STATEMENT
========================================================== */

mysqli_stmt_close($stmt);


/* ==========================================================
   NORMALIZE RESPONSE
========================================================== */

$candidate["id"] =
    (int) ($candidate["id"] ?? 0);

$candidate["student_id"] =
    (int) ($candidate["student_id"] ?? 0);

$candidate["admission_no"] =
    (string) ($candidate["admission_no"] ?? "");

$candidate["full_name"] =
    (string) ($candidate["full_name"] ?? "");

$candidate["department"] =
    (string) ($candidate["department"] ?? "");

$candidate["year"] =
    (string) ($candidate["year"] ?? "");

$candidate["manifesto"] =
    (string) ($candidate["manifesto"] ?? "");

$candidate["status"] =
    (string) ($candidate["status"] ?? "");


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode(
    [
        "success" => true,
        "candidate" => $candidate
    ],
    JSON_UNESCAPED_UNICODE
);

exit();

?>