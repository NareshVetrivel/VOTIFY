<?php
/* ==========================================================
   VOTIFY
   Submit Vote Backend
   File : backend/student/submit_vote.php
========================================================== */

/* ==========================================================
   SESSION
========================================================== */

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if(

    !isset($_SESSION["student_logged_in"]) ||

    $_SESSION["student_logged_in"] !== true

){

    header(

        "Location: ../../pages/student/student_login.php"

    );

    exit();

}

/* ==========================================================
   REQUEST METHOD VALIDATION
========================================================== */

if(

    $_SERVER["REQUEST_METHOD"] !== "POST"

){

    header(

        "Location: ../../pages/student/candidate_selection.php"

    );

    exit();

}

/* ==========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../../config/database.php";

$conn = $GLOBALS["conn"];

/* ==========================================================
   HELPER FUNCTION
========================================================== */

function rollbackAndExit(

    mysqli $conn,

    string $redirect

){

    mysqli_rollback(

        $conn

    );

    mysqli_close(

        $conn

    );

    header(

        "Location: ".$redirect

    );

    exit();

}

/* ==========================================================
   REQUIRED SESSION VALIDATION
========================================================== */

if(

    !isset($_SESSION["student_id"]) ||

    !isset($_SESSION["selected_candidate_id"]) ||

    !isset($_SESSION["selected_candidate_name"])

){

    mysqli_close(

        $conn

    );

    header(

        "Location: ../../pages/student/candidate_selection.php"

    );

    exit();

}

/* ==========================================================
   SESSION VARIABLES
========================================================== */

$studentId =

    (int) $_SESSION["student_id"];

$candidateId =

    $_SESSION["selected_candidate_id"];

$candidateName =

    trim(

        $_SESSION["selected_candidate_name"]

    );

/* ==========================================================
   NOTA CHECK
========================================================== */

$isNOTA = (

    strtoupper(

        $candidateId

    ) === "NOTA"

);

/* ==========================================================
   START DATABASE TRANSACTION
========================================================== */

mysqli_begin_transaction(

    $conn

);

/* ==========================================================
   PART 2
   STUDENT VALIDATION
   DUPLICATE VOTE PROTECTION
========================================================== */

/* ==========================================================
   FETCH STUDENT (LOCK ROW)
========================================================== */

$query = "

SELECT

    id,

    vote_status

FROM students

WHERE

    id = ?

LIMIT 1

FOR UPDATE

";

$stmt = mysqli_prepare(

    $conn,

    $query

);

if(!$stmt){

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $studentId

);

if(

    !mysqli_stmt_execute($stmt)

){

    mysqli_stmt_close(

        $stmt

    );

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

$result = mysqli_stmt_get_result(

    $stmt

);

if(

    mysqli_num_rows($result) !== 1

){

    mysqli_stmt_close(

        $stmt

    );

    rollbackAndExit(

        $conn,

        "../../pages/student/student_login.php"

    );

}

$student = mysqli_fetch_assoc(

    $result

);

mysqli_stmt_close(

    $stmt

);

/* ==========================================================
   CHECK VOTE STATUS
========================================================== */

$currentVoteStatus = trim(

    $student["vote_status"]

);

if(

    strcasecmp(

        $currentVoteStatus,

        "Unvoted"

    ) !== 0

){

    mysqli_commit(

        $conn

    );

    mysqli_close(

        $conn

    );

    unset(

        $_SESSION["selected_candidate_id"],

        $_SESSION["selected_candidate_name"]

    );

    header(

        "Location: ../../pages/student/already_voted.php"

    );

    exit();

}

/* ==========================================================
   PART 3
   CANDIDATE VALIDATION
   NOTA VALIDATION
========================================================== */

/* ==========================================================
   NORMAL CANDIDATE
========================================================== */

if(!$isNOTA){

    $query = "

    SELECT

        id,

        full_name,

        status

    FROM candidates

    WHERE

        id = ?

    LIMIT 1

    FOR UPDATE

    ";

    $stmt = mysqli_prepare(

        $conn,

        $query

    );

    if(!$stmt){

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    $candidateId = (int)$candidateId;

    mysqli_stmt_bind_param(

        $stmt,

        "i",

        $candidateId

    );

    if(

        !mysqli_stmt_execute($stmt)

    ){

        mysqli_stmt_close(

            $stmt

        );

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    $result = mysqli_stmt_get_result(

        $stmt

    );

    if(

        mysqli_num_rows($result) !== 1

    ){

        mysqli_stmt_close(

            $stmt

        );

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    $candidate = mysqli_fetch_assoc(

        $result

    );

    mysqli_stmt_close(

        $stmt

    );

    /* ==============================================
       ACTIVE STATUS CHECK
    ============================================== */

    if(

        strcasecmp(

            trim($candidate["status"]),

            "Active"

        ) !== 0

    ){

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

}

/* ==========================================================
   NOTA VALIDATION
========================================================== */

else{

    $candidateId = null;

}

/* ==========================================================
   PART 4
   INSERT ANONYMOUS VOTE
========================================================== */

/* ==========================================================
   INSERT VOTE
========================================================== */

$query = "

INSERT INTO votes(

    candidate_id

)

VALUES(

    ?

)

";

$stmt = mysqli_prepare(

    $conn,

    $query

);

if(!$stmt){

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

/* ==========================================================
   STORE NULL FOR NOTA
========================================================== */

if($isNOTA){

    $candidateId = null;

}

/* ==========================================================
   BIND PARAMETER
========================================================== */

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $candidateId

);

/* ==========================================================
   EXECUTE INSERT
========================================================== */

if(

    !mysqli_stmt_execute($stmt)

){

    mysqli_stmt_close(

        $stmt

    );

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

/* ==========================================================
   SAVE INSERT ID
========================================================== */

$voteId = mysqli_insert_id(

    $conn

);

mysqli_stmt_close(

    $stmt

);

if(

    $voteId <= 0

){

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

/* ==========================================================
   PART 5
   UPDATE CANDIDATE VOTE COUNT
========================================================== */

/* ==========================================================
   SKIP FOR NOTA
========================================================== */

if(!$isNOTA){

    $query = "

    UPDATE candidates

    SET

        vote_count = vote_count + 1

    WHERE

        id = ?

    LIMIT 1

    ";

    $stmt = mysqli_prepare(

        $conn,

        $query

    );

    if(!$stmt){

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    mysqli_stmt_bind_param(

        $stmt,

        "i",

        $candidateId

    );

    if(

        !mysqli_stmt_execute($stmt)

    ){

        mysqli_stmt_close(

            $stmt

        );

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    /* ==============================================
       VERIFY UPDATE
    ============================================== */

    if(

        mysqli_stmt_affected_rows(

            $stmt

        ) !== 1

    ){

        mysqli_stmt_close(

            $stmt

        );

        rollbackAndExit(

            $conn,

            "../../pages/student/candidate_selection.php"

        );

    }

    mysqli_stmt_close(

        $stmt

    );

}

/* ==========================================================
   PART 6
   UPDATE STUDENT VOTE STATUS
========================================================== */

/* ==========================================================
   UPDATE STATUS
========================================================== */

$query = "

UPDATE students

SET

    vote_status = 'Voted'

WHERE

    id = ?

AND

    vote_status = 'Unvoted'

LIMIT 1

";

$stmt = mysqli_prepare(

    $conn,

    $query

);

if(!$stmt){

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $studentId

);

if(

    !mysqli_stmt_execute($stmt)

){

    mysqli_stmt_close(

        $stmt

    );

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

/* ==========================================================
   VERIFY UPDATE
========================================================== */

if(

    mysqli_stmt_affected_rows(

        $stmt

    ) !== 1

){

    mysqli_stmt_close(

        $stmt

    );

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

mysqli_stmt_close(

    $stmt

);

/* ==========================================================
   PART 7
   COMMIT TRANSACTION
   SESSION CLEANUP
   REDIRECT
========================================================== */

/* ==========================================================
   COMMIT TRANSACTION
========================================================== */

if(

    !mysqli_commit(

        $conn

    )

){

    rollbackAndExit(

        $conn,

        "../../pages/student/candidate_selection.php"

    );

}

/* ==========================================================
   THANK YOU PAGE SESSION
========================================================== */

$_SESSION["vote_submitted"] = true;

/* ==========================================================
   REMOVE TEMPORARY SELECTION SESSION
========================================================== */

unset(

    $_SESSION["selected_candidate_id"],

    $_SESSION["selected_candidate_name"]

);

/* ==========================================================
   CLOSE DATABASE
========================================================== */

mysqli_close(

    $conn

);

/* ==========================================================
   REDIRECT TO THANK YOU PAGE
========================================================== */

header(

    "Location: ../../pages/student/thank_you.php"

);

exit();

?>