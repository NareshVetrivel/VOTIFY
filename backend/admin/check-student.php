<?php

/* ==========================================================
   VOTIFY
   Check Student For Candidate
========================================================== */

session_start();

header("Content-Type: application/json");

require_once "../../config/database.php";

/* ==========================================================
   VALIDATE REQUEST
========================================================== */

if(!isset($_GET["admission_no"])){

    echo json_encode([

        "success" => false,

        "message" => "Admission number is required."

    ]);

    exit();

}

$admissionNo = trim($_GET["admission_no"]);

if($admissionNo === ""){

    echo json_encode([

        "success" => false,

        "message" => "Admission number is required."

    ]);

    exit();

}

/* ==========================================================
   CHECK APPROVED STUDENT
========================================================== */

$query = mysqli_prepare(

    $conn,

    "

    SELECT

        id,
        admission_no,
        full_name,
        department,
        year,
        status

    FROM students

    WHERE admission_no = ?

    LIMIT 1

    "

);

mysqli_stmt_bind_param(

    $query,

    "s",

    $admissionNo

);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

if(mysqli_num_rows($result) === 0){

    echo json_encode([

        "success" => false,

        "message" => "Student not found."

    ]);

    exit();

}

$student = mysqli_fetch_assoc($result);

/* ==========================================================
   CHECK APPROVAL STATUS
========================================================== */

if($student["status"] !== "Approved"){

    echo json_encode([

        "success" => false,

        "message" => "Student is not approved."

    ]);

    exit();

}

/* ==========================================================
   CHECK ALREADY CANDIDATE
========================================================== */

$checkCandidate = mysqli_prepare(

    $conn,

    "

    SELECT id

    FROM candidates

    WHERE student_id = ?

    LIMIT 1

    "

);

mysqli_stmt_bind_param(

    $checkCandidate,

    "i",

    $student["id"]

);

mysqli_stmt_execute($checkCandidate);

$candidateResult = mysqli_stmt_get_result($checkCandidate);

if(mysqli_num_rows($candidateResult) > 0){

    echo json_encode([

        "success" => false,

        "message" => "This student is already a candidate."

    ]);

    exit();

}

/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "student" => [

        "id" => $student["id"],

        "admission_no" => $student["admission_no"],

        "full_name" => $student["full_name"],

        "department" => $student["department"],

        "year" => $student["year"]

    ]

]);