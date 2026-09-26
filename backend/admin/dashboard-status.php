<?php

/* ==========================================================
   VOTIFY
   Dashboard Status API
   File : backend/admin/dashboard-status.php
========================================================== */

session_start();

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   DEFAULT RESPONSE VALUES
========================================================== */

$total = 0;

$pending = 0;

$approved = 0;

$status = "Ready";

$startTimestamp = null;

$stopTimestamp = null;


/* ==========================================================
   TOTAL STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total = (int) $row["total"];

}


/* ==========================================================
   PENDING STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Pending'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $pending = (int) $row["total"];

}


/* ==========================================================
   APPROVED STUDENTS
========================================================== */

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE status = 'Approved'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $approved = (int) $row["total"];

}


/* ==========================================================
   GET CURRENT ELECTION STATUS
========================================================== */

$statusStatement = mysqli_prepare(
    $conn,
    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"
);

if ($statusStatement) {

    if (mysqli_stmt_execute($statusStatement)) {

        $statusResult =
            mysqli_stmt_get_result(
                $statusStatement
            );

        if (
            $statusResult &&
            mysqli_num_rows($statusResult) > 0
        ) {

            $statusRow =
                mysqli_fetch_assoc(
                    $statusResult
                );

            $status =
                trim(
                    (string)
                    $statusRow["election_status"]
                );

        }

    }

    mysqli_stmt_close(
        $statusStatement
    );

}


/* ==========================================================
   VALIDATE ELECTION STATUS
========================================================== */

/*
 * Only these three states are valid for the
 * VOTIFY election workflow.
 */

$allowedStatuses = [
    "Ready",
    "Started",
    "Stopped"
];

if (
    !in_array(
        $status,
        $allowedStatuses,
        true
    )
) {

    $status = "Ready";

}


/* ==========================================================
   GET LATEST ELECTION START EVENT
========================================================== */

/*
 * Every election cycle begins with:
 *
 *     Election Started
 *
 * The latest start event represents the
 * current election cycle.
 *
 * This is required so that the dashboard
 * can restore the timer after refresh.
 */

if (
    $status === "Started" ||
    $status === "Stopped"
) {

    $startStatement = mysqli_prepare(

        $conn,

        "SELECT
            UNIX_TIMESTAMP(created_at) AS start_timestamp
         FROM admin_logs
         WHERE action = 'Election Started'
         ORDER BY id DESC
         LIMIT 1"

    );


    if ($startStatement) {

        if (
            mysqli_stmt_execute(
                $startStatement
            )
        ) {

            $startResult =
                mysqli_stmt_get_result(
                    $startStatement
                );


            if (
                $startResult &&
                mysqli_num_rows($startResult) > 0
            ) {

                $startRow =
                    mysqli_fetch_assoc(
                        $startResult
                    );


                if (
                    isset(
                        $startRow["start_timestamp"]
                    )
                    &&
                    $startRow["start_timestamp"] !== null
                ) {

                    $startTimestamp =
                        (int)
                        $startRow["start_timestamp"];

                }

            }

        }


        mysqli_stmt_close(
            $startStatement
        );

    }

}


/* ==========================================================
   CURRENT ELECTION START VALIDATION
========================================================== */

/*
 * If there is no valid start event, timing
 * information must not be exposed.
 */

if (
    $startTimestamp !== null &&
    $startTimestamp <= 0
) {

    $startTimestamp = null;

}


/* ==========================================================
   GET CURRENT ELECTION STOP EVENT
========================================================== */

/*
 * IMPORTANT:
 *
 * A stop event belongs to the current election
 * cycle only when:
 *
 *     stop.created_at >= current start time
 *
 * This prevents an old election's stop event
 * from being paired with the current election.
 */

if (
    $status === "Stopped" &&
    $startTimestamp !== null
) {

    $stopStatement = mysqli_prepare(

        $conn,

        "SELECT
            UNIX_TIMESTAMP(created_at) AS stop_timestamp
         FROM admin_logs
         WHERE action = 'Election Stopped'
           AND created_at >= FROM_UNIXTIME(?)
         ORDER BY id DESC
         LIMIT 1"

    );


    if ($stopStatement) {

        mysqli_stmt_bind_param(

            $stopStatement,

            "i",

            $startTimestamp

        );


        if (
            mysqli_stmt_execute(
                $stopStatement
            )
        ) {

            $stopResult =
                mysqli_stmt_get_result(
                    $stopStatement
                );


            if (
                $stopResult &&
                mysqli_num_rows($stopResult) > 0
            ) {

                $stopRow =
                    mysqli_fetch_assoc(
                        $stopResult
                    );


                if (
                    isset(
                        $stopRow["stop_timestamp"]
                    )
                    &&
                    $stopRow["stop_timestamp"] !== null
                ) {

                    $candidateStopTimestamp =
                        (int)
                        $stopRow["stop_timestamp"];


                    /* --------------------------------------
                       STOP TIMESTAMP VALIDATION
                    -------------------------------------- */

                    if (
                        $candidateStopTimestamp >=
                        $startTimestamp
                    ) {

                        $stopTimestamp =
                            $candidateStopTimestamp;

                    }

                }

            }

        }


        mysqli_stmt_close(
            $stopStatement
        );

    }

}


/* ==========================================================
   STARTED STATE SAFETY
========================================================== */

/*
 * While the election is running, there must
 * never be a stop timestamp.
 */

if ($status === "Started") {

    $stopTimestamp = null;

}


/* ==========================================================
   STOPPED STATE SAFETY
========================================================== */

/*
 * A stopped election without a valid start event
 * cannot expose a stop timestamp.
 */

if (
    $status === "Stopped" &&
    $startTimestamp === null
) {

    $stopTimestamp = null;

}


/* ==========================================================
   READY STATE RESET
========================================================== */

/*
 * Ready means there is no active/current
 * election timing information to expose.
 */

if ($status === "Ready") {

    $startTimestamp = null;

    $stopTimestamp = null;

}


/* ==========================================================
   RESPONSE
========================================================== */

echo json_encode(

    [

        "success" =>
            true,

        "status" =>
            $status,

        "startTimestamp" =>
            $startTimestamp,

        "stopTimestamp" =>
            $stopTimestamp,

        "total" =>
            $total,

        "pending" =>
            $pending,

        "approved" =>
            $approved

    ],

    JSON_UNESCAPED_UNICODE

);


/* ==========================================================
   CLOSE CONNECTION
========================================================== */

mysqli_close(
    $conn
);

exit;

?>