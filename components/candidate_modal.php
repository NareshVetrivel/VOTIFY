<!-- ==========================================================
     VOTIFY
     Candidate Modal
========================================================== -->

<div

id="candidateModal"

class="

fixed

inset-0

hidden

items-center

justify-center

bg-black/70

backdrop-blur-sm

z-[9999]

px-4
py-6">

<div

class="

glass

rounded-3xl

w-full

max-w-3xl

max-h-[90vh]

overflow-y-auto

p-6

animate-[fadeUp_.25s_ease]">

<!-- =====================================================
HEADER
===================================================== -->

<div class="flex items-start justify-between">

<div>

<h2
id="candidateModalTitle"
class="text-3xl font-bold">

Candidate Details

</h2>

<p
id="candidateModalSubtitle"
class="text-slate-400 mt-2">
Add Candidate
</p>

</div>

<button

id="closeCandidateModal"

type="button"

class="

w-12

h-12

rounded-xl

bg-white/10

hover:bg-red-500/20

transition">

<i class="ri-close-line text-2xl"></i>

</button>

</div>

<!-- =====================================================
FORM
===================================================== -->

<form

id="candidateForm"
enctype="multipart/form-data"

class="mt-8">

<input

type="hidden"

id="candidateId"

name="candidateId">

<input

type="hidden"

id="studentId"

name="studentId">

<!-- =====================================================
GRID
===================================================== -->

<div

class="

grid

grid-cols-1

lg:grid-cols-[220px_1fr]

gap-5">

<!-- =====================================================
LEFT
===================================================== -->

<div>

<label class="block mb-3 font-medium">

Candidate Photo

</label>

<label

for="candidatePhoto"

class="

relative

cursor-pointer

flex

flex-col

items-center

justify-center

w-44

h-44

mx-auto

rounded-2xl

border-2

border-dashed

border-white/20

bg-white/5

hover:border-pink-500

transition">

<svg

xmlns="http://www.w3.org/2000/svg"

class="w-14 h-14 text-slate-400"

fill="none"

viewBox="0 0 24 24"

stroke="currentColor"

stroke-width="2">

<path

stroke-linecap="round"

stroke-linejoin="round"

d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>

</svg>

<p class="mt-4 text-sm text-slate-300">

Click to Upload

</p>

<img
id="photoPreview"
alt="Candidate Photo"
class="
hidden
absolute
inset-0
w-full
h-full
object-cover
rounded-2xl">

</label>

<input
type="file"
id="candidatePhoto"
name="candidatePhoto"
accept=".jpg,.jpeg,.png"
class="hidden">

<p class="text-xs text-slate-500 mt-4 text-center">

JPG, JPEG or PNG

</p>

</div>

<!-- =====================================================
RIGHT
===================================================== -->

<div class="space-y-5">

<!-- Admission -->

<div>

<label class="block mb-3 font-medium">

Admission Number

</label>

<div class="relative">

<input
type="text"
id="admissionNo"
name="admissionNo"
required
autocomplete="off"
placeholder="Enter Admission Number"
class="pl-5 pr-14">

<button

type="button"

id="searchStudent"

class="

absolute

right-5

top-1/2

-translate-y-1/2

text-slate-400

hover:text-white">

<i class="ri-search-line text-xl"></i>

</button>

</div>

</div>

<!-- Candidate Name -->

<div>

<label class="block mb-3 font-medium">

Candidate Name

</label>

<input
type="text"
id="candidateName"
readonly
class="
pl-5
bg-white/5
cursor-not-allowed
select-none
opacity-80
hover:bg-white/5">

</div>

<!-- Department + Year -->

<div class="grid grid-cols-2 gap-6">

<div>

<label class="block mb-3 font-medium">

Department

</label>

<input

type="text"

id="candidateDepartment"

readonly

class="
pl-5
bg-white/5
cursor-not-allowed
select-none
opacity-80
hover:bg-white/5">

</div>

<div>

<label class="block mb-3 font-medium">

Year

</label>

<input

type="text"

id="candidateYear"

readonly

class="
pl-5
bg-white/5
cursor-not-allowed
select-none
opacity-80
hover:bg-white/5">

</div>

</div>

</div>

</div>

<!-- =====================================================
MANIFESTO
===================================================== -->

<div class="mt-8">

<label class="block mb-2">

Manifesto

</label>

<textarea

id="candidateManifesto"

required

name="manifesto"

rows="4"

maxlength="255"

placeholder="Vote for Innovation & Transparency..."

class="

pl-5

pt-4

pr-5

resize-y

min-h-[110px]

max-h-[220px]"></textarea>

<div class="flex justify-between items-center mt-2">

<p class="text-xs text-slate-500">

Maximum 255 characters

</p>

<span

id="manifestoCount"

class="text-sm text-slate-400">

0 / 255

</span>

</div>

</div>

<!-- =====================================================
FOOTER
===================================================== -->

<div

class="

flex

justify-end

gap-4

mt-6">

<button

type="button"

id="cancelCandidate"

class="btn-outline">

Cancel

</button>

<button
type="submit"
id="saveCandidate"
class="btn-primary">

<i class="ri-save-line mr-2"></i>

<span id="saveCandidateText">

Save Candidate

</span>

</button>
</div>

</form>

</div>

</div>