<?php
/* ==========================================================
   VOTIFY
   Admin Activity Logger
   File : backend/admin/log_activity.php
========================================================== */

if (!function_exists("logActivity")) {

    function logActivity(
        $adminId,
        $adminUsername,
        $action,
        $description
    ): bool {

        global $conn;


        /* ==================================================
           DATABASE CONNECTION CHECK
        ================================================== */

        if (
            !isset($conn) ||
            !($conn instanceof mysqli)
        ) {

            error_log(
                "VOTIFY Activity Logger: "
                . "Database connection is unavailable."
            );

            return false;

        }


        /* ==================================================
           GET CLIENT IP ADDRESS
        ================================================== */

        $ipAddress =
            $_SERVER["REMOTE_ADDR"] ?? "Unknown";


        /*
         * Keep IP address within the database
         * column limit.
         */

        $ipAddress =
            substr(
                trim($ipAddress),
                0,
                45
            );


        /* ==================================================
           GET CURRENT VOTIFY TIME
           Timezone : Asia/Kolkata
        ================================================== */

        try {

            $votifyTimezone =
                new DateTimeZone("Asia/Kolkata");

            $createdAtObject =
                new DateTime(
                    "now",
                    $votifyTimezone
                );

            $createdAt =
                $createdAtObject->format(
                    "Y-m-d H:i:s"
                );

        } catch (Exception $e) {

            error_log(
                "VOTIFY Activity Logger: "
                . "Unable to generate activity timestamp. "
                . $e->getMessage()
            );

            return false;

        }


        /* ==================================================
           PREPARE INSERT QUERY
        ================================================== */

        $query = "
            INSERT INTO admin_logs
            (
                admin_id,
                admin_username,
                action,
                description,
                ip_address,
                created_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $query
            );


        /* ==================================================
           PREPARE ERROR
        ================================================== */

        if (!$stmt) {

            error_log(
                "VOTIFY Activity Logger: "
                . "Unable to prepare activity log query. "
                . $conn->error
            );

            return false;

        }


        /* ==================================================
           BIND PARAMETERS
        ================================================== */

        $adminId =
            (int) $adminId;

        $adminUsername =
            (string) $adminUsername;

        $action =
            (string) $action;

        $description =
            (string) $description;

        $createdAt =
            (string) $createdAt;


        if (
            !mysqli_stmt_bind_param(
                $stmt,
                "isssss",
                $adminId,
                $adminUsername,
                $action,
                $description,
                $ipAddress,
                $createdAt
            )
        ) {

            error_log(
                "VOTIFY Activity Logger: "
                . "Unable to bind activity log parameters."
            );

            mysqli_stmt_close($stmt);

            return false;

        }


        /* ==================================================
           EXECUTE INSERT
        ================================================== */

        if (
            !mysqli_stmt_execute($stmt)
        ) {

            error_log(
                "VOTIFY Activity Logger: "
                . "Unable to insert activity log. "
                . mysqli_stmt_error($stmt)
            );

            mysqli_stmt_close($stmt);

            return false;

        }


        /* ==================================================
           CLOSE STATEMENT
        ================================================== */

        mysqli_stmt_close($stmt);


        /* ==================================================
           SUCCESS
        ================================================== */

        return true;

    }

}