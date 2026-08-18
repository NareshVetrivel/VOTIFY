<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/database.php";

/** @var mysqli $conn */

if(!isset($_GET["id"])){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid Request"
    ]);

    exit();

}

$id = intval($_GET["id"]);

$query = "

SELECT *

FROM students

WHERE id = ?

AND status='Approved'

LIMIT 1

";

$stmt = mysqli_prepare($conn,$query);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    echo json_encode([
        "success"=>false,
        "message"=>"Student not found."
    ]);

    exit();

}

$row = mysqli_fetch_assoc($result);

echo json_encode([
    "success"=>true,
    "student"=>$row
]);