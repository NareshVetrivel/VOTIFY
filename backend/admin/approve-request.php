<?php
/* ==========================================================
   VOTIFY
   Approve Student Request
========================================================== */

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION["admin_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);

    exit();

}

require_once "../../config/database.php";
require_once "log_activity.php";

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Student"
    ]);

    exit();

}

/* Student */

$result = mysqli_query(

    $conn,

    "SELECT full_name
     FROM students
     WHERE id=$id
     LIMIT 1"

);

if (!$result || mysqli_num_rows($result) == 0) {

    echo json_encode([
        "success"=>false,
        "message"=>"Student not found."
    ]);

    exit();

}

$student = mysqli_fetch_assoc($result);

/* Update */

$update = mysqli_query(

    $conn,

    "UPDATE students
     SET status='Approved'
     WHERE id=$id"

);

if (!$update) {

    echo json_encode([
        "success"=>false,
        "message"=>"Unable to approve."
    ]);

    exit();

}

/* Log */

logActivity(

    $_SESSION["admin_id"],

    $_SESSION["admin_username"],

    "Student Approved",

    $student["full_name"] .
    " registration approved."

);

echo json_encode([

    "success"=>true,

    "message"=>"Student Approved Successfully."

]);