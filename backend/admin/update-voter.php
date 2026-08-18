<?php

session_start();

header("Content-Type: application/json");

require_once "../../config/database.php";

/** @var mysqli $conn */

if($_SERVER["REQUEST_METHOD"]!=="POST"){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid Request"
    ]);

    exit();

}

$id = intval($_POST["id"]);

$fullName = trim($_POST["full_name"]);
$phone = trim($_POST["phone"]);
$year = trim($_POST["year"]);

$check = mysqli_prepare(
    $conn,
    "SELECT id
     FROM students
     WHERE phone=?
     AND id!=?"
);

mysqli_stmt_bind_param(
    $check,
    "si",
    $phone,
    $id
);

mysqli_stmt_execute($check);

$checkResult = mysqli_stmt_get_result($check);

if(mysqli_num_rows($checkResult) > 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Phone number already exists."
    ]);

    exit();

}

$query = "

UPDATE students

SET

full_name=?,
phone=?,
year=?

WHERE id=?

AND status='Approved'

";

$stmt = mysqli_prepare($conn,$query);

mysqli_stmt_bind_param(

$stmt,

"sssi",

$fullName,

$phone,

$year,

$id

);

$studentQuery = mysqli_prepare(
    $conn,
    "SELECT admission_no FROM students WHERE id=?"
);

mysqli_stmt_bind_param(
    $studentQuery,
    "i",
    $id
);

mysqli_stmt_execute($studentQuery);

$result = mysqli_stmt_get_result($studentQuery);

$student = mysqli_fetch_assoc($result);

if(mysqli_stmt_execute($stmt)){

$admin = $_SESSION["admin_username"];

$adminId = $_SESSION["admin_id"];

$ip = $_SERVER["REMOTE_ADDR"];

$action = "Voter Updated";

$description =
"Edited voter details (Name / Phone / Year) - "
.$student["admission_no"].
" - ".
$fullName;

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

        "message"=>"Voter updated successfully."

    ]);

}

else{

    echo json_encode([

        "success"=>false,

        "message"=>"Database update failed."

    ]);

}