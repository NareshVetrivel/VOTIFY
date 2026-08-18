<?php
/* ==========================================================
   VOTIFY
   Update Candidate
   File : backend/admin/update-candidate.php
========================================================== */

session_start();

header("Content-Type: application/json");

/* ==========================================================
   SESSION
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
   VALIDATION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit();

}

$candidateId = intval($_POST["candidateId"] ?? 0);

$manifesto = trim($_POST["manifesto"] ?? "");

if ($candidateId <= 0 || $manifesto === "") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid candidate data."
    ]);

    exit();

}

/* ==========================================================
   GET OLD PHOTO
========================================================== */

$query = "

SELECT photo, full_name, admission_no

FROM candidates

WHERE id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $candidateId);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Candidate not found."
    ]);

    exit();

}

$candidate = mysqli_fetch_assoc($result);

$newPhoto = $candidate["photo"];

/* ==========================================================
   PHOTO UPDATE
========================================================== */

if (

    isset($_FILES["candidatePhoto"]) &&

    $_FILES["candidatePhoto"]["error"] === UPLOAD_ERR_OK

) {

    $photo = $_FILES["candidatePhoto"];

    $allowed = ["jpg","jpeg","png"];

    $extension = strtolower(

        pathinfo(

            $photo["name"],

            PATHINFO_EXTENSION

        )

    );

    if (!in_array($extension, $allowed)) {

        echo json_encode([
            "success" => false,
            "message" => "Only JPG, JPEG and PNG files are allowed."
        ]);

        exit();

    }

    if ($photo["size"] > 2 * 1024 * 1024) {

        echo json_encode([
            "success" => false,
            "message" => "Photo size must be less than 2 MB."
        ]);

        exit();

    }

    $newPhoto =

    "candidate_"

    .

    time()

    .

    "_"

    .

    bin2hex(random_bytes(4))

    .

    "."

    .

    $extension;

    $uploadPath =

    "../../uploads/candidates/"

    .

    $newPhoto;

    if (!move_uploaded_file($photo["tmp_name"], $uploadPath)) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to upload photo."
        ]);

        exit();

    }

    $oldPhoto =

    "../../uploads/candidates/"

    .

    $candidate["photo"];

    if (

        file_exists($oldPhoto)

    ) {

        unlink($oldPhoto);

    }

}

/* ==========================================================
   UPDATE
========================================================== */

$query = "

UPDATE candidates

SET

manifesto = ?,

photo = ?

WHERE id = ?

";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(

    $stmt,

    "ssi",

    $manifesto,

    $newPhoto,

    $candidateId

);

if (!mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to update candidate."
    ]);

    exit();

}

/* ==========================================================
   ADMIN LOG
========================================================== */

$adminId = $_SESSION["admin_id"];

$admin = $_SESSION["admin_username"] ?? "Admin";

$ip = $_SERVER["REMOTE_ADDR"] ?? "Unknown";

$action = "Candidate Updated";

$description =

"Updated candidate : "

.

$candidate["full_name"]

.

" ("

.

$candidate["admission_no"]

.

")";

$log = "

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

$logStmt = mysqli_prepare($conn, $log);

mysqli_stmt_bind_param(

    $logStmt,

    "issss",

    $adminId,

    $admin,

    $action,

    $description,

    $ip

);

mysqli_stmt_execute($logStmt);

/* ==========================================================
   SUCCESS
========================================================== */

echo json_encode([

    "success" => true,

    "message" => "Candidate updated successfully."

]);

exit();