<!DOCTYPE html>

<?php

require_once "check-registration.php";

if(session_status() === PHP_SESSION_NONE){

    session_start();

}

if(isset($_SESSION["student_id"])){

    header("Location: candidate_selection.php");

    exit();

}

?>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>

        Student Registration | VOTIFY

    </title>

    <!-- Tailwind CSS -->

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Remix Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet">

    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/custom.css">

    <link
        rel="stylesheet"
        href="../../assets/css/animations.css">

</head>

<body class="bg-[#0B1020] text-white min-h-screen flex flex-col overflow-x-hidden">

<!-- Loader -->

<div id="loader-container"></div>

    <!-- Background -->

    <div class="fixed inset-0 -z-10 overflow-hidden">

        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 blur-[140px] rounded-full"></div>

        <div class="absolute bottom-0 right-0 w-96 h-96 bg-pink-600/20 blur-[150px] rounded-full"></div>

        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-purple-600/20 blur-[120px] rounded-full -translate-x-1/2 -translate-y-1/2"></div>

    </div>

    <!-- Header -->

    <div id="header"></div>

    <!-- ========================= -->
    <!-- Registration Section -->
    <!-- ========================= -->

    <section class="flex-1 py-14">

        <div class="max-w-5xl mx-auto px-6">

            <!-- Heading -->

            <div class="text-center mb-10">

                <h1 class="mt-6 text-4xl md:text-5xl font-bold">

                    <span class="gradient-text">

                        Student Registration

                    </span>

                </h1>

                <p class="mt-4 text-slate-400">

                    Secure Registration for MCA Students

                </p>

            </div>

            <!-- Registration Card -->

            <div class="glass rounded-3xl p-8 md:p-10 shadow-2xl">

                <!-- Form Starts Here -->

<form
id="registerForm"

class="grid
grid-cols-1
lg:grid-cols-2
gap-x-8
gap-y-6">

                    <!-- ========================= -->
<!-- Full Name -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="fullName"
        class="block mb-2 font-medium text-slate-300">

        Full Name <span class="text-pink-400">*</span>

    </label>

<input
type="text"
id="fullName"
name="fullName"
placeholder="Enter your full name"
maxlength="100"
autocomplete="off"
class="w-full h-14 px-5 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
required>

<p id="fullNameError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>


<!-- ========================= -->
<!-- Date of Birth -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="dob"
        class="block mb-2 font-medium text-slate-300">

        Date of Birth <span class="text-pink-400">*</span>

    </label>

<input
type="date"
id="dob"
name="dob"
class="w-full h-14 px-5 rounded-xl bg-white/5 border border-white/10 text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
required>

<p id="dobError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>


<!-- ========================= -->
<!-- Admission Number -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="admissionNo"
        class="block mb-2 font-medium text-slate-300">

        Admission Number <span class="text-pink-400">*</span>

    </label>

<input
type="text"
id="admissionNo"
name="admissionNo"
maxlength="30"
autocomplete="off"
placeholder="Enter Admission Number"
class="w-full h-14 px-5 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
required>

<p id="admissionError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>

<!-- ========================= -->
<!-- Phone Number -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="phone"
        class="block mb-2 font-medium text-slate-300">

        Phone Number <span class="text-pink-400">*</span>

    </label>

    <input
        type="tel"
        id="phone"
        name="phone"
        maxlength="10"
        placeholder="Enter phone number"
        class="w-full h-14 px-5 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
        required>

<p id="phoneError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>

<!-- ========================= -->
<!-- College Email -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="email"
        class="block mb-2 font-medium text-slate-300">

        College Email <span class="text-pink-400">*</span>

    </label>

<input
type="email"
id="email"
name="email"
placeholder="yourname@sonatech.ac.in"
class="w-full h-14 px-5 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
required>

<p id="emailError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>


<!-- ========================= -->
<!-- Department -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="department"
        class="block mb-2 font-medium text-slate-300">

        Department <span class="text-pink-400">*</span>

    </label>

    <div
        class="custom-dropdown"
        data-name="department">

        <button
            type="button"
            class="dropdown-button">

            <span>

                Select Department

            </span>

            <i class="ri-arrow-down-s-line"></i>

        </button>

        <div class="dropdown-menu">

            <div
                class="dropdown-item"
                data-value="MCA">

                MCA

            </div>

        </div>

        <input
            type="hidden"
            id="department"
            name="department">

    </div>

    <p
        id="departmentError"
        class="hidden mt-2 text-sm text-red-400 font-medium">
    </p>

</div>

<!-- ========================= -->
<!-- Year -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="year"
        class="block mb-2 font-medium text-slate-300">

        Year <span class="text-pink-400">*</span>

    </label>

    <div
        class="custom-dropdown"
        data-name="year">

        <button
            type="button"
            class="dropdown-button">

            <span>

                Select Year

            </span>

            <i class="ri-arrow-down-s-line"></i>

        </button>

        <div class="dropdown-menu">

            <div
                class="dropdown-item"
                data-value="I Year">

                I Year

            </div>

            <div
                class="dropdown-item"
                data-value="II Year">

                II Year

            </div>

        </div>

        <input
            type="hidden"
            id="year"
            name="year">

    </div>

    <p
        id="yearError"
        class="hidden mt-2 text-sm text-red-400 font-medium">
    </p>

</div>


<!-- ========================= -->
<!-- Gender -->
<!-- ========================= -->

<div class="lg:col-span-2 mb-2">

    <label class="block mb-4 font-medium text-slate-300">

        Gender <span class="text-pink-400">*</span>

    </label>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Male -->

        <label
        class="gender-card cursor-pointer rounded-2xl border border-white/10 bg-white/5 hover:border-blue-500 hover:bg-blue-500/10 transition-all duration-300">

            <input
            type="radio"
            name="gender"
            value="Male"
            class="hidden peer"
            required>

            <div
            class="flex items-center justify-center gap-3 h-14 rounded-2xl peer-checked:border peer-checked:border-blue-500 peer-checked:bg-blue-500/20">

                <i class="ri-men-line text-xl text-blue-400"></i>

                <span class="font-medium">

                    Male

                </span>

            </div>

        </label>

        <!-- Female -->

        <label
        class="gender-card cursor-pointer rounded-2xl border border-white/10 bg-white/5 hover:border-pink-500 hover:bg-pink-500/10 transition-all duration-300">

            <input
            type="radio"
            name="gender"
            value="Female"
            class="hidden peer">

            <div
            class="flex items-center justify-center gap-3 h-14 rounded-2xl peer-checked:border peer-checked:border-pink-500 peer-checked:bg-pink-500/20">

                <i class="ri-women-line text-xl text-pink-400"></i>

                <span class="font-medium">

                    Female

                </span>

            </div>

        </label>

        <!-- Other -->

        <label
        class="gender-card cursor-pointer rounded-2xl border border-white/10 bg-white/5 hover:border-purple-500 hover:bg-purple-500/10 transition-all duration-300">

            <input
            type="radio"
            name="gender"
            value="Other"
            class="hidden peer">

            <div
            class="flex items-center justify-center gap-3 h-14 rounded-2xl peer-checked:border peer-checked:border-purple-500 peer-checked:bg-purple-500/20">

                <i class="ri-user-3-line text-xl text-purple-400"></i>

                <span class="font-medium">

                    Other

                </span>

            </div>

        </label>

    </div>
<p id="genderError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>
</div>


<!-- ========================= -->
<!-- Password -->
<!-- ========================= -->

<div class="mb-6">

    <label
        for="password"
        class="block mb-2 font-medium text-slate-300">

        Password <span class="text-pink-400">*</span>

    </label>

    <div class="relative">

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter password"
            class="w-full h-14 px-5 pr-14 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
            required>

        <button
            type="button"
            id="togglePassword"
            class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-400 transition">

            <i class="ri-eye-line text-xl"></i>

        </button>

    </div>
<p id="passwordError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>
    <!-- Strength Meter -->

    <div
        id="passwordStrength"
        class="hidden mt-4">

        <div
            class="w-full h-2 rounded-full bg-white/10 overflow-hidden">

            <div
                id="strengthBar"
                class="h-full w-0 rounded-full transition-all duration-300">

            </div>

        </div>

        <p
            id="strengthText"
            class="mt-2 text-sm text-slate-400">

            Password Strength

        </p>

    </div>

</div>


<!-- ========================= -->
<!-- Confirm Password -->
<!-- ========================= -->

<div class="mb-8">

    <label
        for="confirmPassword"
        class="block mb-2 font-medium text-slate-300">

        Confirm Password <span class="text-pink-400">*</span>

    </label>

<div class="relative">

    <input
        type="password"
        id="confirmPassword"
        name="confirmPassword"
        placeholder="Re-enter password"
        class="w-full h-14 px-5 pr-14 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
        required>

    <button
        type="button"
        id="toggleConfirmPassword"
        class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pink-400 transition">

        <i class="ri-eye-line text-xl"></i>

    </button>

</div>

<p id="confirmPasswordError"
class="hidden mt-2 text-sm text-red-400 font-medium"></p>

</div>


<!-- ========================= -->
<!-- Register Button -->
<!-- ========================= -->

<div class="md:col-span-2">

<button
type="submit"
class="btn-primary
w-full
py-4
rounded-xl
text-lg
font-semibold">

<i class="ri-user-add-line"></i>

Register Now

</button>

</div>

                </form>

            </div>

        </div>

    </section>

<div
id="successToast"
class="fixed top-6 right-6 translate-x-[120%]
transition-all duration-500
bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl z-[9999]">

<div class="flex items-center gap-3">

<i class="ri-checkbox-circle-fill text-2xl"></i>

<div>

<p class="font-semibold">

Registration Successful

</p>

<p class="text-sm text-green-100">

Redirecting...

</p>

</div>

</div>

</div>

    <!-- Footer -->

    <div id="footer"></div>

    <!-- App JS -->

<script src="../../assets/js/app.js"></script>
<script src="../../assets/js/dropdown.js"></script>
<script src="../../assets/js/register.js"></script>

</body>
</html>