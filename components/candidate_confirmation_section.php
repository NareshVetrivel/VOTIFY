<!-- ==========================================================
CANDIDATE CONFIRMATION SECTION
File : components/candidate_confirmation_section.php
========================================================== -->

<section
    id="candidateConfirmationSection"
    class="hidden px-6 py-10">

    <div class="max-w-7xl mx-auto">

        <!-- ==========================================================
        PAGE TITLE
        ========================================================== -->

        <div class="text-center mb-12">

            <h2
                class="text-5xl font-bold gradient-text">

                Confirm Your Vote

            </h2>

            <p
                class="mt-5
                text-slate-400
                max-w-3xl
                mx-auto">

                Please review your selected candidate carefully before submitting your vote.
                Once confirmed, your vote cannot be changed.

            </p>

        </div>

        <!-- ==========================================================
        CONFIRMATION CARD
        ========================================================== -->

        <div
            class="glass
            rounded-3xl
            overflow-hidden">

            <div
                class="grid
                grid-cols-1
                lg:grid-cols-[360px_1fr]>

                <!-- ==================================================
                LEFT SIDE
                =================================================== -->

                <div
                    class="
                    p-10
                    flex
                    items-center
                    justify-center
                    bg-gradient-to-br
                    from-blue-500/5
                    to-cyan-500/5">

                    <img

                        id="confirmationCandidatePhoto"

                        src=""

                        alt="Selected Candidate"

                        class="
                        w-80
                        h-96
                        rounded-3xl
                        object-cover
                        border-4
                        border-blue-500/30
                        shadow-2xl">

                </div>

                <!-- ==================================================
                RIGHT SIDE
                =================================================== -->

                <div
                    class="p-8
                    flex
                    flex-col">

                    <span
                        class="text-blue-400
                        font-semibold
                        uppercase
                        tracking-widest">

                        Selected Candidate

                    </span>

                    <h3

                        id="confirmationCandidateName"

                        class="
                        text-3xl
                        lg:text-5xl
                        leading-tight
                        font-bold
                        mt-3">

                        Candidate Name

                    </h3>

                    <div
                        class="mt-6
                        space-y-4">

                        <div
                            class="flex
                            items-center
                            gap-3">

                            <i
                                class="ri-building-line
                                text-xl
                                text-blue-400">
                            </i>

                            <span
                                id="confirmationCandidateDepartment"
                                class="text-lg text-slate-300">

                                Department

                            </span>

                        </div>

                        <div
                            class="flex
                            items-center
                            gap-3">

                            <i
                                class="ri-graduation-cap-line
                                text-xl
                                text-blue-400">
                            </i>

                            <span
                                id="confirmationCandidateYear"
                                class="text-lg text-slate-300">

                                Year

                            </span>

                        </div>

                    </div>

                    <!-- Part 2 continues from here -->

                                        <!-- ==================================================
                    MANIFESTO
                    =================================================== -->

                    <div
                        class="mt-8
                        glass
                        rounded-2xl
                        border
                        border-slate-700
                        p-6">

                        <div
                            class="flex
                            items-center
                            gap-3
                            mb-4">

                            <i
                                class="ri-file-list-3-line
                                text-xl
                                text-blue-400">
                            </i>

                            <h4
                                class="text-2xl
                                font-semibold">

                                Manifesto

                            </h4>

                        </div>

                        <div
                            id="confirmationCandidateManifesto"

                            class="
                            text-base
                            text-slate-300
                            leading-8
                            whitespace-pre-line">

                            Candidate manifesto will appear here.

                        </div>

                    </div>

                    <!-- ==================================================
                    WARNING BOX
                    =================================================== -->

                    <div
                        class="
                        mt-8
                        rounded-2xl
                        border
                        border-yellow-500/30
                        bg-yellow-500/10
                        p-5">

                        <div
                            class="flex
                            items-start
                            gap-4">

                            <i
                                class="ri-error-warning-line
                                text-3xl
                                text-yellow-400">
                            </i>

                            <div>

                                <h5
                                    class="font-semibold
                                    text-yellow-300">

                                    Final Confirmation

                                </h5>

                                <p
                                    class="
                                    mt-2
                                    text-sm
                                    text-slate-300
                                    leading-7">

                                    Please verify the candidate details carefully.
                                    Once your vote is submitted, it cannot be edited,
                                    cancelled or changed.

                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- ==================================================
                    CONFIRMATION CHECKBOX
                    =================================================== -->

                    <label
                        class="
                        mt-8
                        flex
                        items-start
                        gap-4
                        cursor-pointer">

                        <input

                            type="checkbox"

                            id="confirmationCheckbox"

                            class="
                            mt-1
                            w-6
                            h-6
                            accent-blue-500">

                        <span
                            class="
                            text-slate-300
                            leading-7">

                            I have reviewed the selected candidate details carefully
                            and understand that my vote is final and cannot be changed
                            after submission.

                        </span>

                    </label>

                    <!-- ==================================================
                    ACTION BUTTONS
                    =================================================== -->

                    <div
                        class="
                        mt-10
                        flex
                        flex-col
                        md:flex-row
                        gap-4">

                        <!-- Part 3 continues from here -->

                                                <!-- ==================================================
                        BACK BUTTON
                        =================================================== -->

                        <button

                            type="button"

                            id="backButton"

                            class="
                            flex-1
                            py-5
                            rounded-2xl
                            font-semibold
                            border
                            border-slate-600
                            hover:bg-slate-700
                            transition-all">

                            <i class="ri-arrow-left-line mr-2"></i>

                            Back

                        </button>

                        <!-- ==================================================
                        CONFIRM BUTTON
                        =================================================== -->

                        <button

                            type="button"

                            id="confirmVoteButton"

                            disabled

                            class="
                            flex-1
                            py-5
                            rounded-2xl
                            font-semibold
                            text-white
                            bg-slate-700
                            text-slate-400
                            cursor-not-allowed
                            transition-all">

                            <i class="ri-shield-check-line mr-2"></i>

                            Confirm Vote

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================================
STUDENT VOTE CONFIRMATION MODAL
========================================================== -->

<?php
require "../../components/student_vote_confirmation_modal.php";
?>