<?php
/* ==========================================================
   VOTIFY
   Email Configuration
   File : config/email.php
   Environment : ByetHost
========================================================== */

$config = [

    /* ======================================================
       SMTP SETTINGS
    ====================================================== */

    "smtp_host" => "",
    "smtp_port" => 587,
    "smtp_secure" => "tls",

    /*
     * These must be the credentials of the
     * Byet-hosted email account used by VOTIFY.
     *
     * DO NOT put Gmail credentials here.
     */
    "smtp_username" => "",
    "smtp_password" => "",


    /* ======================================================
       SENDER
    ====================================================== */

    "from_email" => "",
    "from_name" => "VOTIFY",


    /* ======================================================
       OTP SETTINGS
    ====================================================== */

    // OTP validity: 5 minutes
    "otp_expiry" => 300,

    // Resend OTP cooldown: 30 seconds
    "otp_resend_cooldown" => 30,

    // Maximum incorrect OTP attempts
    "otp_max_attempts" => 5
];


/* ==========================================================
   OPTIONAL LOCAL SECRET CONFIG
   ----------------------------------------------------------
   email.local.php is intentionally NOT committed to Git.
   It may exist only on the production server.
========================================================== */

$localConfigFile = __DIR__ . "/email.local.php";

if (is_file($localConfigFile)) {

    $localConfig = require $localConfigFile;

    if (is_array($localConfig)) {

        $config = array_merge(
            $config,
            $localConfig
        );
    }
}


/* ==========================================================
   SET FROM EMAIL
========================================================== */

if (empty($config["from_email"])) {

    $config["from_email"] =
        $config["smtp_username"];
}


/* ==========================================================
   VALIDATE SMTP CONFIGURATION
========================================================== */

$requiredEmailSettings = [
    "smtp_host",
    "smtp_username",
    "smtp_password",
    "from_email"
];

$emailConfigValid = true;

foreach ($requiredEmailSettings as $setting) {

    if (
        !isset($config[$setting]) ||
        trim((string) $config[$setting]) === ""
    ) {

        $emailConfigValid = false;
        break;
    }
}


/* ==========================================================
   CONFIGURATION ERROR
========================================================== */

if (!$emailConfigValid) {

    error_log(
        "VOTIFY: Email service is not configured correctly."
    );
}


/* ==========================================================
   RETURN FINAL CONFIG
========================================================== */

return $config;

?>