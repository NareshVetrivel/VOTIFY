<?php
/* ==========================================================
   VOTIFY
   Verify Registration OTP
   File : backend/student/verify-register-otp.php
========================================================== */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   REQUIRED FILES
========================================================== */

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../lib/otp.php";


/* ==========================================================
   HELPER - JSON RESPONSE
========================================================== */

function jsonResponse(
    string $status,
    string $message,
    array $extra = []
): never {

    echo json_encode(
        array_merge(
            [
                "status" => $status,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* ==========================================================
   POST ONLY
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    jsonResponse(
        "error",
        "Invalid request method."
    );
}


/* ==========================================================
   DATABASE CONNECTION CHECK
========================================================== */

if (
    !isset($conn) ||
    !($conn instanceof mysqli)
) {

    error_log(
        "VOTIFY OTP Verification: Database connection object missing."
    );

    jsonResponse(
        "error",
        "Database service is currently unavailable. Please try again later."
    );
}


if ($conn->connect_errno) {

    error_log(
        "VOTIFY Database Connection Error: " .
        $conn->connect_error
    );

    jsonResponse(
        "error",
        "Database service is currently unavailable. Please try again later."
    );
}


/* ==========================================================
   GET OTP
========================================================== */

$otp = trim(
    $_POST["otp"] ?? ""
);


/* ==========================================================
   OTP EMPTY
========================================================== */

if ($otp === "") {

    jsonResponse(
        "error",
        "Please enter the OTP.",
        [
            "field" => "otp"
        ]
    );
}


/* ==========================================================
   OTP FORMAT
========================================================== */

if (!preg_match('/^[0-9]{6}$/', $otp)) {

    jsonResponse(
        "error",
        "Enter a valid 6-digit OTP.",
        [
            "field" => "otp"
        ]
    );
}


/* ==========================================================
   CHECK PENDING REGISTRATION
========================================================== */

if (
    !isset($_SESSION["pending_registration"]) ||
    !is_array($_SESSION["pending_registration"])
) {

    jsonResponse(
        "error",
        "Registration session expired. Please register again."
    );
}


/* ==========================================================
   CHECK OTP SESSION
========================================================== */

if (
    empty($_SESSION["register_otp_hash"]) ||
    empty($_SESSION["register_otp_expiry"])
) {

    jsonResponse(
        "error",
        "OTP session expired. Please request a new OTP.",
        [
            "field" => "otp"
        ]
    );
}


/* ==========================================================
   VERIFY OTP
========================================================== */

$otpResult = verifyOtpSession(
    "register",
    $otp,
    5
);


if (
    !is_array($otpResult) ||
    empty($otpResult["success"])
) {

    jsonResponse(
        "error",
        $otpResult["message"] ??
        "Invalid or expired OTP.",
        [
            "field" => "otp"
        ]
    );
}


/* ==========================================================
   GET PENDING REGISTRATION DATA
========================================================== */

$data = $_SESSION["pending_registration"];


/* ==========================================================
   GET REGISTRATION VALUES
========================================================== */

$full_name = trim(
    (string)($data["full_name"] ?? "")
);

$dob = trim(
    (string)($data["dob"] ?? "")
);

$admission_no = strtoupper(
    trim(
        (string)($data["admission_no"] ?? "")
    )
);

$phone = trim(
    (string)($data["phone"] ?? "")
);

$college_email = strtolower(
    trim(
        (string)($data["college_email"] ?? "")
    )
);

$department = trim(
    (string)($data["department"] ?? "")
);

$year = trim(
    (string)($data["year"] ?? "")
);

$gender = trim(
    (string)($data["gender"] ?? "")
);

$hashed_password = (string)(
    $data["password"] ?? ""
);


/* ==========================================================
   VALIDATE PENDING DATA
========================================================== */

if (
    $full_name === "" ||
    $dob === "" ||
    $admission_no === "" ||
    $phone === "" ||
    $college_email === "" ||
    $department === "" ||
    $year === "" ||
    $gender === "" ||
    $hashed_password === ""
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Registration data is incomplete. Please register again."
    );
}


/* ==========================================================
   EMAIL VALIDATION
========================================================== */

if (
    !filter_var(
        $college_email,
        FILTER_VALIDATE_EMAIL
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid college email address."
    );
}


/* ==========================================================
   PASSWORD HASH VALIDATION
========================================================== */

$passwordInfo = password_get_info(
    $hashed_password
);

if (
    empty($passwordInfo["algo"])
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid registration password data. Please register again."
    );
}


/* ==========================================================
   START TRANSACTION
========================================================== */

try {

    if (!$conn->begin_transaction()) {

        throw new Exception(
            "Unable to start database transaction: " .
            $conn->error
        );
    }


    /* ======================================================
       FINAL DUPLICATE CHECK
    ====================================================== */

    $stmt = $conn->prepare(
        "
        SELECT
            id,
            admission_no,
            phone,
            college_email
        FROM students
        WHERE
            admission_no = ?
            OR phone = ?
            OR college_email = ?
        LIMIT 1
        "
    );


    if (!$stmt) {

        throw new Exception(
            "Duplicate check prepare failed: " .
            $conn->error
        );
    }


    $stmt->bind_param(
        "sss",
        $admission_no,
        $phone,
        $college_email
    );


    if (!$stmt->execute()) {

        throw new Exception(
            "Duplicate check execute failed: " .
            $stmt->error
        );
    }


    $stmt->store_result();


    if ($stmt->num_rows > 0) {

        $stmt->close();

        $conn->rollback();


        clearOtpSession("register");

        unset(
            $_SESSION["pending_registration"]
        );


        jsonResponse(
            "error",
            "Student registration already exists."
        );
    }


    $stmt->close();


    /* ======================================================
       INSERT STUDENT
    ====================================================== */

    $stmt = $conn->prepare(
        "
        INSERT INTO students
        (
            full_name,
            dob,
            admission_no,
            phone,
            college_email,
            department,
            `year`,
            gender,
            password,
            status,
            vote_status,
            email_verified
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'Pending',
            'Unvoted',
            1
        )
        "
    );


    if (!$stmt) {

        throw new Exception(
            "Student insert prepare failed: " .
            $conn->error
        );
    }


    /* ======================================================
       BIND INSERT VALUES
    ====================================================== */

    $stmt->bind_param(
        "sssssssss",
        $full_name,
        $dob,
        $admission_no,
        $phone,
        $college_email,
        $department,
        $year,
        $gender,
        $hashed_password
    );


    /* ======================================================
       EXECUTE INSERT
    ====================================================== */

    if (!$stmt->execute()) {

        throw new Exception(
            "Student insert execute failed: " .
            $stmt->error
        );
    }


    /* ======================================================
       GET STUDENT ID
    ====================================================== */

    $studentId = (int)$stmt->insert_id;

    $stmt->close();


    if ($studentId <= 0) {

        throw new Exception(
            "Student registration failed. Student ID was not generated."
        );
    }


    /* ======================================================
       COMMIT
    ====================================================== */

    if (!$conn->commit()) {

        throw new Exception(
            "Database commit failed: " .
            $conn->error
        );
    }


    /* ======================================================
       CLEAR OTP + PENDING SESSION
       ONLY AFTER SUCCESSFUL COMMIT
    ====================================================== */

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );


    /* ======================================================
       SUCCESS
    ====================================================== */

    jsonResponse(
        "success",
        "Email verified. Registration submitted for admin approval.",
        [
            "student_id" => $studentId
        ]
    );


} catch (Throwable $e) {

    /* ======================================================
       ROLLBACK
    ====================================================== */

    try {

        $conn->rollback();

    } catch (Throwable $rollbackError) {

        error_log(
            "VOTIFY Rollback Error: " .
            $rollbackError->getMessage()
        );
    }


    /* ======================================================
       CLOSE STATEMENT
    ====================================================== */

    if (
        isset($stmt) &&
        $stmt instanceof mysqli_stmt
    ) {

        @$stmt->close();
    }


    /* ======================================================
       LOG ACTUAL ERROR
    ====================================================== */

    error_log(
        "VOTIFY Registration OTP Error: " .
        $e->getMessage()
    );


    /* ======================================================
       DEVELOPMENT MODE
       
       IMPORTANT:
       Keep TRUE while debugging.
       Change to FALSE in production.
    ====================================================== */

    $showDevelopmentError = true;


    if ($showDevelopmentError) {

        jsonResponse(
            "error",
            "Unable to complete registration.",
            [
                "debug" => $e->getMessage()
            ]
        );
    }


    /* ======================================================
       PRODUCTION ERROR
    ====================================================== */

    jsonResponse(
        "error",
        "Unable to complete registration. Please try again."
    );
}

?>