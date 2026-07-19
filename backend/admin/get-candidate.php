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

/* ==========================================================
   VALIDATION
========================================================== */

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate."
    ]);

    exit();

}

/* ==========================================================
   FETCH
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
photo,
status

FROM candidates

WHERE id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $id

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Candidate not found."
    ]);

    exit();

}

$candidate = mysqli_fetch_assoc($result);

/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "candidate" => $candidate

]);

exit();