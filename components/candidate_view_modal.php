<!-- ==========================================================
     VOTIFY
     Candidate View Modal
========================================================== -->

<div
id="candidateViewModal"
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
w-full
max-w-4xl
rounded-3xl
overflow-hidden
animate-[fadeUp_.25s_ease]">

<!-- =====================================================
HEADER
===================================================== -->

<div
class="
flex
items-start
justify-between
px-6
pt-6
pb-5
border-b
border-white/10">

<div>

<h2
class="text-3xl font-bold">

Candidate Details

</h2>

<p
class="text-slate-400 mt-1">

View Candidate Information

</p>

</div>

<button
id="closeCandidateViewModal"
type="button"
class="
w-12
h-12
rounded-2xl
bg-white/10
hover:bg-red-500/20
transition">

<i class="ri-close-line text-3xl"></i>

</button>

</div>

<!-- =====================================================
BODY
===================================================== -->

<div class="p-6">

<div
class="
grid
grid-cols-1
lg:grid-cols-[240px_1fr]
gap-6">

<!-- =====================================================
PHOTO
===================================================== -->

<div
class="flex justify-center">

<img

id="viewCandidatePhoto"

src=""

alt="Candidate"

class="
w-48
h-60
rounded-2xl
object-cover
border
border-white/10
shadow-2xl">

</div>

<!-- =====================================================
DETAILS
===================================================== -->

<div
class="grid grid-cols-2 gap-4">

<!-- Candidate Name -->

<div
class="
glass
rounded-2xl
p-4">

<p
class="text-slate-400 text-sm">

Candidate Name

</p>

<h3

id="viewCandidateName"

class="
text-2xl
font-bold
mt-2
break-words">

-

</h3>

</div>

<!-- Admission -->

<div
class="
glass
rounded-2xl
p-4">

<p
class="text-slate-400 text-sm">

Admission Number

</p>

<h3

id="viewCandidateAdmission"

class="
text-2xl
font-bold
mt-2">

-

</h3>

</div>

<!-- Department -->

<div
class="
glass
rounded-2xl
p-4">

<p
class="text-slate-400 text-sm">

Department

</p>

<h3

id="viewCandidateDepartment"

class="
text-xl
font-semibold
mt-2">

-

</h3>

</div>

<!-- Year -->

<div
class="
glass
rounded-2xl
p-4">

<p
class="text-slate-400 text-sm">

Year

</p>

<h3

id="viewCandidateYear"

class="
text-xl
font-semibold
mt-2">

-

</h3>

</div>

</div>

</div>

<!-- =====================================================
MANIFESTO
===================================================== -->

<div class="mt-6">

<p
class="
text-slate-400
text-sm
mb-3">

Manifesto

</p>

<div

id="viewCandidateManifesto"

class="
glass
rounded-2xl
border
border-white/10
p-5
leading-8
text-white
min-h-[100px]
max-h-[180px]
overflow-y-auto
whitespace-pre-wrap">

-

</div>

</div>

<!-- =====================================================
FOOTER
===================================================== -->

<div
class="
flex
justify-end
mt-8">

<button

id="closeCandidateView"

type="button"

class="
px-8
py-3
rounded-2xl
border
border-white/10
bg-white/5
hover:bg-white/10
transition
font-semibold">

Close

</button>

</div>

</div>

</div>

</div>