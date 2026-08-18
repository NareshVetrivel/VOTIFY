<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/database.php";

/** @var mysqli $conn */

if($_SERVER["REQUEST_METHOD"]!=="POST"){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid Request."
    ]);

    exit();

}

$id = intval($_POST["id"]);

$studentQuery = mysqli_prepare(

$conn,

"

SELECT

full_name,

admission_no

FROM students

WHERE id=?

"

);

mysqli_stmt_bind_param(

$studentQuery,

"i",

$id

);

mysqli_stmt_execute($studentQuery);

$result = mysqli_stmt_get_result($studentQuery);

$student = mysqli_fetch_assoc($result);

$query = "

DELETE FROM students

WHERE id=?

AND status='Approved'

";

$stmt = mysqli_prepare($conn,$query);

mysqli_stmt_bind_param($stmt,"i",$id);

if(mysqli_stmt_execute($stmt)){

$admin = $_SESSION["admin_username"];

$adminId = $_SESSION["admin_id"];

$ip = $_SERVER["REMOTE_ADDR"];

$action = "Voter Deleted";

$description =

"Deleted voter : "

.$student["full_name"]

." ("

.$student["admission_no"]

.")";

$log = mysqli_prepare(

$conn,

"

INSERT INTO admin_logs

(

admin_id,

action,

description,

admin_username,

ip_address

)

VALUES

(?,?,?,?,?)

"

);

mysqli_stmt_bind_param(

$log,

"issss",

$adminId,

$action,

$description,

$admin,

$ip

);

mysqli_stmt_execute($log);

    echo json_encode([
        "success"=>true,
        "message"=>"Voter deleted successfully."
    ]);

}
else{

    echo json_encode([
        "success"=>false,
        "message"=>"Unable to delete voter."
    ]);

}