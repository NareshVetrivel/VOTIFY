<?php
/* ==========================================================
   VOTIFY
   Verify Student Login OTP
   File : backend/student/verify-login-otp.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}

header("Content-Type: application/json");


require_once "../../lib/otp.php";


/* ==========================================================
   POST ONLY
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([

        "success" => false,

        "message" =>
            "Invalid request."

    ]);

    exit;
}


/* ==========================================================
   CHECK LOGIN SESSION
========================================================== */

if (
    empty(
        $_SESSION[
            "pending_login_student"
        ]
    )
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Login verification session expired. Please login again."

    ]);

    exit;
}


/* ==========================================================
   RECEIVE OTP
========================================================== */

$otp =
    trim(
        $_POST["otp"] ?? ""
    );


/* ==========================================================
   VERIFY OTP
========================================================== */

$result =
    verifyOtpSession(

        "login",

        $otp,

        5
    );


if (!$result["success"]) {

    echo json_encode([

        "success" => false,

        "message" =>
            $result["message"]

    ]);

    exit;
}


/* ==========================================================
   STUDENT DATA
========================================================== */

$student =
    $_SESSION[
        "pending_login_student"
    ];


/* ==========================================================
   PREVENT SESSION FIXATION
========================================================== */

session_regenerate_id(
    true
);


/* ==========================================================
   CREATE FINAL LOGIN SESSION
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


/* LOGIN TIME */

$_SESSION["login_time"] =
    time();


/* LOGIN STATUS */

$_SESSION["student_logged_in"] =
    true;


/* OPTIONAL OTP FLAG */

$_SESSION["login_otp_verified"] =
    true;


/* ==========================================================
   REMOVE TEMP DATA
========================================================== */

clearOtpSession(
    "login"
);

unset(
    $_SESSION[
        "pending_login_student"
    ]
);


/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "message" =>
        "OTP verified. Login successful.",

    "redirect" =>
        "../../pages/student/security_check.php"

]);

exit;