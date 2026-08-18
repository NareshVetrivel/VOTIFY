<?php
/* ==========================================================
   VOTIFY
   Database Configuration
   File : config/database.php
   Environment : Aiven Cloud MySQL
========================================================== */


/* ==========================================
   DATABASE SETTINGS
========================================== */

$host = "votify-mysql-votify.g.aivencloud.com";

$port = 19516;

$username = "avnadmin";

/*
   IMPORTANT:
   Set the Aiven password as an environment variable:

   Windows CMD:
   set VOTIFY_DB_PASSWORD=YOUR_AIVEN_PASSWORD

   Windows PowerShell:
   $env:VOTIFY_DB_PASSWORD="YOUR_AIVEN_PASSWORD"
*/
$password = getenv("VOTIFY_DB_PASSWORD");

$database = "votify";


/* ==========================================
   SSL / TLS SETTINGS
========================================== */

$caFile = __DIR__ . DIRECTORY_SEPARATOR . "ca.pem";


/* ==========================================
   BASIC CONFIGURATION VALIDATION
========================================== */

if ($password === false || $password === "") {

    die(
        "Database Configuration Error : " .
        "VOTIFY_DB_PASSWORD environment variable is not configured."
    );

}

if (!file_exists($caFile)) {

    die(
        "Database Configuration Error : " .
        "Aiven CA certificate not found."
    );

}


/* ==========================================
   CREATE MYSQLI CONNECTION
========================================== */

/*
   MySQLi SSL configuration.

   MYSQLI_CLIENT_SSL enables encrypted communication.
   The Aiven CA certificate is used to verify the server.
*/

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();

$conn->options(
    MYSQLI_OPT_CONNECT_TIMEOUT,
    10
);

$conn->ssl_set(
    null,       // Client key
    null,       // Client certificate
    $caFile,    // CA certificate
    null,       // CA path
    null        // Cipher
);

$conn->real_connect(
    $host,
    $username,
    $password,
    $database,
    $port,
    null,
    MYSQLI_CLIENT_SSL
);


/* ==========================================
   CONNECTION CHECK
========================================== */

if ($conn->connect_errno) {

    die(
        "Database Connection Failed : " .
        $conn->connect_error
    );

}


/* ==========================================
   CHARACTER SET
========================================== */

$conn->set_charset("utf8mb4");


/* ==========================================
   TIME ZONE
========================================== */

date_default_timezone_set("Asia/Kolkata");

?>