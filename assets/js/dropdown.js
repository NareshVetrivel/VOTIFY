/* ==========================================================
   VOTIFY
   Custom Dropdown
   File : assets/js/dropdown.js
========================================================== */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    initializeDropdowns();

});


/* ==========================================================
   INITIALIZE
========================================================== */

function initializeDropdowns() {

    document
        .querySelectorAll(".custom-dropdown")
        .forEach(dropdown => {

            setupDropdown(dropdown);

        });

}

/* ==========================================================
   SETUP SINGLE DROPDOWN
========================================================== */

function setupDropdown(dropdown) {

    const button =
        dropdown.querySelector(".dropdown-button");

    const menu =
        dropdown.querySelector(".dropdown-menu");

    const items =
        dropdown.querySelectorAll(".dropdown-item");

    const text =
        button.querySelector("span");

    const icon =
        button.querySelector("i");

    const hiddenInput =
        dropdown.querySelector("input[type='hidden']");


    /* ==========================================
       OPEN / CLOSE
    ========================================== */

    button.addEventListener("click", (event) => {

        event.stopPropagation();

        closeAllDropdowns(dropdown);

        menu.classList.toggle("show");

        icon.classList.toggle("rotate-180");

    });


    /* ==========================================
       ITEM SELECT
    ========================================== */

    items.forEach(item => {

        item.addEventListener("click", () => {

            const value =
                item.dataset.value;

            text.innerText = value;

            hiddenInput.value = value;

            menu.classList.remove("show");

            icon.classList.remove("rotate-180");


            /* Selected UI */

            items.forEach(option => {

                option.classList.remove(

                    "selected"

                );

            });

            item.classList.add(

                "selected"

            );

        });

    });

}


/* ==========================================================
   CLOSE OTHER DROPDOWNS
========================================================== */

function closeAllDropdowns(current) {

    document
        .querySelectorAll(".custom-dropdown")
        .forEach(dropdown => {

            if (dropdown === current) return;

            dropdown
                .querySelector(".dropdown-menu")
                .classList.remove("show");

            dropdown
                .querySelector("i")
                .classList.remove("rotate-180");

        });

}


/* ==========================================================
   OUTSIDE CLICK
========================================================== */

document.addEventListener("click", () => {

    closeAllDropdowns(null);

});

/* ==========================================================
   KEYBOARD SUPPORT
========================================================== */

document
.querySelectorAll(".custom-dropdown")
.forEach(dropdown=>{

    const button=
    dropdown.querySelector(".dropdown-button");

    const menu=
    dropdown.querySelector(".dropdown-menu");

    const items=
    dropdown.querySelectorAll(".dropdown-item");

    const hidden=
    dropdown.querySelector("input");

    let index=-1;

    button.addEventListener("keydown",(event)=>{

        switch(event.key){

            case "ArrowDown":

                event.preventDefault();

                if(!menu.classList.contains("show")){

                    button.click();

                }

                index++;

                if(index>=items.length){

                    index=0;

                }

                highlightItem(items,index);

            break;

            case "ArrowUp":

                event.preventDefault();

                if(!menu.classList.contains("show")){

                    button.click();

                }

                index--;

                if(index<0){

                    index=items.length-1;

                }

                highlightItem(items,index);

            break;

            case "Enter":

                event.preventDefault();

                if(index>=0){

                    items[index].click();

                }

            break;

            case "Escape":

                menu.classList.remove("show");

                dropdown
                .querySelector("i")
                .classList.remove("rotate-180");

            break;

            case "Tab":

                menu.classList.remove("show");

                dropdown
                .querySelector("i")
                .classList.remove("rotate-180");

            break;

        }

    });

    hidden.addEventListener("change",()=>{

        dropdown.classList.remove("dropdown-error");

    });

});


/* ==========================================================
   HIGHLIGHT ITEM
========================================================== */

function highlightItem(items,index){

    items.forEach(item=>{

        item.classList.remove("active");

    });

    items[index].classList.add("active");

    items[index].scrollIntoView({

        block:"nearest"

    });

}


/* ==========================================================
   VALIDATION
========================================================== */

function validateDropdown(id,errorId,message){

    const input=
    document.getElementById(id);

    const error=
    document.getElementById(errorId);

    const dropdown=
    input.closest(".custom-dropdown");

    if(input.value===""){

        dropdown.classList.add(

            "dropdown-error"

        );

        error.innerText=message;

        error.classList.remove(

            "hidden"

        );

        return false;

    }

    dropdown.classList.remove(

        "dropdown-error"

    );

    error.classList.add(

        "hidden"

    );

    return true;

}