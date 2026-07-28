<?php
/* ==========================================================
   VOTIFY
   Thank You Page
   File : pages/student/thank_you.php
========================================================== */

/* ==========================================================
   SESSION
========================================================== */

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}

/* ==========================================================
   LOGIN PROTECTION
========================================================== */

if (

    !isset($_SESSION["student_logged_in"]) ||

    $_SESSION["student_logged_in"] !== true

) {

    header("Location: student_login.php");

    exit();

}

/* ==========================================================
   DIRECT ACCESS PROTECTION
========================================================== */

if (

    !isset($_SESSION["vote_submitted"]) ||

    $_SESSION["vote_submitted"] !== true

) {

    header("Location: candidate_selection.php");

    exit();

}

/* ==========================================================
   STUDENT
========================================================== */

$studentName =

htmlspecialchars(

    $_SESSION["student_name"]

);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Vote Submitted | VOTIFY

</title>

<!-- ==========================================================
TAILWIND
========================================================== -->

<script src="https://cdn.tailwindcss.com"></script>

<!-- ==========================================================
REMIX ICONS
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
class="bg-[#0B1020] text-white min-h-screen overflow-hidden">

<!-- ==========================================================
LOADER
========================================================== -->

<div id="loader-container"></div>

<!-- ==========================================================
BACKGROUND
========================================================== -->

<div
class="fixed inset-0 -z-10 overflow-hidden">

    <!-- Blue -->

    <div
    class="absolute
    top-0
    left-0
    w-96
    h-96
    rounded-full
    bg-blue-600/20
    blur-[160px]">
    </div>

    <!-- Purple -->

    <div
    class="absolute
    bottom-0
    right-0
    w-96
    h-96
    rounded-full
    bg-purple-600/20
    blur-[160px]">
    </div>

    <!-- Pink -->

    <div
    class="absolute
    top-1/2
    left-1/2
    -translate-x-1/2
    -translate-y-1/2
    w-80
    h-80
    rounded-full
    bg-pink-500/20
    blur-[140px]">
    </div>

<!-- ==========================================================
HEADER
========================================================== -->

<div id="header"></div>

<!-- ==========================================================
MAIN
========================================================== -->

<main
class="min-h-screen
flex
items-center
justify-center
px-5
py-6">

<div class="w-full max-w-lg">

<!-- ==========================================================
SUCCESS CARD
========================================================== -->

<div
class="glass
rounded-3xl
overflow-hidden
border
border-white/10
shadow-2xl
fade-up">

<!-- ==========================================================
TOP SECTION
========================================================== -->

<div
class="px-7
py-7
text-center">

<!-- Success Icon -->

<div
class="relative
w-20
h-20
mx-auto">

<div
class="absolute
inset-0
rounded-full
bg-gradient-to-br
from-green-400
to-emerald-600
blur-2xl
opacity-30">
</div>

<div
class="relative
w-20
h-20
rounded-full
bg-gradient-to-br
from-green-500
to-emerald-600
flex
items-center
justify-center
shadow-2xl
shadow-green-500/30">

<i
class="ri-check-double-line
text-5xl
text-white">
</i>

</div>

</div>

<!-- Badge -->

<div
class="inline-flex
items-center
gap-2
mt-5
px-4
py-2
rounded-full
bg-green-500/10
border
border-green-500/20
text-green-400
font-semibold">

<i class="ri-shield-check-line"></i>

Vote Successfully Recorded

</div>

<!-- Heading -->

<h1
class="mt-5
text-3xl
md:text-4xl
font-bold
leading-tight">

Thank You!

</h1>

<!-- Welcome -->

<p
class="mt-3
text-base
text-slate-300">

Thank you,

<span
class="font-bold
text-blue-400">

<?php echo $studentName; ?>

</span>

for participating in the election.

</p>

<!-- Description -->

<p
class="mt-3
leading-7
text-sm
text-slate-400
max-w-xl
mx-auto">

Your vote has been securely encrypted,
recorded successfully,
and stored anonymously.

Your participation helps ensure
a transparent and fair election.

</p>

</div>

<!-- ==========================================================
VOTE STATUS
========================================================== -->

<div
class="px-7
pb-7">

<div
class="grid
grid-cols-1
md:grid-cols-3
gap-4">

    <!-- Status -->

    <div
    class="glass
    rounded-2xl
    p-4
    text-center">

        <div
        class="w-12
        h-12
        mx-auto
        rounded-2xl
        bg-green-500/15
        flex
        items-center
        justify-center">

            <i
            class="ri-checkbox-circle-fill
            text-2xl
            text-green-400">
            </i>

        </div>

        <h3
        class="mt-3
        font-bold">

            Vote Status

        </h3>

        <p
        class="mt-1
        text-sm
        text-green-400">

            Successfully Recorded

        </p>

    </div>

    <!-- Privacy -->

    <div
    class="glass
    rounded-2xl
    p-4
    text-center">

        <div
        class="w-12
        h-12
        mx-auto
        rounded-2xl
        bg-blue-500/15
        flex
        items-center
        justify-center">

            <i
            class="ri-lock-2-fill
            text-2xl
            text-blue-400">
            </i>

        </div>

        <h3
        class="mt-3
        font-bold">

            Anonymous

        </h3>

        <p
        class="mt-1
        text-sm
        text-slate-400">

            Identity Protected

        </p>

    </div>

    <!-- Final -->

    <div
    class="glass
    rounded-2xl
    p-4
    text-center">

        <div
        class="w-12
        h-12
        mx-auto
        rounded-2xl
        bg-purple-500/15
        flex
        items-center
        justify-center">

            <i
            class="ri-shield-star-fill
            text-2xl
            text-purple-400">
            </i>

        </div>

        <h3
        class="mt-3
        font-bold">

            Final Submission

        </h3>

        <p
        class="mt-1
        text-sm
        text-slate-400">

            Vote Cannot Be Changed

        </p>

    </div>

</div>

<!-- ==========================================================
NOTICE
========================================================== -->

<div
class="mt-6
rounded-2xl
border
border-blue-500/20
bg-blue-500/10
p-5">

<div
class="flex
items-start
gap-3">

<div
class="w-10
h-10
rounded-xl
bg-blue-500/20
flex
items-center
justify-center
shrink-0">

<i
class="ri-information-fill
text-xl
text-blue-400">
</i>

</div>

<div>

<h3
class="font-bold
text-base">

Election Security Notice

</h3>

<p
class="mt-2
leading-6
text-sm
text-slate-300">

Your vote has been securely encrypted before storage.
For election integrity, no one can identify which candidate
you voted for. Once submitted, your vote is permanently locked
and cannot be modified.

</p>

</div>

</div>

</div>

<!-- ==========================================================
COUNTDOWN
========================================================== -->

<div
class="mt-6
text-center">

<div
class="inline-flex
items-center
justify-center
w-20
h-20
rounded-full
border-4
border-blue-500/30
bg-white/5">

<span

id="countdown"

class="text-4xl
font-bold
text-blue-400">

5

</span>

</div>

<h3
class="mt-4
text-xl
font-bold">

Redirecting...

</h3>

<p
class="mt-2
text-sm
text-slate-400">

You will be securely logged out and redirected
to the Student Login page shortly.

</p>

</div>

<!-- ==========================================================
FOOTER
========================================================== -->

<div id="footer"></div>

</div>

</div>

</main>

<!-- ==========================================================
APP JS
========================================================== -->

<script src="../../assets/js/app.js"></script>

<!-- ==========================================================
THANK YOU JS
========================================================== -->

<script src="../../assets/js/thank_you.js"></script>

</body>

</html>