<?php

if (!isset($selectedCandidate)) {
    return;
}

?>

<!-- ==========================================================
STUDENT VOTE CONFIRMATION MODAL
========================================================== -->

<div

id="confirmationModal"

class="fixed
inset-0
hidden
items-center
justify-center
bg-black/75
backdrop-blur-md
z-[9999]
p-4">

    <!-- ======================================================
    MODAL
    ======================================================= -->

    <div

    class="glass
    rounded-2xl
    w-full
    max-w-lg
    overflow-hidden
    zoom-in">

        <!-- ==================================================
        HEADER
        =================================================== -->

        <div
        class="px-6
        pt-6
        text-center">

            <div
            class="w-16
            h-16
            mx-auto
            rounded-full
            bg-gradient-to-br
            from-blue-600
            to-purple-600
            flex
            items-center
            justify-center">

                <i
                class="ri-shield-check-line
                text-3xl
                text-white">
                </i>

            </div>

            <h2
            class="text-2xl
            font-bold
            mt-4">

                Confirm Your Vote

            </h2>

            <p
            class="text-slate-400
            text-sm
            mt-2
            leading-6">

                Please verify your selection before submitting.
                This action cannot be undone.

            </p>

        </div>

        <!-- ==================================================
        SELECTED CANDIDATE
        =================================================== -->

        <div
        class="mx-6
        mt-6
        rounded-xl
        border
        border-white/10
        bg-white/5
        p-4">

            <p
            class="text-xs
            uppercase
            tracking-wider
            text-slate-400">

                Selected Candidate

            </p>

            <h3
            class="mt-2
            text-lg
            font-bold
            text-blue-400">

                <?php echo htmlspecialchars($selectedCandidate["full_name"]); ?>

            </h3>

        </div>

        <!-- ==================================================
        CHECKBOX
        =================================================== -->

        <div
        class="px-6
        mt-6">

<label
class="flex
items-start
gap-4
cursor-pointer
select-none">

    <input
    type="checkbox"
    id="confirmationCheckbox"
    class="sr-only peer">

    <div
    class="w-7
    h-7
    flex
    items-center
    justify-center
    rounded-lg
    border-2
    border-blue-500
    text-transparent
    transition-all
    duration-200
    peer-checked:bg-blue-600
    peer-checked:border-blue-600
    peer-checked:text-white">

        <i class="ri-check-line text-lg"></i>

    </div>

    <p
    class="flex-1
    text-sm
    leading-7
    text-slate-300">

        I understand that this vote is final and cannot be modified after submission.

    </p>

</label>

        </div>

        <!-- ==================================================
        WARNING
        =================================================== -->

        <div
        class="mx-6
        mt-6
        rounded-xl
        border
        border-red-500/20
        bg-red-500/10
        p-4">

            <div
            class="flex
            gap-3">

                <i
                class="ri-error-warning-line
                text-red-400
                text-xl">
                </i>

                <p
                class="text-sm
                text-slate-300
                leading-6">

                    Once submitted, your vote will be securely recorded and cannot be changed.

                </p>

            </div>

        </div>

        <!-- ==================================================
        BUTTONS
        =================================================== -->

        <div
        class="grid
        grid-cols-2
        gap-4
        p-6">

            <button

            id="cancelConfirmation"

            type="button"

            class="btn-outline">

                <i class="ri-close-line"></i>

                Cancel

            </button>

            <button

            id="submitVoteButton"

            type="button"

            disabled

            class="btn-primary
            opacity-50
            cursor-not-allowed">

                <i class="ri-checkbox-circle-line"></i>

                Confirm

            </button>

        </div>

    </div>

</div>