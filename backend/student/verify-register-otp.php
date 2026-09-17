<?php
/* ==========================================================
   VOTIFY
   Verify Registration OTP
   File : backend/student/verify-register-otp.php
========================================================== */

declare(strict_types=1);


/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ==========================================================
   RESPONSE HEADER
========================================================== */

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================================
   REQUIRED FILES
========================================================== */

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../lib/otp.php";


/* ==========================================================
   JSON RESPONSE HELPER
========================================================== */

function jsonResponse(
    string $status,
    string $message,
    array $extra = []
): never {

    echo json_encode(
        array_merge(
            [
                "status"  => $status,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* ==========================================================
   POST REQUEST ONLY
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


/* ==========================================================
   GET OTP
========================================================== */

$otp = trim(
    (string)($_POST["otp"] ?? "")
);


/* ==========================================================
   OTP REQUIRED
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
   GET PENDING REGISTRATION
========================================================== */

$data = $_SESSION["pending_registration"];


/* ==========================================================
   READ REGISTRATION DATA
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
   FINAL PENDING DATA VALIDATION
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
   FINAL ADMISSION NUMBER VALIDATION
========================================================== */

if (
    !preg_match(
        '/^[0-9]{2}CAPMCA[0-9]{3}$/',
        $admission_no
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid Admission Number. Please register again."
    );
}


/* ==========================================================
   FINAL PHONE VALIDATION
========================================================== */

if (
    !preg_match(
        '/^[6-9][0-9]{9}$/',
        $phone
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid phone number. Please register again."
    );
}


/* ==========================================================
   FINAL EMAIL VALIDATION
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
   COLLEGE EMAIL DOMAIN
========================================================== */

if (
    !preg_match(
        '/^[a-zA-Z0-9._%+-]+@sonatech\.ac\.in$/i',
        $college_email
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
   DEPARTMENT VALIDATION
========================================================== */

if ($department !== "MCA") {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid department. Please register again."
    );
}


/* ==========================================================
   YEAR VALIDATION
========================================================== */

if (
    !in_array(
        $year,
        [
            "I Year",
            "II Year"
        ],
        true
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid year. Please register again."
    );
}


/* ==========================================================
   GENDER VALIDATION
========================================================== */

if (
    !in_array(
        $gender,
        [
            "Male",
            "Female",
            "Other"
        ],
        true
    )
) {

    clearOtpSession("register");

    unset(
        $_SESSION["pending_registration"]
    );

    jsonResponse(
        "error",
        "Invalid gender. Please register again."
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
   DATABASE TRANSACTION
========================================================== */

$stmt = null;

try {

    /* ======================================================
       START TRANSACTION
    ====================================================== */

    if (!$conn->begin_transaction()) {

        throw new Exception(
            "Unable to start database transaction: " .
            $conn->error
        );
    }


    /* ======================================================
       REMOVE OLD REJECTED RECORD
    ====================================================== */

    /*
     * IMPORTANT:
     *
     * Rejected student records should not remain
     * inside the students table.
     *
     * This cleanup protects the registration flow
     * against old rejected records created by the
     * previous system behavior.
     *
     * If the same Admission Number / Phone / Email
     * belongs to a Rejected record, that record is
     * removed before the final duplicate check.
     *
     * This allows the UNIQUE constraints to remain
     * active for valid student records.
     */

    $deleteRejectedStmt = $conn->prepare(
        "
        DELETE FROM students
        WHERE
            LOWER(TRIM(status)) = 'rejected'
            AND (
                admission_no = ?
                OR phone = ?
                OR college_email = ?
            )
        "
    );


    if (!$deleteRejectedStmt) {

        throw new Exception(
            "Rejected record cleanup prepare failed: " .
            $conn->error
        );
    }


    $deleteRejectedStmt->bind_param(
        "sss",
        $admission_no,
        $phone,
        $college_email
    );


    if (!$deleteRejectedStmt->execute()) {

        throw new Exception(
            "Rejected record cleanup execute failed: " .
            $deleteRejectedStmt->error
        );
    }


    $deleteRejectedStmt->close();


    /* ======================================================
       FINAL DUPLICATE CHECK
    ====================================================== */

    $stmt = $conn->prepare(
        "
        SELECT id
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

        $stmt = null;

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

    $stmt = null;


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
       BIND INSERT DATA
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
       GET GENERATED STUDENT ID
    ====================================================== */

    $studentId = (int)$stmt->insert_id;


    $stmt->close();

    $stmt = null;


    if ($studentId <= 0) {

        throw new Exception(
            "Student ID was not generated."
        );
    }


    /* ======================================================
       COMMIT TRANSACTION
    ====================================================== */

    if (!$conn->commit()) {

        throw new Exception(
            "Database commit failed: " .
            $conn->error
        );
    }


    /* ======================================================
       CLEAR OTP SESSION
       ONLY AFTER SUCCESSFUL COMMIT
    ====================================================== */

    clearOtpSession("register");


    unset(
        $_SESSION["pending_registration"]
    );


    /* ======================================================
       SUCCESS RESPONSE
    ====================================================== */

    jsonResponse(
        "success",
        "Email verified. Registration submitted for admin approval.",
        [
            "student_id" => $studentId,
            "redirect"   => "../../index.html"
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
       USER js
    ====================================================== */

    jsonResponse(
        "error",
        "Unable to complete registration. Please try again."
    );
}


/* ==========================================================
   CLOSE DATABASE
========================================================== */

$conn->close();

?>