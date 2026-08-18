<?php

/* ==========================================================
   VOTIFY
   Candidate Photo Endpoint
   File : backend/candidate-photo.php

   Purpose:
   Retrieve candidate photo from Aiven MySQL
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

    http_response_code(500);

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

    exit();

}


$result = mysqli_stmt_get_result($stmt);


if (!$result || mysqli_num_rows($result) !== 1) {

    mysqli_stmt_close($stmt);

    http_response_code(404);

    exit();

}


$candidate = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


/* ==========================================================
   PHOTO VALIDATION
========================================================== */

if (

    empty($candidate["photo"]) ||

    empty($candidate["photo_type"])

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

        $candidate["photo_type"],

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

    "Content-Type: " .

    $candidate["photo_type"]

);

header(

    "X-Content-Type-Options: nosniff"

);


/* ==========================================================
   CACHE SETTINGS
========================================================== */

header(

    "Cache-Control: public, max-age=86400"

);


/* ==========================================================
   OUTPUT IMAGE
========================================================== */

echo $candidate["photo"];

exit();

?>