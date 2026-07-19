<?php
/* ==========================================================
   VOTIFY
   Delete Candidate
   File : backend/admin/delete-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================================
   SESSION PROTECTION
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
   REQUEST VALIDATION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit();

}

$candidateId = intval($_POST["candidateId"] ?? 0);

if ($candidateId <= 0) {

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
admission_no,
photo

FROM candidates

WHERE id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $candidateId

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
   DELETE DATABASE RECORD
========================================================== */

$query = "

DELETE FROM candidates

WHERE id = ?

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $candidateId

);

if (!mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to delete candidate."
    ]);

    exit();

}

/* ==========================================================
   DELETE PHOTO
========================================================== */

$photoPath =

"../../uploads/candidates/"

.

$candidate["photo"];

if (

    !empty($candidate["photo"])

    &&

    file_exists($photoPath)

) {

    unlink($photoPath);

}

/* ==========================================================
   ADMIN LOG
========================================================== */

$adminId = $_SESSION["admin_id"];

$admin =

$_SESSION["admin_username"]

??

"Admin";

$ip =

$_SERVER["REMOTE_ADDR"]

??

"Unknown";

$action =

"Candidate Deleted";

$description =

"Deleted candidate : "

.

$candidate["full_name"]

.

" ("

.

$candidate["admission_no"]

.

")";

$logQuery = "

INSERT INTO admin_logs(

admin_id,

admin_username,

action,

description,

ip_address

)

VALUES(

?,?,?,?,?

)

";

$logStmt = mysqli_prepare(

    $conn,

    $logQuery

);

mysqli_stmt_bind_param(

    $logStmt,

    "issss",

    $adminId,

    $admin,

    $action,

    $description,

    $ip

);

mysqli_stmt_execute($logStmt);

/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Candidate deleted successfully."

]);

exit();