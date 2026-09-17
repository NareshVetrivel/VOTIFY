<?php
/* ==========================================================
   VOTIFY
   Database Configuration
   File : config/database.php
   Environment : Aiven Cloud MySQL
========================================================== */

date_default_timezone_set("Asia/Kolkata");


/* ==========================================================
   DATABASE SETTINGS
========================================================== */

$host = "votify-mysql-votify.g.aivencloud.com";
$port = 19516;
$username = "avnadmin";
$database = "votify";


/*
 * Aiven password
 *
 * IMPORTANT:
 * Do NOT hard-code the password here.
 *
 * Windows PowerShell:
 *
 * $env:VOTIFY_DB_PASSWORD="YOUR_AIVEN_PASSWORD"
 *
 * Then restart Apache from XAMPP.
 */

$password = getenv("VOTIFY_DB_PASSWORD");


/* ==========================================================
   AIVEN CA CERTIFICATE
========================================================== */

$caFile = __DIR__ . DIRECTORY_SEPARATOR . "ca.pem";


/* ==========================================================
   DATABASE UNAVAILABLE HANDLER
========================================================== */

function votifyDatabaseUnavailable(
    string $message = "Database service is currently unavailable."
): void {

    $requestUri =
        $_SERVER["REQUEST_URI"] ?? "";

    $scriptName =
        $_SERVER["SCRIPT_NAME"] ?? "";

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

        echo json_encode(
            [
                "success" => false,
                "status" => "database_unavailable",
                "message" =>
                    "VOTIFY database service is currently unavailable. Please try again later."
            ]
        );

        exit;
    }


    /* ======================================================
       NORMAL PAGE
    ====================================================== */

    http_response_code(503);

    $currentScript =
        basename(
            $_SERVER["SCRIPT_FILENAME"] ?? ""
        );


    if (
        $currentScript ===
        "database-unavailable.php"
    ) {
        exit;
    }


    header(
        "Location: /VOTIFY/pages/database-unavailable.php"
    );

    exit;
}


/* ==========================================================
   CHECK PASSWORD
========================================================== */

if (
    $password === false ||
    trim($password) === ""
) {

    error_log(
        "VOTIFY Database Error: VOTIFY_DB_PASSWORD is not configured."
    );

    votifyDatabaseUnavailable(
        "Database password is not configured."
    );
}


/* ==========================================================
   CHECK CA CERTIFICATE
========================================================== */

if (!is_file($caFile)) {

    error_log(
        "VOTIFY Database Error: Aiven CA certificate not found: " .
        $caFile
    );

    votifyDatabaseUnavailable(
        "Aiven CA certificate is missing."
    );
}


/* ==========================================================
   MYSQLI
========================================================== */

mysqli_report(
    MYSQLI_REPORT_OFF
);


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

if (
    !$conn->options(
        MYSQLI_OPT_CONNECT_TIMEOUT,
        15
    )
) {

    error_log(
        "VOTIFY Database Error: Unable to set connection timeout."
    );
}


/* ==========================================================
   AIVEN SSL/TLS
========================================================== */

if (
    !$conn->ssl_set(
        null,
        null,
        $caFile,
        null,
        null
    )
) {

    error_log(
        "VOTIFY Database Error: Aiven SSL configuration failed."
    );

    @$conn->close();

    votifyDatabaseUnavailable(
        "Unable to configure secure database connection."
    );
}


/* ==========================================================
   CONNECT TO AIVEN
========================================================== */

$connected = @$conn->real_connect(
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


    error_log(
        "VOTIFY Aiven MySQL Connection Failed: " .
        $errorMessage
    );


    @$conn->close();


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


    @$conn->close();


    http_response_code(500);

    header(
        "Content-Type: application/json; charset=UTF-8"
    );

    echo json_encode(
        [
            "success" => false,
            "status" => "database_error",
            "message" =>
                "Unable to set database character encoding."
        ]
    );

    exit;
}


/* ==========================================================
   DATABASE CONNECTED SUCCESSFULLY
========================================================== */

/*
 * A valid Aiven MySQL connection is now available:
 *
 * $conn
 *
 * Example:
 *
 * mysqli_query($conn, "SELECT 1");
 * mysqli_prepare($conn, $query);
 * mysqli_begin_transaction($conn);
 * mysqli_commit($conn);
 * mysqli_rollback($conn);
 */


/* ==========================================================
   END
========================================================== */
?>