<?php
/* ==========================================================
   VOTIFY
   Official Election Result Publisher
   File : backend/admin/publish-result.php

   Purpose:
   - Publish the official result after election is stopped
   - Create a secure verification token
   - Store a snapshot of the result
   - Reuse the existing published token if result is unchanged
========================================================== */

declare(strict_types=1);

session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Content-Type-Options: nosniff");


/* ==========================================================
   ADMIN AUTHENTICATION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Admin authentication required."
    ]);

    exit;
}


/* ==========================================================
   REQUEST METHOD
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";


if (!isset($conn) || !($conn instanceof mysqli)) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database connection unavailable."
    ]);

    exit;
}


/* ==========================================================
   HELPER
========================================================== */

function sendResponse(
    bool $success,
    string $message,
    array $extra = [],
    int $statusCode = 200
): void {

    http_response_code($statusCode);

    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* ==========================================================
   CHECK ELECTION STATUS
========================================================== */

$statusStmt = mysqli_prepare(
    $conn,
    "SELECT election_status
     FROM election_settings
     WHERE id = 1
     LIMIT 1"
);

if (!$statusStmt) {

    error_log(
        "VOTIFY publish-result status prepare failed: "
        . mysqli_error($conn)
    );

    sendResponse(
        false,
        "Unable to verify election status.",
        [],
        500
    );
}


mysqli_stmt_execute($statusStmt);

mysqli_stmt_bind_result(
    $statusStmt,
    $electionStatus
);

$statusFound = mysqli_stmt_fetch(
    $statusStmt
);

mysqli_stmt_close($statusStmt);


if (!$statusFound) {

    sendResponse(
        false,
        "Election settings were not found.",
        [],
        500
    );
}


/* ==========================================================
   RESULT CAN ONLY BE PUBLISHED AFTER STOP
========================================================== */

if ($electionStatus !== "Stopped") {

    sendResponse(
        false,
        "Results can only be published after the election is stopped.",
        [
            "status" => $electionStatus
        ],
        403
    );
}


/* ==========================================================
   FETCH ACTIVE CANDIDATES
========================================================== */

$candidates = [];

$candidateStmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        admission_no,
        full_name,
        department,
        year,
        vote_count
     FROM candidates
     WHERE status = 'Active'
     ORDER BY vote_count DESC, id ASC"
);


if (!$candidateStmt) {

    error_log(
        "VOTIFY publish-result candidate prepare failed: "
        . mysqli_error($conn)
    );

    sendResponse(
        false,
        "Unable to prepare candidate result data.",
        [],
        500
    );
}


mysqli_stmt_execute($candidateStmt);

mysqli_stmt_bind_result(
    $candidateStmt,
    $candidateId,
    $admissionNo,
    $fullName,
    $department,
    $year,
    $voteCount
);


while (mysqli_stmt_fetch($candidateStmt)) {

    $candidates[] = [
        "id" => (int)$candidateId,
        "admission_no" => (string)$admissionNo,
        "full_name" => (string)$fullName,
        "department" => (string)$department,
        "year" => (string)$year,
        "votes" => (int)$voteCount
    ];
}


mysqli_stmt_close($candidateStmt);


/* ==========================================================
   VALIDATE CANDIDATE DATA
========================================================== */

if (empty($candidates)) {

    sendResponse(
        false,
        "No active candidates are available for publishing.",
        [],
        422
    );
}


/* ==========================================================
   TOTAL VOTES
========================================================== */

$totalVotes = 0;

foreach ($candidates as $candidate) {

    $totalVotes += (int)$candidate["votes"];
}


/* ==========================================================
   BUILD OFFICIAL SNAPSHOT
========================================================== */

$winners = [];

$positionNames = [
    0 => "Chairman",
    1 => "Vice Chairman",
    2 => "Joint Secretary"
];


foreach ($candidates as $index => $candidate) {

    $percentage = 0;

    if ($totalVotes > 0) {

        $percentage =
            (
                $candidate["votes"]
                /
                $totalVotes
            ) * 100;
    }


    /*
     * Store complete candidate result snapshot.
     */
    $candidates[$index]["percentage"] =
        round($percentage, 2);


    /*
     * Current VOTIFY certificate architecture
     * recognises the top three candidates.
     */
    if ($index < 3) {

        $winners[] = [

            "position" =>
                $positionNames[$index],

            "candidate_id" =>
                $candidate["id"],

            "admission_no" =>
                $candidate["admission_no"],

            "full_name" =>
                $candidate["full_name"],

            "department" =>
                $candidate["department"],

            "year" =>
                $candidate["year"],

            "votes" =>
                $candidate["votes"],

            "percentage" =>
                $candidates[$index]["percentage"]

        ];
    }
}


/* ==========================================================
   RESULT SNAPSHOT
========================================================== */

$publishedAt =
    date("Y-m-d H:i:s");


$resultSnapshot = [

    "version" => 1,

    "election_status" =>
        "Stopped",

    "published_at" =>
        $publishedAt,

    "total_votes" =>
        $totalVotes,

    "winners" =>
        $winners,

    "candidates" =>
        $candidates
];


$resultJson = json_encode(
    $resultSnapshot,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
);


if ($resultJson === false) {

    error_log(
        "VOTIFY publish-result JSON encode failed."
    );

    sendResponse(
        false,
        "Unable to create official result snapshot.",
        [],
        500
    );
}


/* ==========================================================
   ELECTION INFORMATION
========================================================== */

$collegeName =
    "Sona College of Technology";

$departmentName =
    "MCA Department";

$electionTitle =
    "Student Council Election " . date("Y");


/* ==========================================================
   CHECK EXISTING PUBLISHED RESULT
========================================================== */

$existingStmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        result_token,
        result_data
     FROM election_results
     WHERE status = 'Published'
     ORDER BY id DESC
     LIMIT 1"
);


if (!$existingStmt) {

    error_log(
        "VOTIFY publish-result existing result prepare failed: "
        . mysqli_error($conn)
    );

    sendResponse(
        false,
        "Unable to check the current published result.",
        [],
        500
    );
}


mysqli_stmt_execute($existingStmt);

mysqli_stmt_bind_result(
    $existingStmt,
    $existingId,
    $existingToken,
    $existingData
);


$existingFound =
    mysqli_stmt_fetch($existingStmt);

mysqli_stmt_close($existingStmt);


/* ==========================================================
   REUSE EXISTING RESULT TOKEN
   IF SNAPSHOT HAS NOT CHANGED
========================================================== */

if ($existingFound) {

    $existingDecoded =
        json_decode(
            (string)$existingData,
            true
        );


    if (
        is_array($existingDecoded)
        &&
        isset($existingDecoded["candidates"])
        &&
        json_encode(
            $existingDecoded["candidates"],
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        )
        ===
        json_encode(
            $candidates,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        )
    ) {

        sendResponse(
            true,
            "The current official result is already published.",
            [
                "result_token" =>
                    (string)$existingToken,

                "result_url" =>
                    "/pages/public/result.php?token="
                    . rawurlencode(
                        (string)$existingToken
                    ),

                "reused" =>
                    true
            ]
        );
    }
}


/* ==========================================================
   GENERATE SECURE RESULT TOKEN
========================================================== */

try {

    $resultToken =
        bin2hex(
            random_bytes(32)
        );

} catch (Throwable $exception) {

    error_log(
        "VOTIFY secure token generation failed: "
        . $exception->getMessage()
    );

    sendResponse(
        false,
        "Unable to generate secure result verification token.",
        [],
        500
    );
}


/* ==========================================================
   TRANSACTION
========================================================== */

mysqli_begin_transaction($conn);


try {

    /* ------------------------------------------------------
       Archive existing published result
    ------------------------------------------------------ */

    $archiveStmt = mysqli_prepare(
        $conn,
        "UPDATE election_results
         SET status = 'Archived'
         WHERE status = 'Published'"
    );


    if (!$archiveStmt) {

        throw new RuntimeException(
            "Unable to prepare result archive query."
        );
    }


    if (!mysqli_stmt_execute($archiveStmt)) {

        mysqli_stmt_close($archiveStmt);

        throw new RuntimeException(
            "Unable to archive previous result."
        );
    }


    mysqli_stmt_close($archiveStmt);


    /* ------------------------------------------------------
       Insert new official result
    ------------------------------------------------------ */

    $insertStmt = mysqli_prepare(
        $conn,
        "INSERT INTO election_results
        (
            result_token,
            election_title,
            college_name,
            department_name,
            published_at,
            result_data,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'Published'
        )"
    );


    if (!$insertStmt) {

        throw new RuntimeException(
            "Unable to prepare result insert query."
        );
    }


    mysqli_stmt_bind_param(
        $insertStmt,
        "ssssss",
        $resultToken,
        $electionTitle,
        $collegeName,
        $departmentName,
        $publishedAt,
        $resultJson
    );


    if (!mysqli_stmt_execute($insertStmt)) {

        mysqli_stmt_close($insertStmt);

        throw new RuntimeException(
            "Unable to publish official result."
        );
    }


    $newResultId =
        mysqli_insert_id($conn);


    mysqli_stmt_close($insertStmt);


    /* ------------------------------------------------------
       Commit
    ------------------------------------------------------ */

    if (!mysqli_commit($conn)) {

        throw new RuntimeException(
            "Unable to commit result publication."
        );
    }


} catch (Throwable $exception) {

    mysqli_rollback($conn);

    error_log(
        "VOTIFY publish-result transaction failed: "
        . $exception->getMessage()
    );


    sendResponse(
        false,
        "Unable to publish the official election result.",
        [],
        500
    );
}


/* ==========================================================
   RESULT URL
========================================================== */

$resultUrl =
    "/pages/public/result.php?token="
    . rawurlencode($resultToken);


/* ==========================================================
   SUCCESS
========================================================== */

sendResponse(
    true,
    "Official election result published successfully.",
    [
        "result_id" =>
            (int)$newResultId,

        "result_token" =>
            $resultToken,

        "result_url" =>
            $resultUrl,

        "reused" =>
            false
    ]
);