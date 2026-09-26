<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/* ==========================================================
   VOTIFY
   Add Candidate
   File : backend/admin/add-candidate.php
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
========================================================== */

/*
 * IMPORTANT:
 *
 * Candidate creation is NOT allowed while the election
 * is running.
 *
 * Frontend locking is only for user experience.
 * This backend check is the real security protection.
 */

$electionStatus =
    "Ready";


$statusQuery = "

    SELECT election_status

    FROM election_settings

    WHERE id = 1

    LIMIT 1

";


$statusStmt =
    mysqli_prepare(
        $conn,
        $statusQuery
    );


if (!$statusStmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check election status."
    ]);

    exit();

}


if (!mysqli_stmt_execute($statusStmt)) {

    mysqli_stmt_close(
        $statusStmt
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check election status."
    ]);

    exit();

}


mysqli_stmt_bind_result(

    $statusStmt,

    $databaseElectionStatus

);


if (
    mysqli_stmt_fetch(
        $statusStmt
    )
) {

    $electionStatus =
        trim(
            $databaseElectionStatus
        );

}


mysqli_stmt_close(
    $statusStmt
);


/* ==========================================================
   BLOCK WHILE ELECTION IS RUNNING
========================================================== */

if (
    $electionStatus === "Started"
) {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Adding candidates is disabled while the election is running."
    ]);

    exit();

}


/* ==========================================================
   RECEIVE FORM DATA
========================================================== */

$studentId =
    trim(
        $_POST["studentId"] ?? ""
    );


$admissionNo =
    strtoupper(
        trim(
            $_POST["admissionNo"] ?? ""
        )
    );


$manifesto =
    trim(
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
   ADMISSION NUMBER VALIDATION
========================================================== */

/*
 * VOTIFY admission number format:
 *
 *     25CAPMCA080
 *
 * Required structure:
 *
 *     2 digits
 *     CAPMCA
 *     3 digits
 *
 * Total:
 *     Exactly 11 characters
 *
 * Examples:
 *
 *     25CAPMCA080  -> VALID
 *     25CAPMCA001  -> VALID
 *     25CAPMCA999  -> VALID
 *
 * Invalid:
 *
 *     25capmca080  -> converted to uppercase before validation
 *     25CAPMCA08   -> only 10 characters
 *     25CAPMCA0801 -> 12 characters
 *     25CAPMCAO80  -> O is not a digit
 *     2CAPMCA080   -> incorrect prefix length
 *     25MCA080     -> missing CAP
 *
 * This backend validation is mandatory because frontend
 * JavaScript validation can be bypassed.
 */


/*
 * First enforce exact length.
 */

if (
    strlen($admissionNo) !== 11
) {

    echo json_encode([
        "success" => false,
        "message" => "Admission number must be exactly 11 characters."
    ]);

    exit();

}


/*
 * Then enforce exact format.
 */

if (
    !preg_match(
        "/^[0-9]{2}CAPMCA[0-9]{3}$/",
        $admissionNo
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid admission number format. Use format 25CAPMCA080."
    ]);

    exit();

}


/* ==========================================================
   STUDENT ID VALIDATION
========================================================== */

if (
    !ctype_digit(
        (string) $studentId
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid student."
    ]);

    exit();

}


$studentId =
    (int) $studentId;


/* ==========================================================
   PHOTO VALIDATION
========================================================== */

if (

    !isset(
        $_FILES["candidatePhoto"]
    )

    ||

    $_FILES["candidatePhoto"]["error"] !==
        UPLOAD_ERR_OK

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


$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify student."
    ]);

    exit();

}


mysqli_stmt_bind_param(

    $stmt,

    "is",

    $studentId,

    $admissionNo

);


if (
    !mysqli_stmt_execute(
        $stmt
    )
) {

    mysqli_stmt_close(
        $stmt
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify student."
    ]);

    exit();

}


$result =
    mysqli_stmt_get_result(
        $stmt
    );


if (!$result) {

    mysqli_stmt_close(
        $stmt
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify student."
    ]);

    exit();

}


if (
    mysqli_num_rows(
        $result
    ) === 0
) {

    mysqli_stmt_close(
        $stmt
    );

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit();

}


$student =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


/* ==========================================================
   APPROVED STUDENT CHECK
========================================================== */

if (
    $student["status"] !==
    "Approved"
) {

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


$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check candidate."
    ]);

    exit();

}


mysqli_stmt_bind_param(

    $stmt,

    "i",

    $studentId

);


if (
    !mysqli_stmt_execute(
        $stmt
    )
) {

    mysqli_stmt_close(
        $stmt
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check candidate."
    ]);

    exit();

}


$result =
    mysqli_stmt_get_result(
        $stmt
    );


if (!$result) {

    mysqli_stmt_close(
        $stmt
    );

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check candidate."
    ]);

    exit();

}


if (
    mysqli_num_rows(
        $result
    ) > 0
) {

    mysqli_stmt_close(
        $stmt
    );

    echo json_encode([
        "success" => false,
        "message" => "This student is already a candidate."
    ]);

    exit();

}


mysqli_stmt_close(
    $stmt
);


/* ==========================================================
   IMAGE VALIDATION
========================================================== */

$photo =
    $_FILES["candidatePhoto"];


$fileSize =
    (int) $photo["size"];


$fileTmp =
    $photo["tmp_name"];


/* ==========================================================
   FILE SIZE CHECK
========================================================== */

if (
    $fileSize <= 0
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate photo."
    ]);

    exit();

}


if (
    $fileSize >
    2 * 1024 * 1024
) {

    echo json_encode([
        "success" => false,
        "message" => "Photo size must be less than 2 MB."
    ]);

    exit();

}


/* ==========================================================
   REAL UPLOAD CHECK
========================================================== */

if (
    !is_uploaded_file(
        $fileTmp
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid uploaded photo."
    ]);

    exit();

}


/* ==========================================================
   MIME TYPE CHECK
========================================================== */

$finfo =
    new finfo(
        FILEINFO_MIME_TYPE
    );


$photoType =
    $finfo->file(
        $fileTmp
    );


$allowedTypes = [

    "image/jpeg",
    "image/png"

];


if (
    !in_array(
        $photoType,
        $allowedTypes,
        true
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Only JPG, JPEG and PNG files are allowed."
    ]);

    exit();

}


/* ==========================================================
   IMAGE CONTENT CHECK
========================================================== */

$imageInfo =
    @getimagesize(
        $fileTmp
    );


if (
    $imageInfo === false
) {

    echo json_encode([
        "success" => false,
        "message" => "Uploaded file is not a valid image."
    ]);

    exit();

}


/* ==========================================================
   READ IMAGE
========================================================== */

$photoData =
    file_get_contents(
        $fileTmp
    );


if (
    $photoData === false
) {

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


$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare candidate registration."
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

if (
    !mysqli_stmt_execute(
        $stmt
    )
) {

    $error =
        mysqli_stmt_error(
            $stmt
        );


    mysqli_stmt_close(
        $stmt
    );


    /*
     * Do not expose raw database errors
     * to the production user.
     */

    error_log(
        "VOTIFY Add Candidate DB Error: " .
        $error
    );


    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to add candidate."
    ]);

    exit();

}


mysqli_stmt_close(
    $stmt
);


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


$logStmt =
    mysqli_prepare(
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


    if (
        !mysqli_stmt_execute(
            $logStmt
        )
    ) {

        error_log(
            "VOTIFY Add Candidate Log Error: " .
            mysqli_stmt_error(
                $logStmt
            )
        );

    }


    mysqli_stmt_close(
        $logStmt
    );

}


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "success" =>
        true,

    "message" =>
        "Candidate added successfully."

]);

exit();

?>