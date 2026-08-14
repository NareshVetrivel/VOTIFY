/* ==========================================================
   VOTIFY
   Already Voted Page
   File : assets/js/already_voted.js
========================================================== */


document.addEventListener("DOMContentLoaded", () => {


    /* ==========================================================
       SILENT 5 SECOND REDIRECT
    ========================================================== */

    const redirectDelay = 5000;


    setTimeout(() => {


        /*
         * Already Voted page is only a temporary
         * information page.
         *
         * After 5 seconds, return the student
         * to the VOTIFY landing page.
         */

        window.location.replace(
            "../../index.html"
        );


    }, redirectDelay);


    /* ==========================================================
       PREVENT BACK BUTTON
       ----------------------------------------------------------
       Prevent returning to the previous voting/login flow
       through browser history.
    ========================================================== */

    history.pushState(
        null,
        null,
        location.href
    );


    window.addEventListener("popstate", () => {


        history.pushState(
            null,
            null,
            location.href
        );


    });


});


/* ==========================================================
   PREVENT PAGE CACHE
   ----------------------------------------------------------
   If the page is restored from browser back/forward cache,
   reload it so stale page state is not displayed.
========================================================== */

window.addEventListener("pageshow", (event) => {


    if (event.persisted) {

        window.location.reload();

    }


});