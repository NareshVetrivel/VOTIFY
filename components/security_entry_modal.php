<!-- ==========================================================
   VOTIFY
   Security Entry Modal
   File : components/security_entry_modal.php
========================================================== -->

<div

id="securityEntryModal"

class="fixed
inset-0
z-[99999]
flex
items-center
justify-center
bg-[#0B1020]/80
backdrop-blur-xl">

    <div

    class="glass
    w-[92%]
    max-w-xl
    rounded-3xl
    p-8
    md:p-10
    text-center
    border
    border-white/10">

        <!-- ==============================================
             ICON
        =============================================== -->

        <div

        class="mx-auto
        w-24
        h-24
        rounded-full
        bg-blue-500/20
        flex
        items-center
        justify-center
        mb-6">

            <i

            class="ri-shield-check-line
            text-5xl
            text-blue-400">

            </i>

        </div>

        <!-- ==============================================
             TITLE
        =============================================== -->

        <h2

        class="text-3xl
        md:text-4xl
        font-bold">

            Secure Voting Mode

        </h2>

        <!-- ==============================================
             DESCRIPTION
        =============================================== -->

        <p

        class="mt-5
        text-slate-300
        leading-8">

            Click the button below to enter the secure
            fullscreen voting environment.

            <br><br>

            Once voting starts, security monitoring will
            remain active until your vote is submitted.

        </p>

        <!-- ==============================================
             SECURITY POINTS
        =============================================== -->

        <div

        class="mt-8
        space-y-4
        text-left">

            <div class="flex items-start gap-3">

                <i

                class="ri-checkbox-circle-fill
                text-green-400
                mt-1">

                </i>

                <span>

                    Fullscreen mode will be enabled.

                </span>

            </div>

            <div class="flex items-start gap-3">

                <i

                class="ri-checkbox-circle-fill
                text-green-400
                mt-1">

                </i>

                <span>

                    Security monitoring will begin immediately.

                </span>

            </div>

            <div class="flex items-start gap-3">

                <i

                class="ri-error-warning-fill
                text-yellow-400
                mt-1">

                </i>

                <span>

                    Do not exit fullscreen or switch tabs while voting.

                </span>

            </div>

        </div>

        <!-- ==============================================
             BUTTON
        =============================================== -->

        <button

        id="startVotingButton"

        type="button"

        class="mt-10
        w-full
        py-4
        rounded-2xl
        font-semibold
        text-lg
        text-white
        bg-gradient-to-r
        from-blue-600
        to-cyan-500
        hover:scale-[1.02]
        active:scale-95
        transition-all
        duration-300">

            <i

            class="ri-lock-2-line
            mr-2">

            </i>

            Start Secure Voting

        </button>

    </div>

</div>