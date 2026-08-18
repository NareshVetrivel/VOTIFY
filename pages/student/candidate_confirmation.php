<?php
/* ==========================================================
   VOTIFY
   Candidate Confirmation
   File : pages/student/candidate_confirmation.php
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

        "Location: student_login.php"

    );

    exit();

}

/* ==========================================================
   DATABASE
========================================================== */

require_once "../../config/database.php";

/** @var mysqli $conn */

/* ==========================================================
   VALIDATE SESSION DATA
========================================================== */

if(

    !isset($_SESSION["selected_candidate_id"]) ||

    !isset($_SESSION["selected_candidate_name"])

){

    header(

        "Location: candidate_selection.php"

    );

    exit();

}

/* ==========================================================
   VARIABLES
========================================================== */

$candidateId = $_SESSION["selected_candidate_id"];

$candidateName = $_SESSION["selected_candidate_name"];

$isNOTA = (

    strtoupper($candidateId) === "NOTA"

);

/* ==========================================================
   LOAD CANDIDATE
========================================================== */

$candidate = null;

if(!$isNOTA){

    $query = "

SELECT

    id,

    full_name,

    department,

    year,

    manifesto

FROM candidates

        WHERE

            id = ?

        AND

            status='Active'

        LIMIT 1

    ";

    $statement = mysqli_prepare(

        $conn,

        $query

    );

    mysqli_stmt_bind_param(

        $statement,

        "i",

        $candidateId

    );

    mysqli_stmt_execute(

        $statement

    );

    $result = mysqli_stmt_get_result(

        $statement

    );

    if(

        mysqli_num_rows($result) !== 1

    ){

        header(

            "Location: candidate_selection.php"

        );

        exit();

    }

    $candidate = mysqli_fetch_assoc(

        $result

    );

}

/* ==========================================================
   NOTA DATA
========================================================== */

else{

    $candidate = [

        "id"=>"NOTA",

        "full_name"=>"NOTA",

        "department"=>"None Of The Above",

        "year"=>"Reject All Candidates",

        "manifesto"=>

        "You have chosen NOTA (None Of The Above). This means you do not wish to vote for any available candidate. Your vote will still be recorded as a valid vote.",

        "photo"=>""

    ];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Candidate Confirmation | VOTIFY

</title>

<!-- ==========================================================
TAILWIND
========================================================== -->

<script src="https://cdn.tailwindcss.com"></script>

<!-- ==========================================================
REMIX ICON
========================================================== -->

<link

href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"

rel="stylesheet">

<!-- ==========================================================
CUSTOM CSS
========================================================== -->

<link

rel="stylesheet"

href="../../assets/css/custom.css">

<link

rel="stylesheet"

href="../../assets/css/animations.css">

</head>

<body

id="candidateConfirmationPage"

class="bg-[#0B1020] text-white min-h-screen overflow-x-hidden">

<!-- ==========================================================
LOADER
========================================================== -->

<div id="loader-container"></div>

<!-- ==========================================================
BACKGROUND
========================================================== -->

<div
class="fixed inset-0 -z-10 overflow-hidden">

<div
class="absolute
top-0
left-0
w-96
h-96
bg-blue-600/20
blur-[150px]
rounded-full">
</div>

<div
class="absolute
bottom-0
right-0
w-96
h-96
bg-pink-600/20
blur-[150px]
rounded-full">
</div>

<div
class="absolute
top-1/2
left-1/2
-translate-x-1/2
-translate-y-1/2
w-80
h-80
bg-purple-600/20
blur-[130px]
rounded-full">
</div>

</div>

<!-- ==========================================================
HEADER
========================================================== -->

<div id="header"></div>

<!-- ==========================================================
MAIN START
========================================================== -->

<main
class="px-6 py-10">

<div
class="max-w-7xl mx-auto">

<!-- ==========================================================
PAGE TITLE
========================================================== -->

<div
class="glass
rounded-3xl
p-8
mb-10">

<h1
class="text-4xl
font-bold">

Confirm Your Vote

</h1>

<p
class="mt-3
text-slate-400">

Please verify your selected candidate carefully before submitting your final vote. Once confirmed, your vote cannot be changed.

</p>

</div>

<!-- ==========================================================
PART 2 STARTS HERE
Candidate Preview Card
========================================================== -->

<!-- ==========================================================
CONFIRMATION CARD
========================================================== -->

<div
class="glass
rounded-3xl
overflow-hidden
max-w-5xl
mx-auto">

<div
class="grid
grid-cols-1
lg:grid-cols-2">

    <!-- ======================================================
    LEFT SIDE
    ======================================================= -->

    <div
    class="relative
    min-h-[420px]
    bg-[#131C33]">

        <?php if(!$isNOTA){ ?>

<img

src="../../backend/candidate-photo.php?id=<?php

echo (int) $candidate["id"];

?>"

alt="<?php

echo htmlspecialchars(

    $candidate["full_name"],

    ENT_QUOTES,

    "UTF-8"

);

?>"

class="w-full
h-full
object-cover"

loading="lazy">

            <div
            class="absolute
            inset-x-0
            bottom-0
            h-40
            bg-gradient-to-t
            from-[#111827]
            via-[#111827]/70
            to-transparent">
            </div>

        <?php }else{ ?>

            <div
            class="h-full
            flex
            items-center
            justify-center
            bg-gradient-to-br
            from-red-500/15
            to-pink-500/15">

                <!-- NOTA SVG -->

                <svg
                width="170"
                height="170"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg">

                    <rect
                    x="8"
                    y="3"
                    width="8"
                    height="18"
                    rx="1.5"
                    stroke="#FF6B6B"
                    stroke-width="1.8"/>

                    <line
                    x1="10"
                    y1="7"
                    x2="14"
                    y2="7"
                    stroke="#FF6B6B"
                    stroke-width="1.6"
                    stroke-linecap="round"/>

                    <line
                    x1="10"
                    y1="11"
                    x2="14"
                    y2="11"
                    stroke="#FF6B6B"
                    stroke-width="1.6"
                    stroke-linecap="round"/>

                    <line
                    x1="10"
                    y1="15"
                    x2="14"
                    y2="15"
                    stroke="#FF6B6B"
                    stroke-width="1.6"
                    stroke-linecap="round"/>

                    <line
                    x1="5"
                    y1="5"
                    x2="19"
                    y2="19"
                    stroke="#FF6B6B"
                    stroke-width="2.2"
                    stroke-linecap="round"/>

                    <line
                    x1="19"
                    y1="5"
                    x2="5"
                    y2="19"
                    stroke="#FF6B6B"
                    stroke-width="2.2"
                    stroke-linecap="round"/>

                </svg>

            </div>

        <?php } ?>

    </div>

    <!-- ======================================================
    RIGHT SIDE
    ======================================================= -->

    <div
    class="flex
    flex-col
    p-10">

        <div>

            <span
            class="inline-flex
            items-center
            px-4
            py-2
            rounded-full
            text-sm
            font-semibold
            bg-blue-500/20
            text-blue-300">

                Selected Candidate

            </span>

            <h2
            class="text-4xl
            font-bold
            mt-6">

                <?php

                echo htmlspecialchars(

                    $candidate["full_name"]

                );

                ?>

            </h2>

            <p
            class="text-slate-400
            mt-3
            text-lg">

                <?php

                echo htmlspecialchars(

                    $candidate["department"]

                );

                ?>

            </p>

            <p
            class="text-blue-400
            font-semibold
            mt-2">

                <?php

                echo htmlspecialchars(

                    $candidate["year"]

                );

                ?>

            </p>

        </div>

        <!-- ==================================================
        MANIFESTO
        =================================================== -->

        <div
        class="mt-10">

            <h3
            class="text-2xl
            font-bold
            text-blue-400">

                <?php

                echo $isNOTA

                ?

                "About NOTA"

                :

                "Manifesto";

                ?>

            </h3>

            <p
            class="mt-5
            text-slate-300
            leading-9">

                <?php

                echo nl2br(

                    htmlspecialchars(

                        $candidate["manifesto"]

                    )

                );

                ?>

            </p>

        </div>

        <!-- ==================================================
        WARNING
        =================================================== -->

        <div
        class="mt-10
        rounded-2xl
        border
        border-yellow-500/25
        bg-yellow-500/10
        p-5">

            <div
            class="flex
            gap-4">

                <i
                class="ri-alert-line
                text-yellow-400
                text-2xl">
                </i>

                <div>

                    <h4
                    class="font-semibold">

                        Final Confirmation

                    </h4>

                    <p
                    class="text-sm
                    text-slate-300
                    mt-2">

                        Once your vote is confirmed, it cannot be modified, cancelled or cast again.

                    </p>

                </div>

            </div>

        </div>

        <!-- ==================================================
        BUTTONS
        =================================================== -->

        <div
        class="mt-auto
        pt-10
        flex
        flex-col
        md:flex-row
        gap-5">

            <button

            id="backButton"

            type="button"

            class="btn-outline
            flex-1">

                <i
                class="ri-arrow-left-line">
                </i>

                Back

            </button>

            <button

            id="confirmVoteButton"

            type="button"

            class="btn-primary
            flex-1">

                <i
                class="ri-shield-check-line">
                </i>

                Confirm Vote

            </button>

        </div>

    </div>

</div>

</div>

<?php

$selectedCandidate = $candidate;

require "../../components/student_vote_confirmation_modal.php";

?>

<!-- ==========================================================
SECURITY MODAL
========================================================== -->

<?php require "../../components/security_modal.php"; ?>

</main>
<!-- ==========================================================
FOOTER
========================================================== -->

<div id="footer"></div>

<script src="../../assets/js/app.js"></script>

<script src="../../assets/js/candidate_confirmation.js"></script>

<script src="../../assets/js/security_guard.js"></script>

</body>
</html>