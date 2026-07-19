/* ==========================================================
   VOTIFY
   Toast Notification
========================================================== */

"use strict";

function showToast(type, title, message) {

    const toast =
    document.getElementById("requestToast");

    if (!toast) return;

    const wrapper =
    document.getElementById("toastIconWrapper");

    const icon =
    document.getElementById("toastIcon");

    const toastTitle =
    document.getElementById("toastTitle");

    const toastMessage =
    document.getElementById("toastMessage");

    /* Reset */

    wrapper.className =
    "w-14 h-14 rounded-2xl flex items-center justify-center";

    icon.className = "text-3xl";

    /* Success */

    if(type === "success"){

        wrapper.classList.add("bg-green-500/20");

        icon.classList.add(
            "ri-checkbox-circle-fill",
            "text-green-400"
        );

    }

    /* Error */

    else if(type === "error"){

        wrapper.classList.add("bg-red-500/20");

        icon.classList.add(
            "ri-close-circle-fill",
            "text-red-400"
        );

    }

    /* Warning */

    else{

        wrapper.classList.add("bg-yellow-500/20");

        icon.classList.add(
            "ri-error-warning-fill",
            "text-yellow-400"
        );

    }

    toastTitle.textContent = title;

    toastMessage.textContent = message;

    /* Show */

    toast.classList.remove("translate-x-[120%]");

    toast.classList.add("translate-x-0");

    clearTimeout(window.toastTimer);

    window.toastTimer = setTimeout(() => {

        toast.classList.remove("translate-x-0");

        toast.classList.add("translate-x-[120%]");

    }, 3500);

}