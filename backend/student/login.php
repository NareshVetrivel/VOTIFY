<?php
/* ==========================================================
   VOTIFY
   Student Login Backend
   File : backend/student/login.php
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
   DATABASE CONNECTION
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */

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
   CLEAN INPUT
========================================================== */

function clean(

    string $value

){

    return trim(

        htmlspecialchars(

            $value,

            ENT_QUOTES,

            "UTF-8"

        )

    );

}

/* ==========================================================
   RECEIVE INPUTS
========================================================== */

$admissionNo = clean(

    $_POST["admissionNo"] ?? ""

);

$dob = clean(

    $_POST["dob"] ?? ""

);

$collegeEmail = strtolower(

    clean(

        $_POST["collegeEmail"] ?? ""

    )

);

$password = $_POST["password"] ?? "";

/* ==========================================================
   STANDARDIZE INPUTS
========================================================== */

$admissionNo = strtoupper(

    $admissionNo

);

/* ==========================================================
   EMPTY FIELD VALIDATION
========================================================== */

if(

    empty($admissionNo)

){

    response(

        false,

        "Admission Number is required."

    );

}

if(

    empty($dob)

){

    response(

        false,

        "Date of Birth is required."

    );

}

if(

    empty($collegeEmail)

){

    response(

        false,

        "College Email is required."

    );

}

if(

    empty($password)

){

    response(

        false,

        "Password is required."

    );

}

/* ==========================================================
   EMAIL FORMAT VALIDATION
========================================================== */

if(

    !filter_var(

        $collegeEmail,

        FILTER_VALIDATE_EMAIL

    )

){

    response(

        false,

        "Invalid College Email."

    );

}

/* ==========================================================
   COLLEGE DOMAIN VALIDATION
========================================================== */

if(

    !preg_match(

        "/@sonatech\.ac\.in$/i",

        $collegeEmail

    )

){

    response(

        false,

        "Please use your College Email ID."

    );

}

/* ==========================================================
   PASSWORD VALIDATION
========================================================== */

if(

    strlen($password) < 8

){

    response(

        false,

        "Invalid Password."

    );

}

/* ==========================================================
   FIND STUDENT
========================================================== */

$query = "

SELECT

    id,

    full_name,

    admission_no,

    dob,

    college_email,

    password,

    status,

    vote_status,

    department,

    year

FROM students

WHERE

    admission_no = ?

AND

    dob = ?

AND

    college_email = ?

LIMIT 1

";

/* ==========================================================
   PREPARE STATEMENT
========================================================== */

$stmt = mysqli_prepare(

    $conn,

    $query

);

if(!$stmt){

    mysqli_close($conn);

    response(
        false,
        "Unable to process login request."
    );

}

/* ==========================================================
   BIND PARAMETERS
========================================================== */

mysqli_stmt_bind_param(

    $stmt,

    "sss",

    $admissionNo,

    $dob,

    $collegeEmail

);

/* ==========================================================
   EXECUTE QUERY
========================================================== */

if(

    !mysqli_stmt_execute($stmt)

){

mysqli_stmt_close($stmt);

mysqli_close($conn);

response(
    false,
    "Database execution failed."
);

}

/* ==========================================================
   GET RESULT
========================================================== */

$result = mysqli_stmt_get_result(

    $stmt

);

/* ==========================================================
   STUDENT NOT FOUND
========================================================== */

if(mysqli_num_rows($result) === 0){

    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    response(
        false,
        "Invalid Admission Number, Date of Birth or College Email."
    );

}
/* ==========================================================
   FETCH STUDENT
========================================================== */

$student = mysqli_fetch_assoc(

    $result

);

/* ==========================================================
   CHECK REGISTRATION STATUS
========================================================== */

$status = trim(

    $student["status"]

);

/* ==========================================================
   APPROVED
========================================================== */

if(

    strcasecmp(

        $status,

        "Approved"

    ) !== 0

){

    /* ==============================================
       PENDING
    ============================================== */

    if(

        strcasecmp(

            $status,

            "Pending"

        ) === 0

    ){

        mysqli_stmt_close($stmt);

        mysqli_close($conn);

        response(

            false,

            "Your registration is pending administrator approval."

        );

    }

    /* ==============================================
       REJECTED
    ============================================== */

    if(

        strcasecmp(

            $status,

            "Rejected"

        ) === 0

    ){

        mysqli_stmt_close($stmt);

        mysqli_close($conn);

        response(

            false,

            "Your registration has been rejected. Please contact the administrator."

        );

    }

    /* ==============================================
       INVALID STATUS
    ============================================== */

    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    response(

        false,

        "Invalid account status."

    );

}

/* ==========================================================
   CHECK VOTE STATUS
========================================================== */

$voteStatus = trim(

    $student["vote_status"]

);

/* ==========================================================
   UNVOTED
========================================================== */

if(

    strcasecmp(

        $voteStatus,

        "Unvoted"

    ) !== 0

){

    /* ==============================================
       VOTED
    ============================================== */

    if(

        strcasecmp(

            $voteStatus,

            "Voted"

        ) === 0

    ){

        mysqli_stmt_close($stmt);

        mysqli_close($conn);

        response(

            false,

            "You have already cast your vote."

        );

    }

    /* ==============================================
       INVALID VOTE STATUS
    ============================================== */

    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    response(

        false,

        "Invalid voting status. Please contact the administrator."

    );

}

/* ==========================================================
   PASSWORD VERIFICATION
========================================================== */

$storedPassword =

    $student["password"];

/* ==========================================================
   VERIFY PASSWORD
========================================================== */

if(

    !password_verify(

        $password,

        $storedPassword

    )

){

    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    response(

        false,

        "Incorrect password."

    );

}

/* ==========================================================
   PASSWORD VERIFIED
========================================================== */

/*

Student authentication successful.

Continue to create login session.

*/

/* ==========================================================
   CREATE STUDENT SESSION
========================================================== */

$_SESSION["student_id"] =

    $student["id"];

$_SESSION["student_name"] =

    $student["full_name"];

$_SESSION["student_email"] =

    $student["college_email"];

$_SESSION["admission_no"] =

    $student["admission_no"];

$_SESSION["department"] =

    $student["department"];

$_SESSION["year"] =

    $student["year"];

/* ==========================================================
   LOGIN TIME
========================================================== */

$_SESSION["login_time"] = time();

/* ==========================================================
   LOGIN STATUS
========================================================== */

$_SESSION["student_logged_in"] = true;

/* ==========================================================
   CLEANUP
========================================================== */

mysqli_stmt_close(

    $stmt

);

mysqli_close(

    $conn

);

/* ==========================================================
   LOGIN SUCCESS RESPONSE
========================================================== */

response(

    true,

    "Login successful."

);