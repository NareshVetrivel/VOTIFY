<?php

/* ==========================================================
   VOTIFY
   Candidate Photo Endpoint
   File : backend/candidate-photo.php

   Purpose:
   Retrieve candidate photo from MySQL
   and send it directly to the browser.
========================================================== */


/* ==========================================================
   DATABASE
========================================================== */

require_once "../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   CANDIDATE ID VALIDATION
========================================================== */

$candidateId = $_GET["id"] ?? "";


if (
    $candidateId === "" ||
    !ctype_digit((string) $candidateId)
) {

    http_response_code(400);

    exit();
}


$candidateId = (int) $candidateId;


/* ==========================================================
   FETCH CANDIDATE PHOTO
========================================================== */

$query = "
    SELECT
        photo,
        photo_type
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
        "VOTIFY candidate-photo.php: Unable to prepare photo query."
    );

    http_response_code(500);

    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $candidateId
);


if (!mysqli_stmt_execute($stmt)) {

    error_log(
        "VOTIFY candidate-photo.php: Photo query execution failed."
    );

    mysqli_stmt_close($stmt);

    http_response_code(500);

    exit();
}


/* ==========================================================
   FETCH RESULT
   Using bind_result for shared-host compatibility.
========================================================== */

mysqli_stmt_bind_result(
    $stmt,
    $photo,
    $photoType
);


if (!mysqli_stmt_fetch($stmt)) {

    mysqli_stmt_close($stmt);

    http_response_code(404);

    exit();
}


mysqli_stmt_close($stmt);


/* ==========================================================
   PHOTO VALIDATION
========================================================== */

if (
    empty($photo) ||
    empty($photoType)
) {

    http_response_code(404);

    exit();
}


/* ==========================================================
   ALLOWED IMAGE TYPES
========================================================== */

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

    http_response_code(415);

    exit();
}


/* ==========================================================
   SECURITY HEADERS
========================================================== */

header(
    "Content-Type: " . $photoType
);

header(
    "X-Content-Type-Options: nosniff"
);


/* ==========================================================
   CACHE SETTINGS
========================================================== */

/*
 * Candidate photos can be updated by the admin.
 *
 * Therefore do NOT allow the browser to keep
 * an old candidate photo for 24 hours.
 *
 * This ensures that after editing a candidate photo,
 * the latest image is fetched from the database.
 */

header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Pragma: no-cache"
);

header(
    "Expires: 0"
);


/* ==========================================================
   OUTPUT IMAGE
========================================================== */

echo $photo;

exit();

?>