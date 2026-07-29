<?php
/* ==========================================================
   VOTIFY
   Secure Voting Entry
   File : pages/student/security_check.php
========================================================== */

require_once "check-login.php";

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

/* ==========================================================
   SESSION VALIDATION
========================================================== */

if(
    !isset($_SESSION["student_id"])
){

    header(
        "Location: student_login.php"
    );

    exit();

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

Secure Voting Environment | VOTIFY

</title>

<!-- ==========================================================
TAILWIND CSS
========================================================== -->

<script src="https://cdn.tailwindcss.com"></script>

<!-- ==========================================================
REMIX ICONS
========================================================== -->

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
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
class="bg-[#0B1020] text-white min-h-screen flex flex-col overflow-x-hidden">

<!-- ==========================================================
LOADER
========================================================== -->

<div id="loader-container"></div>

<!-- ==========================================================
ANIMATED BACKGROUND
========================================================== -->

<div class="fixed inset-0 -z-10 overflow-hidden">

<div
class="absolute
top-0
left-0
w-96
h-96
bg-blue-600/20
blur-[140px]
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
w-80
h-80
bg-purple-600/20
blur-[120px]
rounded-full
-transform
-translate-x-1/2
-translate-y-1/2">
</div>

</div>

<!-- ==========================================================
HEADER
========================================================== -->

<div id="header"></div>

<!-- ==========================================================
MAIN SECTION
========================================================== -->

<section
class="flex-1 py-14">

<div
class="max-w-5xl mx-auto px-6">

<!-- ==========================================================
PAGE HEADING
========================================================== -->

<div
class="text-center mb-10">

<div
class="inline-flex
items-center
justify-center
w-24
h-24
rounded-full
bg-blue-500/20
border
border-blue-500/30
mb-6">

<i
class="ri-shield-check-line
text-5xl
text-blue-400">
</i>

</div>

<h1
class="text-4xl
md:text-5xl
font-bold">

<span class="gradient-text">

Secure Voting Environment

</span>

</h1>

<p
class="mt-5
text-slate-400
max-w-3xl
mx-auto
leading-8">

Before entering the Secure Voting Room,
please read all security instructions carefully.
Your voting session will begin only after
you acknowledge the rules below.

</p>

</div>

<!-- ==========================================================
MAIN CARD
========================================================== -->

<div
class="glass
rounded-3xl
p-8
md:p-10
shadow-2xl">

<!-- ==========================================================
WELCOME
========================================================== -->

<div
class="rounded-2xl
border
border-blue-500/20
bg-blue-500/10
p-6
mb-8">

<div
class="flex
items-start
gap-4">

<div
class="w-14
h-14
rounded-2xl
bg-blue-500/20
flex
items-center
justify-center
shrink-0">

<i
class="ri-information-line
text-3xl
text-blue-300">
</i>

</div>

<div>

<h2
class="text-2xl
font-bold
text-blue-300">

Welcome,
<?php
echo htmlspecialchars(
$_SESSION["student_name"]
);
?>

</h2>

<p
class="mt-3
text-slate-300
leading-7">

You are about to enter
the protected voting environment.

For election integrity,
fullscreen mode is mandatory
throughout the voting session.

</p>

</div>

</div>

</div>

<!-- ==========================================================
SECURITY INSTRUCTIONS
========================================================== -->

<div
class="rounded-2xl
border
border-red-500/20
bg-red-500/10
p-7
mb-8">

<div
class="flex
items-center
gap-3
mb-6">

<div
class="w-12
h-12
rounded-xl
bg-red-500/20
flex
items-center
justify-center">

<i
class="ri-shield-keyhole-line
text-2xl
text-red-300">
</i>

</div>

<div>

<h2
class="text-2xl
font-bold
text-red-300">

Security Instructions

</h2>

<p
class="text-slate-400
mt-1">

Please follow all security rules.

</p>

</div>

</div>

<div
class="grid
grid-cols-1
md:grid-cols-2
gap-5">

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Fullscreen mode is mandatory
during the entire voting process.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Do not press the ESC key
unless instructed.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Do not switch browser tabs
or minimize the browser.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Do not switch to other
applications while voting.

</p>

</div>

<div class="flex gap-3">

<i class="ri-error-warning-fill text-yellow-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

First security violation
will display a warning.

</p>

</div>

<div class="flex gap-3">

<i class="ri-close-circle-fill text-red-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Second security violation
will automatically end
your voting session.

</p>

</div>

</div>

</div>

<!-- ==========================================================
ELECTION INSTRUCTIONS
========================================================== -->

<div
class="rounded-2xl
border
border-emerald-500/20
bg-emerald-500/10
p-7
mb-8">

<div
class="flex
items-center
gap-3
mb-6">

<div
class="w-12
h-12
rounded-xl
bg-emerald-500/20
flex
items-center
justify-center">

<i
class="ri-government-line
text-2xl
text-emerald-300">
</i>

</div>

<div>

<h2
class="text-2xl
font-bold
text-emerald-300">

Election Instructions

</h2>

<p
class="text-slate-400
mt-1">

Read carefully before proceeding.

</p>

</div>

</div>

<div
class="grid
grid-cols-1
md:grid-cols-2
gap-5">

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Select only one candidate
for the election.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Review the candidate details
before confirming your vote.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Your vote cannot be changed
after final submission.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

NOTA (None of the Above)
will be available if applicable.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Do not refresh the page
during the voting process.

</p>

</div>

<div class="flex gap-3">

<i class="ri-checkbox-circle-fill text-green-400 text-xl mt-1"></i>

<p class="text-slate-300 leading-7">

Ensure your decision before
clicking Submit Vote.

</p>

</div>

</div>

</div>

<!-- ==========================================================
AGREEMENT
========================================================== -->

<div
class="rounded-2xl
border
border-white/10
bg-white/5
p-6">

<label
for="agreeCheckbox"
class="flex
items-start
gap-4
cursor-pointer
select-none">

<input
type="checkbox"
id="agreeCheckbox"
class="hidden">

<div
id="customCheckbox"
class="
w-8
h-8
rounded-lg
border-2
border-slate-500
bg-white/5
flex
items-center
justify-center
transition-all
duration-300
shrink-0
mt-1">

<i
id="checkboxIcon"
class="
ri-check-line
text-white
text-xl
hidden">
</i>

</div>

<div class="flex-1">

<h3
class="font-semibold
text-xl
text-white">

Voting Declaration

</h3>

<p
class="mt-3
text-slate-400
leading-8">

I have read and understood all
Security Instructions and Election Rules.

I understand that violating the
Secure Voting Environment may
automatically terminate my
voting session.

</p>

</div>

</label>

<div
class="mt-8">

<button
type="button"
id="continueButton"
disabled
class="btn-primary
w-full
h-14
rounded-2xl
text-lg
font-semibold
flex
items-center
justify-center
gap-3
opacity-50
cursor-not-allowed
transition-all
duration-300
shadow-xl">

<i
class="ri-arrow-right-circle-line
text-xl">
</i>

Enter Secure Voting Room

</button>

</div>

<div
id="fullscreenError"
class="hidden
mt-5
rounded-xl
border
border-red-500/20
bg-red-500/10
p-4">

<div
class="flex
items-start
gap-3">

<i
class="ri-error-warning-fill
text-red-400
text-2xl">
</i>

<div>

<p
class="font-semibold
text-red-300">

Fullscreen Required

</p>

<p
class="text-red-200
mt-1">

Please allow Fullscreen Mode
to continue to the voting page.

</p>

</div>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- ==========================================================
FOOTER
========================================================== -->

<div id="footer"></div>

<!-- ==========================================================
APP JS
========================================================== -->

<script src="../../assets/js/app.js"></script>

<!-- ==========================================================
SECURITY CHECK JS
========================================================== -->

<script src="../../assets/js/security_check.js"></script>

</body>

</html>