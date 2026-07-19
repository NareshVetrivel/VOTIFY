/* ==========================================================
   VOTIFY
   Reusable Confirmation Modal
========================================================== */

"use strict";

let confirmationCallback = null;

/* ==========================================================
   OPEN MODAL
========================================================== */

function openConfirmationModal(options) {

    const modal =
        document.getElementById("confirmationModal");

    const icon =
        document.getElementById("confirmIcon");

    const iconWrapper =
        document.getElementById("confirmIconWrapper");

    const title =
        document.getElementById("confirmTitle");

    const message =
        document.getElementById("confirmMessage");

    const confirmButton =
        document.getElementById("confirmOk");

    if (!modal) return;

    title.textContent =
        options.title;

    message.textContent =
        options.message;

    icon.className =
        options.icon +
        " text-5xl";

    iconWrapper.className =
        "mx-auto w-20 h-20 rounded-full flex items-center justify-center";

    confirmButton.className =
        "py-4 rounded-2xl font-semibold text-white transition-all hover:scale-105";

    switch (options.type) {

        case "approve":

            icon.classList.add("text-green-400");

            iconWrapper.classList.add("bg-green-500/20");

            confirmButton.classList.add(
                "bg-gradient-to-r",
                "from-green-500",
                "to-emerald-600"
            );

        break;

        case "reject":

            icon.classList.add("text-red-400");

            iconWrapper.classList.add("bg-red-500/20");

            confirmButton.classList.add(
                "bg-gradient-to-r",
                "from-red-500",
                "to-pink-600"
            );

        break;

        default:

            icon.classList.add("text-blue-400");

            iconWrapper.classList.add("bg-blue-500/20");

            confirmButton.classList.add(
                "bg-gradient-to-r",
                "from-blue-500",
                "to-indigo-600"
            );

    }

    confirmationCallback =
        options.onConfirm;

    modal.classList.remove("hidden");

    modal.classList.add("flex");

}

/* ==========================================================
   BUTTON LOADING
========================================================== */

function setConfirmationLoading(isLoading){

    const button =
    document.getElementById("confirmOk");

    const text =
    document.getElementById("confirmButtonText");

    if(!button || !text){

        return;

    }

    if(isLoading){

        button.disabled = true;

        button.classList.add(

            "opacity-70",

            "cursor-not-allowed"

        );

        text.innerHTML =

        `<i class="ri-loader-4-line animate-spin"></i>

        Processing...`;

    }

    else{

        button.disabled = false;

        button.classList.remove(

            "opacity-70",

            "cursor-not-allowed"

        );

        text.innerHTML = "Confirm";

    }

}

/* ==========================================================
   CLOSE
========================================================== */

function closeConfirmationModal() {

    const modal =
        document.getElementById("confirmationModal");

    modal.classList.remove("flex");

    modal.classList.add("hidden");

}

/* ==========================================================
   EVENTS
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    document

    .getElementById("confirmCancel")

    ?.addEventListener("click", () => {

        closeConfirmationModal();

    });

document

.getElementById("confirmOk")

?.addEventListener("click", async () => {

    if (!confirmationCallback){

        return;

    }

    setConfirmationLoading(true);

    try{

        await confirmationCallback();

    }

finally{

    setConfirmationLoading(false);

    confirmationCallback = null;

}

});

    document

    .getElementById("confirmationModal")

    ?.addEventListener("click", e => {

        if (

            e.target.id ===

            "confirmationModal"

        ) {

            closeConfirmationModal();

        }

    });

});