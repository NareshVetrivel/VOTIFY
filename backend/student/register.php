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

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../lib/otp.php";
require_once __DIR__ . "/../../lib/mailer.php";

$emailConfig = require __DIR__ . "/../../config/email.php";


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

$full_name = trim(
    $_POST["fullName"] ?? ""
);

$dob = trim(
    $_POST["dob"] ?? ""
);

$admission_no = strtoupper(
    trim($_POST["admissionNo"] ?? "")
);

$phone = trim(
    $_POST["phone"] ?? ""
);

$college_email = strtolower(
    trim($_POST["email"] ?? "")
);

$department = trim(
    $_POST["department"] ?? ""
);

$year = trim(
    $_POST["year"] ?? ""
);

$gender = trim(
    $_POST["gender"] ?? ""
);

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


/*
 * Only English letters and spaces are allowed.
 */

if (!preg_match('/^[A-Za-z ]+$/', $full_name)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "fullName",
        "message" =>
            "Full Name can contain only letters and spaces."
    ]);

    exit;
}


/*
 * Prevent a name containing only spaces.
 */

if (!preg_match('/[A-Za-z]/', $full_name)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "fullName",
        "message" =>
            "Please enter a valid Full Name."
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


/*
 * Validate actual date format.
 */

$dobDate = DateTime::createFromFormat(
    "Y-m-d",
    $dob
);

if (
    !$dobDate ||
    $dobDate->format("Y-m-d") !== $dob
) {

    echo json_encode([
        "status"  => "error",
        "field"   => "dob",
        "message" => "Enter a valid Date of Birth."
    ]);

    exit;
}


/* ==========================================================
   ADMISSION NUMBER VALIDATION
========================================================== */

/*
 * Required format:
 *
 * 25CAPMCA080
 *
 * Structure:
 *
 * 2 digits
 * +
 * CAPMCA
 * +
 * 3 digits
 *
 * Total = 11 characters
 */

if ($admission_no === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" => "Admission Number is required."
    ]);

    exit;
}


/*
 * Exact Admission Number pattern.
 *
 * Example:
 *
 * 25CAPMCA080
 * 26CAPMCA123
 */

if (!preg_match(
    '/^[0-9]{2}CAPMCA[0-9]{3}$/',
    $admission_no
)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" =>
            "Admission Number must follow the format 25CAPMCA080."
    ]);

    exit;
}


/* ==========================================================
   PHONE VALIDATION
========================================================== */

if ($phone === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "phone",
        "message" => "Phone Number is required."
    ]);

    exit;
}


/*
 * Indian mobile number:
 *
 * - Exactly 10 digits
 * - First digit must be 6, 7, 8 or 9
 */

if (!preg_match(
    '/^[6-9][0-9]{9}$/',
    $phone
)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "phone",
        "message" =>
            "Enter a valid 10-digit Indian mobile number."
    ]);

    exit;
}


/* ==========================================================
   COLLEGE EMAIL VALIDATION
========================================================== */

if ($college_email === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" => "College Email is required."
    ]);

    exit;
}


if (!filter_var(
    $college_email,
    FILTER_VALIDATE_EMAIL
)) {

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
   DEPARTMENT VALIDATION
========================================================== */

if ($department !== "MCA") {

    echo json_encode([
        "status"  => "error",
        "field"   => "department",
        "message" => "Only MCA students can register."
    ]);

    exit;
}


/* ==========================================================
   YEAR VALIDATION
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
   GENDER VALIDATION
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
   PASSWORD VALIDATION
========================================================== */

if ($password === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" => "Password is required."
    ]);

    exit;
}


/* ---------- Minimum 8 characters ---------- */

if (strlen($password) < 8) {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" =>
            "Password must contain at least 8 characters."
    ]);

    exit;
}


/* ---------- Uppercase letter ---------- */

if (!preg_match('/[A-Z]/', $password)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" =>
            "Password must contain at least one uppercase letter."
    ]);

    exit;
}


/* ---------- Number ---------- */

if (!preg_match('/[0-9]/', $password)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" =>
            "Password must contain at least one number."
    ]);

    exit;
}


/* ---------- Special character ---------- */

if (!preg_match('/[^A-Za-z0-9]/', $password)) {

    echo json_encode([
        "status"  => "error",
        "field"   => "password",
        "message" =>
            "Password must contain at least one special character."
    ]);

    exit;
}


/* ==========================================================
   CONFIRM PASSWORD
========================================================== */

if ($confirm_password === "") {

    echo json_encode([
        "status"  => "error",
        "field"   => "confirmPassword",
        "message" => "Please confirm your password."
    ]);

    exit;
}


if ($password !== $confirm_password) {

    echo json_encode([
        "status"  => "error",
        "field"   => "confirmPassword",
        "message" => "Passwords do not match."
    ]);

    exit;
}


/* ==========================================================
   REMOVE OLD REJECTED RECORD
========================================================== */

/*
 * IMPORTANT:
 *
 * Rejected registrations are not retained in the
 * students table.
 *
 * This is a safety cleanup for any rejected record
 * that may already exist from the previous system flow.
 *
 * Once a rejected record is found for the same
 * Admission Number, Phone Number, or College Email,
 * it is removed before duplicate checks.
 *
 * This allows the database UNIQUE constraints to
 * remain active without blocking re-registration.
 */

$cleanupStmt = $conn->prepare(
    "DELETE FROM students
     WHERE LOWER(TRIM(status)) = 'rejected'
     AND (
         admission_no = ?
         OR phone = ?
         OR college_email = ?
     )"
);

if (!$cleanupStmt) {

    error_log(
        "VOTIFY Registration Rejected Cleanup Prepare Error: " .
        $conn->error
    );

    echo json_encode([
        "status"  => "error",
        "message" => "Unable to process registration."
    ]);

    exit;
}


$cleanupStmt->bind_param(
    "sss",
    $admission_no,
    $phone,
    $college_email
);

$cleanupStmt->execute();

$cleanupStmt->close();


/* ==========================================================
   DUPLICATE ADMISSION NUMBER CHECK
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
        "message" => "Unable to process registration."
    ]);

    exit;
}


$stmt->bind_param(
    "s",
    $admission_no
);

$stmt->execute();

$stmt->store_result();


if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "admissionNo",
        "message" => "Admission Number already registered."
    ]);

    exit;
}


$stmt->close();


/* ==========================================================
   DUPLICATE PHONE NUMBER CHECK
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
        "message" => "Unable to process registration."
    ]);

    exit;
}


$stmt->bind_param(
    "s",
    $phone
);

$stmt->execute();

$stmt->store_result();


if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "phone",
        "message" => "Phone Number already registered."
    ]);

    exit;
}


$stmt->close();


/* ==========================================================
   DUPLICATE COLLEGE EMAIL CHECK
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
        "message" => "Unable to process registration."
    ]);

    exit;
}


$stmt->bind_param(
    "s",
    $college_email
);

$stmt->execute();

$stmt->store_result();


if ($stmt->num_rows > 0) {

    $stmt->close();

    echo json_encode([
        "status"  => "error",
        "field"   => "email",
        "message" => "College Email already registered."
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
        "message" => "Unable to secure password."
    ]);

    exit;
}


/* ==========================================================
   STORE TEMPORARY REGISTRATION DATA
========================================================== */

/*
 * IMPORTANT:
 *
 * Student record is NOT inserted yet.
 *
 * Registration data is temporarily stored
 * inside the session.
 *
 * Actual student INSERT happens only after
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

    unset(
        $_SESSION["pending_registration"]
    );

    echo json_encode([
        "status"  => "error",
        "message" => "Unable to generate OTP."
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

    $otpExpiry = (int) $emailConfig["otp_expiry"];
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