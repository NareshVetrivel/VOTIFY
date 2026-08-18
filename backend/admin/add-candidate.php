<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/* ==========================================================
   VOTIFY
   Add Candidate
   File : backend/admin/add-candidate.php
   Storage : Aiven Cloud MySQL
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

/** @var mysqli $conn */


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


/* ==========================================================
   RECEIVE FORM DATA
========================================================== */

$studentId = trim(
    $_POST["studentId"] ?? ""
);

$admissionNo = strtoupper(
    trim($_POST["admissionNo"] ?? "")
);

$manifesto = trim(
    $_POST["manifesto"] ?? ""
);


/* ==========================================================
   REQUIRED FIELD VALIDATION
========================================================== */

if (

    empty($studentId) ||

    empty($admissionNo) ||

    empty($manifesto)

) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);

    exit();

}


/* ==========================================================
   PHOTO VALIDATION
========================================================== */

if (

    !isset($_FILES["candidatePhoto"]) ||

    $_FILES["candidatePhoto"]["error"] !== UPLOAD_ERR_OK

) {

    echo json_encode([
        "success" => false,
        "message" => "Candidate photo is required."
    ]);

    exit();

}


/* ==========================================================
   VERIFY STUDENT
========================================================== */

$query = "

SELECT

    id,
    full_name,
    admission_no,
    department,
    year,
    status

FROM students

WHERE

    id = ?

AND

    admission_no = ?

LIMIT 1

";

$stmt = mysqli_prepare(
    $conn,
    $query
);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit();

}

mysqli_stmt_bind_param(

    $stmt,

    "is",

    $studentId,

    $admissionNo

);

if (!mysqli_stmt_execute($stmt)) {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => $error
    ]);

    exit();

}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify student."
    ]);

    exit();

}

if (mysqli_num_rows($result) === 0) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit();

}

$student = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ==========================================================
   APPROVED STUDENT CHECK
========================================================== */

if ($student["status"] !== "Approved") {

    echo json_encode([
        "success" => false,
        "message" => "Only approved students can become candidates."
    ]);

    exit();

}


/* ==========================================================
   DUPLICATE CANDIDATE CHECK
========================================================== */

$query = "

SELECT id

FROM candidates

WHERE student_id = ?

LIMIT 1

";

$stmt = mysqli_prepare(
    $conn,
    $query
);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit();

}

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $studentId

);

if (!mysqli_stmt_execute($stmt)) {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => $error
    ]);

    exit();

}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check candidate."
    ]);

    exit();

}

if (mysqli_num_rows($result) > 0) {

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => "This student is already a candidate."
    ]);

    exit();

}

mysqli_stmt_close($stmt);


/* ==========================================================
   IMAGE VALIDATION
========================================================== */

$photo = $_FILES["candidatePhoto"];

$fileSize = $photo["size"];

$fileTmp = $photo["tmp_name"];


/* ==========================================================
   FILE SIZE CHECK
========================================================== */

if ($fileSize <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate photo."
    ]);

    exit();

}

if ($fileSize > 2 * 1024 * 1024) {

    echo json_encode([
        "success" => false,
        "message" => "Photo size must be less than 2 MB."
    ]);

    exit();

}


/* ==========================================================
   REAL IMAGE TYPE CHECK
========================================================== */

if (!is_uploaded_file($fileTmp)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid uploaded photo."
    ]);

    exit();

}

$finfo = new finfo(FILEINFO_MIME_TYPE);

$photoType = $finfo->file($fileTmp);

$allowedTypes = [

    "image/jpeg",
    "image/png"

];

if (!in_array($photoType, $allowedTypes, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Only JPG, JPEG and PNG files are allowed."
    ]);

    exit();

}


/* ==========================================================
   READ IMAGE
========================================================== */

$photoData = file_get_contents($fileTmp);

if ($photoData === false) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to read candidate photo."
    ]);

    exit();

}


/* ==========================================================
   INSERT CANDIDATE
========================================================== */

$query = "

INSERT INTO candidates (

    student_id,
    admission_no,
    full_name,
    department,
    year,
    manifesto,
    photo,
    photo_type

)

VALUES (

    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?

)

";

$stmt = mysqli_prepare(
    $conn,
    $query
);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit();

}


/* ==========================================================
   BIND CANDIDATE DATA
========================================================== */

mysqli_stmt_bind_param(

    $stmt,

    "isssssss",

    $student["id"],

    $student["admission_no"],

    $student["full_name"],

    $student["department"],

    $student["year"],

    $manifesto,

    $photoData,

    $photoType

);


/* ==========================================================
   EXECUTE INSERT
========================================================== */

if (!mysqli_stmt_execute($stmt)) {

    $error = mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    echo json_encode([
        "success" => false,
        "message" => $error
    ]);

    exit();

}

mysqli_stmt_close($stmt);


/* ==========================================================
   ADMIN LOG
========================================================== */

$admin =

    $_SESSION["admin_username"]

    ?? "Admin";

$ip =

    $_SERVER["REMOTE_ADDR"]

    ?? "Unknown";

$action =

    "Candidate Added";

$description =

    "Added candidate : "

    .

    $student["full_name"]

    .

    " ("

    .

    $student["admission_no"]

    .

    ")";

$adminId =

    $_SESSION["admin_id"];


/* ==========================================================
   INSERT ADMIN LOG
========================================================== */

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

    mysqli_stmt_execute($logStmt);

    mysqli_stmt_close($logStmt);

}


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Candidate added successfully."

]);

exit();

?>