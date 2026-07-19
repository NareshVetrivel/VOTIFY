<?php
session_start();

header("Content-Type: application/json");

require_once "../../config/database.php";

/* ===========================
   Statistics
=========================== */

$total = 0;
$pending = 0;
$approved = 0;

$result = mysqli_query($conn,
"SELECT COUNT(*) total FROM students");

if($result){

    $total = mysqli_fetch_assoc($result)["total"];

}

$result = mysqli_query($conn,
"SELECT COUNT(*) total
 FROM students
 WHERE status='Pending'");

if($result){

    $pending = mysqli_fetch_assoc($result)["total"];

}

$result = mysqli_query($conn,
"SELECT COUNT(*) total
 FROM students
 WHERE status='Approved'");

if($result){

    $approved = mysqli_fetch_assoc($result)["total"];

}

/* ===========================
Election Status
=========================== */

$status = "Ready";

$result = mysqli_query(

$conn,

"SELECT election_status
 FROM election_settings
 LIMIT 1"

);

if($result && mysqli_num_rows($result)){

$status =
mysqli_fetch_assoc($result)["election_status"];

}

echo json_encode([

"success"=>true,

"status"=>$status,

"total"=>$total,

"pending"=>$pending,

"approved"=>$approved

]);