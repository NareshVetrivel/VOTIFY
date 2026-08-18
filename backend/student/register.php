<?php
/* ==========================================================
   VOTIFY
   Student Registration
   File : backend/student/register.php
========================================================== */

header("Content-Type: application/json");

/* ==========================================
   DATABASE
========================================== */

require_once("../../config/database.php");

/** @var mysqli $conn */

/* ==========================================
   ELECTION STATUS CHECK
========================================== */

$result = mysqli_query(

    $conn,

    "

    SELECT election_status

    FROM election_settings

    LIMIT 1

    "

);

if(!$result || mysqli_num_rows($result)==0){

    echo json_encode([

        "status" => "error",

        "message" => "Unable to verify election status."

    ]);

    exit;

}

$election = mysqli_fetch_assoc($result);

if(strtolower($election["election_status"]) === "started"){

    echo json_encode([

        "status" => "error",

        "message" => "Registration is unavailable while the election is running."

    ]);

    exit;

}

/* ==========================================
   ALLOW POST ONLY
========================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid Request."
    ]);

    exit;

}


/* ==========================================
   GET FORM DATA
========================================== */

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

/* ==========================================
   SERVER SIDE VALIDATION
========================================== */

/* Full Name */

if (empty($full_name)) {

    echo json_encode([
        "status" => "error",
        "field"  => "fullName",
        "message" => "Full Name is required."
    ]);

    exit;

}


/* Date of Birth */

if (empty($dob)) {

    echo json_encode([
        "status" => "error",
        "field"  => "dob",
        "message" => "Date of Birth is required."
    ]);

    exit;

}


/* Admission Number */

if (empty($admission_no)) {

    echo json_encode([
        "status" => "error",
        "field"  => "admissionNo",
        "message" => "Admission Number is required."
    ]);

    exit;

}


/* Phone Number */

if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {

    echo json_encode([
        "status" => "error",
        "field"  => "phone",
        "message" => "Enter a valid 10-digit phone number."
    ]);

    exit;

}


/* College Email */

if (!filter_var($college_email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "status" => "error",
        "field"  => "email",
        "message" => "Invalid Email Address."
    ]);

    exit;

}

if (!preg_match('/^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/', $college_email)) {

    echo json_encode([
        "status" => "error",
        "field"  => "email",
        "message" => "Use only College Email (@sonatech.ac.in)."
    ]);

    exit;

}


/* Department */

if ($department !== "MCA") {

    echo json_encode([
        "status" => "error",
        "field"  => "department",
        "message" => "Only MCA students can register."
    ]);

    exit;

}


/* Year */

if (!in_array($year, ["I Year", "II Year"])) {

    echo json_encode([
        "status" => "error",
        "field"  => "year",
        "message" => "Select a valid Year."
    ]);

    exit;

}


/* Gender */

if (!in_array($gender, ["Male", "Female", "Other"])) {

    echo json_encode([
        "status" => "error",
        "field"  => "gender",
        "message" => "Select your Gender."
    ]);

    exit;

}


/* Password */

if (strlen($password) < 8) {

    echo json_encode([
        "status" => "error",
        "field"  => "password",
        "message" => "Password must contain at least 8 characters."
    ]);

    exit;

}


/* Confirm Password */

if ($password !== $confirm_password) {

    echo json_encode([
        "status" => "error",
        "field"  => "confirmPassword",
        "message" => "Passwords do not match."
    ]);

    exit;

}

/* ==========================================
   DUPLICATE CHECK
========================================== */

/* Admission Number */

$stmt = $conn->prepare(
    "SELECT id FROM students
     WHERE admission_no = ?"
);

$stmt->bind_param(
    "s",
    $admission_no
);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    echo json_encode([

        "status" => "error",

        "field" => "admissionNo",

        "message" => "Admission Number already registered."

    ]);

    $stmt->close();

    exit;

}

$stmt->close();


/* ==========================================
   Phone Number
========================================== */

$stmt = $conn->prepare(
    "SELECT id FROM students
     WHERE phone = ?"
);

$stmt->bind_param(
    "s",
    $phone
);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    echo json_encode([

        "status" => "error",

        "field" => "phone",

        "message" => "Phone Number already registered."

    ]);

    $stmt->close();

    exit;

}

$stmt->close();


/* ==========================================
   College Email
========================================== */

$stmt = $conn->prepare(
    "SELECT id FROM students
     WHERE college_email = ?"
);

$stmt->bind_param(
    "s",
    $college_email
);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    echo json_encode([

        "status" => "error",

        "field" => "email",

        "message" => "College Email already registered."

    ]);

    $stmt->close();

    exit;

}

$stmt->close();

/* ==========================================
   HASH PASSWORD
========================================== */

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/* ==========================================
   INSERT STUDENT
========================================== */

$stmt = $conn->prepare(

    "INSERT INTO students (

        full_name,

        dob,

        admission_no,

        phone,

        college_email,

        department,

        year,

        gender,

        password,

        status

    )

    VALUES (

        ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending'

    )"

);

$stmt->bind_param(

    "sssssssss",

    $full_name,

    $dob,

    $admission_no,

    $phone,

    $college_email,

    $department,

    $year,

    $gender,

    $hashed_password

);


/* ==========================================
   EXECUTE QUERY
========================================== */

if ($stmt->execute()) {

    echo json_encode([

        "status"  => "success",

        "message" => "Registration Successful."

    ]);

}

else {

    echo json_encode([

        "status"  => "error",

        "message" => "Unable to complete registration."

    ]);

}

/* ==========================================
   CLOSE CONNECTION
========================================== */

$stmt->close();

$conn->close();

exit;

?>