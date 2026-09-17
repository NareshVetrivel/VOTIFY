<!-- ==========================================================
     VOTIFY
     VOTER EDIT MODAL
     File : components/voter_modal.php
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
        z-[999]
        px-4
    "
>

    <!-- ======================================================
         MODAL CONTAINER
    ====================================================== -->

    <div
        class="
            glass
            rounded-3xl
            w-[92%]
            max-w-2xl
            max-h-[90vh]
            overflow-hidden
            p-6
            relative
            animate-scaleIn
        "
    >

        <!-- ==================================================
             HEADER
        ================================================== -->

        <div
            class="
                flex
                items-center
                justify-between
                mb-8
                flex-shrink-0
            "
        >

            <div class="min-w-0">

                <h2
                    class="
                        text-3xl
                        font-bold
                    "
                >

                    Edit Voter

                </h2>

                <p
                    class="
                        text-slate-400
                        mt-2
                    "
                >

                    Update approved voter information.

                </p>

            </div>


            <!-- ==================================================
                 CLOSE BUTTON
            ================================================== -->

            <button
                type="button"
                id="closeVoterModal"
                class="
                    w-11
                    h-11
                    rounded-xl
                    bg-white/10
                    hover:bg-red-500/20
                    hover:text-red-400
                    transition
                    flex
                    items-center
                    justify-center
                    flex-shrink-0
                "
                aria-label="Close edit voter modal"
            >

                <i
                    class="
                        ri-close-line
                        text-2xl
                    "
                ></i>

            </button>

        </div>


        <!-- ==================================================
             SCROLL AREA

             Scrollbar is now kept inside the modal.
        ================================================== -->

        <div
            class="
                max-h-[calc(90vh-150px)]
                overflow-y-auto
                overflow-x-hidden
                pr-2
            "
        >

            <!-- ==================================================
                 FORM
            ================================================== -->

            <form
                id="voterForm"
                class="space-y-6"
            >

                <!-- ==================================================
                     HIDDEN STUDENT ID
                ================================================== -->

                <input
                    type="hidden"
                    id="voterId"
                >


                <!-- ==================================================
                     FULL NAME
                ================================================== -->

                <div>

                    <label
                        for="fullName"
                        class="
                            block
                            mb-2
                            text-slate-300
                        "
                    >

                        Full Name

                    </label>

                    <input
                        type="text"
                        id="fullName"
                        class="
                            w-full
                            px-5
                            uppercase
                        "
                        autocomplete="off"
                        required
                    >

                </div>


                <!-- ==================================================
                     ADMISSION + EMAIL
                ================================================== -->

                <div
                    class="
                        grid
                        md:grid-cols-2
                        gap-6
                    "
                >

                    <!-- Admission -->

                    <div>

                        <label
                            for="admissionNo"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            Admission No

                        </label>

                        <input
                            type="text"
                            id="admissionNo"
                            readonly
                            class="
                                w-full
                                px-5
                                bg-white/5
                                cursor-not-allowed
                            "
                        >

                    </div>


                    <!-- College Email -->

                    <div>

                        <label
                            for="collegeEmail"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            College Email

                        </label>

                        <input
                            type="email"
                            id="collegeEmail"
                            readonly
                            class="
                                w-full
                                px-5
                                bg-white/5
                                cursor-not-allowed
                            "
                        >

                    </div>

                </div>


                <!-- ==================================================
                     PHONE + GENDER
                ================================================== -->

                <div
                    class="
                        grid
                        md:grid-cols-2
                        gap-6
                    "
                >

                    <!-- Phone -->

                    <div>

                        <label
                            for="phone"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            Phone Number

                        </label>

                        <input
                            type="text"
                            id="phone"
                            class="w-full px-5"
                            maxlength="10"
                            inputmode="numeric"
                            autocomplete="off"
                        >

                    </div>


                    <!-- Gender -->

                    <div>

                        <label
                            for="gender"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            Gender

                        </label>

                        <input
                            type="text"
                            id="gender"
                            readonly
                            class="
                                w-full
                                px-5
                                bg-white/5
                                cursor-not-allowed
                            "
                        >

                    </div>

                </div>


                <!-- ==================================================
                     DEPARTMENT + YEAR
                ================================================== -->

                <div
                    class="
                        grid
                        md:grid-cols-2
                        gap-6
                    "
                >

                    <!-- ==================================================
                         DEPARTMENT
                    ================================================== -->

                    <div>

                        <label
                            for="department"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            Department

                        </label>

                        <input
                            type="text"
                            id="department"
                            readonly
                            class="
                                w-full
                                px-5
                                bg-white/5
                                cursor-not-allowed
                            "
                        >

                    </div>


                    <!-- ==================================================
                         YEAR
                    ================================================== -->

                    <div>

                        <label
                            for="yearDropdownButton"
                            class="
                                block
                                mb-2
                                text-slate-300
                            "
                        >

                            Year

                        </label>


                        <!-- ==================================================
                             HIDDEN YEAR VALUE
                        ================================================== -->

                        <input
                            type="hidden"
                            id="year"
                            name="year"
                            value="I Year"
                        >


                        <!-- ==================================================
                             CUSTOM DROPDOWN WRAPPER
                        ================================================== -->

                        <div
                            id="yearDropdown"
                            class="
                                relative
                                isolate
                            "
                        >

                            <!-- ==================================================
                                 DROPDOWN TRIGGER
                            ================================================== -->

                            <button
                                type="button"
                                id="yearDropdownButton"
                                aria-haspopup="listbox"
                                aria-expanded="false"
                                class="
                                    relative
                                    z-10
                                    w-full
                                    h-14
                                    px-5
                                    rounded-xl
                                    bg-[#171D2B]
                                    border
                                    border-white/10
                                    text-left
                                    text-white
                                    flex
                                    items-center
                                    justify-between
                                    transition-all
                                    duration-200
                                    hover:bg-[#1B2233]
                                    hover:border-blue-500/40
                                    focus:outline-none
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-500/20
                                "
                            >

                                <span
                                    id="yearDropdownText"
                                    class="
                                        font-medium
                                        text-white
                                    "
                                >

                                    I Year

                                </span>


                                <i
                                    id="yearDropdownIcon"
                                    class="
                                        ri-arrow-down-s-line
                                        text-xl
                                        text-slate-400
                                        transition-transform
                                        duration-200
                                    "
                                ></i>

                            </button>


                            <!-- ==================================================
                                 DROPDOWN MENU
                            ================================================== -->

                            <div
                                id="yearDropdownMenu"
                                role="listbox"
                                class="
                                    hidden
                                    absolute
                                    left-0
                                    right-0
                                    top-full
                                    mt-2
                                    p-2
                                    rounded-2xl
                                    bg-[#0F172A]
                                    border
                                    border-blue-500/30
                                    shadow-[0_24px_60px_rgba(0,0,0,0.65)]
                                    ring-1
                                    ring-black/30
                                    z-[2147483646]
                                "
                            >

                                <!-- ==================================================
                                     I YEAR
                                ================================================== -->

                                <button
                                    type="button"
                                    role="option"
                                    class="
                                        yearOption
                                        w-full
                                        px-4
                                        py-3.5
                                        rounded-xl
                                        text-left
                                        text-slate-200
                                        bg-[#182236]
                                        hover:bg-blue-500/20
                                        hover:text-blue-400
                                        transition-all
                                        duration-200
                                    "
                                    data-value="I Year"
                                >

                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                        "
                                    >

                                        <span>

                                            I Year

                                        </span>


                                        <i
                                            class="
                                                ri-check-line
                                                yearCheck
                                                text-blue-400
                                                hidden
                                            "
                                        ></i>

                                    </div>

                                </button>


                                <!-- ==================================================
                                     SPACE
                                ================================================== -->

                                <div
                                    class="h-1"
                                    aria-hidden="true"
                                ></div>


                                <!-- ==================================================
                                     II YEAR
                                ================================================== -->

                                <button
                                    type="button"
                                    role="option"
                                    class="
                                        yearOption
                                        w-full
                                        px-4
                                        py-3.5
                                        rounded-xl
                                        text-left
                                        text-slate-200
                                        bg-[#182236]
                                        hover:bg-blue-500/20
                                        hover:text-blue-400
                                        transition-all
                                        duration-200
                                    "
                                    data-value="II Year"
                                >

                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                        "
                                    >

                                        <span>

                                            II Year

                                        </span>


                                        <i
                                            class="
                                                ri-check-line
                                                yearCheck
                                                text-blue-400
                                                hidden
                                            "
                                        ></i>

                                    </div>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     VOTE STATUS
                ================================================== -->

                <div>

                    <label
                        for="voteStatus"
                        class="
                            block
                            mb-2
                            text-slate-300
                        "
                    >

                        Vote Status

                    </label>

                    <input
                        type="text"
                        id="voteStatus"
                        readonly
                        class="
                            w-full
                            px-5
                            bg-white/5
                            cursor-not-allowed
                        "
                    >

                </div>


                <!-- ==================================================
                     BUTTONS
                ================================================== -->

                <div
                    class="
                        flex
                        justify-end
                        gap-4
                        pt-6
                        pb-2
                    "
                >

                    <!-- Cancel -->

                    <button
                        type="button"
                        id="cancelVoter"
                        class="btn-outline"
                    >

                        Cancel

                    </button>


                    <!-- Save -->

                    <button
                        type="submit"
                        class="
                            btn-primary
                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                ri-save-line
                                mr-2
                            "
                        ></i>

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>