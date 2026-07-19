<?php
/* ==========================================================
   VOTIFY
   Admin Login Backend
   File : backend/admin/login.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================
   DATABASE
========================================== */

require_once "../../config/database.php";
require_once "log_activity.php";


/* ==========================================
   GET FORM DATA
========================================== */

$username = trim($_POST["username"] ?? "");

$password = trim($_POST["password"] ?? "");


/* ==========================================
   EMPTY VALIDATION
========================================== */

if ($username === "" || $password === "") {

    echo json_encode([
        "status" => "error",
        "message" => "Username and Password are required."
    ]);

    exit;

}


/* ==========================================
   CHECK ADMIN
========================================== */

$sql = "
SELECT
    id,
    username,
    password
FROM admins
WHERE username = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();


/* ==========================================
   LOGIN VERIFY
========================================== */

if ($result->num_rows === 1) {

    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin["password"])) {

session_regenerate_id(true);

$_SESSION["admin_id"] = $admin["id"];

$_SESSION["admin_username"] = $admin["username"];

/* ==========================================
   SAVE LOGIN LOG
========================================== */

logActivity(

    $admin["id"],

    $admin["username"],

    "Admin Login",

    "Administrator logged into the system."

);

/* ==========================================
   RESPONSE
========================================== */

echo json_encode([

    "status" => "success",

    "message" => "Login Successful"

]);

    }

    else {

        echo json_encode([

            "status" => "error",

            "message" => "Invalid Password."

        ]);

    }

}

else {

    echo json_encode([

        "status" => "error",

        "message" => "Admin Username not found."

    ]);

}

$stmt->close();

$conn->close();

?>