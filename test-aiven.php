<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

echo "<h2>VOTIFY - Aiven Database Connection Test</h2>";

$totalStart = microtime(true);


/* ==========================================================
   DATABASE CONNECTION
========================================================== */

$start = microtime(true);

try {

    require_once __DIR__ . "/config/database.php";

} catch (Throwable $e) {

    echo "❌ Database Connection Failed<br>";
    echo "Error: "
        . htmlspecialchars($e->getMessage())
        . "<br>";

    exit();

}

$connectionTime = microtime(true) - $start;

echo "✅ Aiven DB Connection: "
    . round($connectionTime, 4)
    . " seconds<br>";


/* ==========================================================
   PING TEST
========================================================== */

$start = microtime(true);

if (!$conn->ping()) {

    echo "❌ Database Ping Failed<br>";

    echo "MySQL Error: "
        . htmlspecialchars($conn->error)
        . "<br>";

    $conn->close();

    exit();

}

$pingTime = microtime(true) - $start;

echo "✅ Database Ping: "
    . round($pingTime, 4)
    . " seconds<br>";


/* ==========================================================
   QUERY TEST
========================================================== */

$start = microtime(true);

$result = $conn->query("SELECT 1 AS test");

$queryTime = microtime(true) - $start;

if (!$result) {

    echo "❌ SELECT 1 Failed<br>";

    echo "MySQL Error: "
        . htmlspecialchars($conn->error)
        . "<br>";

    $conn->close();

    exit();

}

$row = $result->fetch_assoc();

if ((int)$row["test"] === 1) {

    echo "✅ SELECT 1 Query: Success<br>";

} else {

    echo "⚠️ SELECT 1 Query: Unexpected Result<br>";

}

echo "Query Time: "
    . round($queryTime, 4)
    . " seconds<br>";


/* ==========================================================
   SERVER INFORMATION
========================================================== */

echo "MySQL Server: "
    . htmlspecialchars($conn->server_info)
    . "<br>";


/* ==========================================================
   TOTAL TEST TIME
========================================================== */

$totalTime = microtime(true) - $totalStart;

$conn->close();

echo "<br>";

echo "<strong>✅ Aiven Database Test Completed</strong><br>";

echo "Total Test Time: "
    . round($totalTime, 4)
    . " seconds";

?>