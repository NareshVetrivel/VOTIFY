/* ==========================================================
   VOTIFY
   Thank You Page
   File : assets/js/thank_you.js
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    /* ==========================================================
       COUNTDOWN ELEMENT
    ========================================================== */

    const countdownElement = document.getElementById("countdown");

    let seconds = 5;

    if (countdownElement) {

        countdownElement.textContent = seconds;

        const countdownInterval = setInterval(() => {

            seconds--;

            countdownElement.textContent = seconds;

            if (seconds <= 0) {

                clearInterval(countdownInterval);

                window.location.replace(
                    "../../backend/student/post_vote_logout.php"
                );

            }

        }, 1000);

    }

    /* ==========================================================
       PREVENT BACK BUTTON AFTER VOTE
    ========================================================== */

    history.pushState(null, null, location.href);

    window.addEventListener("popstate", () => {

        history.pushState(null, null, location.href);

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