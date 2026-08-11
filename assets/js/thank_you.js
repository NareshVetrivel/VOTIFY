/* ==========================================================
   VOTIFY
   Thank You Page
   File : assets/js/thank_you.js
========================================================== */


document.addEventListener("DOMContentLoaded", () => {


    /* ==========================================================
       SILENT 5 SECOND LOGOUT + REDIRECT
    ========================================================== */

    const redirectDelay = 5000;


    setTimeout(() => {


        /*
         * IMPORTANT:
         *
         * Do NOT redirect directly to index.html.
         *
         * First call the secure post-vote logout endpoint.
         *
         * The PHP endpoint will:
         *
         * 1. Clear all session data
         * 2. Delete the PHP session cookie
         * 3. Destroy the session
         * 4. Redirect to index.html
         *
         */

        window.location.replace(
            "../../backend/student/post_vote_logout.php?redirect=index"
        );


    }, redirectDelay);


    /* ==========================================================
       PREVENT BACK BUTTON AFTER VOTE
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
========================================================== */

window.addEventListener("pageshow", (event) => {


    if (event.persisted) {

        window.location.reload();

    }


});