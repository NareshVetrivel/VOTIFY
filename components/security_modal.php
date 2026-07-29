<!-- ==========================================================
     VOTIFY
     Security Warning Modal
     File : components/security_modal.php
========================================================== -->

<div
    id="securityWarningModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-sm">

    <!-- ======================================================
         MODAL
    ======================================================= -->

    <div
        class="w-[92%] max-w-md rounded-3xl border border-white/10 bg-[#111827]/95 shadow-2xl p-8 animate-[fadeIn_.25s_ease]">

        <!-- ==================================================
             ICON
        =================================================== -->

        <div class="flex justify-center">

            <div
                class="flex h-20 w-20 items-center justify-center rounded-full bg-yellow-500/15 border border-yellow-500/30">

                <i class="ri-alert-line text-5xl text-yellow-400"></i>

            </div>

        </div>

        <!-- ==================================================
             TITLE
        =================================================== -->

        <h2
            class="mt-6 text-center text-2xl font-bold text-white">

            Security Warning

        </h2>

        <!-- ==================================================
             MESSAGE
        =================================================== -->

        <p
            id="securityViolationReason"
            class="mt-4 text-center text-slate-300 leading-7">

            Full Screen Mode was interrupted.

        </p>

        <!-- ==================================================
             WARNING COUNT
        =================================================== -->

        <div
            class="mt-6 rounded-2xl border border-yellow-500/20 bg-yellow-500/10 px-4 py-3">

            <p
                id="securityAttempt"
                class="text-center text-sm font-semibold text-yellow-300">

                Warning 1 of 2

            </p>

        </div>

        <!-- ==================================================
             INFO
        =================================================== -->

        <p
            class="mt-6 text-center text-sm text-slate-400">

            Returning to Full Screen is required to continue voting.
            A second security violation will automatically end your voting session.

        </p>

        <!-- ==================================================
             BUTTON
        =================================================== -->

        <button
            id="resumeVotingButton"
            type="button"
            class="mt-8 w-full rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 py-4 font-semibold text-white transition-all duration-300 hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-600/30">

            Return to Voting

        </button>

    </div>

</div>