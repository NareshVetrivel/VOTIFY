<?php
/* ==========================================================
   VOTIFY
   Student Registration + Registration OTP
   File : backend/student/register.php
========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   REQUIRED FILES
========================================================== */

require_once("../../config/database.php");
require_once("../../lib/otp.php");
require_once("../../lib/mailer.php");

$emailConfig = require("../../config/email.php");


/* ==========================================================
   ALLOW POST ONLY
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status"  => "error",
        "message" => "Invalid Request."
    ]);

    exit;
}


/* ==========================================================
   ELECTION STATUS CHECK
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT election_status
     FROM election_settings
     LIMIT 1"
);

if (!$result || mysqli_num_rows($result) === 0) {

    echo json_encode([
        "status"  => "error",
        "message" => "Unable to verify election status."
    ]);

    exit;
}

$election = mysqli_fetch_assoc($result);

if (
    isset($election["election_status"]) &&
    strtolower(trim($election["election_status"])) === "started"
) {

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Registration is unavailable while the election is running."
    ]);

    exit;
}


/* ==========================================================
   GET FORM DATA
========================================================== */

$full_name = trim($_POST["fullName"] ?? "");

$dob = trim($_POST["dob"] ?? "");

$admission_no = strtoupper(
    trim($_POST["admissionNo"] ?? "")
);

$phone = trim($_POST["phone"] ?? "");

$college_email = strtolower(
    trim($_POST["email"] ?? "")
);

$department = trim($_POST["department"] ?? "");

$year = trim($_POST["year"] ?? "");

$gender = trim($_POST["gender"] ?? "");

$password = $_POST["password"] ?? "";

$confirm_password = $_POST["confirmPassword"] ?? "";


/* ==========================================================
   FULL NAME VALIDATION
========================================================== */

if ($full_name === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "fullName",
        "message" => "Full Name is required."
    ]);

    exit;
}

if (!preg_match('/^[A-Za-z ]+$/', $full_name)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "fullName",
        "message" =>
            "Full Name can contain only letters and spaces."
    ]);

    exit;
}


/* ==========================================================
   DOB VALIDATION
========================================================== */

if ($dob === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "dob",
        "message" => "Date of Birth is required."
    ]);

    exit;
}


/* ==========================================================
   ADMISSION NUMBER VALIDATION
========================================================== */

/*
 * Requirement:
 *
 * Example:
 * 25CAPMCA092
 *
 * Allowed:
 * - A-Z
 * - a-z
 * - 0-9
 *
 * Length:
 * - Minimum 10
 * - Maximum 15
 */

if ($admission_no === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" => "Admission Number is required."
    ]);

    exit;
}

if (!preg_match('/^[A-Z0-9]{10,15}$/', $admission_no)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" =>
            "Admission Number must contain only letters and numbers and must be 10 to 15 characters."
    ]);

    exit;
}


/* ==========================================================
   PHONE VALIDATION
========================================================== */

if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "phone",
        "message" =>
            "Enter a valid 10-digit phone number."
    ]);

    exit;
}


/* ==========================================================
   COLLEGE EMAIL VALIDATION
========================================================== */

if (!filter_var($college_email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" => "Invalid Email Address."
    ]);

    exit;
}


/* ==========================================================
   ONLY SONATECH EMAIL
========================================================== */

if (
    !preg_match(
        '/^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/i',
        $college_email
    )
) {

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" =>
            "Use only College Email (@sonatech.ac.in)."
    ]);

    exit;
}


/* ==========================================================
   DEPARTMENT
========================================================== */

if ($department !== "MCA") {

    echo json_encode([
        "status"  => "error",
        "field"   => "department",
        "message" =>
            "Only MCA students can register."
    ]);

    exit;
}


/* ==========================================================
   YEAR
========================================================== */

if (
    !in_array(
        $year,
        ["I Year", "II Year"],
        true
    )
) {

    echo json_encode([
        "status"  => "error",
        "field"   => "year",
        "message" => "Select a valid Year."
    ]);

    exit;
}


/* ==========================================================
   GENDER
========================================================== */

if (
    !in_array(
        $gender,
        ["Male", "Female", "Other"],
        true
    )
) {

    echo json_encode([
        "status"  => "error",
        "field"   => "gender",
        "message" => "Select your Gender."
    ]);

    exit;
}


/* ==========================================================
   PASSWORD
========================================================== */

if (strlen($password) < 8) {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" =>
            "Password must contain at least 8 characters."
    ]);

    exit;
}


/* ==========================================================
   CONFIRM PASSWORD
========================================================== */

if ($password !== $confirm_password) {

    echo json_encode([
        "status"  => "error",
        "field"   => "confirmPassword",
        "message" =>
            "Passwords do not match."
    ]);

    exit;
}


/* ==========================================================
   DUPLICATE ADMISSION NUMBER
========================================================== */

$stmt = $conn->prepare(
    "SELECT id
     FROM students
     WHERE admission_no = ?
     LIMIT 1"
);

if (!$stmt) {

    error_log(
        "VOTIFY Registration Prepare Error: " .
        $conn->error
    );

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Unable to process registration."
    ]);

    exit;
}

$stmt->bind_param("s", $admission_no);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" =>
            "Admission Number already registered."
    ]);

    exit;
}

$stmt->close();


/* ==========================================================
   DUPLICATE PHONE NUMBER
========================================================== */

$stmt = $conn->prepare(
    "SELECT id
     FROM students
     WHERE phone = ?
     LIMIT 1"
);

if (!$stmt) {

    error_log(
        "VOTIFY Registration Prepare Error: " .
        $conn->error
    );

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Unable to process registration."
    ]);

    exit;
}

$stmt->bind_param("s", $phone);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "phone",
        "message" =>
            "Phone Number already registered."
    ]);

    exit;
}

$stmt->close();


/* ==========================================================
   DUPLICATE COLLEGE EMAIL
========================================================== */

$stmt = $conn->prepare(
    "SELECT id
     FROM students
     WHERE college_email = ?
     LIMIT 1"
);

if (!$stmt) {

    error_log(
        "VOTIFY Registration Prepare Error: " .
        $conn->error
    );

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Unable to process registration."
    ]);

    exit;
}

$stmt->bind_param("s", $college_email);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" =>
            "College Email already registered."
    ]);

    exit;
}

$stmt->close();


/* ==========================================================
   HASH PASSWORD
========================================================== */

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);

if ($hashed_password === false) {

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Unable to secure password."
    ]);

    exit;
}


/* ==========================================================
   STORE TEMP REGISTRATION DATA
========================================================== */

/*
 * IMPORTANT:
 *
 * Student record is NOT inserted yet.
 *
 * Data is temporarily stored in session.
 *
 * Actual INSERT happens only after
 * successful OTP verification.
 */

$_SESSION["pending_registration"] = [

    "full_name" =>
        $full_name,

    "dob" =>
        $dob,

    "admission_no" =>
        $admission_no,

    "phone" =>
        $phone,

    "college_email" =>
        $college_email,

    "department" =>
        $department,

    "year" =>
        $year,

    "gender" =>
        $gender,

    "password" =>
        $hashed_password
];


/* ==========================================================
   GENERATE 6 DIGIT OTP
========================================================== */

$otp = generateOtp();


if (
    !is_string($otp) &&
    !is_int($otp)
) {

    unset($_SESSION["pending_registration"]);

    echo json_encode([
        "status"  => "error",
        "message" =>
            "Unable to generate OTP."
    ]);

    exit;
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
   CREATE OTP SESSION
========================================================== */

createOtpSession(
    "register",
    $otp,
    $otpExpiry
);


/* ==========================================================
   SEND OTP EMAIL
========================================================== */

$mailResult = sendOtpMail(
    $college_email,
    $full_name,
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
     * Remove temporary registration
     * if email could not be sent.
     */

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    $mailMessage =
        "Unable to send OTP email. Please try again.";

    if (
        is_array($mailResult) &&
        !empty($mailResult["message"])
    ) {
        $mailMessage =
            $mailResult["message"];
    }

    error_log(
        "VOTIFY Registration OTP Mail Failed: " .
        $mailMessage
    );

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" => $mailMessage
    ]);

    exit;
}


/* ==========================================================
   OTP SENT SUCCESSFULLY
========================================================== */

echo json_encode([

    "status" =>
        "otp_required",

    "message" =>
        "OTP sent successfully to your college email.",

    "email" =>
        maskEmail($college_email),

    "expires_in" =>
        $otpExpiry
]);


/* ==========================================================
   CLOSE DATABASE
========================================================== */

$conn->close();

exit;
?>