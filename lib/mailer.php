<?php

/* ==========================================================
   VOTIFY
   Email / OTP Mailer
   File: lib/mailer.php
   ========================================================== */


/* ==========================================================
   LOAD PHPMailer
   ========================================================== */

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/* ==========================================================
   LOAD EMAIL CONFIG
   ========================================================== */

$emailConfigPath = __DIR__ . '/../config/email.php';

if (!file_exists($emailConfigPath)) {

    throw new Exception(
        'Email configuration file not found.'
    );
}

$emailConfig = require $emailConfigPath;


/* ==========================================================
   SUPPORT OLD CONFIG NAMES
   ========================================================== */

if (
    empty($emailConfig["smtp_username"]) &&
    !empty($emailConfig["username"])
) {

    $emailConfig["smtp_username"] =
        $emailConfig["username"];
}

if (
    empty($emailConfig["smtp_password"]) &&
    !empty($emailConfig["password"])
) {

    $emailConfig["smtp_password"] =
        $emailConfig["password"];
}


/* ==========================================================
   DEFAULT SETTINGS
   ========================================================== */

if (empty($emailConfig["smtp_host"])) {

    $emailConfig["smtp_host"] =
        "smtp.gmail.com";
}

if (empty($emailConfig["smtp_port"])) {

    $emailConfig["smtp_port"] = 587;
}

if (empty($emailConfig["smtp_secure"])) {

    $emailConfig["smtp_secure"] = "tls";
}


/* ==========================================================
   DEFAULT FROM EMAIL
   ========================================================== */

if (
    empty($emailConfig["from_email"]) &&
    !empty($emailConfig["smtp_username"])
) {

    $emailConfig["from_email"] =
        $emailConfig["smtp_username"];
}


/* ==========================================================
   DEFAULT FROM NAME
   ========================================================== */

if (empty($emailConfig["from_name"])) {

    $emailConfig["from_name"] =
        "VOTIFY - Sona College";
}


/* ==========================================================
   SEND OTP MAIL
   ========================================================== */

function sendOtpMail(
    string $recipientEmail,
    string $recipientName,
    string $otp,
    string $purpose = "login"
): array {

    global $emailConfig;


    /* ======================================================
       CLEAN INPUT
       ====================================================== */

    $recipientEmail =
        trim($recipientEmail);

    $recipientName =
        trim($recipientName);

    $otp =
        trim($otp);


    /* ======================================================
       VALIDATE RECIPIENT EMAIL
       ====================================================== */

    if (
        $recipientEmail === "" ||
        !filter_var(
            $recipientEmail,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        return [

            "success" => false,

            "message" =>
                "Invalid recipient email address."

        ];
    }


    /* ======================================================
       VALIDATE OTP
       ====================================================== */

    if (
        !preg_match(
            '/^[0-9]{6}$/',
            $otp
        )
    ) {

        return [

            "success" => false,

            "message" =>
                "Invalid OTP format."

        ];
    }


    /* ======================================================
       CHECK CONFIG
       ====================================================== */

    $requiredConfig = [

        "smtp_host",
        "smtp_port",
        "smtp_secure",
        "smtp_username",
        "smtp_password",
        "from_email",
        "from_name"

    ];


    foreach (
        $requiredConfig as $key
    ) {

        if (
            !isset($emailConfig[$key]) ||
            trim(
                (string)$emailConfig[$key]
            ) === ""
        ) {

            error_log(
                "VOTIFY Email Config Missing: " .
                $key
            );

            return [

                "success" => false,

                "message" =>
                    "Email service is not configured correctly."

            ];
        }
    }


    /* ======================================================
       CREATE PHPMailer
       ====================================================== */

    $mail =
        new PHPMailer(true);


    try {

        /* ==================================================
           SMTP
           ================================================== */

        $mail->isSMTP();

        $mail->Host =
            $emailConfig["smtp_host"];

        $mail->SMTPAuth =
            true;

        $mail->Username =
            $emailConfig["smtp_username"];

        $mail->Password =
            $emailConfig["smtp_password"];

        $mail->Port =
            (int)$emailConfig["smtp_port"];


        /* ==================================================
           SECURITY
           ================================================== */

        $secure =
            strtolower(
                trim(
                    (string)
                    $emailConfig["smtp_secure"]
                )
            );


        if ($secure === "tls") {

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->SMTPAutoTLS = true;

        } elseif ($secure === "ssl") {

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_SMTPS;

        } else {

            $mail->SMTPSecure = false;

            $mail->SMTPAutoTLS = false;
        }


        /* ==================================================
           GENERAL MAIL SETTINGS
           ================================================== */

        $mail->CharSet =
            "UTF-8";

        /*
         * 8bit keeps the mail body simple and readable
         * for normal UTF-8 transactional emails.
         */
        $mail->Encoding =
            "8bit";

        $mail->Timeout =
            20;

        $mail->SMTPKeepAlive =
            false;


        /* ==================================================
           SENDER
           ================================================== */

        $mail->setFrom(
            $emailConfig["from_email"],
            "VOTIFY"
        );


        /*
         * Reply-To remains the same authenticated address.
         */
        $mail->addReplyTo(
            $emailConfig["from_email"],
            "VOTIFY"
        );


        /* ==================================================
           RECIPIENT
           ================================================== */

        $mail->addAddress(
            $recipientEmail,
            $recipientName
        );


        /* ==================================================
           EMAIL TYPE
           ================================================== */

        if (
            $purpose === "registration"
        ) {

            $subject =
                "Your VOTIFY verification code";

            $title =
                "Registration Verification";

            $description =
                "Use the verification code below to verify your college email and complete your VOTIFY registration.";

        } else {

            $subject =
                "Your VOTIFY login verification code";

            $title =
                "Login Verification";

            $description =
                "Use the verification code below to verify your identity and continue to the VOTIFY voting system.";
        }


        /* ==================================================
           SAFE OUTPUT
           ================================================== */

        $safeName =
            htmlspecialchars(
                $recipientName,
                ENT_QUOTES,
                "UTF-8"
            );

        $safeOtp =
            htmlspecialchars(
                $otp,
                ENT_QUOTES,
                "UTF-8"
            );

        $safeTitle =
            htmlspecialchars(
                $title,
                ENT_QUOTES,
                "UTF-8"
            );

        $safeDescription =
            htmlspecialchars(
                $description,
                ENT_QUOTES,
                "UTF-8"
            );


        /* ==================================================
           HTML EMAIL
           ================================================== */

        $htmlBody = <<<HTML
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>VOTIFY Verification Code</title>

</head>


<body style="
margin:0;
padding:0;
background:#f5f6f8;
font-family:Arial,Helvetica,sans-serif;
">

<table
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
style="padding:30px 10px;"
>

<tr>

<td align="center">


<table
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
style="
max-width:520px;
background:#ffffff;
border:1px solid #e5e7eb;
border-radius:10px;
overflow:hidden;
"
>


<!-- HEADER -->

<tr>

<td
style="
background:#111827;
padding:24px;
text-align:center;
"
>

<div
style="
font-size:26px;
font-weight:bold;
color:#ffffff;
"
>

VOTIFY

</div>


<div
style="
font-size:13px;
color:#d1d5db;
margin-top:5px;
"
>

Sona College Online Voting System

</div>

</td>

</tr>


<!-- CONTENT -->

<tr>

<td
style="
padding:30px 25px;
"
>


<p
style="
margin:0 0 15px 0;
font-size:16px;
color:#111827;
"
>

Hello {$safeName},

</p>


<h2
style="
margin:0 0 12px 0;
font-size:21px;
color:#111827;
"
>

{$safeTitle}

</h2>


<p
style="
margin:0 0 22px 0;
font-size:14px;
line-height:1.6;
color:#4b5563;
"
>

{$safeDescription}

</p>


<!-- OTP -->

<table
width="100%"
cellpadding="0"
cellspacing="0"
border="0"
>

<tr>

<td
align="center"
style="
padding:22px;
background:#f3f4f6;
border:1px solid #e5e7eb;
border-radius:8px;
"
>


<div
style="
font-size:12px;
color:#6b7280;
margin-bottom:8px;
text-transform:uppercase;
letter-spacing:1px;
"
>

Verification Code

</div>


<div
style="
font-size:32px;
font-weight:bold;
letter-spacing:7px;
color:#111827;
"
>

{$safeOtp}

</div>


</td>

</tr>

</table>


<p
style="
margin:22px 0 8px 0;
font-size:13px;
line-height:1.5;
color:#4b5563;
"
>

This verification code is valid for a limited time. Do not share this code with anyone.

</p>


<p
style="
margin:0;
font-size:12px;
line-height:1.5;
color:#6b7280;
"
>

If you did not request this code, you can safely ignore this email.

</p>


</td>

</tr>


<!-- FOOTER -->

<tr>

<td
style="
padding:18px 25px;
text-align:center;
background:#f9fafb;
border-top:1px solid #e5e7eb;
"
>

<p
style="
margin:0;
font-size:11px;
color:#6b7280;
"
>

This is an automated email from VOTIFY.

</p>


<p
style="
margin:6px 0 0 0;
font-size:11px;
color:#9ca3af;
"
>

VOTIFY - Sona College Online Voting System

</p>

</td>

</tr>


</table>


</td>

</tr>

</table>

</body>

</html>
HTML;


        /* ==================================================
           PLAIN TEXT VERSION
           ================================================== */

        $plainText =
            "VOTIFY - Sona College Online Voting System\n\n" .

            "Hello " .
            $recipientName .
            ",\n\n" .

            $title .
            "\n\n" .

            $description .
            "\n\n" .

            "Verification Code: " .
            $otp .
            "\n\n" .

            "This verification code is valid for a limited time.\n" .
            "Do not share this code with anyone.\n\n" .

            "If you did not request this code, " .
            "you can safely ignore this email.\n";


        /* ==================================================
           SUBJECT
           ================================================== */

        $mail->Subject =
            $subject;


        /* ==================================================
           BODY
           ================================================== */

        $mail->isHTML(true);

        $mail->Body =
            $htmlBody;

        $mail->AltBody =
            $plainText;


        /* ==================================================
           SEND
           ================================================== */

        $mail->send();


        /* ==================================================
           SUCCESS
           ================================================== */

        return [

            "success" => true,

            "message" =>
                "OTP email sent successfully."

        ];


    } catch (Exception $e) {


        /* ==================================================
           ERROR LOG
           ================================================== */

        error_log(
            "VOTIFY Mail Error: " .
            $mail->ErrorInfo
        );

        error_log(
            "VOTIFY Mail Exception: " .
            $e->getMessage()
        );


        return [

            "success" => false,

            "message" =>
                "Unable to send OTP email. Please try again."

        ];
    }
}

?>