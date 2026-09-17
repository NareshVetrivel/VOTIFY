/* ==========================================================
   VOTIFY
   Reusable Confirmation Modal
   File : assets/js/confirmation_modal.js
========================================================== */

"use strict";


/* ==========================================================
   GLOBAL STATE
========================================================== */

let confirmationCallback = null;

let confirmationProcessing = false;


/* ==========================================================
   OPEN MODAL
========================================================== */

function openConfirmationModal(options) {

    const modal =
        document.getElementById(
            "confirmationModal"
        );

    const icon =
        document.getElementById(
            "confirmIcon"
        );

    const iconWrapper =
        document.getElementById(
            "confirmIconWrapper"
        );

    const title =
        document.getElementById(
            "confirmTitle"
        );

    const message =
        document.getElementById(
            "confirmMessage"
        );

    const confirmButton =
        document.getElementById(
            "confirmOk"
        );


    if (!modal) {

        console.error(
            "VOTIFY: Confirmation modal not found."
        );

        return;

    }


    /*
     * Reset processing state
     * whenever a new confirmation modal opens.
     */

    confirmationProcessing =
        false;


    /* ------------------------------------------------------
       SET CONTENT
    ------------------------------------------------------ */

    if (title) {

        title.textContent =
            options.title || "Confirm Action";

    }


    if (message) {

        message.textContent =
            options.message || "Are you sure?";

    }


    /* ------------------------------------------------------
       SET ICON
    ------------------------------------------------------ */

    if (icon) {

        icon.className =
            (options.icon || "ri-question-line") +
            " text-5xl";

    }


    /* ------------------------------------------------------
       RESET ICON WRAPPER
    ------------------------------------------------------ */

    if (iconWrapper) {

        iconWrapper.className =
            "mx-auto w-20 h-20 rounded-full flex items-center justify-center";

    }


    /* ------------------------------------------------------
       RESET CONFIRM BUTTON
    ------------------------------------------------------ */

    if (confirmButton) {

        confirmButton.className =
            "py-4 rounded-2xl font-semibold text-white transition-all hover:scale-105";

    }


    /* ======================================================
       ACTION TYPE
    ====================================================== */

    switch (
        options.type
    ) {

        case "approve":

            if (icon) {

                icon.classList.add(
                    "text-green-400"
                );

            }


            if (iconWrapper) {

                iconWrapper.classList.add(
                    "bg-green-500/20"
                );

            }


            if (confirmButton) {

                confirmButton.classList.add(
                    "bg-gradient-to-r",
                    "from-green-500",
                    "to-emerald-600"
                );

            }

        break;


        case "reject":

            if (icon) {

                icon.classList.add(
                    "text-red-400"
                );

            }


            if (iconWrapper) {

                iconWrapper.classList.add(
                    "bg-red-500/20"
                );

            }


            if (confirmButton) {

                confirmButton.classList.add(
                    "bg-gradient-to-r",
                    "from-red-500",
                    "to-pink-600"
                );

            }

        break;


        default:

            if (icon) {

                icon.classList.add(
                    "text-blue-400"
                );

            }


            if (iconWrapper) {

                iconWrapper.classList.add(
                    "bg-blue-500/20"
                );

            }


            if (confirmButton) {

                confirmButton.classList.add(
                    "bg-gradient-to-r",
                    "from-blue-500",
                    "to-indigo-600"
                );

            }

        break;

    }


    /* ------------------------------------------------------
       STORE CALLBACK
    ------------------------------------------------------ */

    confirmationCallback =
        typeof options.onConfirm === "function"
            ? options.onConfirm
            : null;


    /* ------------------------------------------------------
       SHOW MODAL
    ------------------------------------------------------ */

    modal.classList.remove(
        "hidden"
    );

    modal.classList.add(
        "flex"
    );


    /*
     * Make sure the confirm button
     * starts in normal state.
     */

    setConfirmationLoading(
        false
    );

}


/* ==========================================================
   BUTTON LOADING
========================================================== */

function setConfirmationLoading(
    isLoading
) {

    const button =
        document.getElementById(
            "confirmOk"
        );

    const text =
        document.getElementById(
            "confirmButtonText"
        );


    if (!button) {

        return;

    }


    if (isLoading) {

        button.disabled =
            true;

        button.setAttribute(
            "aria-disabled",
            "true"
        );

        button.classList.add(
            "opacity-70",
            "cursor-not-allowed"
        );


        /*
         * Keep the existing confirmation
         * button text container.
         */

        if (text) {

            text.innerHTML = `

                <span class="inline-flex items-center justify-center gap-2">

                    <i
                        class="ri-loader-4-line animate-spin"
                        aria-hidden="true">
                    </i>

                    <span>Processing...</span>

                </span>

            `;

        }

        else {

            button.innerHTML = `

                <span class="inline-flex items-center justify-center gap-2">

                    <i
                        class="ri-loader-4-line animate-spin"
                        aria-hidden="true">
                    </i>

                    <span>Processing...</span>

                </span>

            `;

        }

    }

    else {

        button.disabled =
            false;

        button.removeAttribute(
            "aria-disabled"
        );

        button.classList.remove(
            "opacity-70",
            "cursor-not-allowed"
        );


        if (text) {

            text.innerHTML =
                "Confirm";

        }

    }

}


/* ==========================================================
   CLOSE
========================================================== */

function closeConfirmationModal() {

    const modal =
        document.getElementById(
            "confirmationModal"
        );


    if (!modal) {

        return;

    }


    /*
     * Never close while an action is processing.
     */

    if (confirmationProcessing) {

        return;

    }


    modal.classList.remove(
        "flex"
    );

    modal.classList.add(
        "hidden"
    );


    confirmationCallback =
        null;

}


/* ==========================================================
   CONFIRM ACTION
========================================================== */

async function handleConfirmation() {

    /*
     * No callback means nothing to process.
     */

    if (
        typeof confirmationCallback !==
        "function"
    ) {

        return;

    }


    /*
     * Prevent double click / double submission.
     */

    if (confirmationProcessing) {

        return;

    }


    confirmationProcessing =
        true;


    setConfirmationLoading(
        true
    );


    try {

        /*
         * IMPORTANT:
         *
         * The action callback should return:
         *
         * true  → backend action successful
         *
         * false → backend action failed
         */

        const result =
            await confirmationCallback();


        /* ==================================================
           SUCCESS
        ================================================== */

        if (result === true) {

            /*
             * Action completed successfully.
             *
             * NOW close the confirmation modal.
             */

            confirmationProcessing =
                false;


            const modal =
                document.getElementById(
                    "confirmationModal"
                );


            if (modal) {

                modal.classList.remove(
                    "flex"
                );

                modal.classList.add(
                    "hidden"
                );

            }


            confirmationCallback =
                null;


            return;

        }


        /* ==================================================
           FAILURE
        ================================================== */

        /*
         * Keep modal open when backend action fails.
         *
         * requests.js already shows the error toast.
         */

        confirmationProcessing =
            false;


        setConfirmationLoading(
            false
        );

    }

    catch (error) {

        console.error(
            "VOTIFY Confirmation Error:",
            error
        );


        confirmationProcessing =
            false;


        setConfirmationLoading(
            false
        );

    }

}


/* ==========================================================
   EVENTS
========================================================== */

document.addEventListener(
    "DOMContentLoaded",
    () => {


        /* --------------------------------------------------
           CANCEL BUTTON
        -------------------------------------------------- */

        const cancelButton =
            document.getElementById(
                "confirmCancel"
            );


        if (cancelButton) {

            cancelButton.addEventListener(
                "click",
                () => {

                    /*
                     * Do not allow cancel while
                     * approve/reject is processing.
                     */

                    if (
                        confirmationProcessing
                    ) {

                        return;

                    }


                    closeConfirmationModal();

                }
            );

        }


        /* --------------------------------------------------
           CONFIRM BUTTON
        -------------------------------------------------- */

        const confirmButton =
            document.getElementById(
                "confirmOk"
            );


        if (confirmButton) {

            confirmButton.addEventListener(
                "click",
                handleConfirmation
            );

        }


        /* --------------------------------------------------
           BACKDROP CLICK
        -------------------------------------------------- */

        const modal =
            document.getElementById(
                "confirmationModal"
            );


        if (modal) {

            modal.addEventListener(
                "click",
                event => {

                    /*
                     * Close only when clicking
                     * directly on the backdrop.
                     */

                    if (
                        event.target ===
                        modal
                    ) {

                        if (
                            confirmationProcessing
                        ) {

                            return;

                        }


                        closeConfirmationModal();

                    }

                }
            );

        }

    }
);


/* ==========================================================
   END OF CONFIRMATION MODAL JS
========================================================== */