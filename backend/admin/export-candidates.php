<?php
/* ==========================================================
   VOTIFY
   Export Candidates Excel
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

FROM candidates

WHERE 1=1

";

/* ==========================================================
   FILTER
========================================================== */

if($filter=="first"){

    $query .= "

    AND (

        year='1st Year'

        OR

        year='I Year'

    )

    ";

}

elseif($filter=="second"){

    $query .= "

    AND (

        year='2nd Year'

        OR

        year='II Year'

    )

    ";

}

/* ==========================================================
   SEARCH
========================================================== */

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

        department LIKE '%$search%'

        OR

        year LIKE '%$search%'

        OR

        manifesto LIKE '%$search%'

    )

    ";

}

/* ==========================================================
   ORDER
========================================================== */

$query .= "

ORDER BY created_at DESC

";

/* ==========================================================
   EXECUTE QUERY
========================================================== */

$result = mysqli_query(

    $conn,

    $query

);

/* ==========================================================
   DATABASE ERROR
========================================================== */

if(!$result){

    exit(

        "Database Error : "

        .

        mysqli_error($conn)

    );

}

/* ==========================================================
   NO RECORDS
========================================================== */

if(

mysqli_num_rows($result)==0

){

    exit(

        "No candidate records available for export."

    );

}

/* ==========================================================
   FILE NAME
========================================================== */

switch($filter){

    case "first":

        $prefix = "VOTIFY_First_Year_Candidates";

    break;

    case "second":

        $prefix = "VOTIFY_Second_Year_Candidates";

    break;

    default:

        $prefix = "VOTIFY_All_Candidates";

}

$fileName =

$prefix

.

"_"

.

date("Y-m-d_H-i-s")

.

".xls";

/* ==========================================================
   EXCEL HEADERS
========================================================== */

header(

"Content-Type: application/vnd.ms-excel; charset=UTF-8"

);

header(

"Content-Disposition: attachment; filename=\"$fileName\""

);

header("Pragma: no-cache");

header("Expires: 0");

/* ==========================================================
   UTF-8 BOM
========================================================== */

echo "\xEF\xBB\xBF";

/* ==========================================================
   TABLE START
========================================================== */

echo "

<table border='1' cellpadding='8' cellspacing='0'>

<tr style='background:#2563EB;color:#FFFFFF;font-weight:bold;'>

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

$serial = 1;

while(

$row = mysqli_fetch_assoc($result)

){

echo "

<tr>

<td>".$serial++."</td>

<td>".htmlspecialchars($row["admission_no"])."</td>

<td>".htmlspecialchars($row["full_name"])."</td>

<td>".htmlspecialchars($row["department"])."</td>

<td>".htmlspecialchars($row["year"])."</td>

<td>".htmlspecialchars(

mb_strimwidth(

$row["manifesto"],

0,

120,

"..."

)

)."</td>

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

?>