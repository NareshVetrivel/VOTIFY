<?php
/* ==========================================================
   VOTIFY
   Admin Dashboard API
   File : backend/admin/dashboard.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================
   SESSION CHECK
========================================== */

if (!isset($_SESSION["admin_id"])) {

    echo json_encode([

        "status" => "error",

        "message" => "Unauthorized Access"

    ]);

    exit();

}


/* ==========================================
   DATABASE
========================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */

/* ==========================================
   TOTAL REGISTERED STUDENTS
========================================== */

$totalStudents = 0;

$sql = "SELECT COUNT(*) AS total FROM students";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {

    $totalStudents = (int)$row["total"];

}


/* ==========================================
   PENDING APPROVALS
========================================== */

$pendingStudents = 0;

$sql = "

SELECT COUNT(*) AS total

FROM students

WHERE status='Pending'

";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {

    $pendingStudents = (int)$row["total"];

}


/* ==========================================
   APPROVED STUDENTS
========================================== */

$approvedStudents = 0;

$sql = "

SELECT COUNT(*) AS total

FROM students

WHERE status='Approved'

";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {

    $approvedStudents = (int)$row["total"];

}


/* ==========================================
   RESPONSE
========================================== */

echo json_encode([

    "status" => "success",

    "totalStudents" => $totalStudents,

    "pendingStudents" => $pendingStudents,

    "approvedStudents" => $approvedStudents

]);

$conn->close();

?>