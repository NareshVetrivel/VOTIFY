/* ==========================================================
   VOTIFY
   Candidate Confirmation
   File : assets/js/candidate_confirmation.js
========================================================== */

"use strict";


/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        /* ==================================================
           ELEMENTS
        ================================================== */

        const backButton =
            document.getElementById(
                "backButton"
            );

        const confirmVoteButton =
            document.getElementById(
                "confirmVoteButton"
            );

        const confirmationModal =
            document.getElementById(
                "confirmationModal"
            );

        const cancelConfirmation =
            document.getElementById(
                "cancelConfirmation"
            );

        const modalConfirmationCheckbox =
            document.getElementById(
                "modalConfirmationCheckbox"
            );

        const submitVoteButton =
            document.getElementById(
                "submitVoteButton"
            );


        /* ==================================================
           SAFETY CHECK
        ================================================== */

        if (
            !backButton ||
            !confirmVoteButton ||
            !confirmationModal ||
            !cancelConfirmation ||
            !modalConfirmationCheckbox ||
            !submitVoteButton
        ) {

            console.error(
                "VOTIFY: Candidate confirmation elements not found."
            );

            return;

        }


        /* ==================================================
           OPEN CONFIRMATION MODAL
        ================================================== */

        function openConfirmationModal() {

            confirmationModal.classList.remove(
                "hidden"
            );

            confirmationModal.classList.add(
                "flex"
            );

        }


        /* ==================================================
           CLOSE CONFIRMATION MODAL
        ================================================== */

        function closeModal() {

            confirmationModal.classList.remove(
                "flex"
            );

            confirmationModal.classList.add(
                "hidden"
            );

        }


        /* ==================================================
           RESET MODAL
        ================================================== */

        function resetConfirmationModal() {

            modalConfirmationCheckbox.checked =
                false;

            submitVoteButton.disabled =
                true;

            submitVoteButton.classList.add(
                "opacity-50",
                "cursor-not-allowed"
            );

            submitVoteButton.innerHTML = `
                <i class="ri-check-double-line mr-2"></i>
                Submit Vote
            `;

        }


        /* ==================================================
           BACK BUTTON
        ================================================== */

        backButton.addEventListener(
            "click",
            () => {

                closeModal();

                resetConfirmationModal();


                /* ==========================================
                   RESET CONTINUE BUTTON
                ========================================== */

                const continueButton =
                    document.getElementById(
                        "continueButton"
                    );


                if (continueButton) {

                    continueButton.disabled =
                        false;

                    continueButton.innerHTML = `
                        Continue
                        <i class="ri-arrow-right-line ml-2"></i>
                    `;

                    continueButton.classList.remove(
                        "cursor-not-allowed",
                        "bg-slate-700",
                        "text-slate-400"
                    );

                }


                /* ==========================================
                   SHOW SELECTION SECTION
                ========================================== */

                const confirmationSection =
                    document.getElementById(
                        "candidateConfirmationSection"
                    );

                const selectionSection =
                    document.getElementById(
                        "candidateSelectionSection"
                    );


                if (confirmationSection) {

                    confirmationSection.classList.add(
                        "hidden"
                    );

                }


                if (selectionSection) {

                    selectionSection.classList.remove(
                        "hidden"
                    );

                }


                /* ==========================================
                   SCROLL TOP
                ========================================== */

                window.scrollTo({

                    top: 0,

                    behavior: "smooth"

                });

            }
        );


        /* ==================================================
           OPEN MODAL
           
           Production:
           - Request fullscreen
           - Require fullscreen
           
           Testing:
           - Skip fullscreen requirement
           - Open modal directly
        ================================================== */

        confirmVoteButton.addEventListener(
            "click",
            async () => {

                /*
                    Check whether security guard testing mode
                    is enabled.

                    security_guard.js loads before this file.
                */

                const testingMode =
                    typeof VOTIFY_TESTING_MODE !== "undefined" &&
                    VOTIFY_TESTING_MODE;


                /* ==========================================
                   TESTING MODE
                ========================================== */

                if (testingMode) {

                    console.info(
                        "VOTIFY: Confirmation fullscreen skipped (Testing Mode)."
                    );

                    openConfirmationModal();

                    return;

                }


                /* ==========================================
                   PRODUCTION SECURITY MODE
                ========================================== */

                if (
                    typeof requestFullscreen !==
                    "function"
                ) {

                    console.error(
                        "VOTIFY: requestFullscreen function not available."
                    );

                    return;

                }


                await requestFullscreen();


                if (!document.fullscreenElement) {

                    console.warn(
                        "VOTIFY: Confirmation modal blocked because fullscreen was not entered."
                    );

                    return;

                }


                openConfirmationModal();

            }
        );


        /* ==================================================
           CLOSE MODAL
        ================================================== */

        cancelConfirmation.addEventListener(
            "click",
            () => {

                closeModal();

            }
        );


        /* ==================================================
           OUTSIDE CLICK
        ================================================== */

        confirmationModal.addEventListener(
            "click",
            (event) => {

                if (
                    event.target ===
                    confirmationModal
                ) {

                    closeModal();

                }

            }
        );


        /* ==================================================
           CHECKBOX ENABLE
        ================================================== */

        modalConfirmationCheckbox.addEventListener(
            "change",
            () => {

                if (
                    modalConfirmationCheckbox.checked
                ) {

                    submitVoteButton.disabled =
                        false;

                    submitVoteButton.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

                else {

                    submitVoteButton.disabled =
                        true;

                    submitVoteButton.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );

                }

            }
        );


        /* ==================================================
           SUBMIT VOTE
        ================================================== */

        submitVoteButton.addEventListener(
            "click",
            () => {

                if (
                    !modalConfirmationCheckbox.checked
                ) {

                    return;

                }


                /* ==========================================
                   PREVENT MULTIPLE SUBMISSIONS
                ========================================== */

                submitVoteButton.disabled =
                    true;

                submitVoteButton.innerHTML = `
                    <i class="ri-loader-4-line animate-spin mr-2"></i>
                    Submitting Vote...
                `;


                /* ==========================================
                   SUBMIT TO BACKEND
                ========================================== */

                setTimeout(
                    () => {

                        const form =
                            document.createElement(
                                "form"
                            );


                        form.method =
                            "POST";


                        form.action =
                            "../../backend/student/submit_vote.php";


                        document.body.appendChild(
                            form
                        );


                        form.submit();

                    },
                    800
                );

            }
        );


        /* ==================================================
           INITIAL MODAL STATE
        ================================================== */

        resetConfirmationModal();

    }
);


/* ==========================================================
   END OF FILE
========================================================== */