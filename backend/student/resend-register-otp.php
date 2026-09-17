<?php
/* ==========================================================
   VOTIFY
   Resend Registration OTP
   File : backend/student/resend-register-otp.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}

header("Content-Type: application/json");


require_once "../../lib/otp.php";

require_once "../../lib/mailer.php";


$emailConfig =
    require "../../config/email.php";


/* ==========================================================
   POST ONLY
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Invalid request."

    ]);

    exit;
}


/* ==========================================================
   CHECK REGISTRATION SESSION
========================================================== */

if (
    empty(
        $_SESSION["pending_registration"]
    )
) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Registration session expired."

    ]);

    exit;
}


/* ==========================================================
   COOLDOWN
========================================================== */

$cooldown =
    canResendOtp(

        "register",

        $emailConfig[
            "otp_resend_cooldown"
        ]
    );


if (!$cooldown["allowed"]) {

    echo json_encode([

        "status" => "error",

        "message" =>
            "Please wait " .
            $cooldown["remaining"] .
            " seconds before requesting another OTP."

    ]);

    exit;
}


/* ==========================================================
   STUDENT DATA
========================================================== */

$student =
    $_SESSION[
        "pending_registration"
    ];

$email =
    $student[
        "college_email"
    ];

$name =
    $student[
        "full_name"
    ];


/* ==========================================================
   NEW OTP
========================================================== */

$otp =
    generateOtp();


createOtpSession(

    "register",

    $otp,

    $emailConfig[
        "otp_expiry"
    ]
);


/* ==========================================================
   SEND EMAIL
========================================================== */

$mail =
    sendOtpMail(

        $email,

        $name,

        $otp,

        "registration"
    );


if (!$mail["success"]) {

    clearOtpSession(
        "register"
    );

    echo json_encode([

        "status" => "error",

        "message" =>
            $mail["message"]

    ]);

    exit;
}


/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "status" => "success",

    "message" =>
        "New OTP sent successfully.",

    "email" =>
        maskEmail(
            $email
        )

]);

exit;