<?php
/* ==========================================================
   VOTIFY
   Canvassing Reports
   File : pages/admin/canvassing.php
========================================================== */


/* ==========================================================
   SESSION
========================================================== */

session_start();


/* ==========================================================
   SESSION PROTECTION
========================================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.html");

    exit();

}


/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";


/* ==========================================================
   PAGE TITLE
========================================================== */

$pageTitle = "Canvassing Reports";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>


<!-- ======================================================
     TITLE
====================================================== -->

<title>

Canvassing Reports | VOTIFY

</title>


<!-- ======================================================
     TAILWIND
====================================================== -->

<script src="https://cdn.tailwindcss.com"></script>


<!-- ======================================================
     REMIX ICONS
====================================================== -->

<link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet"
>


<!-- ======================================================
     VOTIFY CUSTOM CSS
====================================================== -->

<link
    rel="stylesheet"
    href="../../assets/css/custom.css"
>

<link
    rel="stylesheet"
    href="../../assets/css/animations.css"
>

</head>


<body

class="

bg-[#0B1020]

text-white

min-h-screen

overflow-x-hidden

flex

flex-col"

>


<!-- =====================================================
     LOADER
===================================================== -->

<div id="loader-container"></div>


<!-- =====================================================
     BACKGROUND
===================================================== -->

<div class="fixed inset-0 -z-10 overflow-hidden">

    <!-- Blue Glow -->

    <div
        class="
        absolute
        top-0
        left-0
        w-96
        h-96
        bg-blue-600/20
        blur-[150px]
        rounded-full
        ">
    </div>


    <!-- Pink Glow -->

    <div
        class="
        absolute
        bottom-0
        right-0
        w-96
        h-96
        bg-pink-600/20
        blur-[150px]
        rounded-full
        ">
    </div>


    <!-- Purple Glow -->

    <div
        class="
        absolute
        top-1/2
        left-1/2
        w-80
        h-80
        bg-purple-600/20
        blur-[130px]
        rounded-full
        -translate-x-1/2
        -translate-y-1/2
        ">
    </div>

</div>


<!-- =====================================================
     HEADER
===================================================== -->

<div id="header"></div>


<!-- =====================================================
     MOBILE SIDEBAR OVERLAY
===================================================== -->

<div

id="sidebarOverlay"

class="

fixed

inset-0

bg-black/60

hidden

z-40

lg:hidden"

>

</div>


<!-- =====================================================
     MAIN
===================================================== -->

<main

class="

flex-1

max-w-7xl

w-full

mx-auto

px-4

sm:px-6

lg:px-8

py-8"

>


<div

class="

grid

grid-cols-1

lg:grid-cols-[280px_1fr]

gap-8

items-start"

>


<!-- =====================================================
     ADMIN SIDEBAR
===================================================== -->

<?php

include "../../components/admin_sidebar.php";

?>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<section class="min-w-0">


<!-- =====================================================
     ADMIN TOPBAR
===================================================== -->

<?php

$pageTitle = "Canvassing Reports";

include "../../components/admin_topbar.php";

?>


<!-- =====================================================
     CANVASSING CONTENT
===================================================== -->

<div

id="canvassingContent"

class="space-y-8"

>

<!-- =====================================================
     PART 2 WILL COME HERE
     STATISTICS CARDS
===================================================== -->

<!-- =====================================================
     CANVASSING STATISTICS
===================================================== -->

<section
    id="canvassingStatistics"
    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
>

<?php

/* ==========================================================
   TOTAL CANDIDATES
========================================================== */

$totalCandidates = 0;

$candidateCountQuery = "

    SELECT COUNT(*) AS total_candidates

    FROM candidates

    WHERE status = 'Active'

";

$candidateCountResult = mysqli_query(
    $conn,
    $candidateCountQuery
);

if ($candidateCountResult) {

    $candidateCountRow =
        mysqli_fetch_assoc($candidateCountResult);

    $totalCandidates =
        (int) $candidateCountRow["total_candidates"];

}


/* ==========================================================
   TOTAL VOTES CAST
========================================================== */

$totalVotes = 0;

$totalVotesQuery = "

    SELECT COALESCE(SUM(vote_count), 0) AS total_votes

    FROM candidates

    WHERE status = 'Active'

";

$totalVotesResult = mysqli_query(
    $conn,
    $totalVotesQuery
);

if ($totalVotesResult) {

    $totalVotesRow =
        mysqli_fetch_assoc($totalVotesResult);

    $totalVotes =
        (int) $totalVotesRow["total_votes"];

}


/* ==========================================================
   LEADING CANDIDATE
========================================================== */

$leadingCandidate = null;

$leadingCandidateQuery = "

    SELECT

        id,
        full_name,
        vote_count

    FROM candidates

    WHERE status = 'Active'

    ORDER BY

        vote_count DESC,

        id ASC

    LIMIT 1

";

$leadingCandidateResult = mysqli_query(
    $conn,
    $leadingCandidateQuery
);

if ($leadingCandidateResult) {

    $leadingCandidate =
        mysqli_fetch_assoc(
            $leadingCandidateResult
        );

}


/* ==========================================================
   LEADING CANDIDATE VALUES
========================================================== */

$leadingName = "—";

$leadingVotes = 0;

if ($leadingCandidate) {

    $leadingName =
        $leadingCandidate["full_name"];

    $leadingVotes =
        (int) $leadingCandidate["vote_count"];

}

?>


<!-- ======================================================
     TOTAL CANDIDATES
====================================================== -->

<div

class="
glass
rounded-3xl
p-7
border
border-white/10
hover:border-blue-500/40
hover:shadow-[0_0_35px_rgba(59,130,246,0.20)]
hover:-translate-y-1
transition-all
duration-300
group"

>

    <div

    class="

    flex

    items-center

    justify-between"

    >

        <!-- Content -->

        <div>

            <p

            class="

            text-slate-400

            text-base

            font-medium"

            >

                Total Candidates

            </p>


            <h3

            class="

            text-4xl

            sm:text-5xl

            font-bold

            mt-3"

            >

                <?php

                echo number_format(
                    $totalCandidates
                );

                ?>

            </h3>


            <p

            class="

            text-slate-500

            text-sm

            mt-2"

            >

                Registered candidates

            </p>

        </div>


        <!-- Icon -->

        <div

        class="

        w-16

        h-16

        rounded-2xl

        bg-blue-500/10

        border

        border-blue-500/20

        flex

        items-center

        justify-center

        group-hover:scale-105

        transition-transform"

        >

            <i

            class="

            ri-award-line

            text-3xl

            text-blue-400"

            >

            </i>

        </div>

    </div>

</div>


<!-- ======================================================
     TOTAL VOTES CAST
====================================================== -->

<div

class="
glass
rounded-3xl
p-7
border
border-white/10
hover:border-blue-500/40
hover:shadow-[0_0_35px_rgba(59,130,246,0.20)]
hover:-translate-y-1
transition-all
duration-300
group"

>

    <div

    class="

    flex

    items-center

    justify-between"

    >

        <!-- Content -->

        <div>

            <p

            class="

            text-slate-400

            text-base

            font-medium"

            >

                Total Votes Cast

            </p>


            <h3

            class="

            text-4xl

            sm:text-5xl

            font-bold

            mt-3"

            >

                <?php

                echo number_format(
                    $totalVotes
                );

                ?>

            </h3>


            <p

            class="

            text-slate-500

            text-sm

            mt-2"

            >

                Votes recorded

            </p>

        </div>


        <!-- Icon -->

        <div

        class="

        w-16

        h-16

        rounded-2xl

        bg-green-500/10

        border

        border-green-500/20

        flex

        items-center

        justify-center

        group-hover:scale-105

        transition-transform"

        >

            <i

            class="

            ri-checkbox-circle-line

            text-3xl

            text-green-400"

            >

            </i>

        </div>

    </div>

</div>


<!-- ======================================================
     LEADING CANDIDATE
====================================================== -->

<div

class="
glass
rounded-3xl
p-7
border
border-white/10
hover:border-blue-500/40
hover:shadow-[0_0_35px_rgba(59,130,246,0.20)]
hover:-translate-y-1
transition-all
duration-300
group"

>

    <div

    class="

    flex

    items-center

    justify-between

    gap-4"

    >

        <!-- Content -->

        <div

        class="

        min-w-0

        flex-1"

        >

            <p

            class="

            text-slate-400

            text-base

            font-medium"

            >

                Leading Candidate

            </p>


            <h3

            class="

            text-2xl

            sm:text-3xl

            font-bold

            mt-3

            truncate"

            title="<?php echo htmlspecialchars($leadingName); ?>"

            >

                <?php

                echo htmlspecialchars(
                    $leadingName
                );

                ?>

            </h3>


            <p

            class="

            text-purple-400

            text-sm

            font-medium

            mt-2"

            >

                <?php

                echo number_format(
                    $leadingVotes
                );

                ?>

                votes

            </p>

        </div>


        <!-- Icon -->

        <div

        class="

        flex-shrink-0

        w-16

        h-16

        rounded-2xl

        bg-purple-500/10

        border

        border-purple-500/20

        flex

        items-center

        justify-center

        group-hover:scale-105

        transition-transform"

        >

            <i

            class="

            ri-trophy-line

            text-3xl

            text-purple-400"

            >

            </i>

        </div>

    </div>

</div>


</section>


<!-- =====================================================
     PART 3 WILL COME HERE
     REPORT TABLE
===================================================== -->

<!-- =====================================================
     CANDIDATE VOTE REPORT
===================================================== -->

<section
    id="canvassingReport"
    class="space-y-6"
>


<?php

/* ==========================================================
   FETCH CANDIDATE REPORT
========================================================== */

$reportCandidates = [];

$reportQuery = "

    SELECT

        id,
        full_name,
        year,
        photo,
        vote_count

    FROM candidates

    WHERE status = 'Active'

    ORDER BY

        vote_count DESC,

        id ASC

";

$reportResult = mysqli_query(
    $conn,
    $reportQuery
);


/* ==========================================================
   STORE REPORT DATA
========================================================== */

if ($reportResult) {

    while ($candidate = mysqli_fetch_assoc($reportResult)) {

        $reportCandidates[] = $candidate;

    }

}


/* ==========================================================
   REPORT COUNT
========================================================== */

$reportCount = count($reportCandidates);

?>


<!-- ======================================================
     REPORT HEADER
====================================================== -->

<div

class="

glass

rounded-3xl

p-8

border

border-white/10

transition-all

duration-300

hover:border-blue-500/30

hover:shadow-[0_0_35px_rgba(59,130,246,0.12)]"

>


<!-- ======================================================
     REPORT HEADER + CONTROLS
====================================================== -->

<div

class="

grid

grid-cols-1

xl:grid-cols-[1fr_auto]

gap-6

items-start"

>


<!-- ==================================================
     LEFT SIDE
     TITLE + DESCRIPTION + YEAR FILTERS
================================================== -->

<div

class="

min-w-0"

>


<!-- =================================================
     TITLE
================================================= -->

<div>

    <h3

    class="

    text-2xl

    sm:text-3xl

    font-bold"

    >

        Candidate Vote Report

    </h3>


    <p

    class="

    text-slate-400

    mt-2

    text-sm

    sm:text-base"

    >

        Candidate-wise voting performance

    </p>

</div>


<!-- =================================================
     YEAR FILTERS
================================================= -->

<div

class="

flex

flex-wrap

items-center

gap-3

mt-6"

>


<!-- ==============================================
     ALL
============================================== -->

<button

type="button"

class="

canvassing-year-btn

active

px-6

py-3

rounded-xl

font-semibold

text-white

bg-gradient-to-r

from-blue-500

via-purple-500

to-pink-500

shadow-lg

shadow-purple-500/20

transition-all

duration-300

hover:scale-[1.02]"

data-year="all"

>

All

</button>


<!-- ==============================================
     I YEAR
============================================== -->

<button

type="button"

class="

canvassing-year-btn

px-6

py-3

rounded-xl

font-semibold

text-slate-300

bg-white/5

border

border-white/10

hover:bg-white/10

hover:border-blue-500/30

hover:scale-[1.02]

transition-all

duration-300"

data-year="I Year"

>

I Year

</button>


<!-- ==============================================
     II YEAR
============================================== -->

<button

type="button"

class="

canvassing-year-btn

px-6

py-3

rounded-xl

font-semibold

text-slate-300

bg-white/5

border

border-white/10

hover:bg-white/10

hover:border-blue-500/30

hover:scale-[1.02]

transition-all

duration-300"

data-year="II Year"

>

II Year

</button>


</div>


</div>


<!-- ==================================================
     RIGHT SIDE
     EXPORT + SEARCH
================================================== -->

<div

class="

w-full

xl:w-80

flex

flex-col

items-stretch

gap-10"

>


<!-- =================================================
     EXPORT BUTTON
================================================= -->

<a

href="certificate-preview.php"

id="exportCanvassingReport"

class="

w-full

inline-flex

items-center

justify-center

gap-3

px-6

py-3.5

rounded-2xl

font-semibold

text-white

bg-gradient-to-r

from-blue-500

via-purple-500

to-pink-500

hover:brightness-110

transition-all

duration-300

shadow-lg

shadow-purple-500/20

hover:shadow-[0_0_30px_rgba(139,92,246,0.45)]

no-underline"

>


<!-- =================================================
     CERTIFICATE / EXPORT ICON
================================================= -->

<svg

xmlns="http://www.w3.org/2000/svg"

viewBox="0 0 24 24"

fill="none"

stroke="currentColor"

stroke-width="2"

class="w-5 h-5"

aria-hidden="true"

>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"

/>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M14 2v6h6"

/>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M8 13h8"

/>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M8 17h5"

/>

</svg>


<span>

Export Report

</span>


</a>


<!-- =================================================
     SEARCH
================================================= -->

<div

class="

relative

w-full"

>


<!-- SVG SEARCH ICON -->

<svg

xmlns="http://www.w3.org/2000/svg"

viewBox="0 0 24 24"

fill="none"

stroke="currentColor"

stroke-width="2"

class="

absolute

left-4

top-1/2

-translate-y-1/2

w-5

h-5

text-slate-500

pointer-events-none"

aria-hidden="true"

>

<circle

cx="11"

cy="11"

r="7"

/>

<path

stroke-linecap="round"

d="m20 20-4-4"

/>

</svg>


<input

type="text"

id="canvassingSearch"

placeholder="Search candidates..."

class="

w-full

pl-12

pr-4

py-3.5

rounded-2xl

bg-white/5

border

border-white/10

text-white

placeholder-slate-500

outline-none

focus:border-blue-500/50

focus:ring-2

focus:ring-blue-500/10

transition-all"

>

</div>


</div>


</div>


<!-- ======================================================
     TABLE SPACING
====================================================== -->

<div class="mt-8">


<!-- ======================================================
     TABLE
====================================================== -->

<div

class="

overflow-x-auto

rounded-2xl

border

border-white/10"

>


<table

id="canvassingTable"

class="

w-full

min-w-[900px]

text-left"

>


<!-- =================================================
     TABLE HEADER
================================================= -->

<thead

class="

bg-white/5

border-b

border-white/10"

>

<tr>

<!-- =================================================
     POSITION
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider

text-center"

>

Position

</th>


<!-- =================================================
     PHOTO
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider

text-center"

>

Photo

</th>


<!-- =================================================
     CANDIDATE
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider"

>

Candidate

</th>


<!-- =================================================
     YEAR
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider"

>

Year

</th>


<!-- =================================================
     VOTES
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider

text-center"

>

Votes

</th>


<!-- =================================================
     PERCENTAGE
================================================= -->

<th

class="

px-6

py-5

text-xs

font-semibold

text-slate-400

uppercase

tracking-wider

text-center"

>

Percentage

</th>

</tr>

</thead>

<!-- =================================================
     TABLE BODY
================================================= -->

<tbody

id="canvassingTableBody"

class="divide-y divide-white/5"

>


<?php

/* ==========================================================
   POSITION TRACKING
========================================================== */

$position = 0;

$previousVotes = null;

$previousPosition = 0;


if ($reportCount > 0):


foreach ($reportCandidates as $candidate):


    $currentVotes =
        (int) $candidate["vote_count"];


    /*
       Competition ranking:

       1
       2
       2
       4

       Same vote count = same position.
    */

    if (
        $previousVotes === null ||
        $currentVotes !== $previousVotes
    ) {

        $position++;

        $previousPosition = $position;

    }


    /*
       Percentage
    */

    $percentage = 0;

    if ($totalVotes > 0) {

        $percentage =
            ($currentVotes / $totalVotes) * 100;

    }


    /*
       Leading candidate
    */

    $isLeading =
        ($previousPosition === 1);


?>


<tr

class="

canvassing-row

border-b

border-white/5

hover:bg-white/5

transition"

data-name="<?php

echo strtolower(

    htmlspecialchars(

        $candidate["full_name"]

    )

);

?>"

data-year="<?php

echo htmlspecialchars(

    $candidate["year"]

);

?>"

>


<!-- =================================================
     POSITION
================================================= -->

<td

class="

px-6

py-5

text-center"

>

<?php

if ($previousPosition === 1):

?>

<span

class="

inline-flex

items-center

justify-center

w-14

h-14

rounded-2xl

bg-yellow-500/10

border

border-yellow-400/30

text-3xl

shadow-[0_0_22px_rgba(250,204,21,0.35)]

hover:shadow-[0_0_30px_rgba(250,204,21,0.50)]

hover:scale-105

transition-all

duration-300"

title="Gold Medal"

>

🥇

</span>

<?php

elseif ($previousPosition === 2):

?>

<span

class="

inline-flex

items-center

justify-center

w-14

h-14

rounded-2xl

bg-slate-400/10

border

border-slate-300/30

text-3xl

shadow-[0_0_22px_rgba(203,213,225,0.25)]

hover:shadow-[0_0_30px_rgba(203,213,225,0.40)]

hover:scale-105

transition-all

duration-300"

title="Silver Medal"

>

🥈

</span>

<?php

elseif ($previousPosition === 3):

?>

<span

class="

inline-flex

items-center

justify-center

w-14

h-14

rounded-2xl

bg-orange-500/10

border

border-orange-400/30

text-3xl

shadow-[0_0_22px_rgba(249,115,22,0.30)]

hover:shadow-[0_0_30px_rgba(249,115,22,0.45)]

hover:scale-105

transition-all

duration-300"

title="Bronze Medal"

>

🥉

</span>

<?php

else:

?>

<span

class="

text-slate-400

font-bold

text-lg"

>

#<?php

echo $previousPosition;

?>

</span>

<?php

endif;

?>

</td>


<!-- =================================================
     PHOTO
================================================= -->

<td

class="

px-6

py-5

text-center"

>

<img

src="../../uploads/candidates/<?php

echo htmlspecialchars(

    $candidate["photo"]

);

?>"

alt="<?php

echo htmlspecialchars(

    $candidate["full_name"]

);

?>"

class="

w-14

h-14

rounded-xl

object-cover

mx-auto

border

border-white/10

"

>

</td>


<!-- =================================================
     CANDIDATE NAME
================================================= -->

<td

class="

px-6

py-5"

>

<p

class="

font-semibold

text-white

text-base

sm:text-lg"

>

<?php

echo htmlspecialchars(

    $candidate["full_name"]

);

?>

</p>

</td>


<!-- =================================================
     YEAR
================================================= -->

<td

class="

px-6

py-5"

>

<span

class="

inline-flex

px-3

py-1.5

rounded-lg

bg-blue-500/10

border

border-blue-500/20

text-blue-300

text-sm

font-medium"

>

<?php

echo htmlspecialchars(

    $candidate["year"]

);

?>

</span>

</td>


<!-- =================================================
     VOTES
================================================= -->

<td

class="

px-6

py-5

text-center"

>

<span

class="

text-lg

font-bold

text-white"

>

<?php

echo number_format(

    $currentVotes

);

?>

</span>

</td>


<!-- =================================================
     PERCENTAGE
================================================= -->

<td

class="

px-6

py-5

text-center"

>

<div

class="

inline-flex

items-center

gap-2"

>

<!-- Progress -->

<div

class="

w-16

h-2

rounded-full

bg-white/10

overflow-hidden"

>

<div

class="

h-full

rounded-full

bg-gradient-to-r

from-blue-500

to-purple-500"

style="width: <?php

echo min(

    100,

    round(

        $percentage,

        2

    )

);

?>%;"

>

</div>

</div>


<!-- Percentage -->

<span

class="

text-sm

font-semibold

text-slate-300

whitespace-nowrap"

>

<?php

echo number_format(

    $percentage,

    2

);

?>%

</span>

</div>

</td>


</tr>


<?php


$previousVotes =
    $currentVotes;


endforeach;


else:


?>


<!-- =================================================
     EMPTY STATE
================================================= -->

<tr>

<td

colspan="6"

class="

px-6

py-16

text-center"

>

<div

class="

flex

flex-col

items-center

justify-center"

>

<div

class="

w-16

h-16

rounded-2xl

bg-white/5

border

border-white/10

flex

items-center

justify-center"

>

<svg

xmlns="http://www.w3.org/2000/svg"

viewBox="0 0 24 24"

fill="none"

stroke="currentColor"

stroke-width="1.8"

class="

w-8

h-8

text-slate-500"

aria-hidden="true"

>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M4 4h16v16H4z"

/>

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M4 9h4l2 3h4l2-3h4"

/>

</svg>

</i>

</div>


<h4

class="

text-lg

font-semibold

text-slate-300

mt-4"

>

No Candidates Found

</h4>


<p

class="

text-slate-500

text-sm

mt-1"

>

There are currently no active candidates
in this election.

</p>

</div>

</td>

</tr>


<?php

endif;

?>


<!-- =================================================
     NO SEARCH RESULTS
================================================= -->

<tr

id="canvassingNoResults"

class="hidden"

>

<td

colspan="6"

class="

px-6

py-14

text-center"

>

<div

class="

flex

flex-col

items-center"

>

<svg

xmlns="http://www.w3.org/2000/svg"

viewBox="0 0 24 24"

fill="none"

stroke="currentColor"

stroke-width="1.8"

class="

w-10

h-10

text-slate-500"

aria-hidden="true"

>

<circle

cx="11"

cy="11"

r="7"

/>

<path

stroke-linecap="round"

d="m20 20-4-4"

/>

<path

stroke-linecap="round"

d="M8.5 11h5"

/>

</svg>


<p

class="

text-slate-300

font-semibold

mt-3"

>

No matching candidates

</p>


<p

class="

text-slate-500

text-sm

mt-1"

>

Try changing your search or year filter.

</p>

</div>

</td>

</tr>


</tbody>

</table>

</div>

</div>


</section>


</div>


</section>


</div>


</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<div id="footer"></div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/canvassing.js"></script>

</body>

</html>