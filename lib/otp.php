<?php
/* ==========================================================
   VOTIFY
   OTP Helper Functions
   File : lib/otp.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();
}


/* ==========================================================
   GENERATE 6 DIGIT OTP
========================================================== */

function generateOtp(): string
{
    return (string) random_int(
        100000,
        999999
    );
}


/* ==========================================================
   CREATE OTP SESSION
========================================================== */

function createOtpSession(
    string $type,
    string $otp,
    int $expirySeconds = 300
): void {

    $_SESSION[$type . "_otp_hash"] =
        password_hash(
            $otp,
            PASSWORD_DEFAULT
        );

    $_SESSION[$type . "_otp_expiry"] =
        time() + $expirySeconds;

    $_SESSION[$type . "_otp_created_at"] =
        time();

    $_SESSION[$type . "_otp_attempts"] = 0;
}


/* ==========================================================
   VERIFY OTP
========================================================== */

function verifyOtpSession(
    string $type,
    string $otp,
    int $maxAttempts = 5
): array {

    $hashKey =
        $type . "_otp_hash";

    $expiryKey =
        $type . "_otp_expiry";

    $attemptKey =
        $type . "_otp_attempts";


    /* OTP NOT FOUND */

    if (
        empty($_SESSION[$hashKey]) ||
        empty($_SESSION[$expiryKey])
    ) {

        return [

            "success" => false,

            "message" =>
                "OTP session not found. Please request a new OTP."
        ];
    }


    /* MAX ATTEMPTS */

    $attempts =
        $_SESSION[$attemptKey] ?? 0;

    if ($attempts >= $maxAttempts) {

        clearOtpSession($type);

        return [

            "success" => false,

            "message" =>
                "Maximum OTP attempts exceeded. Please request a new OTP."
        ];
    }


    /* OTP EXPIRED */

    if (time() > $_SESSION[$expiryKey]) {

        clearOtpSession($type);

        return [

            "success" => false,

            "message" =>
                "OTP has expired. Please request a new OTP."
        ];
    }


    /* INVALID FORMAT */

    if (!preg_match('/^[0-9]{6}$/', $otp)) {

        return [

            "success" => false,

            "message" =>
                "Enter a valid 6-digit OTP."
        ];
    }


    /* INCREASE ATTEMPT */

    $_SESSION[$attemptKey] =
        $attempts + 1;


    /* VERIFY HASH */

    if (
        !password_verify(
            $otp,
            $_SESSION[$hashKey]
        )
    ) {

        $remaining =
            $maxAttempts -
            $_SESSION[$attemptKey];

        return [

            "success" => false,

            "message" =>
                "Incorrect OTP. {$remaining} attempt(s) remaining."
        ];
    }


    return [

        "success" => true,

        "message" => "OTP verified successfully."
    ];
}


/* ==========================================================
   CHECK RESEND COOLDOWN
========================================================== */

function canResendOtp(
    string $type,
    int $cooldown = 30
): array {

    $createdKey =
        $type . "_otp_created_at";

    if (empty($_SESSION[$createdKey])) {

        return [
            "allowed" => true,
            "remaining" => 0
        ];
    }

    $elapsed =
        time() -
        $_SESSION[$createdKey];

    if ($elapsed >= $cooldown) {

        return [
            "allowed" => true,
            "remaining" => 0
        ];
    }

    return [

        "allowed" => false,

        "remaining" =>
            $cooldown - $elapsed
    ];
}


/* ==========================================================
   CLEAR OTP SESSION
========================================================== */

function clearOtpSession(
    string $type
): void {

    unset(
        $_SESSION[$type . "_otp_hash"],
        $_SESSION[$type . "_otp_expiry"],
        $_SESSION[$type . "_otp_created_at"],
        $_SESSION[$type . "_otp_attempts"]
    );
}


/* ==========================================================
   MASK EMAIL
========================================================== */

function maskEmail(
    string $email
): string {

    $parts =
        explode(
            "@",
            $email
        );

    if (count($parts) !== 2) {

        return $email;
    }

    $username = $parts[0];
    $domain = $parts[1];

    $length =
        strlen($username);

    if ($length <= 3) {

        $masked =
            substr($username, 0, 1) .
            "***";

    } else {

        $masked =
            substr($username, 0, 2) .
            str_repeat(
                "*",
                max(3, $length - 4)
            ) .
            substr($username, -2);
    }

    return
        $masked .
        "@" .
        $domain;
}