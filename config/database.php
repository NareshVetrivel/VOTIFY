<?php
/* ==========================================================
   VOTIFY
   Database Configuration
   File : config/database.php
========================================================== */

/* ==========================================
   DATABASE SETTINGS
========================================== */

$host = "localhost";

$port = "3306";

$username = "root";

$password = "tiger";

$database = "votify_db";


/* ==========================================
   CREATE CONNECTION
========================================== */

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $port
);


/* ==========================================
   CONNECTION CHECK
========================================== */

if ($conn->connect_error) {

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