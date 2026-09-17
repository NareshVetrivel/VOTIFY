<?php
/* ==========================================================
   VOTIFY
   Resend Registration OTP
   File : backend/student/resend-register-otp.php
========================================================== */

declare(strict_types=1);


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ==========================================================
   RESPONSE HEADER
========================================================== */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/* ==========================================================
   REQUIRED FILES
========================================================== */

require_once __DIR__ . "/../../lib/otp.php";
require_once __DIR__ . "/../../lib/mailer.php";

$emailConfig = require __DIR__ . "/../../config/email.php";


/* ==========================================================
   JSON RESPONSE HELPER
========================================================== */

function resendOtpResponse(
    string $status,
    string $message,
    array $extra = []
): never {

    echo json_encode(
        array_merge(
            [
                "status" => $status,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* ==========================================================
   POST REQUEST ONLY
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    resendOtpResponse(
        "error",
        "Invalid request method."
    );
}


/* ==========================================================
   CHECK EMAIL CONFIG
========================================================== */

if (!is_array($emailConfig)) {

    error_log(
        "VOTIFY Resend OTP Error: Email configuration is invalid."
    );

    resendOtpResponse(
        "error",
        "Unable to send OTP. Please try again later."
    );
}


/* ==========================================================
   OTP EXPIRY
========================================================== */

$otpExpiry = 300;

if (
    isset($emailConfig["otp_expiry"]) &&
    is_numeric($emailConfig["otp_expiry"])
) {

    $otpExpiry =
        (int)$emailConfig["otp_expiry"];
}


if ($otpExpiry <= 0) {
    $otpExpiry = 300;
}


/* ==========================================================
   RESEND COOLDOWN
========================================================== */

$resendCooldown = 30;

if (
    isset(
        $emailConfig["otp_resend_cooldown"]
    ) &&
    is_numeric(
        $emailConfig["otp_resend_cooldown"]
    )
) {

    $resendCooldown =
        (int)$emailConfig[
            "otp_resend_cooldown"
        ];
}


if ($resendCooldown < 0) {
    $resendCooldown = 30;
}


/* ==========================================================
   CHECK PENDING REGISTRATION
========================================================== */

if (
    !isset(
        $_SESSION["pending_registration"]
    ) ||
    !is_array(
        $_SESSION["pending_registration"]
    )
) {

    resendOtpResponse(
        "error",
        "Registration session expired. Please register again."
    );
}


/* ==========================================================
   CHECK RESEND COOLDOWN
========================================================== */

$cooldown =
    canResendOtp(
        "register",
        $resendCooldown
    );


if (
    !is_array($cooldown) ||
    empty($cooldown["allowed"])
) {

    $remaining =
        (int)(
            $cooldown["remaining"] ?? 0
        );


    resendOtpResponse(
        "error",
        "Please wait {$remaining} seconds before requesting another OTP.",
        [
            "remaining" => $remaining
        ]
    );
}


/* ==========================================================
   GET PENDING REGISTRATION
========================================================== */

$student =
    $_SESSION["pending_registration"];


/* ==========================================================
   GET STUDENT NAME
========================================================== */

$name =
    trim(
        (string)(
            $student["full_name"] ?? ""
        )
    );


/* ==========================================================
   GET COLLEGE EMAIL
========================================================== */

$email =
    strtolower(
        trim(
            (string)(
                $student["college_email"] ?? ""
            )
        )
    );


/* ==========================================================
   VALIDATE PENDING STUDENT DATA
========================================================== */

if (
    $name === "" ||
    $email === ""
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    resendOtpResponse(
        "error",
        "Registration data is incomplete. Please register again."
    );
}


/* ==========================================================
   VALIDATE COLLEGE EMAIL
========================================================== */

if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    resendOtpResponse(
        "error",
        "Invalid college email address. Please register again."
    );
}


/* ==========================================================
   COLLEGE EMAIL DOMAIN
========================================================== */

if (
    !preg_match(
        '/^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/i',
        $email
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    resendOtpResponse(
        "error",
        "Invalid college email address. Please register again."
    );
}


/* ==========================================================
   GENERATE NEW OTP
========================================================== */

$otp =
    generateOtp();


/* ==========================================================
   CREATE NEW OTP SESSION
========================================================== */

/*
 * This replaces the previous OTP.
 *
 * The old OTP becomes invalid.
 */

createOtpSession(
    "register",
    $otp,
    $otpExpiry
);


/* ==========================================================
   SEND NEW OTP EMAIL
========================================================== */

$mailResult =
    sendOtpMail(
        $email,
        $name,
        $otp,
        "registration"
    );


/* ==========================================================
   CHECK MAIL RESULT
========================================================== */

if (
    !is_array($mailResult) ||
    !isset($mailResult["success"]) ||
    !$mailResult["success"]
) {

    /*
     * Remove the newly created OTP
     * if email delivery fails.
     */

    clearOtpSession(
        "register"
    );


    $mailMessage =
        "Unable to send OTP email. Please try again.";


    if (
        is_array($mailResult) &&
        !empty($mailResult["message"])
    ) {

        $mailMessage =
            (string)$mailResult["message"];
    }


    error_log(
        "VOTIFY Resend Registration OTP Failed: " .
        $mailMessage
    );


    resendOtpResponse(
        "error",
        $mailMessage
    );
}


/* ==========================================================
   SUCCESS
========================================================== */

resendOtpResponse(
    "success",
    "New OTP sent successfully.",
    [
        "email" =>
            maskEmail($email),

        "expires_in" =>
            $otpExpiry,

        "cooldown" =>
            $resendCooldown
    ]
);