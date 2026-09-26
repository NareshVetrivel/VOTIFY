<?php
/* ==========================================================
   VOTIFY
   Save Selected Candidate
   File : backend/student/save_selection.php

   Purpose:
   - Validate logged-in student
   - Validate selected candidate
   - Read candidate data from database
   - Save trusted candidate information to session
   - Return clean JSON response
========================================================== */


/* ==========================================================
   OUTPUT BUFFER
   Prevent accidental PHP warnings/whitespace from corrupting
   the JSON response expected by voting.js.
========================================================== */

ob_start();


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/* ==========================================================
   RESPONSE TYPE
========================================================== */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/* ==========================================================
   JSON RESPONSE HELPER
========================================================== */

function sendResponse(
    bool $success,
    string $message,
    array $extra = [],
    int $statusCode = 200
) {

    /*
        Clear anything accidentally written before JSON.
        This is important because even one PHP warning or
        whitespace character can break JSON.parse().
    */

    while (ob_get_level() > 0) {

        ob_end_clean();

    }


    http_response_code($statusCode);


    $response = array_merge(

        [
            "success" => $success,
            "message" => $message
        ],

        $extra

    );


    $json = json_encode(

        $response,

        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_INVALID_UTF8_SUBSTITUTE

    );


    /*
        Extra safety in case json_encode itself fails.
    */

    if ($json === false) {

        $json = json_encode(

            [
                "success" => false,
                "message" => "Unable to generate server response."
            ],

            JSON_UNESCAPED_UNICODE

        );

    }


    echo $json;

    exit();

}


/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if (

    !isset($_SESSION["student_logged_in"]) ||

    $_SESSION["student_logged_in"] !== true

) {

    sendResponse(

        false,

        "Unauthorized access.",

        [],

        401

    );

}


/* ==========================================================
   REQUEST METHOD VALIDATION
========================================================== */

if (

    ($_SERVER["REQUEST_METHOD"] ?? "") !== "POST"

) {

    sendResponse(

        false,

        "Invalid request method.",

        [],

        405

    );

}


/* ==========================================================
   DATABASE
========================================================== */

try {

    require_once "../../config/database.php";

} catch (Throwable $error) {

    error_log(

        "VOTIFY save_selection.php: Database configuration error - " .

        $error->getMessage()

    );


    sendResponse(

        false,

        "Database connection error.",

        [],

        500

    );

}


/** @var mysqli $conn */


/* ==========================================================
   DATABASE CONNECTION VALIDATION
========================================================== */

if (

    !isset($conn) ||

    !($conn instanceof mysqli)

) {

    error_log(

        "VOTIFY save_selection.php: Invalid database connection."

    );


    sendResponse(

        false,

        "Database connection error.",

        [],

        500

    );

}


/* ==========================================================
   RECEIVE CANDIDATE ID
========================================================== */

$candidateId = trim(

    (string) (

        $_POST["candidate_id"] ?? ""

    )

);


/* ==========================================================
   BASIC VALIDATION
========================================================== */

if ($candidateId === "") {

    sendResponse(

        false,

        "Candidate information is missing.",

        [],

        400

    );

}


/* ==========================================================
   NOTA
========================================================== */

$isNOTA = (

    strtoupper($candidateId) === "NOTA"

);


if ($isNOTA) {

    /* ======================================================
       SAVE NOTA SELECTION
    ====================================================== */

    $_SESSION["selected_candidate_id"] =
        "NOTA";

    $_SESSION["selected_candidate_name"] =
        "NOTA";

    $_SESSION["selected_candidate_photo"] =
        "";

    $_SESSION["selected_candidate_department"] =
        "None Of The Above";

    $_SESSION["selected_candidate_year"] =
        "Reject All Candidates";

    $_SESSION["selected_candidate_manifesto"] =
        "Select NOTA if you believe none of the available candidates deserve your vote. Your vote will still be counted as a valid vote.";


    /* ======================================================
       SUCCESS RESPONSE
    ====================================================== */

    sendResponse(

        true,

        "NOTA selection saved successfully.",

        [

            "candidate" => [

                "id" => "NOTA",

                "name" => "NOTA",

                "department" =>
                    "None Of The Above",

                "year" =>
                    "Reject All Candidates",

                "photo" => "",

                "manifesto" =>
                    $_SESSION[
                        "selected_candidate_manifesto"
                    ]

            ]

        ]

    );

}


/* ==========================================================
   NORMAL CANDIDATE ID VALIDATION
========================================================== */

if (!ctype_digit($candidateId)) {

    sendResponse(

        false,

        "Invalid candidate.",

        [],

        400

    );

}


/* ==========================================================
   CONVERT CANDIDATE ID
========================================================== */

$candidateId = (int) $candidateId;


/* ==========================================================
   BASIC INTEGER SAFETY
========================================================== */

if ($candidateId <= 0) {

    sendResponse(

        false,

        "Invalid candidate.",

        [],

        400

    );

}


/* ==========================================================
   FETCH TRUSTED CANDIDATE DATA
========================================================== */

$query = "

    SELECT

        id,
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


try {

    $stmt = mysqli_prepare(

        $conn,

        $query

    );

} catch (Throwable $error) {

    error_log(

        "VOTIFY save_selection.php: Prepare exception - " .

        $error->getMessage()

    );


    mysqli_close($conn);


    sendResponse(

        false,

        "Database error.",

        [],

        500

    );

}


if (!$stmt) {

    error_log(

        "VOTIFY save_selection.php: Unable to prepare candidate query. " .

        mysqli_error($conn)

    );


    mysqli_close($conn);


    sendResponse(

        false,

        "Database error.",

        [],

        500

    );

}


/* ==========================================================
   BIND CANDIDATE ID
========================================================== */

if (!mysqli_stmt_bind_param(

    $stmt,

    "i",

    $candidateId

)) {

    error_log(

        "VOTIFY save_selection.php: Unable to bind candidate ID."

    );


    mysqli_stmt_close($stmt);

    mysqli_close($conn);


    sendResponse(

        false,

        "Database error.",

        [],

        500

    );

}


/* ==========================================================
   EXECUTE QUERY
========================================================== */

if (!mysqli_stmt_execute($stmt)) {

    error_log(

        "VOTIFY save_selection.php: Candidate query execution failed. " .

        mysqli_stmt_error($stmt)

    );


    mysqli_stmt_close($stmt);

    mysqli_close($conn);


    sendResponse(

        false,

        "Database error.",

        [],

        500

    );

}


/* ==========================================================
   FETCH RESULT
   bind_result is retained for shared-host compatibility.
========================================================== */

if (!mysqli_stmt_bind_result(

    $stmt,

    $dbCandidateId,

    $dbFullName,

    $dbDepartment,

    $dbYear,

    $dbManifesto,

    $dbPhoto,

    $dbStatus

)) {

    error_log(

        "VOTIFY save_selection.php: Unable to bind result."

    );


    mysqli_stmt_close($stmt);

    mysqli_close($conn);


    sendResponse(

        false,

        "Database error.",

        [],

        500

    );

}


/* ==========================================================
   FETCH CANDIDATE
========================================================== */

if (!mysqli_stmt_fetch($stmt)) {

    mysqli_stmt_close($stmt);

    mysqli_close($conn);


    sendResponse(

        false,

        "Candidate not found.",

        [],

        404

    );

}


/* ==========================================================
   CLOSE STATEMENT
========================================================== */

mysqli_stmt_close($stmt);


/* ==========================================================
   ACTIVE STATUS CHECK
========================================================== */

if (

    strcasecmp(

        trim((string) $dbStatus),

        "Active"

    ) !== 0

) {

    mysqli_close($conn);


    sendResponse(

        false,

        "Candidate is inactive.",

        [],

        400

    );

}


/* ==========================================================
   NORMALIZE DATABASE VALUES
========================================================== */

$dbCandidateId = (int) $dbCandidateId;

$dbFullName = trim(

    (string) $dbFullName

);

$dbDepartment = trim(

    (string) $dbDepartment

);

$dbYear = trim(

    (string) $dbYear

);

$dbPhoto = trim(

    (string) $dbPhoto

);

$dbManifesto = (string) $dbManifesto;


/* ==========================================================
   CANDIDATE DATA VALIDATION
========================================================== */

if ($dbFullName === "") {

    mysqli_close($conn);


    sendResponse(

        false,

        "Candidate information is incomplete.",

        [],

        400

    );

}


/* ==========================================================
   SAVE TRUSTED DATA TO SESSION
========================================================== */

$_SESSION["selected_candidate_id"] =
    $dbCandidateId;

$_SESSION["selected_candidate_name"] =
    $dbFullName;

$_SESSION["selected_candidate_photo"] =
    $dbPhoto;

$_SESSION["selected_candidate_department"] =
    $dbDepartment;

$_SESSION["selected_candidate_year"] =
    $dbYear;

$_SESSION["selected_candidate_manifesto"] =
    $dbManifesto;


/* ==========================================================
   CLOSE DATABASE CONNECTION
========================================================== */

mysqli_close($conn);


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

sendResponse(

    true,

    "Candidate selection saved successfully.",

    [

        "candidate" => [

            "id" =>
                $dbCandidateId,

            "name" =>
                $dbFullName,

            "department" =>
                $dbDepartment,

            "year" =>
                $dbYear,

            "photo" =>
                $dbPhoto,

            "manifesto" =>
                $dbManifesto

        ]

    ],

    200

);


/* ==========================================================
   END OF FILE
========================================================== */
?>