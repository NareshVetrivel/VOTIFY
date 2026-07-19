<?php
/* ==========================================================
   VOTIFY
   Student Login
========================================================== */

require_once "check-election.php";

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

if(isset($_SESSION["student_id"])){

    header("Location: candidate_selection.php");

    exit();

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Student Login | VOTIFY

</title>

<script src="https://cdn.tailwindcss.com"></script>

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
rel="stylesheet">

<link
rel="stylesheet"
href="../../assets/css/custom.css">

<link
rel="stylesheet"
href="../../assets/css/animations.css">

</head>

<body
class="bg-[#0B1020] text-white min-h-screen flex flex-col overflow-x-hidden">

<div id="loader-container"></div>

<div id="header"></div>

<main class="flex-1 flex items-center justify-center px-6">

<div
class="glass rounded-3xl max-w-xl w-full p-12 text-center animate-[fadeUp_.35s_ease]">

<div
class="mx-auto w-24 h-24 rounded-full bg-blue-500/15 border border-blue-500/30 flex items-center justify-center">

<i class="ri-login-circle-line text-5xl text-blue-400"></i>

</div>

<h1 class="mt-8 text-4xl font-bold">

Student Login

</h1>

<p class="mt-5 text-slate-400 leading-8">

Temporary Login Page

</p>

</div>

</main>

<div id="footer"></div>

<script src="../../assets/js/app.js"></script>

</body>

</html>