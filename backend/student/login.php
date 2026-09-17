<?php
/* ==========================================================
   VOTIFY
   Student Login Backend
   File : backend/student/login.php

   FLOW:

   LOGIN:
   1. Validate student details
   2. Check email verified
   3. Check Approved status
   4. Check Unvoted status
   5. Verify password
   6. Generate OTP
   7. Send OTP to college email
   8. Store OTP in session
   9. Return otp_required=true

   VERIFY OTP:
   1. Read OTP
   2. Check pending login
   3. Check expiry
   4. Check attempts
   5. Verify OTP
   6. Create login session
   7. Return dashboard URL
========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ==========================================================
   RESPONSE
========================================================== */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/* ==========================================================
   DATABASE
========================================================== */

require_once __DIR__ . "/../../config/database.php";


/* ==========================================================
   MAILER
========================================================== */

require_once __DIR__ . "/../../lib/mailer.php";


/* ==========================================================
   RESPONSE HELPER
========================================================== */

function response(
    bool $success,
    string $message,
    array $extra = []
) {

    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        )
    );

    exit();
}


/* ==========================================================
   POST ONLY
========================================================== */

if (
    ($_SERVER["REQUEST_METHOD"] ?? "") !== "POST"
) {

    response(
        false,
        "Invalid request method."
    );

}


/* ==========================================================
   ACTION
========================================================== */

$action =
    trim(
        $_POST["action"] ?? "login"
    );


/* ==========================================================
   CLEAN INPUT
========================================================== */

function cleanInput($value): string
{

    return trim(
        htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            "UTF-8"
        )
    );

}


/* ==========================================================
   ==========================================================
   VERIFY OTP
   ==========================================================
========================================================== */

if ($action === "verify_otp") {

    $otp =
        cleanInput(
            $_POST["otp"] ?? ""
        );


    /* ======================================================
       OTP FORMAT
    ====================================================== */

    if (
        !preg_match(
            "/^[0-9]{6}$/",
            $otp
        )
    ) {

        response(
            false,
            "Please enter a valid 6-digit OTP."
        );

    }


    /* ======================================================
       CHECK PENDING LOGIN
    ====================================================== */

    if (
        empty(
            $_SESSION["student_login_otp_pending"]
        )
    ) {

        response(
            false,
            "No login OTP is pending. Please login again."
        );

    }


    /* ======================================================
       CHECK EXPIRY
    ====================================================== */

    $expires =
        (int)(
            $_SESSION[
                "student_login_otp_expires"
            ] ?? 0
        );


    if ($expires <= time()) {

        clearLoginOtpSession();


        response(
            false,
            "OTP expired. Please login again to receive a new OTP.",
            [
                "otp_expired" => true
            ]
        );

    }


    /* ======================================================
       ATTEMPT LIMIT
    ====================================================== */

    $attempts =
        (int)(
            $_SESSION[
                "student_login_attempts"
            ] ?? 0
        );


    if ($attempts >= 5) {

        clearLoginOtpSession();


        response(
            false,
            "Too many incorrect OTP attempts. Please login again."
        );

    }


    /* ======================================================
       GET HASH
    ====================================================== */

    $otpHash =
        $_SESSION[
            "student_login_otp_hash"
        ] ?? "";


    /* ======================================================
       VERIFY
    ====================================================== */

    if (
        empty($otpHash) ||
        !password_verify(
            $otp,
            $otpHash
        )
    ) {

        $_SESSION[
            "student_login_attempts"
        ] =
            $attempts + 1;


        $remaining =
            5 -
            $_SESSION[
                "student_login_attempts"
            ];


        response(
            false,
            "Incorrect OTP.",
            [
                "remaining_attempts" =>
                    max(
                        0,
                        $remaining
                    )
            ]
        );

    }


    /* ======================================================
       OTP CORRECT
    ====================================================== */

    $studentId =
        (int)(
            $_SESSION[
                "student_login_id"
            ] ?? 0
        );


    if ($studentId <= 0) {

        clearLoginOtpSession();


        response(
            false,
            "Login session expired. Please login again."
        );

    }


    /* ======================================================
       REGENERATE SESSION ID
    ====================================================== */

    session_regenerate_id(true);


    /* ======================================================
       CREATE LOGIN SESSION
    ====================================================== */

    $_SESSION["student_logged_in"] =
        true;


    $_SESSION["student_id"] =
        $studentId;


    $_SESSION["student_name"] =
        $_SESSION[
            "student_login_name"
        ] ?? "";


    $_SESSION["student_email"] =
        $_SESSION[
            "student_login_email"
        ] ?? "";


    $_SESSION["student_admission_no"] =
        $_SESSION[
            "student_login_admission_no"
        ] ?? "";


    $_SESSION["student_department"] =
        $_SESSION[
            "student_login_department"
        ] ?? "";


    $_SESSION["student_year"] =
        $_SESSION[
            "student_login_year"
        ] ?? "";


    $_SESSION["student_login_time"] =
        time();


    /* ======================================================
       CLEAR OTP SESSION
    ====================================================== */

    clearLoginOtpSession();


    /* ======================================================
       SUCCESS
    ====================================================== */

    response(
        true,
        "Login successful.",
        [
            "otp_verified" => true,
            "logged_in" => true,
            "redirect" =>
                "security_check.php"
        ]
    );

}


/* ==========================================================
   ==========================================================
   NORMAL LOGIN
   ==========================================================
========================================================== */


/* ==========================================================
   RECEIVE INPUT
========================================================== */

$admissionNo =
    strtoupper(
        cleanInput(
            $_POST["admissionNo"] ?? ""
        )
    );


$dob =
    cleanInput(
        $_POST["dob"] ?? ""
    );


$collegeEmail =
    strtolower(
        cleanInput(
            $_POST["collegeEmail"] ?? ""
        )
    );


$password =
    $_POST["password"] ?? "";


/* ==========================================================
   EMPTY VALIDATION
========================================================== */

if ($admissionNo === "") {

    response(
        false,
        "Admission Number is required."
    );

}


if ($dob === "") {

    response(
        false,
        "Date of Birth is required."
    );

}


if ($collegeEmail === "") {

    response(
        false,
        "College Email is required."
    );

}


if ($password === "") {

    response(
        false,
        "Password is required."
    );

}


/* ==========================================================
   ADMISSION NUMBER
========================================================== */

if (
    !preg_match(
        "/^[A-Z0-9]{10,15}$/",
        $admissionNo
    )
) {

    response(
        false,
        "Invalid Admission Number format."
    );

}


/* ==========================================================
   EMAIL
========================================================== */

if (
    !filter_var(
        $collegeEmail,
        FILTER_VALIDATE_EMAIL
    )
) {

    response(
        false,
        "Invalid College Email."
    );

}


/* ==========================================================
   EMAIL DOMAIN
========================================================== */

if (
    !preg_match(
        "/@sonatech\.ac\.in$/i",
        $collegeEmail
    )
) {

    response(
        false,
        "Please use your official College Email ID."
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
        year,
        email_verified

    FROM students

    WHERE
        admission_no = ?
        AND dob = ?
        AND college_email = ?

    LIMIT 1

";


$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


if (!$stmt) {

    error_log(
        "VOTIFY LOGIN PREPARE ERROR: " .
        mysqli_error($conn)
    );


    response(
        false,
        "Unable to process login request."
    );

}


/* ==========================================================
   BIND
========================================================== */

mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $admissionNo,
    $dob,
    $collegeEmail
);


/* ==========================================================
   EXECUTE
========================================================== */

if (
    !mysqli_stmt_execute($stmt)
) {

    error_log(
        "VOTIFY LOGIN EXECUTE ERROR: " .
        mysqli_stmt_error($stmt)
    );


    mysqli_stmt_close($stmt);


    response(
        false,
        "Database execution failed."
    );

}


/* ==========================================================
   RESULT
========================================================== */

$result =
    mysqli_stmt_get_result(
        $stmt
    );


/* ==========================================================
   STUDENT NOT FOUND
========================================================== */

if (
    !$result ||
    mysqli_num_rows($result) === 0
) {

    mysqli_stmt_close($stmt);


    response(
        false,
        "Invalid Admission Number, Date of Birth or College Email."
    );

}


/* ==========================================================
   STUDENT
========================================================== */

$student =
    mysqli_fetch_assoc(
        $result
    );


/* ==========================================================
   EMAIL VERIFIED
========================================================== */

$emailVerified =
    (int)(
        $student["email_verified"] ?? 0
    );


if ($emailVerified !== 1) {

    mysqli_stmt_close($stmt);


    response(
        false,
        "Your college email is not verified. Please complete registration email verification first."
    );

}


/* ==========================================================
   STATUS
========================================================== */

$status =
    trim(
        $student["status"] ?? ""
    );


/* ==========================================================
   APPROVED
========================================================== */

if (
    strcasecmp(
        $status,
        "Approved"
    ) !== 0
) {

    if (
        strcasecmp(
            $status,
            "Pending"
        ) === 0
    ) {

        mysqli_stmt_close($stmt);


        response(
            false,
            "Your registration is pending administrator approval."
        );

    }


    if (
        strcasecmp(
            $status,
            "Rejected"
        ) === 0
    ) {

        mysqli_stmt_close($stmt);


        response(
            false,
            "Your registration has been rejected. Please contact the administrator."
        );

    }


    mysqli_stmt_close($stmt);


    response(
        false,
        "Invalid account status."
    );

}


/* ==========================================================
   VOTE STATUS
========================================================== */

$voteStatus =
    trim(
        $student["vote_status"] ?? ""
    );


if (
    strcasecmp(
        $voteStatus,
        "Unvoted"
    ) !== 0
) {

    if (
        strcasecmp(
            $voteStatus,
            "Voted"
        ) === 0
    ) {

        mysqli_stmt_close($stmt);


        response(
            false,
            "You have already cast your vote.",
            [
                "already_voted" => true
            ]
        );

    }


    mysqli_stmt_close($stmt);


    response(
        false,
        "Invalid voting status. Please contact the administrator."
    );

}


/* ==========================================================
   PASSWORD
========================================================== */

$storedPassword =
    $student["password"] ?? "";


if (
    !password_verify(
        $password,
        $storedPassword
    )
) {

    mysqli_stmt_close($stmt);


    response(
        false,
        "Incorrect password."
    );

}


/* ==========================================================
   GENERATE OTP
========================================================== */

try {

    $otp =
        (string)
        random_int(
            100000,
            999999
        );

} catch (Throwable $e) {

    error_log(
        "VOTIFY OTP GENERATION ERROR: " .
        $e->getMessage()
    );


    mysqli_stmt_close($stmt);


    response(
        false,
        "Unable to generate OTP. Please try again."
    );

}


/* ==========================================================
   OTP EXPIRY
   5 MINUTES
========================================================== */

$otpExpires =
    time() + 300;


/* ==========================================================
   CLEAR OLD OTP
========================================================== */

clearLoginOtpSession();


/* ==========================================================
   STORE OTP HASH
========================================================== */

$_SESSION[
    "student_login_otp_hash"
] =
    password_hash(
        $otp,
        PASSWORD_DEFAULT
    );


$_SESSION[
    "student_login_otp_expires"
] =
    $otpExpires;


/* ==========================================================
   STORE STUDENT
========================================================== */

$_SESSION[
    "student_login_id"
] =
    (int)$student["id"];


$_SESSION[
    "student_login_name"
] =
    $student["full_name"];


$_SESSION[
    "student_login_email"
] =
    $student["college_email"];


$_SESSION[
    "student_login_admission_no"
] =
    $student["admission_no"];


$_SESSION[
    "student_login_department"
] =
    $student["department"];


$_SESSION[
    "student_login_year"
] =
    $student["year"];


$_SESSION[
    "student_login_attempts"
] =
    0;


$_SESSION[
    "student_login_otp_pending"
] =
    true;


/* ==========================================================
   CLOSE DB
========================================================== */

mysqli_stmt_close($stmt);


/* ==========================================================
   SEND OTP
========================================================== */

$mailResult =
    sendOtpMail(
        $collegeEmail,
        $student["full_name"],
        $otp,
        "login"
    );


/* ==========================================================
   EMAIL FAILED
========================================================== */

if (
    !is_array($mailResult) ||
    empty(
        $mailResult["success"]
    )
) {

    clearLoginOtpSession();


    response(
        false,
        $mailResult["message"] ??
        "Unable to send login OTP. Please try again."
    );

}


/* ==========================================================
   MASK EMAIL
========================================================== */

function maskCollegeEmail(
    string $email
): string {

    $parts =
        explode(
            "@",
            $email,
            2
        );


    if (
        count($parts) !== 2
    ) {

        return $email;

    }


    $local =
        $parts[0];

    $domain =
        $parts[1];


    $length =
        strlen($local);


    if ($length <= 2) {

        $maskedLocal =
            substr(
                $local,
                0,
                1
            ) .
            "******";

    } else {

        $maskedLocal =
            substr(
                $local,
                0,
                2
            ) .
            str_repeat(
                "*",
                max(
                    6,
                    $length - 2
                )
            );

    }


    return
        $maskedLocal .
        "@" .
        $domain;

}


/* ==========================================================
   MASK EMAIL
========================================================== */

$maskedEmail =
    maskCollegeEmail(
        $collegeEmail
    );


/* ==========================================================
   OTP SENT
========================================================== */

response(
    true,
    "OTP sent to your college email.",
    [
        "otp_required" => true,
        "requires_otp" => true,
        "otp_expires_in" => 300,
        "email" => $maskedEmail
    ]
);


/* ==========================================================
   CLEAR LOGIN OTP SESSION
========================================================== */

function clearLoginOtpSession()
{

    unset(
        $_SESSION["student_login_otp_hash"],
        $_SESSION["student_login_otp_expires"],
        $_SESSION["student_login_id"],
        $_SESSION["student_login_name"],
        $_SESSION["student_login_email"],
        $_SESSION["student_login_admission_no"],
        $_SESSION["student_login_department"],
        $_SESSION["student_login_year"],
        $_SESSION["student_login_attempts"],
        $_SESSION["student_login_otp_pending"]
    );

}

?>