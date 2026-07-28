<!-- ==========================================================
     VOTIFY
     Security Warning Modal
     File : components/security_warning_modal.php
========================================================== -->

<div

id="securityWarningModal"

class="

fixed
inset-0
z-[9999]
hidden
items-center
justify-center
bg-black/70
backdrop-blur-sm
px-5">

    <!-- ======================================================
         MODAL
    ======================================================= -->

    <div

    class="

    glass
    w-full
    max-w-md
    rounded-3xl
    border
    border-red-500/20
    shadow-2xl
    overflow-hidden
    animate-[fadeIn_.25s_ease]">

        <!-- ==================================================
             HEADER
        =================================================== -->

        <div

        class="

        px-8
        pt-8
        text-center">

            <!-- Icon -->

            <div

            class="

            w-20
            h-20
            mx-auto
            rounded-full
            bg-red-500/15
            flex
            items-center
            justify-center">

                <i

                class="

                ri-alarm-warning-fill
                text-red-400
                text-5xl">

                </i>

            </div>

            <h2

            class="

            mt-5
            text-2xl
            font-bold">

                Security Warning

            </h2>

            <p

            class="

            mt-3
            text-slate-400
            leading-7">

                A security violation has been detected during voting.

            </p>

        </div>

        <!-- ==================================================
             BODY
        =================================================== -->

        <div

        class="

        px-8
        pt-7
        space-y-5">

            <!-- Reason -->

            <div

            class="

            rounded-2xl
            bg-red-500/10
            border
            border-red-500/20
            p-5">

                <p

                class="

                text-sm
                text-slate-400">

                    Detected Event

                </p>

                <p

                id="securityViolationReason"

                class="

                mt-2
                font-semibold
                text-red-400">

                    Fullscreen Exited

                </p>

            </div>

            <!-- Attempt -->

            <div

            class="

            rounded-2xl
            bg-blue-500/10
            border
            border-blue-500/20
            p-5">

                <p

                class="

                text-sm
                text-slate-400">

                    Warning Count

                </p>

                <p

                class="

                mt-2
                text-lg
                font-bold">

                    <span

                    id="securityAttempt">

                        1

                    </span>

                    /

                    <span>

                        2

                    </span>

                </p>

            </div>

            <!-- Notice -->

            <div

            class="

            rounded-2xl
            bg-yellow-500/10
            border
            border-yellow-500/20
            p-5">

                <div

                class="

                flex
                gap-3">

                    <i

                    class="

                    ri-error-warning-fill
                    text-yellow-400
                    text-xl
                    mt-1">

                    </i>

                    <p

                    class="

                    text-sm
                    text-slate-300
                    leading-6">

                        Returning to Full Screen will allow you to continue voting.

                        <br><br>

                        <strong class="text-red-400">

                            The next security violation will automatically end your voting session.

                        </strong>

                    </p>

                </div>

            </div>

        </div>

        <!-- ==================================================
             BUTTON
        =================================================== -->

        <div

        class="

        p-8">

            <button

            id="resumeVotingButton"

            type="button"

            class="

            btn-primary
            w-full">

                <i class="ri-fullscreen-fill"></i>

                Return to Full Screen

            </button>

        </div>

    </div>

</div>