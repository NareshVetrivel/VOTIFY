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

    /*
     * Prevent invalid expiry values.
     */

    if ($expirySeconds <= 0) {
        $expirySeconds = 300;
    }


    /*
     * Store only the hashed OTP.
     *
     * The original OTP is never stored
     * directly inside the session.
     */

    $_SESSION[$type . "_otp_hash"] =
        password_hash(
            $otp,
            PASSWORD_DEFAULT
        );


    /*
     * OTP expiry timestamp.
     */

    $_SESSION[$type . "_otp_expiry"] =
        time() + $expirySeconds;


    /*
     * Used for resend cooldown.
     */

    $_SESSION[$type . "_otp_created_at"] =
        time();


    /*
     * Reset verification attempts
     * whenever a new OTP is created.
     */

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

    /*
     * Prevent invalid maximum-attempt configuration.
     */

    if ($maxAttempts <= 0) {
        $maxAttempts = 5;
    }


    $hashKey =
        $type . "_otp_hash";

    $expiryKey =
        $type . "_otp_expiry";

    $attemptKey =
        $type . "_otp_attempts";


    /* ======================================================
       OTP SESSION CHECK
    ====================================================== */

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


    /* ======================================================
       OTP FORMAT CHECK
    ====================================================== */

    if (
        !is_string($otp) ||
        !preg_match('/^[0-9]{6}$/', $otp)
    ) {

        return [
            "success" => false,
            "message" =>
                "Enter a valid 6-digit OTP."
        ];
    }


    /* ======================================================
       OTP EXPIRY CHECK
    ====================================================== */

    if (
        time() >=
        (int)$_SESSION[$expiryKey]
    ) {

        clearOtpSession($type);

        return [
            "success" => false,
            "message" =>
                "OTP has expired. Please request a new OTP."
        ];
    }


    /* ======================================================
       GET CURRENT ATTEMPTS
    ====================================================== */

    $attempts =
        (int)(
            $_SESSION[$attemptKey] ?? 0
        );


    /* ======================================================
       MAXIMUM ATTEMPTS CHECK
    ====================================================== */

    if ($attempts >= $maxAttempts) {

        clearOtpSession($type);

        return [
            "success" => false,
            "message" =>
                "Maximum OTP attempts exceeded. Please request a new OTP."
        ];
    }


    /* ======================================================
       INCREASE ATTEMPT COUNT
    ====================================================== */

    $attempts++;

    $_SESSION[$attemptKey] =
        $attempts;


    /* ======================================================
       VERIFY OTP HASH
    ====================================================== */

    $isValid =
        password_verify(
            $otp,
            $_SESSION[$hashKey]
        );


    /* ======================================================
       INVALID OTP
    ====================================================== */

    if (!$isValid) {

        $remaining =
            max(
                0,
                $maxAttempts - $attempts
            );


        /*
         * If this was the final attempt,
         * invalidate the OTP immediately.
         */

        if ($remaining === 0) {

            clearOtpSession($type);

            return [
                "success" => false,
                "message" =>
                    "Incorrect OTP. Maximum OTP attempts exceeded. Please request a new OTP."
            ];
        }


        return [
            "success" => false,
            "message" =>
                "Incorrect OTP. {$remaining} attempt(s) remaining."
        ];
    }


    /* ======================================================
       OTP VERIFIED
    ====================================================== */

    return [
        "success" => true,
        "message" =>
            "OTP verified successfully."
    ];
}


/* ==========================================================
   CHECK RESEND COOLDOWN
========================================================== */

function canResendOtp(
    string $type,
    int $cooldown = 30
): array {

    /*
     * Prevent invalid cooldown values.
     */

    if ($cooldown < 0) {
        $cooldown = 30;
    }


    $createdKey =
        $type . "_otp_created_at";


    /* ======================================================
       NO PREVIOUS OTP
    ====================================================== */

    if (
        empty($_SESSION[$createdKey])
    ) {

        return [
            "allowed" => true,
            "remaining" => 0
        ];
    }


    /* ======================================================
       CALCULATE ELAPSED TIME
    ====================================================== */

    $elapsed =
        time() -
        (int)$_SESSION[$createdKey];


    /* ======================================================
       COOLDOWN COMPLETED
    ====================================================== */

    if ($elapsed >= $cooldown) {

        return [
            "allowed" => true,
            "remaining" => 0
        ];
    }


    /* ======================================================
       COOLDOWN STILL ACTIVE
    ====================================================== */

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

    /*
     * Split email into username
     * and domain.
     */

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


    $username =
        $parts[0];

    $domain =
        $parts[1];


    $length =
        strlen($username);


    /* ======================================================
       SHORT USERNAME
    ====================================================== */

    if ($length <= 3) {

        $masked =
            substr(
                $username,
                0,
                1
            ) .
            "***";

    } else {

        /*
         * Keep first 2 and last 2
         * characters visible.
         */

        $masked =
            substr(
                $username,
                0,
                2
            ) .
            str_repeat(
                "*",
                max(
                    3,
                    $length - 4
                )
            ) .
            substr(
                $username,
                -2
            );
    }


    return
        $masked .
        "@" .
        $domain;
}

?>