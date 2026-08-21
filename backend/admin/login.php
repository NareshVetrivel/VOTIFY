<?php
/* ==========================================================
   VOTIFY
   Admin Login Backend
   File : backend/admin/login.php
========================================================== */

session_start();

header("Content-Type: application/json");


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */

require_once "log_activity.php";


/* ==========================================================
   GET CLIENT IP ADDRESS
========================================================== */

function getClientIp(): string
{
    /*
    ----------------------------------------------------------
    LOCAL / XAMPP
    Normally returns:
    127.0.0.1 or ::1

    LIVE SERVER
    Returns the client public IP.
    ----------------------------------------------------------
    */

    $ip = $_SERVER["REMOTE_ADDR"] ?? "UNKNOWN";

    /*
    ----------------------------------------------------------
    OPTIONAL PROXY / CLOUDFLARE SUPPORT

    Only trust these headers if your deployment environment
    uses a trusted reverse proxy.
    ----------------------------------------------------------
    */

    if (
        !empty($_SERVER["HTTP_CF_CONNECTING_IP"])
    ) {
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    return substr($ip, 0, 45);
}


/* ==========================================================
   GET FORM DATA
========================================================== */

$username = trim($_POST["username"] ?? "");

$password = $_POST["password"] ?? "";


/* ==========================================================
   EMPTY VALIDATION
========================================================== */

if ($username === "" || $password === "") {

    echo json_encode([
        "status" => "error",
        "message" => "Username and Password are required."
    ]);

    exit;

}


/* ==========================================================
   CHECK ADMIN ACCOUNT
========================================================== */

$sql = "
    SELECT
        id,
        username,
        email,
        password,
        role,
        is_active
    FROM admins
    WHERE username = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);


/* ==========================================================
   DATABASE ERROR CHECK
========================================================== */

if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Unable to process login request."
    ]);

    exit;

}


$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();


/* ==========================================================
   ADMIN NOT FOUND
========================================================== */

if ($result->num_rows !== 1) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "status" => "error",
        "message" => "Invalid username or password."
    ]);

    exit;

}


$admin = $result->fetch_assoc();


/* ==========================================================
   CHECK ADMIN ACCOUNT STATUS
========================================================== */

if ((int)$admin["is_active"] !== 1) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "status" => "error",
        "message" => "This admin account has been disabled."
    ]);

    exit;

}


/* ==========================================================
   VERIFY PASSWORD
========================================================== */

if (!password_verify($password, $admin["password"])) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        "status" => "error",
        "message" => "Invalid username or password."
    ]);

    exit;

}


/* ==========================================================
   LOGIN SUCCESS
========================================================== */

session_regenerate_id(true);


/* ==========================================================
   STORE ADMIN SESSION
========================================================== */

$_SESSION["admin_id"] = (int)$admin["id"];

$_SESSION["admin_username"] = $admin["username"];

$_SESSION["admin_email"] = $admin["email"];

$_SESSION["admin_role"] = $admin["role"];

$_SESSION["admin_logged_in"] = true;


/* ==========================================================
   GET LOGIN IP
========================================================== */

$loginIp = getClientIp();


/* ==========================================================
   UPDATE LAST LOGIN DETAILS
========================================================== */

$updateSql = "
    UPDATE admins
    SET
        last_login = NOW(),
        last_login_ip = ?
    WHERE id = ?
";

$updateStmt = $conn->prepare($updateSql);

if ($updateStmt) {

    $adminId = (int)$admin["id"];

    $updateStmt->bind_param(
        "si",
        $loginIp,
        $adminId
    );

    $updateStmt->execute();

    $updateStmt->close();
}


/* ==========================================================
   SAVE LOGIN ACTIVITY
========================================================== */

logActivity(

    (int)$admin["id"],

    $admin["username"],

    "Admin Login",

    "Administrator logged into the system. Role: "
    . $admin["role"]
    . ". Login IP: "
    . $loginIp

);


/* ==========================================================
   CLOSE DATABASE
========================================================== */

$stmt->close();

$conn->close();


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "status" => "success",

    "message" => "Login Successful",

    "admin" => [

        "username" => $admin["username"],

        "email" => $admin["email"],

        "role" => $admin["role"]

    ]

]);

exit;

?>