<?php
/* ==========================================================
   VOTIFY
   Admin Login Backend
   File : backend/admin/login.php
========================================================== */

session_start();

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   REQUEST METHOD
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);

    exit;

}


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
    CLOUDFLARE SUPPORT
    ----------------------------------------------------------

    Only use this when the deployment environment
    actually uses Cloudflare as a trusted proxy.
    */

    if (
        !empty($_SERVER["HTTP_CF_CONNECTING_IP"])
    ) {

        $ip =
            $_SERVER["HTTP_CF_CONNECTING_IP"];

    }


    return substr(
        trim($ip),
        0,
        45
    );
}


/* ==========================================================
   GET FORM DATA
========================================================== */

$username =
    trim(
        (string) (
            $_POST["username"] ?? ""
        )
    );

$password =
    (string) (
        $_POST["password"] ?? ""
    );


/* ==========================================================
   EMPTY VALIDATION
========================================================== */

if (
    $username === "" ||
    $password === ""
) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Username and Password are required."
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


$stmt =
    mysqli_prepare(
        $conn,
        $sql
    );


/* ==========================================================
   DATABASE PREPARE ERROR
========================================================== */

if (!$stmt) {

    error_log(
        "VOTIFY Admin Login: "
        . "Unable to prepare admin lookup."
    );

    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Unable to process login request."
    ]);

    exit;

}


/* ==========================================================
   BIND USERNAME
========================================================== */

if (
    !mysqli_stmt_bind_param(
        $stmt,
        "s",
        $username
    )
) {

    mysqli_stmt_close($stmt);

    error_log(
        "VOTIFY Admin Login: "
        . "Unable to bind username."
    );

    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Unable to process login request."
    ]);

    exit;

}


/* ==========================================================
   EXECUTE ADMIN LOOKUP
========================================================== */

if (
    !mysqli_stmt_execute($stmt)
) {

    mysqli_stmt_close($stmt);

    error_log(
        "VOTIFY Admin Login: "
        . "Admin lookup execution failed."
    );

    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Unable to process login request."
    ]);

    exit;

}


/* ==========================================================
   FETCH ADMIN DATA
========================================================== */

/*
 * bind_result() is used instead of get_result()
 * for broader shared-host compatibility.
 */

mysqli_stmt_bind_result(

    $stmt,

    $adminId,
    $adminUsername,
    $adminEmail,
    $adminPassword,
    $adminRole,
    $adminIsActive

);


$adminFound =
    mysqli_stmt_fetch($stmt);


mysqli_stmt_close($stmt);


/* ==========================================================
   ADMIN NOT FOUND
========================================================== */

if (!$adminFound) {

    mysqli_close($conn);

    http_response_code(401);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Invalid username or password."
    ]);

    exit;

}


/* ==========================================================
   CHECK ADMIN ACCOUNT STATUS
========================================================== */

if ((int) $adminIsActive !== 1) {

    mysqli_close($conn);

    http_response_code(403);

    echo json_encode([
        "status" => "error",
        "message" =>
            "This admin account has been disabled."
    ]);

    exit;

}


/* ==========================================================
   VERIFY PASSWORD
========================================================== */

if (
    !password_verify(
        $password,
        $adminPassword
    )
) {

    mysqli_close($conn);

    http_response_code(401);

    echo json_encode([
        "status" => "error",
        "message" =>
            "Invalid username or password."
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

$_SESSION["admin_id"] =
    (int) $adminId;

$_SESSION["admin_username"] =
    $adminUsername;

$_SESSION["admin_email"] =
    $adminEmail;

$_SESSION["admin_role"] =
    $adminRole;

$_SESSION["admin_logged_in"] =
    true;


/* ==========================================================
   GET LOGIN IP
========================================================== */

$loginIp =
    getClientIp();


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


$updateStmt =
    mysqli_prepare(
        $conn,
        $updateSql
    );


if ($updateStmt) {

    $adminIdForUpdate =
        (int) $adminId;


    mysqli_stmt_bind_param(

        $updateStmt,

        "si",

        $loginIp,

        $adminIdForUpdate

    );


    /*
     * Last-login information is supplementary.
     * A failure here must not invalidate an otherwise
     * successful authentication.
     */

    if (
        !mysqli_stmt_execute(
            $updateStmt
        )
    ) {

        error_log(
            "VOTIFY Admin Login: "
            . "Unable to update last login details."
        );

    }


    mysqli_stmt_close(
        $updateStmt
    );

}


/* ==========================================================
   SAVE LOGIN ACTIVITY
========================================================== */

logActivity(

    (int) $adminId,

    $adminUsername,

    "Admin Login",

    "Administrator logged into the system. Role: "
    . $adminRole
    . ". Login IP: "
    . $loginIp

);


/* ==========================================================
   CLOSE DATABASE
========================================================== */

mysqli_close($conn);


/* ==========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "status" =>
        "success",

    "message" =>
        "Login Successful",

    "admin" => [

        "username" =>
            $adminUsername,

        "email" =>
            $adminEmail,

        "role" =>
            $adminRole

    ]

], JSON_UNESCAPED_UNICODE);

exit;

?>