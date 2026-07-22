/* ==========================================================
   VOTIFY
   Candidate Selection
   File : assets/js/candidate_selection.js
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

        const candidateCards = document.querySelectorAll(

            ".candidate-card"

        );

        const continueButton = document.getElementById(

            "continueButton"

        );

        const successToast = document.getElementById(

            "successToast"

        );

        const errorToast = document.getElementById(

            "errorToast"

        );

        const errorToastMessage = document.getElementById(

            "errorToastMessage"

        );

        const logoutModal = document.getElementById(

            "logoutModal"

        );

        const desktopLogout = document.getElementById(

            "desktopLogout"

        );

        const cancelLogout = document.getElementById(

            "cancelLogout"

        );

        /* ==================================================
           VARIABLES
        ================================================== */

        let selectedCandidateId = null;

        let selectedCandidateName = "";

        let activeCard = null;

        /* ==================================================
           SUCCESS TOAST
        ================================================== */

        function showSuccessToast(

            title,

            message

        ){

            successToast.querySelector(

                "p"

            ).textContent = title;

            successToast.querySelectorAll(

                "p"

            )[1].textContent = message;

            successToast.classList.remove(

                "translate-x-[140%]"

            );

            successToast.classList.add(

                "translate-x-0"

            );

            setTimeout(

                () => {

                    successToast.classList.remove(

                        "translate-x-0"

                    );

                    successToast.classList.add(

                        "translate-x-[140%]"

                    );

                },

                2500

            );

        }

        /* ==================================================
           ERROR TOAST
        ================================================== */

        function showErrorToast(

            message

        ){

            errorToastMessage.textContent = message;

            errorToast.classList.remove(

                "translate-x-[140%]"

            );

            errorToast.classList.add(

                "translate-x-0"

            );

            setTimeout(

                () => {

                    errorToast.classList.remove(

                        "translate-x-0"

                    );

                    errorToast.classList.add(

                        "translate-x-[140%]"

                    );

                },

                3000

            );

        }

        /* ==================================================
           ENABLE CONTINUE
        ================================================== */

        function enableContinueButton(){

            continueButton.disabled = false;

            continueButton.classList.remove(

                "cursor-not-allowed",

                "bg-slate-700",

                "text-slate-400"

            );

        }

        /* ==================================================
           DISABLE CONTINUE
        ================================================== */

        function disableContinueButton(){

            continueButton.disabled = true;

            continueButton.classList.add(

                "cursor-not-allowed",

                "bg-slate-700",

                "text-slate-400"

            );

        }

        disableContinueButton();

        /* ==================================================
           UTILITY
        ================================================== */

        function getRibbon(

            card

        ){

            return card.querySelector(

                ".selectedRibbon"

            );

        }

        function getInner(

            card

        ){

            return card.querySelector(

                ".candidate-inner"

            );

        }

        function flipOpen(

            card

        ){

            getInner(

                card

            ).classList.add(

                "flipped"

            );

        }

        function flipClose(

            card

        ){

            getInner(

                card

            ).classList.remove(

                "flipped"

            );

        }

                /* ==================================================
           PART 2
           CARD OPEN / CLOSE (3D FLIP)
        ================================================== */

        candidateCards.forEach(

            (card) => {

                const front = card.querySelector(

                    ".candidate-front"

                );

                const closeButton = card.querySelector(

                    ".closeCard"

                );

                /* ==========================================
                   OPEN CARD
                ========================================== */

                front.addEventListener(

                    "click",

                    () => {

                        /* Don't open disabled cards */

                        if(

                            card.classList.contains(

                                "disabled"

                            )

                        ){

                            return;

                        }

                        /* Close previously opened card */

                        if(

                            activeCard &&

                            activeCard !== card

                        ){

                            flipClose(

                                activeCard

                            );

                        }

                        flipOpen(

                            card

                        );

                        activeCard = card;

                    }

                );

                /* ==========================================
                   CLOSE CARD
                ========================================== */

                closeButton.addEventListener(

                    "click",

                    () => {

                        /* Selected card should remain locked */

                        if(

                            card.classList.contains(

                                "selected"

                            )

                        ){

                            return;

                        }

                        flipClose(

                            card

                        );

                        activeCard = null;

                    }

                );

            }

        );

        /* ==================================================
           ESC KEY CLOSE CARD
        ================================================== */

        document.addEventListener(

            "keydown",

            (event) => {

                if(

                    event.key !== "Escape"

                ){

                    return;

                }

                if(

                    activeCard &&

                    !activeCard.classList.contains(

                        "selected"

                    )

                ){

                    flipClose(

                        activeCard

                    );

                    activeCard = null;

                }

            }

        );

                /* ==================================================
           PART 3
           SELECT / DESELECT CANDIDATE
        ================================================== */

        document.querySelectorAll(

            ".selectCandidate"

        ).forEach(

            (button) => {

                button.addEventListener(

                    "click",

                    () => {

                        const card = button.closest(

                            ".candidate-card"

                        );

                        /* Already Selected */

                        if(

                            selectedCandidateId !== null

                        ){

                            showErrorToast(

                                "Please deselect your current candidate first."

                            );

                            return;

                        }

                        /* Store Selection */

                        selectedCandidateId =

                            button.dataset.id;

                        selectedCandidateName =

                            button.dataset.name;

                        /* Card State */

                        card.classList.add(

                            "selected"

                        );

                        /* Ribbon */

                        const ribbon = getRibbon(

                            card

                        );

                        if(

                            ribbon

                        ){

                            ribbon.classList.remove(

                                "hidden"

                            );

                        }

                        /* Show Deselect Button */

                        const deselectButton = card.querySelector(

                            ".deselectCandidate"

                        );

                        if(

                            deselectButton

                        ){

                            deselectButton.classList.remove(

                                "hidden"

                            );

                        }

/* Disable Select Button */

button.disabled = true;

button.style.cursor = "not-allowed";

button.classList.add(
    "opacity-60"
);

                        /* Close Card */

                        flipClose(

                            card

                        );

                        activeCard = null;

                        /* Disable Other Cards */

                        candidateCards.forEach(

                            (otherCard) => {

                                if(

                                    otherCard !== card

                                ){

                                    otherCard.classList.add(

                                        "disabled"

                                    );

                                }

                            }

                        );

                        enableContinueButton();

                        showSuccessToast(

                            "Candidate Selected",

                            selectedCandidateName + " selected successfully."

                        );

                    }

                );

            }

        );

        /* ==================================================
           DESELECT
        ================================================== */

        document.querySelectorAll(

            ".deselectCandidate"

        ).forEach(

            (button) => {

                button.addEventListener(

                    "click",

                    () => {

                        const card = button.closest(

                            ".candidate-card"

                        );

                        selectedCandidateId = null;

                        selectedCandidateName = "";

                        card.classList.remove(

                            "selected"

                        );

                        const ribbon = getRibbon(

                            card

                        );

                        if(

                            ribbon

                        ){

                            ribbon.classList.add(

                                "hidden"

                            );

                        }

                        button.classList.add(

                            "hidden"

                        );

                        /* Enable Select Button Again */

const selectButton = card.querySelector(
    ".selectCandidate"
);

selectButton.disabled = false;

selectButton.style.cursor = "pointer";

selectButton.classList.remove(
    "opacity-60"
);

                        candidateCards.forEach(

                            (otherCard) => {

                                otherCard.classList.remove(

                                    "disabled"

                                );

                            }

                        );

                        flipClose(

                            card

                        );

                        activeCard = null;

                        disableContinueButton();

                        showSuccessToast(

                            "Selection Removed",

                            "Please choose another candidate."

                        );

                    }

                );

            }

        );

        /* ==================================================
           REOPEN SELECTED CARD
        ================================================== */

        candidateCards.forEach(

            (card) => {

                card.addEventListener(

                    "click",

                    (event) => {

                        if(

                            !card.classList.contains(

                                "selected"

                            )

                        ){

                            return;

                        }

                        if(

                            event.target.closest(

                                ".deselectCandidate"

                            )

                        ){

                            return;

                        }

                        flipOpen(

                            card

                        );

                        activeCard = card;

                    }

                );

            }

        );

                /* ==================================================
           PART 4
           CONTINUE BUTTON
        ================================================== */

        continueButton.addEventListener(

            "click",

            () => {

                /* ==========================================
                   VALIDATION
                ========================================== */

                if(

                    selectedCandidateId === null

                ){

                    showErrorToast(

                        "Please select one candidate."

                    );

                    return;

                }

                /* ==========================================
                   SAVE SESSION
                ========================================== */

                sessionStorage.setItem(

                    "selectedCandidateId",

                    selectedCandidateId

                );

                sessionStorage.setItem(

                    "selectedCandidateName",

                    selectedCandidateName

                );

                /* ==========================================
                   BUTTON LOADING
                ========================================== */

                continueButton.disabled = true;

                continueButton.innerHTML = `

                    <i class="ri-loader-4-line animate-spin"></i>

                    Processing...

                `;

                /* ==========================================
                   REDIRECT
                ========================================== */

                setTimeout(

                    () => {

                        window.location.href =

                        "candidate_confirmation.php";

                    },

                    700

                );

            }

        );

                /* ==================================================
           PART 5
           LOGOUT MODAL
        ================================================== */

        if(

            desktopLogout &&

            logoutModal

        ){

            desktopLogout.addEventListener(

                "click",

                () => {

                    logoutModal.classList.remove(

                        "hidden"

                    );

                    logoutModal.classList.add(

                        "flex"

                    );

                }

            );

        }

        if(

            cancelLogout &&

            logoutModal

        ){

            cancelLogout.addEventListener(

                "click",

                () => {

                    logoutModal.classList.remove(

                        "flex"

                    );

                    logoutModal.classList.add(

                        "hidden"

                    );

                }

            );

        }

        /* ==================================================
           CLICK OUTSIDE MODAL
        ================================================== */

        if(

            logoutModal

        ){

            logoutModal.addEventListener(

                "click",

                (event) => {

                    if(

                        event.target === logoutModal

                    ){

                        logoutModal.classList.remove(

                            "flex"

                        );

                        logoutModal.classList.add(

                            "hidden"

                        );

                    }

                }

            );

        }

        /* ==================================================
           ESC KEY
        ================================================== */

        document.addEventListener(

            "keydown",

            (event) => {

                if(

                    event.key !== "Escape"

                ){

                    return;

                }

                /* Close Logout */

                if(

                    logoutModal &&

                    logoutModal.classList.contains(

                        "flex"

                    )

                ){

                    logoutModal.classList.remove(

                        "flex"

                    );

                    logoutModal.classList.add(

                        "hidden"

                    );

                }

                /* Close Open Card */

                if(

                    activeCard &&

                    !activeCard.classList.contains(

                        "selected"

                    )

                ){

                    flipClose(

                        activeCard

                    );

                    activeCard = null;

                }

            }

        );

        /* ==================================================
           INITIAL CLEANUP
        ================================================== */

        candidateCards.forEach(

            (card) => {

                card.classList.remove(

                    "selected",

                    "disabled"

                );

                flipClose(

                    card

                );

                const ribbon = getRibbon(

                    card

                );

                if(

                    ribbon

                ){

                    ribbon.classList.add(

                        "hidden"

                    );

                }

                const deselectButton = card.querySelector(

                    ".deselectCandidate"

                );

                if(

                    deselectButton

                ){

                    deselectButton.classList.add(

                        "hidden"

                    );

                }

            }

        );

        selectedCandidateId = null;

        selectedCandidateName = "";

        activeCard = null;

        disableContinueButton();

    }

);

/* ==========================================================
   END OF FILE
========================================================== */