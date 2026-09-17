<?php
/* ==========================================================
   VOTIFY
   Resend Login OTP
   File : backend/student/resend-login-otp.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}

header("Content-Type: application/json");


require_once "../../config/database.php";

require_once "../../lib/otp.php";

require_once "../../lib/mailer.php";


$emailConfig =
    require "../../config/email.php";


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
   CHECK PENDING LOGIN
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
            "Login session expired. Please login again."

    ]);

    exit;
}


$student =
    $_SESSION[
        "pending_login_student"
    ];


/* ==========================================================
   RECHECK DATABASE BEFORE RESEND
========================================================== */

$stmt =
    $conn->prepare(

        "SELECT
            id,
            college_email,
            status,
            vote_status,
            email_verified

         FROM students

         WHERE id = ?

         LIMIT 1"

    );


$stmt->bind_param(

    "i",

    $student["id"]
);

$stmt->execute();

$result =
    $stmt->get_result();


if ($result->num_rows === 0) {

    $stmt->close();

    unset(
        $_SESSION[
            "pending_login_student"
        ]
    );

    clearOtpSession(
        "login"
    );

    echo json_encode([

        "success" => false,

        "message" =>
            "Student account not found."

    ]);

    exit;
}


$dbStudent =
    $result->fetch_assoc();

$stmt->close();


/* ==========================================================
   APPROVAL CHECK
========================================================== */

if (
    strcasecmp(
        $dbStudent["status"],
        "Approved"
    ) !== 0
) {

    clearOtpSession(
        "login"
    );

    unset(
        $_SESSION[
            "pending_login_student"
        ]
    );

    echo json_encode([

        "success" => false,

        "message" =>
            "Your account is not approved."

    ]);

    exit;
}


/* ==========================================================
   EMAIL VERIFIED CHECK
========================================================== */

if (
    (int)
    $dbStudent[
        "email_verified"
    ] !== 1
) {

    clearOtpSession(
        "login"
    );

    unset(
        $_SESSION[
            "pending_login_student"
        ]
    );

    echo json_encode([

        "success" => false,

        "message" =>
            "College email is not verified."

    ]);

    exit;
}


/* ==========================================================
   VOTE STATUS CHECK
========================================================== */

if (
    strcasecmp(
        $dbStudent["vote_status"],
        "Unvoted"
    ) !== 0
) {

    clearOtpSession(
        "login"
    );

    unset(
        $_SESSION[
            "pending_login_student"
        ]
    );

    echo json_encode([

        "success" => false,

        "message" =>
            "You have already cast your vote."

    ]);

    exit;
}


/* ==========================================================
   EMAIL CHECK
========================================================== */

$email =
    strtolower(
        trim(
            $dbStudent[
                "college_email"
            ]
        )
    );


if (
    empty($email) ||
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    ) ||
    !preg_match(
        "/@sonatech\\.ac\\.in$/i",
        $email
    )
) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Valid registered college email not found."

    ]);

    exit;
}


/* ==========================================================
   RESEND COOLDOWN
========================================================== */

$cooldown =
    canResendOtp(

        "login",

        $emailConfig[
            "otp_resend_cooldown"
        ]
    );


if (!$cooldown["allowed"]) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Please wait " .
            $cooldown["remaining"] .
            " seconds before requesting another OTP."

    ]);

    exit;
}


/* ==========================================================
   CREATE NEW OTP
========================================================== */

$otp =
    generateOtp();


createOtpSession(

    "login",

    $otp,

    $emailConfig[
        "otp_expiry"
    ]
);


/* ==========================================================
   SEND
========================================================== */

$mail =
    sendOtpMail(

        $email,

        $student[
            "full_name"
        ],

        $otp,

        "login"
    );


if (!$mail["success"]) {

    clearOtpSession(
        "login"
    );

    echo json_encode([

        "success" => false,

        "message" =>
            $mail["message"]

    ]);

    exit;
}


/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "message" =>
        "New OTP sent successfully.",

    "email" =>
        maskEmail(
            $email
        )

]);

exit;