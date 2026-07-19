<?php
/* ==========================================================
   VOTIFY
   Update Election Status
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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Request"
    ]);

    exit();

}

$status = $_POST["status"] ?? "";

$allowed = ["Ready", "Started", "Stopped"];

if (!in_array($status, $allowed)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Status"
    ]);

    exit();

}

$query = "

UPDATE election_settings

SET election_status='$status'

WHERE id=1

";

if (mysqli_query($conn, $query)) {

    if ($status == "Started") {

        logActivity(

            $_SESSION["admin_id"],

            $_SESSION["admin_username"],

            "Election Started",

            "Administrator started the election."

        );

    }

    elseif ($status == "Stopped") {

        logActivity(

            $_SESSION["admin_id"],

            $_SESSION["admin_username"],

            "Election Stopped",

            "Administrator stopped the election."

        );

    }

    echo json_encode([

        "success" => true,

        "status" => $status

    ]);

}

else {

    echo json_encode([

        "success" => false,

        "message" => mysqli_error($conn)

    ]);

}
