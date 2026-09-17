<?php

mysqli_report(MYSQLI_REPORT_OFF);

$caFile = __DIR__ . "\config\ca.pem";

$conn = mysqli_init();

$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 15);

if (!$conn->ssl_set(null, null, $caFile, null, null)) {
    die("SSL SET FAILED: " . $conn->error . PHP_EOL);
}

$connected = $conn->real_connect(
    "votify-mysql-votify.g.aivencloud.com",
    "avnadmin",
    getenv("VOTIFY_DB_PASSWORD"),
    "votify",
    19516,
    null,
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    echo "DB FAILED: " . $conn->connect_error . PHP_EOL;
    exit;
}

echo "DB CONNECTED" . PHP_EOL;

$result = $conn->query("SELECT 1");

echo $result ? "QUERY OK" . PHP_EOL : "QUERY FAILED: " . $conn->error . PHP_EOL;

$conn->close();
