<?php
/* ==========================================================
   VOTIFY
   Update Candidate
   File : backend/admin/update-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   SESSION
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
   Candidate editing is NOT allowed while election is running.
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
        "VOTIFY update-candidate.php: Unable to prepare election status query."
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
        "VOTIFY update-candidate.php: Election status query failed."
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

mysqli_stmt_close($statusStmt);


/* ==========================================================
   DEFAULT STATUS
========================================================== */

if (!$statusFound || !$electionStatus) {

    $electionStatus = "Ready";
}


/* ==========================================================
   BLOCK UPDATE WHILE ELECTION IS RUNNING
========================================================== */

if ($electionStatus === "Started") {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Editing candidates is disabled while the election is running."
    ]);

    exit();
}


/* ==========================================================
   VALIDATE CANDIDATE DATA
========================================================== */

$candidateId = intval(
    $_POST["candidateId"] ?? 0
);

$manifesto = trim(
    $_POST["manifesto"] ?? ""
);


if ($candidateId <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate ID."
    ]);

    exit();
}


if ($manifesto === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Manifesto is required."
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

    error_log(
        "VOTIFY update-candidate.php: Candidate lookup prepare failed."
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


mysqli_stmt_bind_result(
    $stmt,
    $dbCandidateId,
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
   ADMISSION NUMBER VALIDATION
========================================================== */

/*
 * Candidate admission numbers in VOTIFY must follow:
 *
 *     25CAPMCA080
 *
 * Structure:
 *
 *     2 digits
 *     CAPMCA
 *     3 digits
 *
 * Total:
 *
 *     Exactly 11 characters.
 *
 * This is backend validation.
 *
 * Even if JavaScript validation is bypassed, an invalid
 * admission number already stored against the candidate
 * will not be allowed to continue through the update flow.
 */


/* ==========================================================
   NORMALIZE ADMISSION NUMBER
========================================================== */

$candidateAdmissionNo =
    strtoupper(
        trim(
            (string) $candidateAdmissionNo
        )
    );


/* ==========================================================
   EXACT LENGTH CHECK
========================================================== */

if (
    strlen($candidateAdmissionNo) !== 11
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate admission number. Admission number must be exactly 11 characters."
    ]);

    exit();
}


/* ==========================================================
   EXACT FORMAT CHECK
========================================================== */

if (
    !preg_match(
        "/^[0-9]{2}CAPMCA[0-9]{3}$/",
        $candidateAdmissionNo
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate admission number format. Use format 25CAPMCA080."
    ]);

    exit();
}


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
        SET manifesto = ?
        WHERE id = ?
    ";


    $stmt = mysqli_prepare(
        $conn,
        $query
    );


    if (!$stmt) {

        error_log(
            "VOTIFY update-candidate.php: Manifesto update prepare failed."
        );

        http_response_code(500);

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

        error_log(
            "VOTIFY update-candidate.php: Manifesto update failed - "
            . mysqli_stmt_error($stmt)
        );

        mysqli_stmt_close($stmt);

        http_response_code(500);

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


    /* ======================================================
       UPLOAD ERROR
    ====================================================== */

    if ($photo["error"] !== UPLOAD_ERR_OK) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Candidate photo upload failed."
        ]);

        exit();
    }


    $fileSize = intval(
        $photo["size"]
    );

    $fileTmp = $photo["tmp_name"];


    /* ======================================================
       FILE SIZE
    ====================================================== */

    if ($fileSize <= 0) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Invalid candidate photo."
        ]);

        exit();
    }


    if ($fileSize > 2 * 1024 * 1024) {

        http_response_code(400);

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

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Invalid uploaded photo."
        ]);

        exit();
    }


    /* ======================================================
       REAL MIME TYPE CHECK
    ====================================================== */

    if (!class_exists("finfo")) {

        error_log(
            "VOTIFY update-candidate.php: Fileinfo extension unavailable."
        );

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Unable to validate candidate photo."
        ]);

        exit();
    }


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

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Only JPG, JPEG and PNG files are allowed."
        ]);

        exit();
    }


    /* ======================================================
       VERIFY ACTUAL IMAGE CONTENT
    ====================================================== */

    $imageInfo = @getimagesize(
        $fileTmp
    );


    if ($imageInfo === false) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Uploaded file is not a valid image."
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

        http_response_code(400);

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

        error_log(
            "VOTIFY update-candidate.php: Photo update prepare failed."
        );

        http_response_code(500);

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

        error_log(
            "VOTIFY update-candidate.php: Photo update failed - "
            . mysqli_stmt_error($stmt)
        );

        mysqli_stmt_close($stmt);

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Unable to update candidate."
        ]);

        exit();
    }


    mysqli_stmt_close($stmt);
}


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


/* Prevent unnecessarily large log values */

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


$action = "Candidate Updated";


$description =
    "Updated candidate : "
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
            "VOTIFY update-candidate.php: Admin log failed - "
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

    "message" => "Candidate updated successfully."

]);

exit();

?>