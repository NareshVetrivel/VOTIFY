<?php
/* ==========================================================
   VOTIFY
   Admin Activity Logger
========================================================== */

if (!function_exists("logActivity")) {

    function logActivity(
        $adminId,
        $adminUsername,
        $action,
        $description
    ) {

        global $conn;

        $ipAddress =
            $_SERVER["REMOTE_ADDR"] ?? "Unknown";

        $query = "

        INSERT INTO admin_logs

        (
            admin_id,
            admin_username,
            action,
            description,
            ip_address
        )

        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )

        ";

        $stmt = $conn->prepare($query);

        if (!$stmt) {

            return false;

        }

        $stmt->bind_param(

            "issss",

            $adminId,

            $adminUsername,

            $action,

            $description,

            $ipAddress

        );

        $stmt->execute();

        $stmt->close();

        return true;

    }

}