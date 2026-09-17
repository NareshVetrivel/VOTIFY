<?php
/* ==========================================================
   VOTIFY
   Export Candidates Excel
   File : backend/admin/export-candidates.php
========================================================== */

session_start();


/* ==========================================================
   SESSION PROTECTION
========================================================== */

if(
    !isset($_SESSION["admin_id"]) ||
    (int)$_SESSION["admin_id"] <= 0
){

    http_response_code(401);

    header(
        "Content-Type: text/plain; charset=UTF-8"
    );

    echo "Unauthorized access.";

    exit();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */


/* ==========================================================
   GET FILTERS
========================================================== */

$search =
    trim(
        $_GET["search"] ?? ""
    );


$filter =
    trim(
        $_GET["filter"] ?? "all"
    );


/*
 * Only allow known filter values.
 *
 * This prevents unexpected values from
 * affecting the query.
 */

$allowedFilters = [

    "all",
    "first",
    "second"

];


if(
    !in_array(
        $filter,
        $allowedFilters,
        true
    )
){

    $filter =
        "all";

}


/* ==========================================================
   BASE QUERY
========================================================== */

$query = "

    SELECT

        id,
        student_id,
        admission_no,
        full_name,
        department,
        year,
        manifesto,
        status,
        vote_count,
        created_at,
        updated_at

    FROM candidates

    WHERE 1=1

";


/* ==========================================================
   QUERY PARAMETERS
========================================================== */

$types = "";

$params = [];


/* ==========================================================
   YEAR FILTER
========================================================== */

if(
    $filter ===
    "first"
){

    $query .= "

        AND (

            year = ?

            OR

            year = ?

        )

    ";

    $types .= "ss";

    $params[] =
        "1st Year";

    $params[] =
        "I Year";

}


elseif(
    $filter ===
    "second"
){

    $query .= "

        AND (

            year = ?

            OR

            year = ?

        )

    ";

    $types .= "ss";

    $params[] =
        "2nd Year";

    $params[] =
        "II Year";

}


/* ==========================================================
   SEARCH FILTER
========================================================== */

if(
    $search !==
    ""
){

    $searchPattern =
        "%" .
        $search .
        "%";


    $query .= "

        AND (

            full_name LIKE ?

            OR

            admission_no LIKE ?

            OR

            department LIKE ?

            OR

            year LIKE ?

            OR

            manifesto LIKE ?

        )

    ";


    $types .= "sssss";


    $params[] =
        $searchPattern;

    $params[] =
        $searchPattern;

    $params[] =
        $searchPattern;

    $params[] =
        $searchPattern;

    $params[] =
        $searchPattern;

}


/* ==========================================================
   ORDER
========================================================== */

$query .= "

    ORDER BY created_at DESC

";


/* ==========================================================
   PREPARE QUERY
========================================================== */

$stmt =
    mysqli_prepare(
        $conn,
        $query
    );


/* ==========================================================
   PREPARE ERROR
========================================================== */

if(
    !$stmt
){

    http_response_code(500);

    header(
        "Content-Type: text/plain; charset=UTF-8"
    );

    echo "Unable to prepare export request.";

    exit();

}


/* ==========================================================
   BIND PARAMETERS
========================================================== */

if(
    $types !==
    ""
){

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );

}


/* ==========================================================
   EXECUTE
========================================================== */

if(
    !mysqli_stmt_execute(
        $stmt
    )
){

    mysqli_stmt_close(
        $stmt
    );


    http_response_code(500);

    header(
        "Content-Type: text/plain; charset=UTF-8"
    );

    echo "Unable to export candidate data.";

    exit();

}


/* ==========================================================
   GET RESULT
========================================================== */

$result =
    mysqli_stmt_get_result(
        $stmt
    );


/* ==========================================================
   RESULT ERROR
========================================================== */

if(
    !$result
){

    mysqli_stmt_close(
        $stmt
    );


    http_response_code(500);

    header(
        "Content-Type: text/plain; charset=UTF-8"
    );

    echo "Unable to read candidate records.";

    exit();

}


/* ==========================================================
   NO RECORDS
========================================================== */

if(
    mysqli_num_rows(
        $result
    ) === 0
){

    /*
     * IMPORTANT:
     *
     * Do NOT redirect.
     *
     * The frontend fetch() receives this response
     * and displays the existing VOTIFY toast.
     */

    http_response_code(200);

    header(
        "Content-Type: text/plain; charset=UTF-8"
    );

    header(
        "Cache-Control: no-store, no-cache, must-revalidate"
    );

    header(
        "Pragma: no-cache"
    );


    echo
        "No candidate records available for export.";


    mysqli_stmt_close(
        $stmt
    );


    exit();

}


/* ==========================================================
   FILE NAME PREFIX
========================================================== */

switch(
    $filter
){

    case "first":

        $prefix =
            "VOTIFY_First_Year_Candidates";

        break;


    case "second":

        $prefix =
            "VOTIFY_Second_Year_Candidates";

        break;


    default:

        $prefix =
            "VOTIFY_All_Candidates";

        break;

}


/* ==========================================================
   FILE NAME
========================================================== */

$fileName =

    $prefix

    . "_"

    . date(
        "Y-m-d_H-i-s"
    )

    . ".xls";


/* ==========================================================
   EXCEL HEADERS
========================================================== */

header(
    "Content-Type: application/vnd.ms-excel; charset=UTF-8"
);


header(
    'Content-Disposition: attachment; filename="' .
    $fileName .
    '"'
);


header(
    "Cache-Control: no-store, no-cache, must-revalidate"
);


header(
    "Pragma: no-cache"
);


header(
    "Expires: 0"
);


/* ==========================================================
   UTF-8 BOM
========================================================== */

echo "\xEF\xBB\xBF";


/* ==========================================================
   EXCEL TABLE START
========================================================== */

echo "

<table
    border='1'
    cellpadding='8'
    cellspacing='0'
>

<tr
    style='
        background:#2563EB;
        color:#FFFFFF;
        font-weight:bold;
    '
>

<th>S.No</th>

<th>Admission No</th>

<th>Candidate Name</th>

<th>Department</th>

<th>Year</th>

<th>Manifesto</th>

<th>Added Date</th>

</tr>

";


/* ==========================================================
   TABLE DATA
========================================================== */

$serial =
    1;


while(
    $row =
    mysqli_fetch_assoc(
        $result
    )
){

    $admissionNo =
        htmlspecialchars(
            $row["admission_no"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        );


    $fullName =
        htmlspecialchars(
            $row["full_name"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        );


    $department =
        htmlspecialchars(
            $row["department"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        );


    $year =
        htmlspecialchars(
            $row["year"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        );


    $manifesto =
        $row["manifesto"] ?? "";


    /*
     * Keep exported manifesto readable.
     */

    if(
        mb_strlen(
            $manifesto,
            "UTF-8"
        ) > 120
    ){

        $manifesto =
            mb_substr(
                $manifesto,
                0,
                120,
                "UTF-8"
            )
            . "...";

    }


    $manifesto =
        htmlspecialchars(
            $manifesto,
            ENT_QUOTES,
            "UTF-8"
        );


    /* ======================================================
       ADDED DATE
    ====================================================== */

    $createdAt =
        $row["created_at"] ?? "";


    $addedDate =
        "";


    if(
        !empty(
            $createdAt
        )
    ){

        $timestamp =
            strtotime(
                $createdAt
            );


        if(
            $timestamp !==
            false
        ){

            $addedDate =
                date(
                    "d-m-Y",
                    $timestamp
                );

        }

    }


    /* ======================================================
       ROW
    ====================================================== */

    echo "

    <tr>

        <td>"
        . $serial++
        . "</td>

        <td>"
        . $admissionNo
        . "</td>

        <td>"
        . $fullName
        . "</td>

        <td>"
        . $department
        . "</td>

        <td>"
        . $year
        . "</td>

        <td>"
        . $manifesto
        . "</td>

        <td>"
        . $addedDate
        . "</td>

    </tr>

    ";

}


/* ==========================================================
   TABLE END
========================================================== */

echo "

</table>

";


/* ==========================================================
   CLOSE STATEMENT
========================================================== */

mysqli_stmt_close(
    $stmt
);


/* ==========================================================
   END
========================================================== */

exit();

?>