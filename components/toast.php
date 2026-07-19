<!-- ==========================================================
     VOTIFY
     Reusable Toast Notification
========================================================== -->

<div

id="requestToast"

class="

fixed

top-6

right-6

translate-x-[120%]

transition-all

duration-500

ease-out

z-[9999]

rounded-2xl

px-6

py-5

min-w-[340px]

glass

border

border-white/10

shadow-2xl">

    <div class="flex items-center gap-4">

        <!-- Icon -->

        <div

        id="toastIconWrapper"

        class="

        w-14

        h-14

        rounded-2xl

        flex

        items-center

        justify-center

        bg-green-500/20">

            <i

            id="toastIcon"

            class="

            ri-checkbox-circle-fill

            text-3xl

            text-green-400">

            </i>

        </div>

        <!-- Content -->

        <div class="flex-1">

            <h3

            id="toastTitle"

            class="

            text-lg

            font-bold">

                Success

            </h3>

            <p

            id="toastMessage"

            class="

            text-sm

            text-slate-300

            mt-1">

                Operation completed successfully.

            </p>

        </div>

    </div>

</div>