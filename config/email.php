<?php
/* ==========================================================
   VOTIFY
   Email Configuration
   File : config/email.php
========================================================== */

$config = [

    // Gmail SMTP
    "smtp_host" => "smtp.gmail.com",
    "smtp_port" => 587,
    "smtp_secure" => "tls",

    // These values are loaded from email.local.php
    "smtp_username" => "",
    "smtp_password" => "",

    // Sender details
    "from_email" => "",
    "from_name" => "VOTIFY",

    // OTP validity: 5 minutes
    "otp_expiry" => 300,

    // Resend OTP cooldown: 30 seconds
    "otp_resend_cooldown" => 30,

    // Maximum incorrect OTP attempts
    "otp_max_attempts" => 5
];


/* ==========================================================
   LOAD LOCAL SECRET CONFIG
========================================================== */

$localConfigFile = __DIR__ . "/email.local.php";

if (file_exists($localConfigFile)) {

    $localConfig = require $localConfigFile;

    if (is_array($localConfig)) {

        $config = array_merge(
            $config,
            $localConfig
        );
    }
}


/* ==========================================================
   VALIDATE REQUIRED SMTP CONFIG
========================================================== */

if (
    empty($config["smtp_username"]) ||
    empty($config["smtp_password"])
) {

    error_log(
        "VOTIFY: SMTP username or password is missing."
    );
}


/* ==========================================================
   SET FROM EMAIL
   If from_email is empty, use SMTP username
========================================================== */

if (empty($config["from_email"])) {

    $config["from_email"] =
        $config["smtp_username"];
}


/* ==========================================================
   RETURN FINAL CONFIG
========================================================== */

return $config;

?>