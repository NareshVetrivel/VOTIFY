<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
/* ==========================================================
   VOTIFY
   Add Candidate
   File : backend/admin/add-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================================
   SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit();

}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";
/** @var mysqli $conn */
/* ==========================================================
   REQUEST VALIDATION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit();

}

/* ==========================================================
   RECEIVE FORM DATA
========================================================== */

$studentId = trim($_POST["studentId"] ?? "");

$admissionNo = strtoupper(
    trim($_POST["admissionNo"] ?? "")
);

$manifesto = trim(
    $_POST["manifesto"] ?? ""
);

/* ==========================================================
   REQUIRED FIELD VALIDATION
========================================================== */

if (

    empty($studentId) ||

    empty($admissionNo) ||

    empty($manifesto)

){

    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);

    exit();

}

/* ==========================================================
   PHOTO VALIDATION
========================================================== */

if (

    !isset($_FILES["candidatePhoto"]) ||

    $_FILES["candidatePhoto"]["error"] !== UPLOAD_ERR_OK

){

    echo json_encode([
        "success" => false,
        "message" => "Candidate photo is required."
    ]);

    exit();

}

/* ==========================================================
   VERIFY STUDENT
========================================================== */

$query = "

SELECT

id,
full_name,
admission_no,
department,
year,
status

FROM students

WHERE

id = ?

AND

admission_no = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $query);

if(!$stmt){

    die(json_encode([
        "success"=>false,
        "message"=>mysqli_error($conn)
    ]));

}

mysqli_stmt_bind_param(

    $stmt,

    "is",

    $studentId,

    $admissionNo

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit();

}

$student = mysqli_fetch_assoc($result);

/* ==========================================================
   APPROVED STUDENT CHECK
========================================================== */

if ($student["status"] !== "Approved") {

    echo json_encode([
        "success" => false,
        "message" => "Only approved students can become candidates."
    ]);

    exit();

}

/* ==========================================================
   DUPLICATE CANDIDATE CHECK
========================================================== */

$query = "

SELECT id

FROM candidates

WHERE student_id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $studentId

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    echo json_encode([
        "success" => false,
        "message" => "This student is already a candidate."
    ]);

    exit();

}

/* ==========================================================
   PART 2
   Image Validation & Upload
========================================================== */

/* ==========================================================
   IMAGE VALIDATION
========================================================== */

$photo = $_FILES["candidatePhoto"];

$allowedExtensions = [

    "jpg",

    "jpeg",

    "png"

];

$fileName = $photo["name"];

$fileSize = $photo["size"];

$fileTmp = $photo["tmp_name"];

$fileExtension = strtolower(

    pathinfo(

        $fileName,

        PATHINFO_EXTENSION

    )

);

if(

    !in_array(

        $fileExtension,

        $allowedExtensions

    )

){

    echo json_encode([

        "success" => false,

        "message" =>

        "Only JPG, JPEG and PNG files are allowed."

    ]);

    exit();

}

if($fileSize > 2 * 1024 * 1024){

    echo json_encode([

        "success" => false,

        "message" =>

        "Photo size must be less than 2 MB."

    ]);

    exit();

}

/* ==========================================================
   UPLOAD PHOTO
========================================================== */

$newFileName =

"candidate_"

.

time()

.

"_"

.

bin2hex(

    random_bytes(4)

)

.

"."

.

$fileExtension;

$uploadDirectory =

"../../uploads/candidates/";

$uploadPath =

$uploadDirectory

.

$newFileName;

if(

    !move_uploaded_file(

        $fileTmp,

        $uploadPath

    )

){

    echo json_encode([

        "success" => false,

        "message" =>

        "Unable to upload candidate photo."

    ]);

    exit();

}

/* ==========================================================
   INSERT CANDIDATE
========================================================== */

$query = "

INSERT INTO candidates(

student_id,

admission_no,

full_name,

department,

year,

manifesto,

photo

)

VALUES(

?,

?,

?,

?,

?,

?,

?

)

";

$stmt = mysqli_prepare($conn, $query);

if(!$stmt){

    die(json_encode([

        "success"=>false,

        "message"=>mysqli_error($conn)

    ]));

}

$bind = mysqli_stmt_bind_param(

    $stmt,

    "issssss",

    $student["id"],

    $student["admission_no"],

    $student["full_name"],

    $student["department"],

    $student["year"],

    $manifesto,

    $newFileName

);

if(!$bind){

    die(json_encode([

        "success"=>false,

        "message"=>mysqli_stmt_error($stmt)

    ]));

}

$insertSuccess = mysqli_stmt_execute($stmt);

if(!$insertSuccess){

    die(json_encode([

        "success"=>false,

        "message"=>mysqli_stmt_error($stmt)

    ]));

}

/* ==========================================================
   PART 3
   Admin Logs + JSON Response
========================================================== */

/* ==========================================================
   ADMIN LOG
========================================================== */

if($insertSuccess){

    $admin =

    $_SESSION["admin_username"] ?? "Admin";

    $ip =

    $_SERVER["REMOTE_ADDR"] ?? "Unknown";

    $action =

    "Candidate Added";

    $description =

    "Added candidate : "

    .

    $student["full_name"]

    .

    " ("

    .

    $student["admission_no"]

    .

    ")";

$adminId = $_SESSION["admin_id"];

$logQuery = "

INSERT INTO admin_logs(

admin_id,

admin_username,

action,

description,

ip_address

)

VALUES(

?,?,?,?,?

)

";

$logStmt = mysqli_prepare($conn,$logQuery);

mysqli_stmt_bind_param(

    $logStmt,

    "issss",

    $adminId,

    $admin,

    $action,

    $description,

    $ip

);

    mysqli_stmt_execute(

        $logStmt

    );

    echo json_encode([

        "success" => true,

        "message" =>

        "Candidate added successfully."

    ]);

    exit();

}

/* ==========================================================
   INSERT FAILED
========================================================== */

if(

    file_exists(

        $uploadPath

    )

){

    unlink(

        $uploadPath

    );

}

echo json_encode([

    "success" => false,

    "message" =>

    "Unable to add candidate."

]);

exit();