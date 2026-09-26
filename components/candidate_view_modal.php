<!-- ==========================================================
     VOTIFY
     Candidate View Modal
     File : components/candidate_view_modal.php
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
        px-3
        sm:px-4
        py-4
        overflow-y-auto
    "
    role="dialog"
    aria-modal="true"
    aria-labelledby="candidateViewModalTitle"
    aria-describedby="candidateViewModalDescription"
>

    <div
        class="
            glass
            w-full
            max-w-3xl
            max-h-[84vh]
            rounded-3xl
            overflow-hidden
            shadow-2xl
            animate-[fadeUp_.25s_ease]
            my-auto
        "
    >

        <!-- =====================================================
             HEADER
        ===================================================== -->

        <div
            class="
                flex
                items-start
                justify-between
                gap-4
                px-5
                sm:px-6
                pt-4
                sm:pt-5
                pb-4
                border-b
                border-white/10
            "
        >

            <div class="min-w-0">

                <h2
                    id="candidateViewModalTitle"
                    class="
                        text-xl
                        sm:text-2xl
                        font-bold
                        text-white
                    "
                >
                    Candidate Details
                </h2>

                <p
                    id="candidateViewModalDescription"
                    class="
                        text-slate-400
                        mt-1
                        text-xs
                        sm:text-sm
                    "
                >
                    View Candidate Information
                </p>

            </div>


            <!-- =================================================
                 TOP-RIGHT CLOSE ICON
            ================================================= -->

            <button
                id="closeCandidateViewModal"
                type="button"
                class="
                    shrink-0
                    w-10
                    h-10
                    sm:w-11
                    sm:h-11
                    rounded-2xl
                    bg-white/10
                    hover:bg-red-500/20
                    hover:text-red-300
                    transition
                    duration-200
                    flex
                    items-center
                    justify-center
                    text-slate-300
                    focus:outline-none
                    focus:ring-2
                    focus:ring-white/20
                "
                title="Close"
                aria-label="Close candidate details"
            >

                <i
                    class="
                        ri-close-line
                        text-2xl
                    "
                    aria-hidden="true"
                ></i>

            </button>

        </div>


        <!-- =====================================================
             SCROLLABLE BODY
        ===================================================== -->

        <div
            class="
                px-5
                sm:px-6
                py-5
                overflow-y-auto
                max-h-[calc(84vh-82px)]
            "
        >

            <!-- =================================================
                 TOP SECTION
            ================================================= -->

            <div
                class="
                    grid
                    grid-cols-1
                    lg:grid-cols-[190px_1fr]
                    gap-5
                    items-start
                "
            >

                <!-- =================================================
                     PHOTO
                ================================================= -->

                <div
                    class="
                        flex
                        justify-center
                        lg:justify-start
                        items-start
                    "
                >

                    <div
                        class="
                            relative
                            w-40
                            h-52
                            sm:w-44
                            sm:h-56
                            rounded-2xl
                            overflow-hidden
                            bg-white/5
                            border
                            border-white/10
                            shadow-xl
                        "
                    >

                        <img
                            id="viewCandidatePhoto"
                            src=""
                            alt="Candidate"
                            class="
                                w-full
                                h-full
                                object-cover
                            "
                        >

                        <!-- PHOTO FALLBACK -->

                        <div
                            id="viewCandidatePhotoFallback"
                            class="
                                hidden
                                absolute
                                inset-0
                                items-center
                                justify-center
                                bg-white/5
                                text-slate-500
                            "
                            aria-hidden="true"
                        >

                            <i
                                class="
                                    ri-user-3-line
                                    text-5xl
                                "
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     DETAILS
                ================================================= -->

                <div
                    class="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        gap-3
                    "
                >

                    <!-- Candidate Name -->

                    <div
                        class="
                            glass
                            rounded-2xl
                            p-3.5
                            sm:p-4
                            border
                            border-white/5
                            min-w-0
                        "
                    >

                        <p
                            class="
                                text-slate-400
                                text-xs
                                sm:text-sm
                            "
                        >
                            Candidate Name
                        </p>

                        <h3
                            id="viewCandidateName"
                            class="
                                text-lg
                                sm:text-xl
                                font-bold
                                mt-1.5
                                break-words
                                text-white
                            "
                        >
                            -
                        </h3>

                    </div>


                    <!-- Admission -->

                    <div
                        class="
                            glass
                            rounded-2xl
                            p-3.5
                            sm:p-4
                            border
                            border-white/5
                            min-w-0
                        "
                    >

                        <p
                            class="
                                text-slate-400
                                text-xs
                                sm:text-sm
                            "
                        >
                            Admission Number
                        </p>

                        <h3
                            id="viewCandidateAdmission"
                            class="
                                text-lg
                                sm:text-xl
                                font-bold
                                mt-1.5
                                break-all
                                text-white
                            "
                        >
                            -
                        </h3>

                    </div>


                    <!-- Department -->

                    <div
                        class="
                            glass
                            rounded-2xl
                            p-3.5
                            sm:p-4
                            border
                            border-white/5
                            min-w-0
                        "
                    >

                        <p
                            class="
                                text-slate-400
                                text-xs
                                sm:text-sm
                            "
                        >
                            Department
                        </p>

                        <h3
                            id="viewCandidateDepartment"
                            class="
                                text-base
                                sm:text-lg
                                font-semibold
                                mt-1.5
                                break-words
                                text-white
                            "
                        >
                            -
                        </h3>

                    </div>


                    <!-- Year -->

                    <div
                        class="
                            glass
                            rounded-2xl
                            p-3.5
                            sm:p-4
                            border
                            border-white/5
                            min-w-0
                        "
                    >

                        <p
                            class="
                                text-slate-400
                                text-xs
                                sm:text-sm
                            "
                        >
                            Year
                        </p>

                        <h3
                            id="viewCandidateYear"
                            class="
                                text-base
                                sm:text-lg
                                font-semibold
                                mt-1.5
                                break-words
                                text-white
                            "
                        >
                            -
                        </h3>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 MANIFESTO
            ================================================= -->

            <div class="mt-5">

                <p
                    class="
                        text-slate-400
                        text-xs
                        sm:text-sm
                        mb-2
                    "
                >
                    Manifesto
                </p>


                <div
                    id="viewCandidateManifesto"
                    class="
                        glass
                        rounded-2xl
                        border
                        border-white/10
                        px-4
                        py-4
                        leading-6
                        text-sm
                        sm:text-base
                        text-white
                        min-h-[80px]
                        max-h-[150px]
                        overflow-y-auto
                        whitespace-pre-wrap
                        break-words
                    "
                >
                    -
                </div>

            </div>

        </div>

    </div>

</div>