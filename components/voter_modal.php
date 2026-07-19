<!-- ==========================================================
     VOTER EDIT MODAL
========================================================== -->

<div

id="voterModal"

class="

fixed

inset-0

bg-black/70

backdrop-blur-sm

hidden

items-center

justify-center

z-[999]">

<div
class="
glass
rounded-3xl
w-[92%]
max-w-2xl
max-h-[90vh]
overflow-y-auto
p-6
relative
animate-scaleIn">

<!-- Header -->

<div class="flex items-center justify-between mb-8">

<div>

<h2 class="text-3xl font-bold">

Edit Voter

</h2>

<p class="text-slate-400 mt-2">

Update approved voter information.

</p>

</div>

<button

id="closeVoterModal"

class="

w-11

h-11

rounded-xl

bg-white/10

hover:bg-red-500/20

hover:text-red-400

transition">

<i class="ri-close-line text-2xl"></i>

</button>

</div>

<!-- Form -->

<form

id="voterForm"

class="space-y-6">

<input

type="hidden"

id="voterId">

<!-- Full Name -->

<div>

<label class="block mb-2 text-slate-300">

Full Name

</label>

<input

type="text"

id="fullName"

class="w-full px-5"

required>

</div>

<!-- Two Columns -->

<div class="grid md:grid-cols-2 gap-6">

<div>

<label class="block mb-2 text-slate-300">

Admission No

</label>

<input
type="text"
id="admissionNo"
readonly
class="w-full px-5 bg-white/5 cursor-not-allowed">

</div>

<div>

<label class="block mb-2 text-slate-300">

College Email

</label>

<input
type="email"
id="collegeEmail"
readonly
class="w-full px-5 bg-white/5 cursor-not-allowed">

</div>

</div>

<!-- Two Columns -->

<div class="grid md:grid-cols-2 gap-6">

<div>

<label class="block mb-2 text-slate-300">

Phone Number

</label>

<input

type="text"

id="phone"

class="w-full px-5">

</div>

<div>

<label class="block mb-2 text-slate-300">

Gender

</label>

<input
type="text"
id="gender"
readonly
class="w-full px-5 bg-white/5 cursor-not-allowed">

</div>

</div>

<!-- Two Columns -->

<div class="grid md:grid-cols-2 gap-6">

<div>

<label class="block mb-2 text-slate-300">

Department

</label>

<input
type="text"
id="department"
readonly
class="w-full px-5 bg-white/5 cursor-not-allowed">

</div>

<div>

<label class="block mb-2 text-slate-300">

Year

</label>

<select

id="year"

class="w-full px-5">

<option>I Year</option>

<option>II Year</option>

</select>

</div>

</div>

<!-- Vote Status -->

<div>

<label class="block mb-2 text-slate-300">

Vote Status

</label>

<input
type="text"
id="voteStatus"
readonly
class="w-full px-5 bg-white/5 cursor-not-allowed">

</div>

<!-- Buttons -->

<div

class="

flex

justify-end

gap-4

pt-6">

<button

type="button"

id="cancelVoter"

class="btn-outline">

Cancel

</button>

<button

type="submit"

class="btn-primary">

<i class="ri-save-line mr-2"></i>

Save Changes

</button>

</div>

</form>

</div>

</div>