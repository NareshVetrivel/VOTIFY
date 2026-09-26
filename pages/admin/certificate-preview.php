<?php
/* ==========================================================
   VOTIFY
   Official Election Result Certificate

   File:
   pages/admin/certificate-preview.php

   REBUILD
   PART 1 — Certificate Base + Official Header
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

session_start();


/* ==========================================================
   ADMIN SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.html");

    exit();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   CERTIFICATE ACCESS CONTROL
========================================================== */

/*
 * Certificate results are available ONLY after the election
 * has been stopped.
 *
 * This is a SERVER-SIDE security check.
 *
 * Frontend button locking alone is not sufficient because
 * an admin could directly open:
 *
 * pages/admin/certificate-preview.php
 *
 * Therefore:
 *
 * Ready   -> BLOCK
 * Started -> BLOCK
 * Stopped -> ALLOW
 *
 * If the database/status check fails, access is denied
 * (fail-closed security behaviour).
 */

$certificateElectionStatus = null;


/* ==========================================================
   FETCH CURRENT ELECTION STATUS
========================================================== */

$statusStmt = mysqli_prepare(
    $conn,
    "
        SELECT election_status
        FROM election_settings
        WHERE id = 1
        LIMIT 1
    "
);


if (!$statusStmt) {

    http_response_code(500);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >
        <title>VOTIFY | Certificate Unavailable</title>

        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: #080d1c;
                color: #ffffff;
                font-family:
                    Inter,
                    Arial,
                    Helvetica,
                    sans-serif;
            }

            .access-card {
                width: 100%;
                max-width: 520px;
                padding: 36px 30px;
                text-align: center;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 22px;
                background:
                    linear-gradient(
                        145deg,
                        #11182b,
                        #0b1223
                    );
                box-shadow:
                    0 25px 70px rgba(0,0,0,0.45);
            }

            .icon {
                width: 68px;
                height: 68px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(239,68,68,0.12);
                border: 1px solid rgba(239,68,68,0.28);
                color: #f87171;
                font-size: 30px;
            }

            h1 {
                margin: 0 0 10px;
                font-size: 25px;
            }

            p {
                margin: 0;
                color: #94a3b8;
                line-height: 1.7;
                font-size: 15px;
            }

            .back-btn {
                display: inline-flex;
                margin-top: 24px;
                padding: 12px 20px;
                border-radius: 12px;
                text-decoration: none;
                color: #ffffff;
                font-weight: 700;
                background:
                    linear-gradient(
                        100deg,
                        #2563eb,
                        #9333ea,
                        #ec4899
                    );
            }
        </style>
    </head>

    <body>

        <div class="access-card">

            <div class="icon">!</div>

            <h1>Certificate Unavailable</h1>

            <p>
                VOTIFY could not verify the current election status.
                Please return to the Canvassing Reports page and try again.
            </p>

            <a
                class="back-btn"
                href="canvassing.php"
            >
                Back to Canvassing Reports
            </a>

        </div>

    </body>
    </html>
    <?php

    exit();

}


mysqli_stmt_bind_result(
    $statusStmt,
    $certificateElectionStatus
);


if (
    !mysqli_stmt_execute($statusStmt)
    ||
    !mysqli_stmt_fetch($statusStmt)
) {

    mysqli_stmt_close($statusStmt);

    http_response_code(500);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >
        <title>VOTIFY | Certificate Unavailable</title>

        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: #080d1c;
                color: #ffffff;
                font-family:
                    Inter,
                    Arial,
                    Helvetica,
                    sans-serif;
            }

            .access-card {
                width: 100%;
                max-width: 520px;
                padding: 36px 30px;
                text-align: center;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 22px;
                background:
                    linear-gradient(
                        145deg,
                        #11182b,
                        #0b1223
                    );
                box-shadow:
                    0 25px 70px rgba(0,0,0,0.45);
            }

            .icon {
                width: 68px;
                height: 68px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(239,68,68,0.12);
                border: 1px solid rgba(239,68,68,0.28);
                color: #f87171;
                font-size: 30px;
            }

            h1 {
                margin: 0 0 10px;
                font-size: 25px;
            }

            p {
                margin: 0;
                color: #94a3b8;
                line-height: 1.7;
                font-size: 15px;
            }

            .back-btn {
                display: inline-flex;
                margin-top: 24px;
                padding: 12px 20px;
                border-radius: 12px;
                text-decoration: none;
                color: #ffffff;
                font-weight: 700;
                background:
                    linear-gradient(
                        100deg,
                        #2563eb,
                        #9333ea,
                        #ec4899
                    );
            }
        </style>
    </head>

    <body>

        <div class="access-card">

            <div class="icon">!</div>

            <h1>Certificate Unavailable</h1>

            <p>
                VOTIFY could not verify the current election status.
                Certificate access has been blocked for security.
            </p>

            <a
                class="back-btn"
                href="canvassing.php"
            >
                Back to Canvassing Reports
            </a>

        </div>

    </body>
    </html>
    <?php

    exit();

}


mysqli_stmt_close($statusStmt);


/* ==========================================================
   NORMALIZE STATUS
========================================================== */

$certificateElectionStatus =
    trim(
        (string) $certificateElectionStatus
    );


/* ==========================================================
   ONLY STOPPED ELECTION MAY VIEW CERTIFICATE
========================================================== */

if (
    $certificateElectionStatus !== "Stopped"
) {

    http_response_code(403);

    $isRunning =
        $certificateElectionStatus === "Started";

    $accessTitle =
        $isRunning
            ? "Certificate Export Disabled"
            : "Certificate Not Available Yet";

    $accessMessage =
        $isRunning
            ? "Certificate viewing and downloading are disabled while the election is running. Results and certificates will be available after the election is stopped."
            : "The election has not been stopped yet. The official certificate will be available only after the election is stopped.";

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>
            VOTIFY | <?= htmlspecialchars(
                $accessTitle,
                ENT_QUOTES,
                "UTF-8"
            ); ?>
        </title>

        <style>

            * {
                box-sizing: border-box;
            }

            html,
            body {
                margin: 0;
                min-height: 100%;
            }

            body {

                min-height: 100vh;

                display: flex;

                align-items: center;

                justify-content: center;

                padding: 24px;

                background:

                    radial-gradient(
                        circle at top,
                        rgba(37,99,235,0.16),
                        transparent 40%
                    ),

                    #080d1c;

                color: #ffffff;

                font-family:
                    Inter,
                    Arial,
                    Helvetica,
                    sans-serif;

            }


            .access-wrapper {

                width: 100%;

                max-width: 560px;

            }


            .access-card {

                position: relative;

                overflow: hidden;

                padding: 42px 32px 34px;

                text-align: center;

                border:

                    1px solid
                    rgba(
                        255,
                        255,
                        255,
                        0.10
                    );

                border-radius: 24px;

                background:

                    linear-gradient(
                        145deg,
                        rgba(17,24,43,0.98),
                        rgba(8,15,31,0.98)
                    );

                box-shadow:

                    0 30px 90px
                    rgba(
                        0,
                        0,
                        0,
                        0.48
                    );

            }


            .access-card::before {

                content: "";

                position: absolute;

                left: 0;

                right: 0;

                top: 0;

                height: 4px;

                background:

                    linear-gradient(
                        90deg,
                        #2563eb,
                        #9333ea,
                        #ec4899
                    );

            }


            .access-icon {

                width: 76px;

                height: 76px;

                margin: 0 auto 22px;

                display: flex;

                align-items: center;

                justify-content: center;

                border-radius: 50%;

                background:

                    rgba(
                        245,
                        158,
                        11,
                        0.10
                    );

                border:

                    1px solid
                    rgba(
                        245,
                        158,
                        11,
                        0.30
                    );

                color: #fbbf24;

                font-size: 34px;

                font-weight: 800;

            }


            .access-label {

                margin: 0 0 9px;

                color: #64748b;

                font-size: 11px;

                font-weight: 800;

                letter-spacing: 2px;

                text-transform: uppercase;

            }


            h1 {

                margin: 0 0 14px;

                color: #ffffff;

                font-size: 27px;

                line-height: 1.2;

                font-weight: 800;

            }


            .access-message {

                margin: 0 auto;

                max-width: 470px;

                color: #94a3b8;

                font-size: 15px;

                line-height: 1.75;

            }


            .status-pill {

                display: inline-flex;

                align-items: center;

                justify-content: center;

                margin-top: 22px;

                padding: 8px 13px;

                border-radius: 999px;

                background:
                    rgba(
                        245,
                        158,
                        11,
                        0.10
                    );

                border:
                    1px solid
                    rgba(
                        245,
                        158,
                        11,
                        0.24
                    );

                color: #fbbf24;

                font-size: 11px;

                font-weight: 800;

                letter-spacing: 0.8px;

                text-transform: uppercase;

            }


            .back-btn {

                display: inline-flex;

                align-items: center;

                justify-content: center;

                margin-top: 28px;

                min-height: 48px;

                padding:
                    0 22px;

                border-radius: 13px;

                text-decoration: none;

                color: #ffffff;

                font-size: 14px;

                font-weight: 800;

                background:

                    linear-gradient(
                        100deg,
                        #2563eb 0%,
                        #4f46e5 30%,
                        #9333ea 65%,
                        #ec4899 100%
                    );

                box-shadow:

                    0 10px 28px
                    rgba(
                        37,
                        99,
                        235,
                        0.20
                    );

            }


            .back-btn:hover {

                filter: brightness(1.06);

            }


            @media (max-width: 600px) {

                body {

                    padding: 16px;

                }


                .access-card {

                    padding:
                        36px 22px 28px;

                    border-radius: 20px;

                }


                h1 {

                    font-size: 23px;

                }


                .access-message {

                    font-size: 14px;

                }

            }

        </style>

    </head>

    <body>

        <main class="access-wrapper">

            <section
                class="access-card"
                role="alert"
                aria-live="polite"
            >

                <div
                    class="access-icon"
                    aria-hidden="true"
                >
                    !
                </div>


                <p class="access-label">

                    VOTIFY Election System

                </p>


                <h1>

                    <?= htmlspecialchars(
                        $accessTitle,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>

                </h1>


                <p class="access-message">

                    <?= htmlspecialchars(
                        $accessMessage,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>

                </p>


                <div class="status-pill">

                    Election Status:
                    <?= htmlspecialchars(
                        $certificateElectionStatus,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>

                </div>


                <a
                    href="canvassing.php"
                    class="back-btn"
                >
                    Back to Canvassing Reports
                </a>

            </section>

        </main>

    </body>
    </html>
    <?php

    exit();

}


/* ==========================================================
   BASIC ELECTION INFORMATION
========================================================== */

$collegeName =
    "Sona College of Technology";

$departmentName =
    "MCA Department";

$electionTitle =
    "Student Council Election 2026";


/* ==========================================================
   FETCH ACTIVE CANDIDATES
========================================================== */

$candidates = [];


$query = "

SELECT

    id,
    full_name,
    year,
    photo,
    photo_type,
    vote_count

    FROM candidates

    WHERE status = 'Active'

    ORDER BY

        vote_count DESC,

        id ASC

";


$result = mysqli_query(
    $conn,
    $query
);


if ($result) {

    while (
        $row = mysqli_fetch_assoc($result)
    ) {

        $candidates[] = $row;

    }

}


/* ==========================================================
   TOTAL VOTES
========================================================== */

$totalVotes = 0;


foreach ($candidates as $candidate) {

    $totalVotes +=
        (int) $candidate["vote_count"];

}


/* ==========================================================
   CALCULATE PERCENTAGE
========================================================== */

foreach ($candidates as &$candidate) {

    $candidate["votes"] =
        (int) $candidate["vote_count"];


    if ($totalVotes > 0) {

        $candidate["percentage"] =
            (
                $candidate["votes"]
                /
                $totalVotes
            ) * 100;

    }

    else {

        $candidate["percentage"] = 0;

    }

}


unset($candidate);


/* ==========================================================
   TOP THREE
========================================================== */

$chairman =
    $candidates[0] ?? null;


$viceChairman =
    $candidates[1] ?? null;


$jointSecretary =
    $candidates[2] ?? null;


/* ==========================================================
   OFFICIAL RESULT PUBLICATION / VERIFICATION TOKEN
========================================================== */

/*
 * The certificate is generated only after the election is
 * Stopped. At that point we create/reuse the current official
 * result snapshot in election_results.
 *
 * Single-current-election model:
 *
 * - Only one result is kept as Published.
 * - When a new election starts, its previous Published result
 *   is no longer considered current.
 * - When the new election is stopped and the certificate is
 *   opened, a fresh snapshot/token is created.
 *
 * The QR code therefore contains a real public verification URL
 * instead of plain text.
 */

$resultId = "";
$resultVerificationUrl = "";
$resultPublishedAt = null;
$resultPublicationError = null;


/* ----------------------------------------------------------
   FIND THE LATEST ELECTION START EVENT
---------------------------------------------------------- */

$latestElectionStart = null;

$startStmt = mysqli_prepare(
    $conn,
    "
        SELECT created_at
        FROM admin_logs
        WHERE action = 'Election Started'
        ORDER BY created_at DESC
        LIMIT 1
    "
);

if ($startStmt) {

    mysqli_stmt_bind_result(
        $startStmt,
        $latestElectionStart
    );

    if (!mysqli_stmt_execute($startStmt)) {
        $latestElectionStart = null;
    } else {
        mysqli_stmt_fetch($startStmt);
    }

    mysqli_stmt_close($startStmt);
}


/* ----------------------------------------------------------
   BUILD OFFICIAL RESULT SNAPSHOT
---------------------------------------------------------- */

$resultSnapshotCandidates = [];

foreach ($candidates as $index => $candidate) {

    $rank = $index + 1;

    if ($rank === 1) {
        $position = "Chairman";
    } elseif ($rank === 2) {
        $position = "Vice-Chairman";
    } elseif ($rank === 3) {
        $position = "Joint Secretary";
    } else {
        $position = "Candidate";
    }

    $resultSnapshotCandidates[] = [
        "rank" => $rank,
        "position" => $position,
        "candidate" => (string) $candidate["full_name"],
        "year" => (string) $candidate["year"],
        "votes" => (int) $candidate["votes"],
        "percentage" => round(
            (float) $candidate["percentage"],
            2
        )
    ];
}

$resultSnapshot = [
    "positions" => $resultSnapshotCandidates,
    "total_votes" => (int) $totalVotes
];

$resultSnapshotJson = json_encode(
    $resultSnapshot,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_THROW_ON_ERROR
);


/* ----------------------------------------------------------
   LOOK FOR CURRENTLY PUBLISHED RESULT
---------------------------------------------------------- */

$currentPublishedId = null;
$currentPublishedToken = null;
$currentPublishedAt = null;
$currentPublishedData = null;

$currentResultStmt = mysqli_prepare(
    $conn,
    "
        SELECT
            id,
            result_token,
            published_at,
            result_data
        FROM election_results
        WHERE status = 'Published'
        ORDER BY id DESC
        LIMIT 1
    "
);

if ($currentResultStmt) {

    mysqli_stmt_bind_result(
        $currentResultStmt,
        $currentPublishedId,
        $currentPublishedToken,
        $currentPublishedAt,
        $currentPublishedData
    );

    if (!mysqli_stmt_execute($currentResultStmt)) {
        $currentPublishedId = null;
        $currentPublishedToken = null;
        $currentPublishedAt = null;
        $currentPublishedData = null;
    } else {
        mysqli_stmt_fetch($currentResultStmt);
    }

    mysqli_stmt_close($currentResultStmt);
}


/* ----------------------------------------------------------
   DECIDE WHETHER CURRENT RESULT CAN BE REUSED
---------------------------------------------------------- */

$reusePublishedResult = false;

if (
    $currentPublishedId !== null &&
    $currentPublishedToken !== null &&
    $currentPublishedAt !== null
) {

    /*
     * If an election start event exists, a Published result is
     * current only when it was published after that start.
     */
    if ($latestElectionStart !== null) {

        $startTimestamp =
            strtotime((string) $latestElectionStart);

        $publishedTimestamp =
            strtotime((string) $currentPublishedAt);

        if (
            $startTimestamp !== false &&
            $publishedTimestamp !== false &&
            $publishedTimestamp >= $startTimestamp
        ) {
            $reusePublishedResult = true;
        }

    } else {

        /*
         * If the start log cannot be found, do not rotate an
         * already-published result unnecessarily.
         */
        $reusePublishedResult = true;
    }
}


/* ----------------------------------------------------------
   CREATE NEW CURRENT RESULT WHEN REQUIRED
---------------------------------------------------------- */

if ($reusePublishedResult) {

    $resultId =
        (string) $currentPublishedToken;

    $resultPublishedAt =
        (string) $currentPublishedAt;

} else {

    try {

        /*
         * Cryptographically secure 64-character token.
         * This is the public verification identifier.
         */
        $newResultToken =
            bin2hex(
                random_bytes(32)
            );

        /*
         * Keep only the current election result.
         * This matches the VOTIFY single-election maintenance
         * model requested for the project.
         */
        $deleteStmt = mysqli_prepare(
            $conn,
            "
                DELETE FROM election_results
                WHERE status = 'Published'
            "
        );

        if (!$deleteStmt) {
            throw new RuntimeException(
                "Unable to prepare result cleanup."
            );
        }

        if (!mysqli_stmt_execute($deleteStmt)) {

            mysqli_stmt_close($deleteStmt);

            throw new RuntimeException(
                "Unable to replace current published result."
            );
        }

        mysqli_stmt_close($deleteStmt);


        /*
         * Publish the immutable snapshot for this stopped
         * election.
         */
        $insertStmt = mysqli_prepare(
            $conn,
            "
                INSERT INTO election_results
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
                    NOW(),
                    ?,
                    'Published'
                )
            "
        );

        if (!$insertStmt) {

            throw new RuntimeException(
                "Unable to prepare result publication."
            );
        }


        mysqli_stmt_bind_param(
            $insertStmt,
            "sssss",
            $newResultToken,
            $electionTitle,
            $collegeName,
            $departmentName,
            $resultSnapshotJson
        );


        if (!mysqli_stmt_execute($insertStmt)) {

            mysqli_stmt_close($insertStmt);

            throw new RuntimeException(
                "Unable to publish the official result."
            );
        }


        mysqli_stmt_close($insertStmt);


        $resultId =
            $newResultToken;

        $resultPublishedAt =
            date("Y-m-d H:i:s");

    } catch (Throwable $publicationError) {

        error_log(
            "VOTIFY result publication failed: " .
            $publicationError->getMessage()
        );

        $resultPublicationError =
            "The official result could not be published. " .
            "Please refresh the page and try again.";

    }
}


/* ----------------------------------------------------------
   BUILD PUBLIC VERIFICATION URL
---------------------------------------------------------- */

if (
    $resultPublicationError === null &&
    $resultId !== ''
) {

    $https =
        (
            (!empty($_SERVER["HTTPS"]) &&
                $_SERVER["HTTPS"] !== "off")
            ||
            (
                isset($_SERVER["SERVER_PORT"]) &&
                (int) $_SERVER["SERVER_PORT"] === 443
            )
        );

    $scheme =
        $https
            ? "https"
            : "http";

    $host =
        $_SERVER["HTTP_HOST"]
        ?? "votify.byethost16.com";

    $resultVerificationUrl =
        $scheme .
        "://" .
        $host .
        "/pages/public/result.php?token=" .
        rawurlencode($resultId);

}


/* ==========================================================
   GENERATED DATE / TIME
========================================================== */

$generatedDate =
    date("d F Y");


$generatedTime =
    date("h:i A");


/* ==========================================================
   SAFE CANDIDATE PHOTO PATH
========================================================== */

function candidatePhoto($photo, $photoType = "image/jpeg")
{

    if (empty($photo)) {

        return "../../assets/images/logo.png";

    }

    return "data:" .
        htmlspecialchars(
            $photoType,
            ENT_QUOTES,
            "UTF-8"
        ) .
        ";base64," .
        base64_encode($photo);

}

/* ==========================================================
   SAFE TEXT
========================================================== */

function safeText($value)
{

    return htmlspecialchars(

        (string) $value,

        ENT_QUOTES,

        "UTF-8"

    );

}


/* ==========================================================
   FAIL-CLOSED PUBLIC RESULT REQUIREMENT
========================================================== */

if ($resultPublicationError !== null) {

    http_response_code(500);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >
        <title>VOTIFY | Result Publication Error</title>

        <style>
            * {
                box-sizing: border-box;
            }

            html,
            body {
                margin: 0;
                min-height: 100%;
            }

            body {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background:
                    radial-gradient(
                        circle at top,
                        rgba(37,99,235,0.16),
                        transparent 40%
                    ),
                    #080d1c;
                color: #ffffff;
                font-family:
                    Inter,
                    Arial,
                    Helvetica,
                    sans-serif;
            }

            .error-card {
                width: 100%;
                max-width: 560px;
                padding: 42px 32px 34px;
                text-align: center;
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 24px;
                background:
                    linear-gradient(
                        145deg,
                        rgba(17,24,43,0.98),
                        rgba(8,15,31,0.98)
                    );
                box-shadow:
                    0 30px 90px rgba(0,0,0,0.48);
            }

            .error-icon {
                width: 76px;
                height: 76px;
                margin: 0 auto 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(239,68,68,0.10);
                border: 1px solid rgba(239,68,68,0.28);
                color: #f87171;
                font-size: 34px;
                font-weight: 800;
            }

            .error-label {
                margin: 0 0 9px;
                color: #64748b;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 2px;
                text-transform: uppercase;
            }

            h1 {
                margin: 0 0 14px;
                color: #ffffff;
                font-size: 27px;
                line-height: 1.2;
            }

            p {
                margin: 0 auto;
                max-width: 470px;
                color: #94a3b8;
                font-size: 15px;
                line-height: 1.75;
            }

            .back-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-top: 28px;
                min-height: 48px;
                padding: 0 22px;
                border-radius: 13px;
                text-decoration: none;
                color: #ffffff;
                font-size: 14px;
                font-weight: 800;
                background:
                    linear-gradient(
                        100deg,
                        #2563eb,
                        #4f46e5,
                        #9333ea,
                        #ec4899
                    );
            }
        </style>
    </head>

    <body>
        <main class="error-card" role="alert">
            <div class="error-icon" aria-hidden="true">!</div>

            <p class="error-label">
                VOTIFY Election System
            </p>

            <h1>
                Official Result Unavailable
            </h1>

            <p>
                <?= safeText($resultPublicationError); ?>
            </p>

            <a
                href="canvassing.php"
                class="back-btn"
            >
                Back to Canvassing Reports
            </a>
        </main>
    </body>
    </html>
    <?php

    exit();

}

?>


<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>


<title>
    Official Election Result | VOTIFY
</title>


<!-- ======================================================
     GOOGLE FONTS
====================================================== -->

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>


<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
>


<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>


<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Libre+Baskerville:wght@400;700&display=swap"
    rel="stylesheet"
>


<!-- ======================================================
     REMIX ICONS
====================================================== -->



<style>


/* ========================================================
   GLOBAL RESET
======================================================== */

* {

    box-sizing: border-box;

}


html {

    margin: 0;

    padding: 0;

}


body {

    margin: 0;

    padding: 30px;

    min-height: 100vh;

    background:

        #080d1c;

    font-family:

        "Inter",

        Arial,

        Helvetica,

        sans-serif;

    color:

        #14234f;

}

/* ========================================================
   VOTIFY CERTIFICATE DOWNLOAD BUTTON
   PREMIUM GRADIENT • BOTTOM RIGHT
======================================================== */

.certificate-download-btn {

    /* ----------------------------------------------------
       POSITION
    ---------------------------------------------------- */

    position: fixed;

    right: 24px;

    bottom: 24px;

    top: auto;


    /* ----------------------------------------------------
       SIZE
    ---------------------------------------------------- */

    min-width: 210px;

    height: 58px;

    padding:
        0 26px;


    /* ----------------------------------------------------
       LAYOUT
    ---------------------------------------------------- */

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 12px;


    /* ----------------------------------------------------
       BORDER
    ---------------------------------------------------- */

    border: none;

    border-radius: 15px;


    /* ----------------------------------------------------
       VOTIFY GRADIENT
       BLUE → PURPLE → PINK
    ---------------------------------------------------- */

    background:

        linear-gradient(
            100deg,
            #2563eb 0%,
            #4f46e5 30%,
            #9333ea 65%,
            #ec4899 100%
        );


    /* ----------------------------------------------------
       TEXT
    ---------------------------------------------------- */

    color: #ffffff;

    font-family:
        "Inter",
        Arial,
        Helvetica,
        sans-serif;

    font-size: 17px;

    font-weight: 800;

    letter-spacing: -0.2px;


    /* ----------------------------------------------------
       CURSOR
    ---------------------------------------------------- */

    cursor: pointer;


    /* ----------------------------------------------------
       LAYER
    ---------------------------------------------------- */

    z-index: 9999;


    /* ----------------------------------------------------
       NORMAL SHADOW
    ---------------------------------------------------- */

    box-shadow:

        0 10px 28px
        rgba(
            37,
            99,
            235,
            0.24
        ),

        0 6px 18px
        rgba(
            147,
            51,
            234,
            0.18
        );


    /* ----------------------------------------------------
       TRANSITION

       IMPORTANT:
       NO transform here
       → button will NOT move upward
    ---------------------------------------------------- */

    transition:

        box-shadow 0.25s ease,

        filter 0.25s ease,

        opacity 0.2s ease;

}


/* ========================================================
   DOWNLOAD ICON
======================================================== */

.certificate-download-btn svg {

    width: 23px;

    height: 23px;

    flex-shrink: 0;

    display: block;

    stroke: #ffffff;

    stroke-width: 2.4;

    transition: none;

}


/* ========================================================
   HOVER
   STRONGER GLOW ONLY

   IMPORTANT:
   NO transform
======================================================== */

.certificate-download-btn:hover {

    transform: none;

    filter: brightness(1.05);

    box-shadow:

        0 0 0 1px
        rgba(
            255,
            255,
            255,
            0.12
        ),

        0 0 24px
        rgba(
            37,
            99,
            235,
            0.55
        ),

        0 0 42px
        rgba(
            147,
            51,
            234,
            0.42
        ),

        0 14px 34px
        rgba(
            236,
            72,
            153,
            0.28
        );

}


/* ========================================================
   ICON — NO MOVEMENT
======================================================== */

.certificate-download-btn:hover svg {

    transform: none;

}


/* ========================================================
   ACTIVE / CLICK
======================================================== */

.certificate-download-btn:active {

    transform: none;

    filter: brightness(0.98);

    box-shadow:

        0 0 18px
        rgba(
            147,
            51,
            234,
            0.40
        );

}


/* ========================================================
   MOBILE
======================================================== */

@media (max-width: 600px) {

    .certificate-download-btn {

        right: 14px;

        bottom: 14px;

        top: auto;

        min-width: 0;

        width: calc(
            100vw - 28px
        );

        max-width: 280px;

        height: 52px;

        padding:
            0 20px;

        border-radius: 13px;

        font-size: 15px;

    }


    .certificate-download-btn svg {

        width: 21px;

        height: 21px;

    }

}


/* ========================================================
   PRINT / PDF
   BUTTON MUST NOT APPEAR
======================================================== */

@media print {

    .certificate-download-btn {

        display:
            none !important;

    }

}

/* ========================================================
   PREVIEW AREA
======================================================== */

.certificate-preview {

    width: 100%;

    display: flex;

    justify-content: center;

    align-items: flex-start;

}


/* ========================================================
   A4 LANDSCAPE CERTIFICATE

   297mm × 210mm
======================================================== */

.certificate {

    position: relative;

    width: 297mm;

    min-height: 210mm;

    max-width: 100%;

    overflow: hidden;

    background:

        #ffffff;

    border:

        2px solid
        #d4af37;

    box-shadow:

        0 0 0 5px
        #101a4d,

        0 0 0 7px
        #d4af37,

        0 30px 70px
        rgba(
            0,
            0,
            0,
            0.45
        );

}


/* ========================================================
   OUTER DECORATIVE BORDER
======================================================== */

.certificate::before {

    content: "";

    position: absolute;

    inset: 10px;

    border:

        1px solid
        rgba(
            212,
            175,
            55,
            0.75
        );

    pointer-events: none;

    z-index: 1;

}


/* ========================================================
   INNER BORDER
======================================================== */

.certificate::after {

    content: "";

    position: absolute;

    inset: 17px;

    border:

        1px solid
        rgba(
            37,
            99,
            235,
            0.10
        );

    pointer-events: none;

    z-index: 1;

}


/* ========================================================
   MAIN CONTENT
======================================================== */

.certificate-inner {

    position: relative;

    z-index: 5;

    padding:
        10px
        38px
        20px;

}

/* ========================================================
   OFFICIAL HEADER
======================================================== */

.certificate-header {

    display: grid;

    grid-template-columns:
        145px
        1fr
        145px;

    align-items: center;

    gap: 18px;

    min-height: 118px;

    padding:
        4px
        8px
        8px;

}


/* ========================================================
   LOGO AREA
======================================================== */

.logo-area {

    display: flex;

    justify-content: center;

    align-items: center;

}


.votify-logo {

    width: 132px;

    height: 132px;

    object-fit: contain;

    display: block;

    transform: translateY(10px);

}


/* ========================================================
   TITLE AREA
======================================================== */

.title-area {

    text-align: center;

    min-width: 0;

}


/* ========================================================
   OFFICIAL LABEL
======================================================== */

.official-label {

    margin: 0 0 7px;

    color: #64748b;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 3px;

    text-transform: uppercase;

}


/* ========================================================
   MAIN TITLE
======================================================== */

.result-title {

    margin: 0;

    font-family:

        "Libre Baskerville",

        Georgia,

        serif;

    /* Reduced by 2px */
    font-size: 36px;

    line-height: 1.08;

    font-weight: 700;

    letter-spacing: 0.5px;

    color: #173b9b;

}


/* ========================================================
   TITLE GOLD LINE
======================================================== */

.title-rule {

    width: 180px;

    height: 3px;

    margin:

        7px auto
        8px;

    border-radius: 999px;

    background:

        linear-gradient(

            90deg,

            #1d4ed8,

            #d4af37,

            #1d4ed8

        );

}


/* ========================================================
   ELECTION TITLE
======================================================== */

.election-title {

    margin: 0;

    color: #162653;

    /* Reduced by 2px */
    font-size: 15px;

    font-weight: 800;

    letter-spacing: 0.4px;

}


/* ========================================================
   COLLEGE DETAILS
======================================================== */

.college-info {

    display: flex;

    justify-content: center;

    align-items: center;

    flex-wrap: wrap;

    gap: 10px;

    margin-top: 7px;

    color: #263b68;

    font-size: 14px;

    font-weight: 700;

    letter-spacing: 0.15px;

}


.college-dot {

    color: #c49a2e;

    font-size: 16px;

    font-weight: 900;

}


/* ========================================================
   COLLEGE LOGO AREA
======================================================== */

.college-logo-area {

    display: flex;

    justify-content: center;

    align-items: center;

    width: 100%;

    height: 100%;

}


/* ========================================================
   SONA COLLEGE LOGO
======================================================== */

.sona-logo {

    width: 145px;

    height: auto;

    object-fit: contain;

    display: block;

    /*
       Keep width unchanged.
       Increase visible height only.
    */

    transform: scaleY(1.18);

    transform-origin: center center;

}


/* ========================================================
   SONA LOGO — PRINT SAFETY
======================================================== */

@media print {

    .sona-logo {

        width: 145px;

        height: auto;

        transform: scaleY(1.18);

        transform-origin: center center;

    }

}


/* ========================================================
   HEADER DIVIDER
======================================================== */

.header-divider {

    display: flex;

    align-items: center;

    gap: 12px;

    margin:
        1px 8px 0;

    position: relative;

    top: -8px;

}


.header-divider-line {

    flex: 1;

    height: 1px;

    background:

        linear-gradient(

            90deg,

            transparent,

            rgba(
                212,
                175,
                55,
                0.65

            )

        );

}


.header-divider-line.right {

    background:

        linear-gradient(

            90deg,

            rgba(
                212,
                175,
                55,
                0.65
            ),

            transparent

        );

}


.header-divider-mark {

    width: 9px;

    height: 9px;

    transform:
        rotate(45deg);

    border:

        2px solid
        #d4af37;

    background: #ffffff;

}

/* ========================================================
   CERTIFICATE SECTION
======================================================== */

.certificate-section {

    margin-top: 9px;

    width: 100%;

}


/* ========================================================
   SECTION HEADING
======================================================== */

.section-heading {

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 14px;

    margin: 0 0 12px;

}

/* ========================================================
   OFFICIAL WINNERS — MOVE UP ONLY
======================================================== */

.winners-heading {

    position: relative;

    top: -8px;

}

/* ========================================================
   SECTION SIDE LINES
======================================================== */

.section-line {

    flex: 1;

    height: 1px;

    background:
        linear-gradient(
            90deg,
            transparent,
            rgba(
                212,
                175,
                55,
                0.65
            )
        );

}


.section-line:last-child {

    background:
        linear-gradient(
            90deg,
            rgba(
                212,
                175,
                55,
                0.65
            ),
            transparent
        );

}


/* ========================================================
   SECTION BADGE
======================================================== */

.section-badge {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-width: 230px;

    padding:
        9px
        22px;

    border-radius: 9px;

    border:
        1px solid
        #d4af37;

    background:

        linear-gradient(
            135deg,
            #101a63,
            #1f2d91
        );

    color: #ffffff;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 0.8px;

    text-transform: uppercase;

    box-shadow:

        0 5px 14px
        rgba(
            16,
            26,
            77,
            0.18
        );

}


.certificate-svg-icon {

    display: block;

    flex-shrink: 0;

    color: currentColor;

}

.section-svg-icon {

    width: 16px;

    height: 16px;

    color: #f5d36b;

}


/* ========================================================
   PART 3 PLACEHOLDER
======================================================== */

.part-two-space {

    display: none;

}

/* ========================================================
   PART 2 — OFFICIAL WINNER SHOWCASE
======================================================== */

.winners-showcase {

    margin-top: 16px;

    display: grid;

    grid-template-columns:
        1fr
        1.18fr
        1fr;

    gap: 16px;

    align-items: stretch;

}


/* ========================================================
   WINNER CARD
======================================================== */

.winner-card {

    position: relative;

    min-height: 178px;

    padding: 18px;

    display: grid;

    grid-template-columns:
        100px
        1fr;

    align-items: center;

    gap: 16px;

    background:

        linear-gradient(
            145deg,
            #ffffff,
            #f8faff
        );

    border:

        1px solid
        #dbe3f3;

    border-radius: 16px;

    overflow: visible;

}


/* ========================================================
   SIDE WINNERS
======================================================== */

.winner-card.side-winner {

    border-top:

        3px solid
        #3159d8;

}


/* ========================================================
   CHAIRMAN
======================================================== */

.winner-card.chairman {

    min-height: 198px;

    border:

        2px solid
        #d4af37;

    background:

        linear-gradient(
            145deg,
            #fffdf5,
            #ffffff
        );

    box-shadow:

        0 12px 30px
        rgba(
            212,
            175,
            55,
            0.16
        );

}


/* ========================================================
   WINNER MEDAL / CROWN BADGE
======================================================== */

.winner-rank {

    position: absolute;

    top: -22px;

    right: 18px;

    width: 52px;

    height: 52px;

    display: flex;

    align-items: center;

    justify-content: center;

    z-index: 20;

    border-radius: 50%;

    background: #ffffff;

    box-shadow:
        0 6px 15px
        rgba(15, 23, 42, 0.18);

}


/* ========================================================
   SVG MEDAL
======================================================== */

.winner-medal {

    width: 52px;

    height: 52px;

    display: block;

}


/* ========================================================
   CHAIRMAN — PREMIUM GOLD CROWN BADGE
======================================================== */

/* ========================================================
   CHAIRMAN — SOFT PREMIUM GOLD CROWN BADGE
======================================================== */

.winner-rank.rank-gold {

    width: 64px;

    height: 64px;

    top: -29px;

    right: 18px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    /*
       Softer gold background.
       Crown itself remains #D8A018.
    */

    background:

        linear-gradient(
            145deg,
            #fffdf7 0%,
            #fbf4d8 55%,
            #f3e4ad 100%
        );

    /*
       Softer border
    */

    border:

        2px solid
        #d7b84f;

    /*
       Very subtle premium shadow
    */

    box-shadow:

        0 6px 16px
        rgba(
            166,
            124,
            0,
            0.12
        );

}


/* ========================================================
   PROFESSIONAL CROWN SVG
======================================================== */

.winner-rank.rank-gold .winner-medal {

    width: 46px;

    height: 46px;

    display: block;

}


/* ========================================================
   SILVER MEDAL
======================================================== */

.winner-rank.rank-silver {

    background:
        linear-gradient(
            145deg,
            #ffffff,
            #edf2f7
        );

    border:
        2px solid
        #aeb8c8;

}


/* ========================================================
   BRONZE MEDAL
======================================================== */

.winner-rank.rank-bronze {

    background:
        linear-gradient(
            145deg,
            #fff8f0,
            #f5dfca
        );

    border:
        2px solid
        #c98245;

}


/* ========================================================
   PRINT
======================================================== */

@media print {

    .winner-rank {

        box-shadow: none;

    }

}


/* ========================================================
   PHOTO
======================================================== */

.winner-photo-wrap {

    width: 100px;

    height: 125px;

    padding: 4px;

    border-radius: 14px;

    background:

        linear-gradient(
            135deg,
            #2563eb,
            #9333ea
        );

}


.chairman .winner-photo-wrap {

    width: 108px;

    height: 134px;

    background:

        linear-gradient(
            135deg,
            #d4af37,
            #f4c95d
        );

}


.winner-photo {

    width: 100%;

    height: 100%;

    display: block;

    object-fit: cover;

    object-position: center;

    border-radius: 10px;

    background: #e2e8f0;

}


/* ========================================================
   WINNER CONTENT
======================================================== */

.winner-content {

    min-width: 0;

}


.winner-position {

    margin: 0 0 6px;

    color: #3159d8;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 1.4px;

    text-transform: uppercase;

}


.chairman .winner-position {

    color: #a67c00;

}


.winner-name {

    margin: 0;

    color: #10255c;

    font-size: 18px;

    line-height: 1.15;

    font-weight: 800;

    text-transform: uppercase;

    word-break: normal;

}


.chairman .winner-name {

    font-size: 20px;

}


/* ========================================================
   SMALL GOLD DIVIDER
======================================================== */

.winner-divider {

    width: 54px;

    height: 2px;

    margin:

        9px 0;

    border-radius: 999px;

    background:

        linear-gradient(
            90deg,
            #3159d8,
            #c83ccf
        );

}


.chairman .winner-divider {

    background:

        #d4af37;

}


/* ========================================================
   VOTE STATS
======================================================== */

.winner-stats {

    display: flex;

    align-items: center;

    gap: 16px;

}


.winner-stat {

    min-width: 62px;

}


.winner-stat-value {

    margin: 0;

    color: #14275d;

    font-size: 20px;

    line-height: 1;

    font-weight: 800;

}


.winner-stat-label {

    margin: 4px 0 0;

    color: #64748b;

    font-size: 7px;

    font-weight: 800;

    letter-spacing: 0.8px;

    text-transform: uppercase;

}


.winner-stat-divider {

    width: 1px;

    height: 32px;

    background: #d7deea;

}


/* ========================================================
   CENTER CHAIRMAN EMPHASIS
======================================================== */

.chairman .winner-content {

    padding-right: 4px;

}


.chairman .winner-stat-value {

    color: #9a7000;

}


/* ========================================================
   MOBILE / SMALL PREVIEW
======================================================== */

@media (max-width: 900px) {

    .winners-showcase {

        grid-template-columns: 1fr;

    }


    .winner-card,
    .winner-card.chairman {

        min-height: 160px;

    }

}


/* ========================================================
   PRINT SAFETY
======================================================== */

@media print {

    .winner-card {

        box-shadow: none;

        break-inside: avoid;

    }

}

/* ========================================================
   PART 3 — RESULTS + QR VERIFICATION LAYOUT
======================================================== */

.results-verification-grid {

    width: 100%;

    display: grid;

    grid-template-columns:
        minmax(0, 68fr)
        minmax(220px, 32fr);

    gap: 14px;

    align-items: stretch;

}


/* ========================================================
   RESULTS TABLE WRAPPER
======================================================== */

.results-table-wrap {

    width: 100%;

    position: relative;

    overflow: hidden;

    border:
        1px solid
        #dbe3f3;

    border-radius: 10px;

    background:
        #ffffff;

}

/* ========================================================
   VERIFIED WATERMARK
   TABLE BODY ONLY
   SLIGHTLY LEFT OF CENTER
   NEVER COVER TABLE HEADER
======================================================== */

.verified-table-watermark {

    position: absolute;

    /*
       Start below the table header.
    */

    top: 36px;

    bottom: 0;

    left: 0;

    right: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    pointer-events: none;

    z-index: 3;

}


/* ========================================================
   VERIFIED WATERMARK IMAGE
======================================================== */

.verified-table-watermark img {

    width: 135px;

    height: 135px;

    object-fit: contain;

    display: block;

    /*
       Move watermark slightly LEFT.
       Negative value = left.
    */

    transform: translateX(-55px);

    /*
       Very subtle watermark.
    */

    opacity: 0.10;

}

/* ========================================================
   VERIFIED WATERMARK IMAGE
======================================================== */

.verified-table-watermark img {

    width: 135px;

    height: 135px;

    object-fit: contain;

    display: block;

    /*
       Subtle premium watermark.
    */

    opacity: 0.10;

}


/* ========================================================
   TABLE
======================================================== */

.results-table {

    position: relative;

    z-index: 2;

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

    background: transparent;

}


/* ========================================================
   TABLE HEADER
   ALWAYS ABOVE WATERMARK
======================================================== */

.results-table thead {

    position: relative;

    z-index: 10;

}


.results-table thead th {

    position: relative;

    z-index: 10;

    padding:
        9px
        10px;

    background:
        #101a63;

    color:
        #ffffff;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 0.8px;

    text-transform: uppercase;

    text-align: left;

    border-right:
        1px solid
        rgba(
            255,
            255,
            255,
            0.12
        );

}


.results-table thead th:last-child {

    border-right: none;

}


/* ========================================================
   TABLE
======================================================== */

.results-table {

    position: relative;

    z-index: 2;

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

    /*
       IMPORTANT:
       Table itself must remain transparent
       so watermark can appear behind body.
    */

    background: transparent;

}


/* ========================================================
   TABLE HEADER
======================================================== */

.results-table thead th {

    position: relative;

    z-index: 5;

    padding:
        9px
        10px;

    background:
        #101a63;

    color:
        #ffffff;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 0.8px;

    text-transform: uppercase;

    text-align: left;

    border-right:
        1px solid
        rgba(
            255,
            255,
            255,
            0.12
        );

}


.results-table thead th:last-child {

    border-right: none;

}


/* ========================================================
   COLUMN WIDTHS
======================================================== */

.results-table .rank-col {

    width: 12%;

    text-align: center;

}


.results-table .candidate-col {

    width: 43%;

}


.results-table .position-col {

    width: 30%;

}


.results-table .votes-col {

    width: 15%;

    text-align: center;

}


/* ========================================================
   TABLE BODY
   SEMI-TRANSPARENT
   WATERMARK CAN APPEAR ABOVE IT
======================================================== */

.results-table tbody td {

    padding:
        8px
        10px;

    color:
        #172554;

    font-size:
        10px;

    font-weight:
        600;

    border-bottom:
        1px solid
        #e5e7eb;

    vertical-align:
        middle;

    /*
       IMPORTANT:
       Keep body cells slightly transparent.
       Watermark remains visible underneath
       the text while watermark layer is above.
    */

    background:
        rgba(
            255,
            255,
            255,
            0.88
        );

}

/* ========================================================
   ROW BACKGROUNDS
   SEMI-TRANSPARENT FOR WATERMARK
======================================================== */

.results-table tbody tr.rank-1 td {

    background:
        rgba(
            255,
            253,
            245,
            0.88
        );

}


.results-table tbody tr.rank-2 td {

    background:
        rgba(
            248,
            250,
            252,
            0.88
        );

}


.results-table tbody tr.rank-3 td {

    background:
        rgba(
            255,
            250,
            245,
            0.88
        );

}


.results-table tbody tr.rank-other td {

    background:
        rgba(
            255,
            255,
            255,
            0.88
        );

}


.results-table tbody tr:last-child td {

    border-bottom: none;

}


/* ========================================================
   RANK
======================================================== */

.result-rank {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    width: 28px;

    height: 28px;

    border-radius: 50%;

    border:
        1px solid
        #dbe3f3;

    background:
        #f8fafc;

    color:
        #475569;

    font-size: 9px;

    font-weight: 800;

}


/* ========================================================
   TOP 3 ROWS
======================================================== */

.result-row.rank-1 {

    background:
        #fffdf5;

}


.result-row.rank-1 .result-rank {

    background:
        #fff4c7;

    border-color:
        #e1bd52;

    color:
        #946f00;

}


.result-row.rank-2 {

    background:
        #f8fafc;

}


.result-row.rank-2 .result-rank {

    background:
        #edf2f7;

    border-color:
        #b7c0ce;

    color:
        #475569;

}


.result-row.rank-3 {

    background:
        #fffaf5;

}


.result-row.rank-3 .result-rank {

    background:
        #f5dfca;

    border-color:
        #c98245;

    color:
        #8a4b16;

}


/* ========================================================
   CANDIDATE NAME
======================================================== */

.result-candidate-name {

    color:
        #14275d;

    font-size: 10px;

    font-weight: 800;

    text-transform: uppercase;

}


/* ========================================================
   POSITION / STATUS
======================================================== */

.result-status {

    display: inline-flex;

    align-items: center;

    padding:
        4px
        9px;

    border-radius: 999px;

    border:
        1px solid
        #dbe3f3;

    background:
        #f8fafc;

    color:
        #64748b;

    font-size: 7.5px;

    font-weight: 800;

    letter-spacing: 0.6px;

    text-transform: uppercase;

}


.result-row.rank-1 .result-status {

    border-color:
        #e6c76b;

    background:
        #fff8df;

    color:
        #946f00;

}


.result-row.rank-2 .result-status {

    border-color:
        #cbd5e1;

    background:
        #f1f5f9;

    color:
        #475569;

}


.result-row.rank-3 .result-status {

    border-color:
        #e4b88f;

    background:
        #fff1e5;

    color:
        #92501d;

}


/* ========================================================
   VOTES
======================================================== */

.result-votes {

    color:
        #14275d;

    font-size: 12px;

    font-weight: 800;

}


.result-row.rank-1 .result-votes {

    color:
        #946f00;

}


/* ========================================================
   THANK YOU MESSAGE IMAGE
   IMAGE ONLY — NO CARD BORDER / BACKGROUND / SHADOW
======================================================== */

.thankyou-card {

    min-height: 100%;

    padding: 0;

    display: flex;

    align-items: stretch;

    justify-content: stretch;

    /*
       IMPORTANT:
       No border here.
       The artwork itself is the complete visual.
    */

    border: none;

    border-radius: 0;

    background: transparent;

    overflow: hidden;

    position: relative;

    box-shadow: none;

}


/* ========================================================
   APPROVED THANK-YOU ARTWORK
======================================================== */

.thankyou-card-image {

    width: 100%;

    height: 100%;

    min-height: 178px;

    display: block;

    object-fit: cover;

    object-position: center;

    border: none;

    outline: none;

    box-shadow: none;

}


/* ========================================================
   THANK-YOU IMAGE PRINT SAFETY
======================================================== */

@media print {

    .thankyou-card {

        border: none;

        border-radius: 0;

        box-shadow: none;

        background: transparent;

        break-inside: avoid;

        page-break-inside: avoid;

    }


    .thankyou-card-image {

        border: none;

        outline: none;

        box-shadow: none;

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;

    }

}


/* ========================================================
   THANK-YOU IMAGE RESPONSIVE
======================================================== */

@media (max-width: 800px) {

    .thankyou-card {

        min-height: 180px;

    }


    .thankyou-card-image {

        min-height: 180px;

        object-fit: cover;

    }

}

/* ========================================================
   FOOTER PREPARATION
======================================================== */

.certificate-footer {

    margin-top: 18px;

    padding-top: 14px;

    border-top:

        1px solid
        rgba(
            212,
            175,
            55,
            0.45
        );

}


.footer-meta {

    display: grid;

    grid-template-columns:

        repeat(
            4,
            1fr
        );

    gap: 10px;

}


.footer-box {

    min-height: 48px;

    display: flex;

    align-items: center;

    gap: 8px;

    padding:

        8px

        10px;

    border-radius: 8px;

    background:

        #f8fafc;

    border:

        1px solid
        #e5e7eb;

}


.footer-icon {

    width: 27px;

    height: 27px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 7px;

    background: #eef2ff;

    overflow: hidden;

}

.footer-svg-icon {

    width: 16px;

    height: 16px;

    color: #4f46e5;

}


/* ========================================================
   VERIFIED PNG ICON
======================================================== */

.footer-verified-icon {

    width: 20px;

    height: 20px;

    display: block;

    object-fit: contain;

}


.footer-label {

    margin: 0;

    color: #64748b;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 0.8px;

    text-transform: uppercase;

}


.footer-value {

    margin: 3px 0 0;

    color: #172554;

    font-size: 10px;

    font-weight: 700;

}


/* ========================================================
   SECURITY BAR
======================================================== */

.security-note {

    margin-top: 10px;

    padding:
        8px
        16px;

    border-radius: 7px;

    background:

        #101a4d;

    color: #ffffff;

    text-align: center;

    font-size: 9px;

    font-weight: 600;

    letter-spacing: 0.2px;

}


.security-svg-icon {

    width: 14px;

    height: 14px;

    vertical-align: -2px;

    color: #f5c84c;

}


/* ========================================================
   SCREEN RESPONSIVE
======================================================== */

@media (max-width: 1100px) {

    body {

        padding: 20px;

    }


    .certificate {

        width: 100%;

    }


    .certificate-header {

        grid-template-columns:

            110px

            1fr

            110px;

    }


    .votify-logo {

        width: 95px;

        height: 95px;

    }


    .result-title {

        font-size: 31px;

    }

}


/* ========================================================
   TABLET
======================================================== */

@media (max-width: 800px) {

    body {

        padding: 12px;

    }


    .certificate-inner {

        padding:

            20px

            24px;

    }


    .certificate-header {

        grid-template-columns:

            1fr;

        gap: 10px;

        text-align: center;

    }


    .logo-area {

        order: 1;

    }


    .title-area {

        order: 2;

    }


    .status-area {

        order: 3;

    }


    .votify-logo {

        width: 85px;

        height: 85px;

    }


    .result-title {

        font-size: 28px;

    }


    .footer-meta {

        grid-template-columns:

            repeat(
                2,
                1fr
            );

    }

}


/* ========================================================
   MOBILE
======================================================== */

@media (max-width: 520px) {

    body {

        padding: 8px;

    }


    .certificate {

        border-radius: 10px;

    }


    .certificate-inner {

        padding:

            18px

            15px;

    }


    .result-title {

        font-size: 23px;

    }


    .election-title {

        font-size: 14px;

    }


    .college-info {

        font-size: 11px;

    }


    .footer-meta {

        grid-template-columns:

            1fr;

    }

}


/* ========================================================
   PRINT — A4 LANDSCAPE
   FINAL CERTIFICATE FIT
======================================================== */

@page {

    size: A4 landscape;

    margin: 0;

}


@media print {

    /* ----------------------------------------------------
       PAGE
    ---------------------------------------------------- */

    html,
    body {

        width: 297mm;

        height: 210mm;

        margin: 0;

        padding: 0;

        background: #ffffff;

        overflow: hidden;

    }


    body {

        -webkit-print-color-adjust: exact;

        print-color-adjust: exact;

    }


    /* ----------------------------------------------------
       HIDE DOWNLOAD BUTTON
    ---------------------------------------------------- */

    .certificate-download-btn {

        display: none !important;

    }


    /* ----------------------------------------------------
       CERTIFICATE PREVIEW
    ---------------------------------------------------- */

    .certificate-preview {

        width: 297mm;

        height: 210mm;

        margin: 0;

        padding: 0;

        display: block;

        overflow: hidden;

    }


    /* ----------------------------------------------------
       CERTIFICATE
    ---------------------------------------------------- */

    .certificate {

        width: 297mm;

        /*
         * Do not force the certificate to 210mm.
         * Let the actual content determine its height.
         */

        height: auto;

        min-height: 210mm;

        max-width: none;

        overflow: visible;

        border-radius: 0;

        box-shadow: none;

    }


    /* ----------------------------------------------------
       SCALE ONLY FOR PDF / PRINT
    ---------------------------------------------------- */

.certificate-inner {

    transform: none;

    transform-origin: top left;

    width: 100%;

    min-height: 0;

}


    /* ----------------------------------------------------
       KEEP HEADER IN 3 COLUMNS
    ---------------------------------------------------- */

    .certificate-header {

        display: grid !important;

        grid-template-columns:
            145px
            1fr
            145px !important;

    }


    /* ----------------------------------------------------
       KEEP WINNERS IN ONE ROW
    ---------------------------------------------------- */

    .winners-showcase {

        display: grid !important;

        grid-template-columns:
            1fr
            1.18fr
            1fr !important;

    }


    /* ----------------------------------------------------
       TABLE
    ---------------------------------------------------- */

    .results-table-wrap {

        overflow: visible;

        break-inside: avoid;

        page-break-inside: avoid;

    }


    .results-table tr {

        break-inside: avoid;

        page-break-inside: avoid;

    }


    /* ----------------------------------------------------
       FOOTER
    ---------------------------------------------------- */

    .certificate-footer {

        break-inside: avoid;

        page-break-inside: avoid;

    }


    .footer-meta {

        display: grid !important;

        grid-template-columns:
            repeat(4, 1fr) !important;

    }

}


</style>


<!-- ======================================================
     PDF GENERATION LIBRARIES
====================================================== -->

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
></script>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
></script>

</head>


<body>

<!-- ======================================================
     CERTIFICATE DOWNLOAD BUTTON
====================================================== -->

<button
    type="button"
    class="certificate-download-btn"
    onclick="downloadCertificate()"
    aria-label="Download Certificate"
    title="Download Certificate"
>

    <!-- Download SVG -->

    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >

        <path
            d="M12 3v12"
        />

        <path
            d="m7 10 5 5 5-5"
        />

        <path
            d="M5 21h14"
        />

    </svg>

<span>
    Download Certificate
</span>

</button>

<!-- ======================================================
     CERTIFICATE PREVIEW
====================================================== -->

<div class="certificate-preview">


    <!-- ==================================================
         A4 CERTIFICATE
    ================================================== -->

    <main class="certificate">


        <!-- ==================================================
             INNER CONTENT
        ================================================== -->

        <div class="certificate-inner">

            <!-- ==============================================
                 OFFICIAL HEADER
            =============================================== -->

            <header class="certificate-header">


                <!-- ==========================================
                     LOGO
                =========================================== -->

                <div class="logo-area">

                    <img

                        src="../../assets/images/logo.png"

                        alt="VOTIFY Logo"

                        class="votify-logo"

                    >

                </div>


                <!-- ==========================================
                     TITLE
                =========================================== -->

                <div class="title-area">

                    <h1 class="result-title">

                        OFFICIAL ELECTION RESULT

                    </h1>


                    <div class="title-rule"></div>


                    <p class="election-title">

                        <?= safeText(
                            $electionTitle
                        ); ?>

                    </p>


                    <div class="college-info">


                        <span>

                            <?= safeText(
                                $collegeName
                            ); ?>

                        </span>


                        <span class="college-dot">

                            •

                        </span>


                        <span>

                            <?= safeText(
                                $departmentName
                            ); ?>

                        </span>


                    </div>


                </div>


<!-- ==========================================
     SONA COLLEGE OF TECHNOLOGY LOGO
=========================================== -->

<div class="college-logo-area">

    <img
        src="../../assets/images/sona-college-logo.png"
        alt="Sona College of Technology"
        class="sona-logo"
    >

</div>

            </header>


            <!-- ==============================================
                 HEADER DIVIDER
            =============================================== -->

            <div class="header-divider">


                <span class="header-divider-line"></span>


                <span class="header-divider-mark"></span>


                <span class="header-divider-line right"></span>


            </div>

<!-- ======================================================
     PART 2
     OFFICIAL WINNER SHOWCASE
====================================================== -->

<section class="certificate-section">


    <!-- ==================================================
         SECTION TITLE
    =================================================== -->

<div class="section-heading winners-heading">

    <span class="section-line"></span>

    <div class="section-badge">

        <svg
                class="certificate-svg-icon section-svg-icon"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
                focusable="false"
            >
                <path
                    d="M8 3h8v2a4 4 0 0 1-8 0V3Z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linejoin="round"
                />
                <path
                    d="M8 5H5a4 4 0 0 0 4 4M16 5h3a4 4 0 0 1-4 4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
                <path
                    d="M12 9v4m-3 0h6m-4 0-1.5 5h5L13 13"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <path
                    d="M8.5 20h7"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>

        OFFICIAL WINNERS

    </div>

    <span class="section-line"></span>

</div>


    <!-- ==================================================
         WINNERS
    =================================================== -->

    <div class="winners-showcase">


        <!-- ==============================================
             JOINT SECRETARY — POSITION 3
        =============================================== -->

        <?php if ($jointSecretary): ?>


        <article class="winner-card side-winner">


            <!-- RANK -->

<div class="winner-rank rank-bronze">

    <svg
        class="winner-medal"
        viewBox="0 0 100 100"
        xmlns="http://www.w3.org/2000/svg"
        aria-label="Third place medal"
    >

        <!-- Ribbon -->
        <path
            d="M35 8H50V35H35Z"
            fill="#b87333"
        />

        <path
            d="M50 8H65V35H50Z"
            fill="#d99a63"
        />


        <!-- Medal Outer -->
        <circle
            cx="50"
            cy="58"
            r="32"
            fill="#c98245"
            stroke="#8a4b16"
            stroke-width="4"
        />


        <!-- Medal Inner -->
        <circle
            cx="50"
            cy="58"
            r="24"
            fill="#f4d3b5"
            stroke="#ffffff"
            stroke-width="3"
        />


        <!-- Number -->
        <text
            x="50"
            y="67"
            text-anchor="middle"
            font-size="27"
            font-weight="800"
            fill="#8a4b16"
            font-family="Arial, sans-serif"
        >
            3
        </text>

    </svg>

</div>


            <!-- PHOTO -->

            <div class="winner-photo-wrap">

                <img

                    src="<?= candidatePhoto(
                        $jointSecretary["photo"],
                        $jointSecretary["photo_type"]
                    ); ?>"

                    alt="<?= safeText(
                        $jointSecretary["full_name"]
                    ); ?>"

                    class="winner-photo"

                >

            </div>


            <!-- CONTENT -->

            <div class="winner-content">


                <p class="winner-position">

                    Joint Secretary

                </p>


                <h3 class="winner-name">

                    <?= safeText(
                        $jointSecretary["full_name"]
                    ); ?>

                </h3>


                <div class="winner-divider"></div>


                <div class="winner-stats">


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $jointSecretary["votes"]
                            ); ?>

                        </p>


                        <p class="winner-stat-label">

                            Votes

                        </p>

                    </div>


                    <span
                        class="winner-stat-divider"
                    ></span>


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $jointSecretary["percentage"],
                                2
                            ); ?>%

                        </p>


                        <p class="winner-stat-label">

                            Percentage

                        </p>

                    </div>


                </div>


            </div>


        </article>


        <?php endif; ?>


        <!-- ==============================================
             CHAIRMAN — POSITION 1
        =============================================== -->

        <?php if ($chairman): ?>


        <article
            class="
                winner-card
                chairman
            "
        >


            <!-- RANK -->

<!-- ==================================================
     CHAIRMAN PREMIUM GOLD CROWN EMBLEM
================================================== -->

<div
    class="winner-rank rank-gold"
    aria-label="Chairman winner"
>

    <svg
        class="winner-medal"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 717 635"
        role="img"
        aria-label="Chairman crown"
    >

        <!-- ============================================
             CROWN BODY
        ============================================= -->

        <path
            d="
                M 76 218

                C 82 224, 89 231, 96 238
                C 124 266, 154 290, 187 298

                C 214 305, 235 286, 253 257
                C 275 222, 294 177, 319 119

                C 310 111, 304 100, 304 89

                C 304 70, 320 55, 341 55

                C 361 55, 377 70, 377 89

                C 377 100, 371 111, 362 119

                C 387 177, 406 222, 428 257
                C 446 286, 467 305, 494 298

                C 527 290, 557 266, 585 238
                C 592 231, 599 224, 605 218

                L 543 455

                C 542 461, 536 466, 530 465

                C 469 452, 407 446, 341 446

                C 275 446, 213 452, 152 465

                C 146 466, 140 461, 139 455

                Z
            "
            fill="#D8A018"
        />


        <!-- ============================================
             LEFT ROUND TIP
        ============================================= -->

        <circle
            cx="70"
            cy="193"
            r="35"
            fill="#D8A018"
        />


        <!-- ============================================
             TOP ROUND TIP
        ============================================= -->

        <circle
            cx="341"
            cy="90"
            r="36"
            fill="#D8A018"
        />


        <!-- ============================================
             RIGHT ROUND TIP
        ============================================= -->

        <circle
            cx="611"
            cy="193"
            r="35"
            fill="#D8A018"
        />


        <!-- ============================================
             CROWN BASE / BAND
        ============================================= -->

        <path
            d="
                M 146 489

                C 205 475, 272 468, 341 468
                C 410 468, 477 475, 536 489

                C 541 490, 544 495, 544 501

                L 543 526

                C 543 536, 535 543, 526 542

                C 466 532, 405 527, 341 527

                C 277 527, 216 532, 156 542

                C 147 543, 139 536, 139 526

                L 138 501

                C 138 495, 141 490, 146 489

                Z
            "
            fill="#D8A018"
        />

    </svg>

</div>


            <!-- PHOTO -->

            <div class="winner-photo-wrap">

                <img

                    src="<?= candidatePhoto(
                        $chairman["photo"],
                        $chairman["photo_type"]
                    ); ?>"

                    alt="<?= safeText(
                        $chairman["full_name"]
                    ); ?>"

                    class="winner-photo"

                >

            </div>


            <!-- CONTENT -->

            <div class="winner-content">


                <p class="winner-position">

                    Chairman

                </p>


                <h3 class="winner-name">

                    <?= safeText(
                        $chairman["full_name"]
                    ); ?>

                </h3>


                <div class="winner-divider"></div>


                <div class="winner-stats">


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $chairman["votes"]
                            ); ?>

                        </p>


                        <p class="winner-stat-label">

                            Votes

                        </p>

                    </div>


                    <span
                        class="winner-stat-divider"
                    ></span>


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $chairman["percentage"],
                                2
                            ); ?>%

                        </p>


                        <p class="winner-stat-label">

                            Percentage

                        </p>

                    </div>


                </div>


            </div>


        </article>


        <?php endif; ?>


        <!-- ==============================================
             VICE CHAIRMAN — POSITION 2
        =============================================== -->

        <?php if ($viceChairman): ?>


        <article class="winner-card side-winner">


            <!-- RANK -->

<div class="winner-rank rank-silver">

    <svg
        class="winner-medal"
        viewBox="0 0 100 100"
        xmlns="http://www.w3.org/2000/svg"
        aria-label="Second place medal"
    >

        <!-- Ribbon -->
        <path
            d="M35 8H50V35H35Z"
            fill="#8e99aa"
        />

        <path
            d="M50 8H65V35H50Z"
            fill="#cbd3df"
        />


        <!-- Medal Outer -->
        <circle
            cx="50"
            cy="58"
            r="32"
            fill="#aeb8c8"
            stroke="#64748b"
            stroke-width="4"
        />


        <!-- Medal Inner -->
        <circle
            cx="50"
            cy="58"
            r="24"
            fill="#e9edf3"
            stroke="#ffffff"
            stroke-width="3"
        />


        <!-- Number -->
        <text
            x="50"
            y="67"
            text-anchor="middle"
            font-size="27"
            font-weight="800"
            fill="#475569"
            font-family="Arial, sans-serif"
        >
            2
        </text>

    </svg>

</div>


            <!-- PHOTO -->

            <div class="winner-photo-wrap">

                <img

                    src="<?= candidatePhoto(
                    $viceChairman["photo"],
                    $viceChairman["photo_type"]
                ); ?>"

                    alt="<?= safeText(
                        $viceChairman["full_name"]
                    ); ?>"

                    class="winner-photo"

                >

            </div>


            <!-- CONTENT -->

            <div class="winner-content">


                <p class="winner-position">

                    Vice-Chairman

                </p>


                <h3 class="winner-name">

                    <?= safeText(
                        $viceChairman["full_name"]
                    ); ?>

                </h3>


                <div class="winner-divider"></div>


                <div class="winner-stats">


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $viceChairman["votes"]
                            ); ?>

                        </p>


                        <p class="winner-stat-label">

                            Votes

                        </p>

                    </div>


                    <span
                        class="winner-stat-divider"
                    ></span>


                    <div class="winner-stat">

                        <p class="winner-stat-value">

                            <?= number_format(
                                $viceChairman["percentage"],
                                2
                            ); ?>%

                        </p>


                        <p class="winner-stat-label">

                            Percentage

                        </p>

                    </div>


                </div>


            </div>


        </article>


        <?php endif; ?>


    </div>


</section>

<!-- ======================================================
     PART 3
     ALL CANDIDATES + QR VERIFICATION
====================================================== -->

<section class="certificate-section">


    <!-- ==================================================
         SECTION TITLE
    =================================================== -->

    <div class="section-heading">


        <span class="section-line"></span>


        <div class="section-badge">

            <svg
                class="certificate-svg-icon section-svg-icon"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
                focusable="false"
            >
                <rect x="4" y="11" width="3.5" height="8" rx="1" fill="currentColor"/>
                <rect x="10.25" y="7" width="3.5" height="12" rx="1" fill="currentColor"/>
                <rect x="16.5" y="4" width="3.5" height="15" rx="1" fill="currentColor"/>
                <path
                    d="M3.5 20h17"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>

            ALL CANDIDATES RESULTS

        </div>


        <span class="section-line"></span>


    </div>


    <!-- ==================================================
         RESULTS + QR
    =================================================== -->

    <div class="results-verification-grid">


        <!-- ==============================================
             LEFT — CANDIDATES TABLE
        =============================================== -->

        <div class="results-table-wrap">


            <table class="results-table">


                <thead>

                    <tr>


                        <th class="rank-col">

                            Rank

                        </th>


                        <th class="candidate-col">

                            Candidate

                        </th>


                        <th class="position-col">

                            Position / Status

                        </th>


                        <th class="votes-col">

                            Votes

                        </th>


                    </tr>

                </thead>


                <tbody>


                    <?php foreach (
                        $candidates
                        as $index => $candidate
                    ): ?>


                        <?php

                        $rank =
                            $index + 1;


                        if ($rank === 1) {

                            $position =
                                "Chairman";

                        }

                        elseif ($rank === 2) {

                            $position =
                                "Vice-Chairman";

                        }

                        elseif ($rank === 3) {

                            $position =
                                "Joint Secretary";

                        }

                        else {

                            $position =
                                "Candidate";

                        }

                        ?>


                        <tr
                            class="
                                result-row
                                rank-<?= $rank <= 3
                                    ? $rank
                                    : 'other'; ?>
                            "
                        >


                            <!-- RANK -->

                            <td class="rank-col">

                                <span
                                    class="result-rank"
                                >

                                    <?= str_pad(
                                        $rank,
                                        2,
                                        "0",
                                        STR_PAD_LEFT
                                    ); ?>

                                </span>

                            </td>


                            <!-- CANDIDATE -->

                            <td class="candidate-col">

                                <span
                                    class="result-candidate-name"
                                >

                                    <?= safeText(
                                        $candidate["full_name"]
                                    ); ?>

                                </span>

                            </td>


                            <!-- POSITION -->

                            <td class="position-col">

                                <span
                                    class="result-status"
                                >

                                    <?= safeText(
                                        $position
                                    ); ?>

                                </span>

                            </td>


                            <!-- VOTES -->

                            <td class="votes-col">

                                <span
                                    class="result-votes"
                                >

                                    <?= number_format(
                                        $candidate["votes"]
                                    ); ?>

                                </span>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


            <!-- ==========================================
                 VERIFIED WATERMARK
                 BODY AREA ONLY
                 NEVER TOUCH TABLE HEADER
            =========================================== -->

            <div
                class="verified-table-watermark"
                aria-hidden="true"
            >

                <img
                    src="../../assets/images/verified.png"
                    alt=""
                >

            </div>


        </div>


<!-- ==============================================
     RIGHT — THANK YOU MESSAGE IMAGE
     NO CARD BORDER
=============================================== -->

<div
    class="thankyou-card"
    aria-label="Thank You, Voters"
>

    <img
        src="../../assets/images/thankyou-voters.png"
        alt="Thank You, Voters. Your participation made this election a success. Students Today, Leaders Tomorrow."
        class="thankyou-card-image"
    >

</div>


    </div>


</section>


            <!-- ==============================================
                 FOOTER
            =============================================== -->

            <footer class="certificate-footer">


                <div class="footer-meta">


                    <!-- ELECTION DATE -->

                    <div class="footer-box">


                        <div class="footer-icon">

                            <svg
                            class="certificate-svg-icon footer-svg-icon"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                            focusable="false"
                        >
                            <rect
                                x="3.5"
                                y="5"
                                width="17"
                                height="15"
                                rx="2"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M7.5 3.5v3M16.5 3.5v3M3.5 9h17"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <path
                                d="M8 13h.01M12 13h.01M16 13h.01M8 16.5h.01M12 16.5h.01M16 16.5h.01"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                            />
                        </svg>

                        </div>


                        <div>

                            <p class="footer-label">

                                Election Date

                            </p>


                            <p class="footer-value">

                                <?= safeText(
                                    $generatedDate
                                ); ?>

                            </p>

                        </div>


                    </div>


                    <!-- RESULT ID -->

                    <div class="footer-box">


                        <div class="footer-icon">

                            <svg
                            class="certificate-svg-icon footer-svg-icon"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                            focusable="false"
                        >
                            <path
                                d="M12 4a7 7 0 0 1 7 7v1"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                            <path
                                d="M12 7a4 4 0 0 1 4 4v3"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                            <path
                                d="M12 10a1 1 0 0 1 1 1v5.5c0 1.8.5 2.9 1.2 4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                            <path
                                d="M8.5 20.5c-1.2-1.7-1.5-3.3-1.5-5.5V11a5 5 0 0 1 10 0v1"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                            <path
                                d="M5 18c-.8-1.6-1-3.1-1-5.5V11a8 8 0 0 1 16 0v1"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                        </div>


                        <div>

                            <p class="footer-label">

                                Result ID

                            </p>


                            <p class="footer-value">

                                <?= safeText(
                                    $resultId
                                ); ?>

                            </p>

                        </div>


                    </div>


<!-- ==================================================
     VERIFICATION
================================================== -->

<div class="footer-box">


    <div class="footer-icon">

        <img
            src="../../assets/images/verified.png"
            alt="Verified"
            class="footer-verified-icon"
        >

    </div>


    <div>

        <p class="footer-label">

            Verification

        </p>


        <p class="footer-value">

            Verified &amp; Secured

        </p>

    </div>


</div>


                    <!-- GENERATED -->

                    <div class="footer-box">


                        <div class="footer-icon">

                            <svg
                            class="certificate-svg-icon footer-svg-icon"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                            focusable="false"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="8.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M12 7v5l3.5 2"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        </div>


                        <div>

                            <p class="footer-label">

                                Generated

                            </p>


                            <p class="footer-value">

                                <?= safeText(
                                    $generatedTime
                                ); ?>

                            </p>

                        </div>


                    </div>


                </div>


                <!-- SECURITY NOTE -->

                <div class="security-note">

                    <svg
                    class="certificate-svg-icon security-svg-icon"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                    focusable="false"
                >
                    <rect
                        x="5"
                        y="10"
                        width="14"
                        height="10"
                        rx="2"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M8 10V7a4 4 0 0 1 8 0v3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                    <circle cx="12" cy="15" r="1.2" fill="currentColor"/>
                    <path
                        d="M12 16.2v1.7"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

                    &nbsp;

                    This document represents the final vote count
                    recorded by the VOTIFY election system.

                </div>


            </footer>


        </div>


    </main>


</div>

<script>
/* ========================================================
   VOTIFY
   ONE-PAGE A4 LANDSCAPE PDF DOWNLOAD
======================================================== */

/* ========================================================
   CERTIFICATE ACCESS TOAST
======================================================== */

function showCertificateAccessToast(
    type,
    title,
    message
){

    /*
     * Use the existing VOTIFY toast system when available.
     */

    if (
        typeof showToast === "function"
    ) {

        showToast(
            type,
            title,
            message
        );

        return;

    }


    /*
     * Fallback for the standalone certificate page.
     * This keeps the security check functional even if
     * toast.js is not loaded on this page.
     */

    console.warn(
        title + ": " + message
    );

}


/* ========================================================
   DOWNLOAD CERTIFICATE
======================================================== */

async function downloadCertificate() {

    /*
     * IMPORTANT:
     *
     * The certificate page itself is protected server-side.
     * This additional status check protects an already-open
     * certificate tab if the election is started elsewhere.
     *
     * Example:
     *
     * 1. Admin opens certificate while election is Stopped.
     * 2. Another tab starts the election.
     * 3. Admin returns to this certificate tab.
     * 4. Download must be blocked.
     */

    try {

        const statusResponse =
            await fetch(
                "../../backend/admin/dashboard-status.php",
                {
                    method: "GET",
                    credentials: "same-origin",
                    cache: "no-store",
                    headers: {
                        "Accept": "application/json"
                    }
                }
            );


        if (!statusResponse.ok) {

            throw new Error(
                "Unable to verify election status."
            );

        }


        const statusResult =
            await statusResponse.json();


        if (
            !statusResult ||
            statusResult.success !== true ||
            statusResult.status !== "Stopped"
        ) {

            const isRunning =
                statusResult &&
                statusResult.status === "Started";


            showCertificateAccessToast(
                "warning",
                "Download Disabled",
                isRunning
                    ? "Certificate download is disabled while the election is running."
                    : "Certificate download is available only after the election is stopped."
            );


            return;

        }

    }

    catch (statusError) {

        console.error(
            "VOTIFY certificate status check failed:",
            statusError
        );


        showCertificateAccessToast(
            "error",
            "Download Blocked",
            "Election status could not be verified. Please refresh the page and try again."
        );


        return;

    }


    const button =
        document.querySelector(
            ".certificate-download-btn"
        );


    const certificate =
        document.querySelector(
            ".certificate"
        );


    /* ----------------------------------------------------
       BASIC VALIDATION
    ---------------------------------------------------- */

    if (!certificate) {

        console.error(
            "VOTIFY Certificate element not found."
        );

        return;

    }


    if (
        typeof html2canvas === "undefined"
        ||
        typeof window.jspdf === "undefined"
    ) {

        console.error(
            "PDF libraries are not loaded."
        );

        alert(
            "PDF download service is not ready. Please refresh the page and try again."
        );

        return;

    }


    /* ----------------------------------------------------
       PREVENT DOUBLE CLICK
    ---------------------------------------------------- */

    if (
        button.dataset.downloading === "true"
    ) {

        return;

    }


    button.dataset.downloading =
        "true";


    /* ----------------------------------------------------
       SAVE ORIGINAL BUTTON
    ---------------------------------------------------- */

    const originalHTML =
        button.innerHTML;


    /* ----------------------------------------------------
       LOADING ICON
    ---------------------------------------------------- */

    button.innerHTML = `

        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >

            <circle
                cx="12"
                cy="12"
                r="9"
            />

            <path
                d="M12 8v4l2.5 2.5"
            />

        </svg>

    `;


    try {


        /* =================================================
           WAIT FOR FONTS
        ================================================= */

        if (
            document.fonts
            &&
            document.fonts.ready
        ) {

            await document.fonts.ready;

        }


        /* =================================================
           WAIT FOR IMAGES
        ================================================= */

        const images =
            certificate.querySelectorAll(
                "img"
            );


        await Promise.all(

            Array.from(images)
                .map(
                    image =>
                        new Promise(
                            resolve => {

                                if (
                                    image.complete
                                ) {

                                    resolve();

                                }

                                else {

                                    image.onload =
                                        resolve;

                                    image.onerror =
                                        resolve;

                                }

                            }
                        )
                )

        );


        /* =================================================
           CREATE PDF CLONE
        ================================================= */

        const clone =
            certificate.cloneNode(true);


        /* -------------------------------------------------
           REMOVE DOWNLOAD BUTTON IF IT EVER EXISTS
        ------------------------------------------------- */

        const clonedButton =
            clone.querySelector(
                ".certificate-download-btn"
            );


        if (clonedButton) {

            clonedButton.remove();

        }


        /* =================================================
           PDF RENDER CONTAINER
        ================================================= */

        const pdfContainer =
            document.createElement(
                "div"
            );


        pdfContainer.style.position =
            "fixed";

        pdfContainer.style.left =
            "-100000px";

        pdfContainer.style.top =
            "0";

        pdfContainer.style.width =
            "297mm";

        pdfContainer.style.height =
            "auto";

        pdfContainer.style.margin =
            "0";

        pdfContainer.style.padding =
            "0";

        pdfContainer.style.background =
            "#ffffff";

        pdfContainer.style.overflow =
            "visible";

        pdfContainer.style.zIndex =
            "-9999";


        /* =================================================
           PDF CLONE BASE STYLE
        ================================================= */

        clone.style.width =
            "297mm";

        clone.style.maxWidth =
            "none";

        clone.style.minHeight =
            "0";

        clone.style.height =
            "auto";

        clone.style.margin =
            "0";

        clone.style.padding =
            "0";

        clone.style.borderRadius =
            "0";

        clone.style.boxShadow =
            "none";

        clone.style.overflow =
            "visible";

        clone.style.transform =
            "none";


        /* =================================================
           IMPORTANT
           REMOVE SCREEN RESPONSIVE BEHAVIOUR
        ================================================= */

        const cloneHeader =
            clone.querySelector(
                ".certificate-header"
            );


        if (cloneHeader) {

            cloneHeader.style.display =
                "grid";

            cloneHeader.style.gridTemplateColumns =
                "145px 1fr 145px";

        }


        const cloneWinners =
            clone.querySelector(
                ".winners-showcase"
            );


        if (cloneWinners) {

            cloneWinners.style.display =
                "grid";

            cloneWinners.style.gridTemplateColumns =
                "1fr 1.18fr 1fr";

        }


        const cloneFooterMeta =
            clone.querySelector(
                ".footer-meta"
            );


        if (cloneFooterMeta) {

            cloneFooterMeta.style.display =
                "grid";

            cloneFooterMeta.style.gridTemplateColumns =
                "repeat(4, 1fr)";

        }


        /* =================================================
           REMOVE PRINT TRANSFORM
        ================================================= */

        const cloneInner =
            clone.querySelector(
                ".certificate-inner"
            );


        if (cloneInner) {

            cloneInner.style.transform =
                "none";

            cloneInner.style.width =
                "auto";

            cloneInner.style.minHeight =
                "0";

        }


        /* =================================================
           APPEND CLONE
        ================================================= */

        pdfContainer.appendChild(
            clone
        );


        document.body.appendChild(
            pdfContainer
        );


        /* =================================================
           FORCE BROWSER LAYOUT
        ================================================= */

        await new Promise(
            resolve =>
                requestAnimationFrame(
                    () =>
                        requestAnimationFrame(
                            resolve
                        )
                )
        );


        /* =================================================
           MEASURE ACTUAL CONTENT
        ================================================= */

        const contentWidth =
            clone.scrollWidth;


        const contentHeight =
            clone.scrollHeight;


        console.log(
            "VOTIFY PDF width:",
            contentWidth
        );

        console.log(
            "VOTIFY PDF height:",
            contentHeight
        );


        /* =================================================
           A4 LANDSCAPE SIZE
           
           297mm × 210mm
        ================================================= */

        const A4_WIDTH =
            297;

        const A4_HEIGHT =
            210;


        /* =================================================
           CSS PIXELS PER MM
           
           Browser standard:
           96px = 25.4mm
        ================================================= */

        const PX_PER_MM =
            96 / 25.4;


        const targetWidth =
            A4_WIDTH *
            PX_PER_MM;


        const targetHeight =
            A4_HEIGHT *
            PX_PER_MM;


        /* =================================================
           DYNAMIC SCALE
           
           NEVER CUT CONTENT
        ================================================= */

        const widthScale =
            targetWidth /
            contentWidth;


        const heightScale =
            targetHeight /
            contentHeight;


        const scale =
            Math.min(
                widthScale,
                heightScale
            );


        console.log(
            "VOTIFY PDF scale:",
            scale
        );


        /* =================================================
           APPLY SCALE
        ================================================= */

        clone.style.transform =
            `scale(${scale})`;

        clone.style.transformOrigin =
            "top left";


        clone.style.width =
            `${contentWidth}px`;

        clone.style.height =
            `${contentHeight}px`;


        /* =================================================
           GIVE BROWSER TIME TO APPLY SCALE
        ================================================= */

        await new Promise(
            resolve =>
                requestAnimationFrame(
                    resolve
                )
        );


        /* =================================================
           CAPTURE CERTIFICATE
        ================================================= */

        const canvas =
            await html2canvas(
                clone,
                {

                    scale: 2,

                    useCORS: true,

                    allowTaint: false,

                    backgroundColor:
                        "#ffffff",

                    imageTimeout:
                        15000,

                    logging:
                        false,

                    scrollX:
                        0,

                    scrollY:
                        0

                }
            );


        /* =================================================
           CREATE PDF
           
           ONE PAGE ONLY
        ================================================= */

        const {
            jsPDF
        } = window.jspdf;


        const pdf =
            new jsPDF({

                orientation:
                    "landscape",

                unit:
                    "mm",

                format:
                    "a4",

                compress:
                    true

            });


        /* =================================================
           PDF PAGE SIZE
        ================================================= */

        const pageWidth =
            pdf.internal.pageSize.getWidth();


        const pageHeight =
            pdf.internal.pageSize.getHeight();


        /* =================================================
           CONVERT CANVAS TO IMAGE
        ================================================= */

        const imageData =
            canvas.toDataURL(
                "image/jpeg",
                0.98
            );


        /* =================================================
           FINAL FIT
           
           Keep entire certificate
           inside A4.
        ================================================= */

        const imageRatio =
            canvas.width /
            canvas.height;


        const pageRatio =
            pageWidth /
            pageHeight;


        let finalWidth;
        let finalHeight;
        let offsetX;
        let offsetY;


        if (
            imageRatio >
            pageRatio
        ) {

            finalWidth =
                pageWidth;

            finalHeight =
                pageWidth /
                imageRatio;

            offsetX =
                0;

            offsetY =
                (
                    pageHeight -
                    finalHeight
                ) / 2;

        }

        else {

            finalHeight =
                pageHeight;

            finalWidth =
                pageHeight *
                imageRatio;

            offsetY =
                0;

            offsetX =
                (
                    pageWidth -
                    finalWidth
                ) / 2;

        }


        /* =================================================
           WHITE PAGE BACKGROUND
        ================================================= */

        pdf.setFillColor(
            255,
            255,
            255
        );


        pdf.rect(
            0,
            0,
            pageWidth,
            pageHeight,
            "F"
        );


        /* =================================================
           ADD COMPLETE CERTIFICATE
           
           ONLY ON PAGE 1
        ================================================= */

        pdf.addImage(

            imageData,

            "JPEG",

            offsetX,

            offsetY,

            finalWidth,

            finalHeight,

            undefined,

            "FAST"

        );


        /* =================================================
           GUARANTEE ONE PAGE
        ================================================= */

        while (
            pdf.getNumberOfPages() >
            1
        ) {

            pdf.deletePage(
                pdf.getNumberOfPages()
            );

        }


        /* =================================================
           DOWNLOAD FILE
        ================================================= */

        const fileName =
            "VOTIFY-Election-Result-" +
            "<?= date('Y'); ?>" +
            ".pdf";


        pdf.save(
            fileName
        );


        /* =================================================
           REMOVE TEMPORARY CLONE
        ================================================= */

        pdfContainer.remove();


    }

    catch (error) {


        console.error(
            "VOTIFY PDF generation failed:",
            error
        );


        alert(
            "Unable to generate the certificate PDF. Please try again."
        );


        /* -------------------------------------------------
           REMOVE TEMPORARY CLONE
        ------------------------------------------------- */

        const temporaryContainers =
            document.querySelectorAll(
                "body > div"
            );


        temporaryContainers.forEach(
            element => {

                if (
                    element.style.left ===
                    "-100000px"
                ) {

                    element.remove();

                }

            }
        );

    }


    finally {


        /* =================================================
           RESTORE DOWNLOAD BUTTON
        ================================================= */

        button.innerHTML =
            originalHTML;


        button.dataset.downloading =
            "false";

    }

}

</script>

</body>

</html>