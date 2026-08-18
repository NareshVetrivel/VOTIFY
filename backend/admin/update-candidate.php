<?php
/* ==========================================================
   VOTIFY
   Update Candidate
   File : backend/admin/update-candidate.php
   Storage : Aiven Cloud MySQL
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
   REQUEST VALIDATION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit();

}


$candidateId = intval(
    $_POST["candidateId"] ?? 0
);

$manifesto = trim(
    $_POST["manifesto"] ?? ""
);


if (

    $candidateId <= 0 ||

    $manifesto === ""

) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate data."
    ]);

    exit();

}


/* ==========================================================
   GET CANDIDATE
========================================================== */

$query = "

SELECT

    id,
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

    echo json_encode([
        "success" => false,
        "message" => "Unable to find candidate."
    ]);

    exit();

}


$result = mysqli_stmt_get_result($stmt);


if (!$result || mysqli_num_rows($result) === 0) {

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
   CHECK WHETHER NEW PHOTO IS UPLOADED
========================================================== */

$hasNewPhoto = (

    isset($_FILES["candidatePhoto"]) &&

    $_FILES["candidatePhoto"]["error"] === UPLOAD_ERR_OK

);


/* ==========================================================
   UPDATE MANIFESTO ONLY
   IF NO NEW PHOTO
========================================================== */

if (!$hasNewPhoto) {

    $query = "

    UPDATE candidates

    SET

        manifesto = ?

    WHERE id = ?

    ";


    $stmt = mysqli_prepare(
        $conn,
        $query
    );


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to prepare update."
        ]);

        exit();

    }


    mysqli_stmt_bind_param(

        $stmt,

        "si",

        $manifesto,

        $candidateId

    );


    if (!mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        echo json_encode([
            "success" => false,
            "message" => "Unable to update candidate."
        ]);

        exit();

    }


    mysqli_stmt_close($stmt);

}


/* ==========================================================
   UPDATE PHOTO + MANIFESTO
========================================================== */

else {

    $photo = $_FILES["candidatePhoto"];

    $fileSize = $photo["size"];

    $fileTmp = $photo["tmp_name"];


    /* ======================================================
       FILE SIZE
    ====================================================== */

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


    /* ======================================================
       VERIFY UPLOADED FILE
    ====================================================== */

    if (!is_uploaded_file($fileTmp)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid uploaded photo."
        ]);

        exit();

    }


    /* ======================================================
       REAL IMAGE TYPE CHECK
    ====================================================== */

    $finfo = new finfo(
        FILEINFO_MIME_TYPE
    );


    $photoType = $finfo->file(
        $fileTmp
    );


    $allowedTypes = [

        "image/jpeg",

        "image/png"

    ];


    if (!in_array(

        $photoType,

        $allowedTypes,

        true

    )) {

        echo json_encode([
            "success" => false,
            "message" => "Only JPG, JPEG and PNG files are allowed."
        ]);

        exit();

    }


    /* ======================================================
       READ PHOTO
    ====================================================== */

    $photoData = file_get_contents(
        $fileTmp
    );


    if ($photoData === false) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to read candidate photo."
        ]);

        exit();

    }


    /* ======================================================
       UPDATE DATABASE
    ====================================================== */

    $query = "

    UPDATE candidates

    SET

        manifesto = ?,

        photo = ?,

        photo_type = ?

    WHERE id = ?

    ";


    $stmt = mysqli_prepare(
        $conn,
        $query
    );


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to prepare photo update."
        ]);

        exit();

    }


    mysqli_stmt_bind_param(

        $stmt,

        "sssi",

        $manifesto,

        $photoData,

        $photoType,

        $candidateId

    );


    if (!mysqli_stmt_execute($stmt)) {

        $error = mysqli_stmt_error(
            $stmt
        );

        mysqli_stmt_close($stmt);

        echo json_encode([
            "success" => false,
            "message" => $error
        ]);

        exit();

    }


    mysqli_stmt_close($stmt);

}


/* ==========================================================
   ADMIN LOG
========================================================== */

$adminId =

    $_SESSION["admin_id"];


$admin =

    $_SESSION["admin_username"]

    ?? "Admin";


$ip =

    $_SERVER["REMOTE_ADDR"]

    ?? "Unknown";


$action =

    "Candidate Updated";


$description =

    "Updated candidate : "

    .

    $candidate["full_name"]

    .

    " ("

    .

    $candidate["admission_no"]

    .

    ")";


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


    mysqli_stmt_execute(
        $logStmt
    );


    mysqli_stmt_close(
        $logStmt
    );

}


/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Candidate updated successfully."

]);

exit();

?>