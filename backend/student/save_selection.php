<?php
/* ==========================================================
   VOTIFY
   Save Selected Candidate
   File : backend/student/save_selection.php
========================================================== */

/* ==========================================================
   SESSION
========================================================== */

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   RESPONSE TYPE
========================================================== */

header(

    "Content-Type: application/json"

);

/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if(

    !isset($_SESSION["student_logged_in"]) ||

    $_SESSION["student_logged_in"] !== true

){

    echo json_encode([

        "success" => false,

        "message" => "Unauthorized access."

    ]);

    exit();

}

/* ==========================================================
   REQUEST METHOD VALIDATION
========================================================== */

if(

    $_SERVER["REQUEST_METHOD"] !== "POST"

){

    echo json_encode([

        "success" => false,

        "message" => "Invalid request."

    ]);

    exit();

}

/* ==========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../../config/database.php";

/* ==========================================================
   HELPER FUNCTION
========================================================== */

function response(

    bool $success,

    string $message

){

    echo json_encode([

        "success" => $success,

        "message" => $message

    ]);

    exit();

}

/* ==========================================================
   PART 2
   RECEIVE CANDIDATE
   VALIDATE CANDIDATE
========================================================== */

/* ==========================================================
   RECEIVE INPUT
========================================================== */

$candidateId =

    $_POST["candidate_id"] ?? "";

$candidateName =

    trim(

        $_POST["candidate_name"] ?? ""

    );

/* ==========================================================
   BASIC VALIDATION
========================================================== */

if(

    $candidateId === "" ||

    $candidateName === ""

){

    response(

        false,

        "Candidate information is missing."

    );

}

/* ==========================================================
   NOTA VALIDATION
========================================================== */

$isNOTA = (

    strtoupper(

        (string)$candidateId

    ) === "NOTA"

);

if($isNOTA){

    $_SESSION["selected_candidate_id"] = "NOTA";

    $_SESSION["selected_candidate_name"] = "NOTA";

}

/* ==========================================================
   NORMAL CANDIDATE VALIDATION
========================================================== */

else{

    if(

        !ctype_digit(

            (string)$candidateId

        )

    ){

        response(

            false,

            "Invalid candidate."

        );

    }

    $candidateId = (int)$candidateId;

    $query = "

    SELECT

        id,

        full_name,

        status

    FROM candidates

    WHERE

        id = ?

    LIMIT 1

    ";

    $stmt = mysqli_prepare(

        $conn,

        $query

    );

    if(!$stmt){

        response(

            false,

            "Database error."

        );

    }

    mysqli_stmt_bind_param(

        $stmt,

        "i",

        $candidateId

    );

    if(

        !mysqli_stmt_execute(

            $stmt

        )

    ){

        mysqli_stmt_close(

            $stmt

        );

        response(

            false,

            "Database error."

        );

    }

    $result = mysqli_stmt_get_result(

        $stmt

    );

    if(

        mysqli_num_rows(

            $result

        ) !== 1

    ){

        mysqli_stmt_close(

            $stmt

        );

        response(

            false,

            "Candidate not found."

        );

    }

    $candidate = mysqli_fetch_assoc(

        $result

    );

    mysqli_stmt_close(

        $stmt

    );

    /* ==============================================
       ACTIVE STATUS CHECK
    ============================================== */

    if(

        strcasecmp(

            trim(

                $candidate["status"]

            ),

            "Active"

        ) !== 0

    ){

        response(

            false,

            "Candidate is inactive."

        );

    }

    /* ==============================================
       NAME VERIFICATION
    ============================================== */

    if(

        trim(

            $candidate["full_name"]

        ) !== $candidateName

    ){

        response(

            false,

            "Candidate verification failed."

        );

    }

}

/* ==========================================================
   PART 3
   SAVE SESSION
   RETURN JSON SUCCESS
========================================================== */

/* ==========================================================
   SAVE NORMAL CANDIDATE SESSION
========================================================== */

if(!$isNOTA){

    $_SESSION["selected_candidate_id"] =

        $candidate["id"];

    $_SESSION["selected_candidate_name"] =

        trim(

            $candidate["full_name"]

        );

}

/* ==========================================================
   CLOSE DATABASE CONNECTION
========================================================== */

mysqli_close(

    $conn

);

/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

response(

    true,

    "Candidate selection saved successfully."

);

?>