<?php
/* ==========================================================
   VOTIFY
   Election Not Started
   File : pages/student/election_not_started.php
========================================================== */

require_once "../../backend/student/page_guard.php";

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Election Has Ended | VOTIFY

</title>

<!-- Tailwind -->

<script src="https://cdn.tailwindcss.com"></script>

<!-- Remix Icons -->

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
rel="stylesheet">

<!-- CSS -->

<link
rel="stylesheet"
href="../../assets/css/custom.css">

<link
rel="stylesheet"
href="../../assets/css/animations.css">

</head>

<body
class="bg-[#0B1020] text-white min-h-screen flex flex-col overflow-x-hidden">

<!-- Loader -->

<div id="loader-container"></div>

<!-- Background -->

<div class="fixed inset-0 -z-10 overflow-hidden">

<div
class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 blur-[150px] rounded-full">
</div>

<div
class="absolute bottom-0 right-0 w-96 h-96 bg-pink-600/20 blur-[150px] rounded-full">
</div>

<div
class="absolute top-1/2 left-1/2 w-80 h-80 bg-purple-600/20 blur-[130px] rounded-full -translate-x-1/2 -translate-y-1/2">
</div>

</div>

<!-- Header -->

<div id="header"></div>

<!-- Main -->

<main
class="flex-1 flex items-center justify-center px-6 py-12 min-h-[70vh]">

<div
class="glass rounded-3xl max-w-2xl w-full p-10 text-center shadow-2xl animate-[fadeUp_.35s_ease]">

<!-- Icon -->

<div
class="mx-auto w-24 h-24 rounded-full bg-red-500/15 border border-red-500/30 flex items-center justify-center">

<i class="ri-medal-line text-5xl text-red-400"></i>

</div>

<!-- Title -->

<h1
class="mt-8 text-4xl font-bold">

Election Has Ended

</h1>

<!-- Description -->

<p
class="mt-5 text-lg leading-8 text-slate-400">

The election has been successfully completed.

</p>

<p
class="mt-3 text-slate-500">

Thank you for participating in the voting process.

Student login is temporarily unavailable while the system prepares for the next election.

</p>

<!-- Notice -->

<div
class="mt-8 rounded-2xl border border-blue-500/20 bg-blue-500/10 p-5">

<div class="flex items-start gap-4">

<i class="ri-information-line text-2xl text-blue-400 mt-1"></i>

<div class="text-left">

<h3 class="font-semibold text-blue-300">

What's Next?

</h3>

<p class="mt-2 text-sm text-slate-300 leading-7">

Please wait until the administrator announces the next election. Student login will automatically become available once the next election is scheduled.

</p>

</div>

</div>

</div>

<!-- Button -->

<div class="mt-10">

<a
href="../../index.html"
class="btn-primary inline-flex items-center gap-3 px-8 py-4 rounded-2xl">

<i class="ri-arrow-left-line text-xl"></i>

Back to Home

</a>

</div>

</div>

</main>

<!-- Footer -->

<div id="footer"></div>

<!-- JS -->

<script src="../../assets/js/app.js"></script>

</body>

</html>