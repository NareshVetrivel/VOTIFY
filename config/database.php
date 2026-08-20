<?php
/* ==========================================================
   VOTIFY
   Database Configuration
   File : config/database.php
   Environment : Aiven Cloud MySQL
========================================================== */

"use strict";


/* ==========================================================
   TIME ZONE
========================================================== */

date_default_timezone_set("Asia/Kolkata");


/* ==========================================================
   DATABASE SETTINGS
========================================================== */

$host = "votify-mysql-votify.g.aivencloud.com";

$port = 19516;

$username = "avnadmin";

$password = getenv("VOTIFY_DB_PASSWORD");

$database = "votify";


/* ==========================================================
   SSL / TLS SETTINGS
========================================================== */

$caFile =
    __DIR__ .
    DIRECTORY_SEPARATOR .
    "ca.pem";


/* ==========================================================
   DATABASE ERROR HANDLER
========================================================== */

function votifyDatabaseUnavailable($message = "Database service is currently unavailable.")
{

    /*
     * Check whether this request is coming from
     * a backend / AJAX endpoint.
     *
     * Backend requests must receive JSON instead
     * of an HTML error page.
     */

    $requestUri =
        $_SERVER["REQUEST_URI"] ?? "";

    $scriptName =
        $_SERVER["SCRIPT_NAME"] ?? "";

    $isBackendRequest =
        (
            strpos(
                $requestUri,
                "/backend/"
            ) !== false
        )
        ||
        (
            strpos(
                $scriptName,
                "/backend/"
            ) !== false
        );


    /* ======================================================
       BACKEND / AJAX RESPONSE
    ====================================================== */

    if ($isBackendRequest) {

        http_response_code(503);

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        echo json_encode(
            [
                "success" => false,
                "status" => "database_unavailable",
                "message" =>
                    "VOTIFY database service is currently unavailable. Please try again later."
            ]
        );

        exit();

    }


    /* ======================================================
       NORMAL PAGE REQUEST
    ====================================================== */

    http_response_code(503);

    /*
     * Prevent redirect loops.
     *
     * database-unavailable.php itself must NEVER include
     * database.php.
     */

    $currentScript =
        basename(
            $_SERVER["SCRIPT_FILENAME"] ?? ""
        );

    if (
        $currentScript ===
        "database-unavailable.php"
    ) {

        exit();

    }


    /*
     * VOTIFY local project path.
     */

    header(
        "Location: /VOTIFY/pages/database-unavailable.php"
    );

    exit();

}


/* ==========================================================
   BASIC CONFIGURATION VALIDATION
========================================================== */

if (
    $password === false ||
    trim($password) === ""
) {

    votifyDatabaseUnavailable(
        "Database password is not configured."
    );

}


if (
    !file_exists($caFile)
) {

    votifyDatabaseUnavailable(
        "Aiven CA certificate is missing."
    );

}


/* ==========================================================
   INITIALIZE MYSQLI
========================================================== */

mysqli_report(
    MYSQLI_REPORT_OFF
);

$conn = mysqli_init();


/* ==========================================================
   MYSQL CONNECTION TIMEOUT
========================================================== */

if (!$conn) {

    votifyDatabaseUnavailable(
        "Unable to initialize database connection."
    );

}


$conn->options(
    MYSQLI_OPT_CONNECT_TIMEOUT,
    10
);


/* ==========================================================
   SSL / TLS CONFIGURATION
========================================================== */

$sslConfigured =
    $conn->ssl_set(
        null,
        null,
        $caFile,
        null,
        null
    );


if (!$sslConfigured) {

    @mysqli_close($conn);

    votifyDatabaseUnavailable(
        "Unable to configure secure database connection."
    );

}


/* ==========================================================
   CONNECT TO AIVEN MYSQL
========================================================== */

/*
 * The @ operator prevents raw mysqli warnings such as:
 *
 * Warning: mysqli::real_connect()
 * php_network_getaddresses...
 *
 * from being displayed to the user.
 *
 * We handle the failure ourselves below.
 */

$connected =
    @$conn->real_connect(
        $host,
        $username,
        $password,
        $database,
        $port,
        null,
        MYSQLI_CLIENT_SSL
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

    @mysqli_close($conn);

    /*
     * Log the technical error for development/server logs.
     *
     * Do NOT expose this technical information to users.
     */

    error_log(
        "VOTIFY Database Connection Failed: " .
        $errorMessage
    );


    votifyDatabaseUnavailable(
        $errorMessage
    );

}


/* ==========================================================
   CHARACTER SET
========================================================== */

if (
    !$conn->set_charset("utf8mb4")
) {

    error_log(
        "VOTIFY Database Character Set Error: " .
        $conn->error
    );

}


/* ==========================================================
   CONNECTION SUCCESS
========================================================== */

/*
 * At this point:
 *
 * $conn
 *
 * contains a valid secure MySQL connection.
 *
 * Existing VOTIFY files can continue using:
 *
 * mysqli_query($conn, ...)
 * mysqli_prepare($conn, ...)
 * mysqli_begin_transaction($conn)
 * etc.
 */


/* ==========================================================
   END DATABASE CONFIGURATION
========================================================== */
?>