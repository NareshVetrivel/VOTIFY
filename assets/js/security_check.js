/* ==========================================================
   VOTIFY
   Secure Voting Entry
   File : assets/js/security_check.js
========================================================== */

"use strict";

/* ==========================================================
   DOM ELEMENTS
========================================================== */

const agreeCheckbox = document.getElementById(
    "agreeCheckbox"
);

const continueButton = document.getElementById(
    "continueButton"
);

const fullscreenError = document.getElementById(
    "fullscreenError"
);

const customCheckbox = document.getElementById(
    "customCheckbox"
);

const checkboxIcon = document.getElementById(
    "checkboxIcon"
);

/* ==========================================================
   CONFIGURATION
========================================================== */

const VOTING_URL =

"voting.php";

/* ==========================================================
   BUTTON STATE
========================================================== */

let isProcessing = false;

/* ==========================================================
   SHOW ERROR
========================================================== */

function showFullscreenError(){

    if(!fullscreenError){

        return;

    }

    fullscreenError.classList.remove(

        "hidden"

    );

}

/* ==========================================================
   HIDE ERROR
========================================================== */

function hideFullscreenError(){

    if(!fullscreenError){

        return;

    }

    fullscreenError.classList.add(

        "hidden"

    );

}

/* ==========================================================
   UPDATE CUSTOM CHECKBOX
========================================================== */

function updateCustomCheckbox(){

    if(
        agreeCheckbox.checked
    ){

        customCheckbox.classList.remove(
            "border-slate-500",
            "bg-white/5"
        );

        customCheckbox.classList.add(
            "border-blue-500",
            "bg-blue-600",
            "shadow-lg",
            "shadow-blue-500/40",
            "scale-110"
        );

        checkboxIcon.classList.remove(
            "hidden"
        );

    }

    else{

        customCheckbox.classList.remove(
            "border-blue-500",
            "bg-blue-600",
            "shadow-lg",
            "shadow-blue-500/40",
            "scale-110"
        );

        customCheckbox.classList.add(
            "border-slate-500",
            "bg-white/5"
        );

        checkboxIcon.classList.add(
            "hidden"
        );

    }

}

/* ==========================================================
   ENABLE CONTINUE BUTTON
========================================================== */

function enableContinueButton(){

    continueButton.disabled = false;

    continueButton.classList.remove(

        "opacity-50",
        "cursor-not-allowed"

    );

}

/* ==========================================================
   DISABLE CONTINUE BUTTON
========================================================== */

function disableContinueButton(){

    continueButton.disabled = true;

    continueButton.classList.add(

        "opacity-50",
        "cursor-not-allowed"

    );

}

/* ==========================================================
   LOADING STATE
========================================================== */

function showLoadingState(){

    isProcessing = true;

    continueButton.disabled = true;

    continueButton.innerHTML = `

        <i class="ri-loader-4-line animate-spin text-xl"></i>

        Entering Secure Voting Room...

    `;

}

/* ==========================================================
   RESTORE BUTTON
========================================================== */

function restoreButton(){

    isProcessing = false;

    continueButton.innerHTML = `

        <i class="ri-arrow-right-circle-line text-xl"></i>

        Enter Secure Voting Room

    `;

    if(

        agreeCheckbox.checked

    ){

        enableContinueButton();

    }

    else{

        disableContinueButton();

    }

}

/* ==========================================================
   CHECKBOX EVENT
========================================================== */

agreeCheckbox.addEventListener(

    "change",

    function(){

        hideFullscreenError();

        updateCustomCheckbox();

        if(
            agreeCheckbox.checked
        ){

            enableContinueButton();

        }

        else{

            disableContinueButton();

        }

    }

);

/* ==========================================================
   INITIAL STATE
========================================================== */

disableContinueButton();

hideFullscreenError();

updateCustomCheckbox();

/* ==========================================================
   FULLSCREEN SUPPORT
========================================================== */

async function requestSecureFullscreen(){

    if(

        !document.documentElement.requestFullscreen

    ){

        showFullscreenError();

        restoreButton();

        return;

    }

    try{

        await document.documentElement.requestFullscreen();

        window.location.href =

        VOTING_URL;

    }

    catch(error){

        console.error(

            "Fullscreen Error :",

            error

        );

        showFullscreenError();

        restoreButton();

    }

}

/* ==========================================================
   CONTINUE BUTTON
========================================================== */

continueButton.addEventListener(

    "click",

    async function(){

        if(

            isProcessing

        ){

            return;

        }

        hideFullscreenError();

        if(

            !agreeCheckbox.checked

        ){

            return;

        }

        showLoadingState();

        await requestSecureFullscreen();

    }

);

/* ==========================================================
   EXIT FULLSCREEN EVENT
========================================================== */

document.addEventListener(

    "fullscreenchange",

    function(){

        if(

            document.fullscreenElement

        ){

            return;

        }

    }

);

/* ==========================================================
   PAGE VISIBILITY
========================================================== */

document.addEventListener(

    "visibilitychange",

    function(){

        if(

            document.visibilityState ===

            "visible"

        ){

            hideFullscreenError();

        }

    }

);

/* ==========================================================
   INITIALIZE
========================================================== */

document.addEventListener(

    "DOMContentLoaded",

    function(){

        disableContinueButton();

        hideFullscreenError();

        console.log(

            "VOTIFY Secure Voting Entry Initialized"

        );

    }

);