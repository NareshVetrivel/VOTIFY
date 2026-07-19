<!-- ==========================================================
     VOTIFY
     Reusable Confirmation Modal
========================================================== -->

<div

id="confirmationModal"

class="

fixed

inset-0

hidden

items-center

justify-center

bg-black/70

backdrop-blur-sm

z-[9999]

px-4">

<div

class="

glass

rounded-3xl

w-full

max-w-md

p-8

animate-[fadeUp_.25s_ease]">

<!-- Icon -->

<div class="text-center">

<div

id="confirmIconWrapper"

class="

mx-auto

w-20

h-20

rounded-full

bg-blue-500/20

flex

items-center

justify-center">

<i

id="confirmIcon"

class="

ri-question-line

text-5xl

text-blue-400">

</i>

</div>

<!-- Title -->

<h2

id="confirmTitle"

class="

text-3xl

font-bold

mt-6">

Confirm Action

</h2>

<!-- Message -->

<p

id="confirmMessage"

class="

text-slate-400

mt-3

leading-7">

Are you sure you want to continue?

</p>

</div>

<!-- Buttons -->

<div

class="

grid

grid-cols-2

gap-4

mt-8">

<button

id="confirmCancel"

type="button"

class="

py-4

rounded-2xl

bg-white/10

hover:bg-white/20

transition

font-semibold">

Cancel

</button>

<button

id="confirmOk"

class="

btn-primary

flex-1

flex

items-center

justify-center

gap-2">

<span id="confirmButtonText">

Confirm

</span>

</button>

</div>

</div>

</div>