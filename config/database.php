<?php
/* ==========================================================
   VOTIFY
   Database Configuration
   File : config/database.php
   Environment : ByetHost MySQL
========================================================== */

date_default_timezone_set("Asia/Kolkata");


/* ==========================================================
   LOAD LOCAL / PRODUCTION DATABASE CONFIGURATION
========================================================== */

/*
 * IMPORTANT:
 * database.local.php contains sensitive database credentials.
 *
 * This file is excluded from Git using .gitignore.
 *
 * Do NOT put database credentials directly inside this file.
 */

$localConfig = __DIR__ . "/database.local.php";

if (!file_exists($localConfig)) {

    error_log(
        "VOTIFY Database Error: database.local.php is missing."
    );

    http_response_code(500);

    die(
        "Database configuration is missing. " .
        "Please contact the administrator."
    );
}

require_once $localConfig;


/* ==========================================================
   DATABASE SETTINGS VALIDATION
========================================================== */

if (
    !isset($host) ||
    !isset($port) ||
    !isset($username) ||
    !isset($password) ||
    !isset($database)
) {

    error_log(
        "VOTIFY Database Error: incomplete database configuration."
    );

    http_response_code(500);

    die(
        "Database configuration is incomplete. " .
        "Please contact the administrator."
    );
}


/* ==========================================================
   DATABASE UNAVAILABLE HANDLER
========================================================== */

function votifyDatabaseUnavailable(
    string $message = "Database service is currently unavailable."
): void {

    $requestUri = $_SERVER["REQUEST_URI"] ?? "";
    $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";

    $isBackendRequest =
        strpos($requestUri, "/backend/") !== false ||
        strpos($scriptName, "/backend/") !== false;


    /* ======================================================
       BACKEND / AJAX
    ====================================================== */

    if ($isBackendRequest) {

        http_response_code(503);

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        echo json_encode([
            "success" => false,
            "status" => "database_unavailable",
            "message" =>
                "VOTIFY database service is currently unavailable. Please try again later."
        ]);

        exit;
    }


    /* ======================================================
       NORMAL PAGE
    ====================================================== */

    http_response_code(503);

    $currentScript = basename(
        $_SERVER["SCRIPT_FILENAME"] ?? ""
    );

    if ($currentScript === "database-unavailable.php") {
        exit;
    }

    header(
        "Location: /pages/database-unavailable.php"
    );

    exit;
}


/* ==========================================================
   MYSQLI
========================================================== */

mysqli_report(MYSQLI_REPORT_OFF);


/* ==========================================================
   INITIALIZE CONNECTION
========================================================== */

$conn = mysqli_init();

if (!$conn) {

    error_log(
        "VOTIFY Database Error: mysqli initialization failed."
    );

    votifyDatabaseUnavailable(
        "Unable to initialize database connection."
    );
}


/* ==========================================================
   CONNECTION TIMEOUT
========================================================== */

$conn->options(
    MYSQLI_OPT_CONNECT_TIMEOUT,
    15
);


/* ==========================================================
   CONNECT TO BYETHOST MYSQL
========================================================== */

$connected = @$conn->real_connect(
    $host,
    $username,
    $password,
    $database,
    $port
);


/* ==========================================================
   CONNECTION CHECK
========================================================== */

if (
    !$connected ||
    $conn->connect_errno
) {

    $errorMessage =
        $conn->connect_error ??
        "Unknown database connection error.";

    error_log(
        "VOTIFY MySQL Connection Failed: " .
        $errorMessage
    );

    @$conn->close();

    votifyDatabaseUnavailable(
        "Database connection failed."
    );
}


/* ==========================================================
   CHARACTER SET
========================================================== */

if (!$conn->set_charset("utf8mb4")) {

    error_log(
        "VOTIFY Database Character Set Error: " .
        $conn->error
    );

    @$conn->close();

    http_response_code(500);

    header(
        "Content-Type: application/json; charset=UTF-8"
    );

    echo json_encode([
        "success" => false,
        "status" => "database_error",
        "message" =>
            "Unable to set database character encoding."
    ]);

    exit;
}


/* ==========================================================
   DATABASE CONNECTED SUCCESSFULLY
========================================================== */

/*
 * Active database connection:
 *
 * $conn
 *
 * PHP timezone:
 * Asia/Kolkata
 */


/* ==========================================================
   END
========================================================== */
?>