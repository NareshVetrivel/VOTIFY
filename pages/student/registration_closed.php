<?php
/* ==========================================================
   VOTIFY
   Registration Closed
   File : pages/student/registration_closed.php
========================================================== */
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Registration Closed | VOTIFY

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
BACKGROUND
========================================================== -->

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

<!-- ==========================================================
HEADER
========================================================== -->

<div id="header"></div>

<!-- ==========================================================
MAIN
========================================================== -->

<main
class="flex-1 flex items-center justify-center px-6 py-12 min-h-[70vh]">

<div
class="glass rounded-3xl max-w-2xl w-full p-10 text-center shadow-2xl animate-[fadeUp_.35s_ease]">

<!-- =====================================================
ICON
===================================================== -->

<div
class="mx-auto w-24 h-24 rounded-full bg-red-500/15 border border-red-500/30 flex items-center justify-center">

<i class="ri-lock-line text-5xl text-red-400"></i>

</div>

<!-- =====================================================
TITLE
===================================================== -->

<h1
class="mt-8 text-4xl font-bold">

Registration Closed

</h1>

<!-- =====================================================
DESCRIPTION
===================================================== -->

<p
class="mt-5 text-lg leading-8 text-slate-400">

Student registration is temporarily unavailable because the election is currently in progress.

</p>

<p
class="mt-3 text-slate-500">

Please wait until the administrator ends the election. Registration will automatically reopen after the election is completed.

</p>

<!-- =====================================================
NOTICE
===================================================== -->

<div
class="mt-8 rounded-2xl border border-yellow-500/20 bg-yellow-500/10 p-5">

<div class="flex items-start gap-4">

<i class="ri-information-line text-2xl text-yellow-400 mt-1"></i>

<div class="text-left">

<h3 class="font-semibold text-yellow-300">

Why can't I register?

</h3>

<p class="mt-2 text-sm text-slate-300 leading-7">

To ensure a secure and fair voting process, new student registrations are disabled while the election is active.

</p>

</div>

</div>

</div>

<!-- =====================================================
BUTTON
===================================================== -->

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

<!-- ==========================================================
FOOTER
========================================================== -->

<div id="footer"></div>

<!-- ==========================================================
APP JS
========================================================== -->

<script src="../../assets/js/app.js"></script>

</body>

</html>