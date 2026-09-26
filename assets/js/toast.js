/* ==========================================================
   VOTIFY
   Toast Notification
   File : assets/js/toast.js
========================================================== */

"use strict";


/* ==========================================================
   SHOW TOAST
========================================================== */

function showToast(
    type,
    title,
    message
) {

    const toast =
        document.getElementById(
            "requestToast"
        );


    /*
     * Toast element must exist.
     */
    if (!toast) {

        console.error(
            "VOTIFY Toast: #requestToast not found."
        );

        return;

    }


    const wrapper =
        document.getElementById(
            "toastIconWrapper"
        );


    const icon =
        document.getElementById(
            "toastIcon"
        );


    const toastTitle =
        document.getElementById(
            "toastTitle"
        );


    const toastMessage =
        document.getElementById(
            "toastMessage"
        );


    /*
     * Validate required elements.
     */
    if (
        !wrapper ||
        !icon ||
        !toastTitle ||
        !toastMessage
    ) {

        console.error(
            "VOTIFY Toast: Required toast elements are missing."
        );

        return;

    }


    /* ======================================================
       FRONT LAYER
    ====================================================== */

    toast.style.position =
        "fixed";

    toast.style.zIndex =
        "2147483647";

    toast.style.pointerEvents =
        "auto";


    /* ======================================================
       RESET
    ====================================================== */

    wrapper.className =
        "w-14 h-14 rounded-2xl flex items-center justify-center";


    icon.className =
        "text-3xl";


    /* ======================================================
       SUCCESS
    ====================================================== */

    if (
        type === "success"
    ) {

        wrapper.classList.add(
            "bg-green-500/20"
        );


        icon.classList.add(
            "ri-checkbox-circle-fill",
            "text-green-400"
        );

    }


    /* ======================================================
       ERROR
    ====================================================== */

    else if (
        type === "error"
    ) {

        wrapper.classList.add(
            "bg-red-500/20"
        );


        icon.classList.add(
            "ri-close-circle-fill",
            "text-red-400"
        );

    }


    /* ======================================================
       WARNING
    ====================================================== */

    else {

        wrapper.classList.add(
            "bg-yellow-500/20"
        );


        icon.classList.add(
            "ri-error-warning-fill",
            "text-yellow-400"
        );

    }


    /* ======================================================
       CONTENT
    ====================================================== */

    toastTitle.textContent =
        title || "Notification";


    toastMessage.textContent =
        message || "";


    /* ======================================================
       SHOW
    ====================================================== */

    /*
     * Remove Tailwind hidden-position state.
     */
    toast.classList.remove(
        "translate-x-[120%]"
    );


    toast.classList.add(
        "translate-x-0"
    );


    /*
     * Explicit inline transform.
     *
     * This guarantees the toast appears even if
     * Tailwind class processing/cascade behaves differently.
     */
    toast.style.transform =
        "translateX(0)";


    toast.style.opacity =
        "1";


    toast.style.visibility =
        "visible";


    console.log(
        "VOTIFY Toast:",
        type,
        title,
        message
    );


    /* ======================================================
       CLEAR PREVIOUS TIMER
    ====================================================== */

    clearTimeout(
        window.toastTimer
    );


    /* ======================================================
       AUTO HIDE
    ====================================================== */

    window.toastTimer =
        setTimeout(
            () => {

                toast.classList.remove(
                    "translate-x-0"
                );


                toast.classList.add(
                    "translate-x-[120%]"
                );


                toast.style.transform =
                    "translateX(120%)";


                toast.style.opacity =
                    "0";


                toast.style.visibility =
                    "hidden";

            },
            3500
        );

}


/* ==========================================================
   EXPLICIT GLOBAL EXPORT
========================================================== */

/*
 * Make sure requests.js can always access:
 *
 *     window.showToast()
 *
 * regardless of browser/script scope behavior.
 */

window.showToast =
    showToast;


/* ==========================================================
   END OF TOAST.JS
========================================================== */