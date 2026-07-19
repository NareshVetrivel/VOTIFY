<?php
/* ==========================================================
   VOTIFY
   Export Voters Excel
========================================================== */

session_start();

/* ==========================================================
   SESSION
========================================================== */

if(!isset($_SESSION["admin_id"])){

    exit("Unauthorized");

}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/* ==========================================================
   GET FILTERS
========================================================== */

$search = trim($_GET["search"] ?? "");

$filter = trim($_GET["filter"] ?? "all");

/* ==========================================================
   QUERY
========================================================== */

$query = "

SELECT *

FROM students

WHERE status='Approved'

";

/* ==========================================
   FILTER
========================================== */

if($filter=="voted"){

    $query .= "

    AND vote_status='Voted'

    ";

}

elseif($filter=="unvoted"){

    $query .= "

    AND vote_status='Unvoted'

    ";

}

/* ==========================================
   SEARCH
========================================== */

if($search!=""){

    $search = mysqli_real_escape_string(

        $conn,

        $search

    );

    $query .= "

    AND (

        full_name LIKE '%$search%'

        OR

        admission_no LIKE '%$search%'

        OR

        college_email LIKE '%$search%'

        OR

        department LIKE '%$search%'

        OR

        year LIKE '%$search%'

        OR

        vote_status LIKE '%$search%'

    )

    ";

}

/* ==========================================
   ORDER
========================================== */

$query .= "

ORDER BY full_name ASC

";

$result = mysqli_query($conn,$query);

/* ==========================================================
   NO RECORDS
========================================================== */

if(

!$result ||

mysqli_num_rows($result)==0

){

    exit(

        "No records available for export."

    );

}

/* ==========================================================
   FILE NAME
========================================================== */

switch($filter){

    case "voted":

        $prefix = "VOTIFY_Voted_Students";

    break;

    case "unvoted":

        $prefix = "VOTIFY_Unvoted_Students";

    break;

    default:

        $prefix = "VOTIFY_All_Voters";

}

$fileName =

$prefix .

"_" .

date("Y-m-d_H-i-s") .

".xls";

header(

"Content-Type: application/vnd.ms-excel"

);

header(

"Content-Disposition: attachment; filename=\"$fileName\""

);

header("Pragma: no-cache");

header("Expires: 0");

/* ==========================================================
   TABLE START
========================================================== */

echo "

<table border='1' cellpadding='8' cellspacing='0'>

<tr style='background:#2563EB;color:#FFFFFF;font-weight:bold;'>

<th>Full Name</th>

<th>Admission No</th>

<th>College Email</th>

<th>Department</th>

<th>Year</th>

<th>Vote Status</th>

<th>Registered Date</th>

</tr>

";

/* ==========================================================
   TABLE DATA
========================================================== */

while(

$row = mysqli_fetch_assoc($result)

){

echo "

<tr>

<td>".htmlspecialchars($row["full_name"])."</td>

<td>".htmlspecialchars($row["admission_no"])."</td>

<td>".htmlspecialchars($row["college_email"])."</td>

<td>".htmlspecialchars($row["department"])."</td>

<td>".htmlspecialchars($row["year"])."</td>

<td>".htmlspecialchars($row["vote_status"])."</td>

<td>".date(

"d-m-Y",

strtotime($row["created_at"])

)."</td>

</tr>

";

}

/* ==========================================================
   TABLE END
========================================================== */

echo "</table>";

exit();