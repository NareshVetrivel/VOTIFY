/* ==========================================================
   VOTIFY
   Voting
   File : assets/js/voting.js
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

        const candidateCards =
            document.querySelectorAll(".candidate-card");

        const continueButton =
            document.getElementById("continueButton");

        const successToast =
            document.getElementById("successToast");

        const errorToast =
            document.getElementById("errorToast");

        const errorToastMessage =
            document.getElementById("errorToastMessage");

        const logoutModal =
            document.getElementById("logoutModal");

        const desktopLogout =
            document.getElementById("desktopLogout");

        const cancelLogout =
            document.getElementById("cancelLogout");


        /* ==================================================
           SAFETY CHECK
        ================================================== */

        if (!continueButton) {

            console.error(
                "VOTIFY: Continue button not found."
            );

            return;

        }


        /* ==================================================
           VARIABLES
        ================================================== */

        let selectedCandidate = null;

        let activeCard = null;


        /* ==================================================
           SUCCESS TOAST
        ================================================== */

        function showSuccessToast(
            title,
            message
        ) {

            if (!successToast) {

                return;

            }


            const paragraphs =
                successToast.querySelectorAll("p");


            if (paragraphs[0]) {

                paragraphs[0].textContent =
                    title;

            }


            if (paragraphs[1]) {

                paragraphs[1].textContent =
                    message;

            }


            successToast.classList.remove(
                "translate-x-[140%]"
            );

            successToast.classList.add(
                "translate-x-0"
            );


            setTimeout(() => {

                successToast.classList.remove(
                    "translate-x-0"
                );

                successToast.classList.add(
                    "translate-x-[140%]"
                );

            }, 2500);

        }


        /* ==================================================
           ERROR TOAST
        ================================================== */

        function showErrorToast(message) {

            if (
                !errorToast ||
                !errorToastMessage
            ) {

                return;

            }


            errorToastMessage.textContent =
                message;


            errorToast.classList.remove(
                "translate-x-[140%]"
            );

            errorToast.classList.add(
                "translate-x-0"
            );


            setTimeout(() => {

                errorToast.classList.remove(
                    "translate-x-0"
                );

                errorToast.classList.add(
                    "translate-x-[140%]"
                );

            }, 3000);

        }


        /* ==================================================
           ENABLE CONTINUE BUTTON
        ================================================== */

        function enableContinueButton() {

            continueButton.disabled =
                false;


            continueButton.classList.remove(
                "cursor-not-allowed",
                "bg-slate-700",
                "text-slate-400"
            );

        }


        /* ==================================================
           DISABLE CONTINUE BUTTON
        ================================================== */

        function disableContinueButton() {

            continueButton.disabled =
                true;


            continueButton.classList.add(
                "cursor-not-allowed",
                "bg-slate-700",
                "text-slate-400"
            );

        }


        /* ==================================================
           RESET CONTINUE BUTTON
        ================================================== */

        function resetContinueButton() {

            continueButton.disabled =
                false;


            continueButton.innerHTML = `
                Continue
                <i class="ri-arrow-right-line ml-2"></i>
            `;


            continueButton.classList.remove(
                "cursor-not-allowed",
                "bg-slate-700",
                "text-slate-400"
            );

        }


        /* ==================================================
           UTILITY
        ================================================== */

        function getRibbon(card) {

            return card.querySelector(
                ".selectedRibbon"
            );

        }


        function getInner(card) {

            return card.querySelector(
                ".candidate-inner"
            );

        }


        function flipOpen(card) {

            const inner =
                getInner(card);


            if (!inner) {

                return;

            }


            inner.classList.add(
                "flipped"
            );

        }


        function flipClose(card) {

            const inner =
                getInner(card);


            if (!inner) {

                return;

            }


            inner.classList.remove(
                "flipped"
            );

        }


        /* ==================================================
           CARD OPEN / CLOSE
        ================================================== */

        candidateCards.forEach((card) => {

            const front =
                card.querySelector(
                    ".candidate-front"
                );


            const closeButton =
                card.querySelector(
                    ".closeCard"
                );


            /* ==============================================
               OPEN CARD
            ============================================== */

            if (front) {

                front.addEventListener(
                    "click",
                    () => {

                        if (
                            card.classList.contains(
                                "disabled"
                            )
                        ) {

                            return;

                        }


                        if (
                            activeCard &&
                            activeCard !== card
                        ) {

                            flipClose(
                                activeCard
                            );

                        }


                        flipOpen(card);

                        activeCard =
                            card;

                    }
                );

            }


            /* ==============================================
               CLOSE CARD
            ============================================== */

            if (closeButton) {

                closeButton.addEventListener(
                    "click",
                    (event) => {

                        event.stopPropagation();


                        if (
                            card.classList.contains(
                                "selected"
                            )
                        ) {

                            return;

                        }


                        flipClose(card);

                        activeCard =
                            null;

                    }
                );

            }

        });


        /* ==================================================
           ESC KEY
        ================================================== */

        document.addEventListener(
            "keydown",
            (event) => {

                if (
                    event.key !== "Escape"
                ) {

                    return;

                }


                if (
                    activeCard &&
                    !activeCard.classList.contains(
                        "selected"
                    )
                ) {

                    flipClose(
                        activeCard
                    );

                    activeCard =
                        null;

                }

            }
        );


        /* ==================================================
           SELECT CANDIDATE
        ================================================== */

        document
            .querySelectorAll(".selectCandidate")
            .forEach((button) => {

                button.addEventListener(
                    "click",
                    (event) => {

                        event.stopPropagation();


                        const card =
                            button.closest(
                                ".candidate-card"
                            );


                        if (!card) {

                            return;

                        }


                        /* ==================================
                           ALREADY SELECTED
                        ================================== */

                        if (
                            selectedCandidate !== null
                        ) {

                            showErrorToast(
                                "Please deselect your current candidate first."
                            );

                            return;

                        }


                        /* ==================================
                           TEMPORARY CLIENT DATA
                        ================================== */

                        selectedCandidate = {

                            id:
                                button.dataset.id || "",

                            name:
                                button.dataset.name || "",

                            department:
                                button.dataset.department || "",

                            year:
                                button.dataset.year || "",

                            photo:
                                button.dataset.photo || "",

                            manifesto:
                                button.dataset.manifesto || ""

                        };


                        console.log(
                            "VOTIFY: Candidate selected:",
                            selectedCandidate
                        );


                        /* ==================================
                           SELECTED STATE
                        ================================== */

                        card.classList.add(
                            "selected"
                        );


                        /* ==================================
                           RIBBON
                        ================================== */

                        const ribbon =
                            getRibbon(card);


                        if (ribbon) {

                            ribbon.classList.remove(
                                "hidden"
                            );

                        }


                        /* ==================================
                           DESELECT BUTTON
                        ================================== */

                        const deselectButton =
                            card.querySelector(
                                ".deselectCandidate"
                            );


                        if (deselectButton) {

                            deselectButton.classList.remove(
                                "hidden"
                            );

                        }


                        /* ==================================
                           DISABLE SELECT BUTTON
                        ================================== */

                        button.disabled =
                            true;


                        button.style.cursor =
                            "not-allowed";


                        button.classList.add(
                            "opacity-60"
                        );


                        /* ==================================
                           CLOSE CARD
                        ================================== */

                        flipClose(card);

                        activeCard =
                            null;


                        /* ==================================
                           DISABLE OTHER CARDS
                        ================================== */

                        candidateCards.forEach(
                            (otherCard) => {

                                if (
                                    otherCard !== card
                                ) {

                                    otherCard.classList.add(
                                        "disabled"
                                    );

                                }

                            }
                        );


                        /* ==================================
                           ENABLE CONTINUE
                        ================================== */

                        enableContinueButton();


                        /* ==================================
                           SUCCESS MESSAGE
                        ================================== */

                        showSuccessToast(
                            "Candidate Selected",
                            selectedCandidate.name +
                            " selected successfully."
                        );

                    }
                );

            });


        /* ==================================================
           DESELECT CANDIDATE
        ================================================== */

        document
            .querySelectorAll(".deselectCandidate")
            .forEach((button) => {

                button.addEventListener(
                    "click",
                    (event) => {

                        event.stopPropagation();


                        const card =
                            button.closest(
                                ".candidate-card"
                            );


                        if (!card) {

                            return;

                        }


                        selectedCandidate =
                            null;


                        /* ==================================
                           REMOVE SELECTED STATE
                        ================================== */

                        card.classList.remove(
                            "selected"
                        );


                        /* ==================================
                           HIDE RIBBON
                        ================================== */

                        const ribbon =
                            getRibbon(card);


                        if (ribbon) {

                            ribbon.classList.add(
                                "hidden"
                            );

                        }


                        /* ==================================
                           HIDE DESELECT BUTTON
                        ================================== */

                        button.classList.add(
                            "hidden"
                        );


                        /* ==================================
                           ENABLE SELECT BUTTON
                        ================================== */

                        const selectButton =
                            card.querySelector(
                                ".selectCandidate"
                            );


                        if (selectButton) {

                            selectButton.disabled =
                                false;


                            selectButton.style.cursor =
                                "pointer";


                            selectButton.classList.remove(
                                "opacity-60"
                            );

                        }


                        /* ==================================
                           ENABLE ALL CARDS
                        ================================== */

                        candidateCards.forEach(
                            (otherCard) => {

                                otherCard.classList.remove(
                                    "disabled"
                                );

                            }
                        );


                        flipClose(card);

                        activeCard =
                            null;


                        disableContinueButton();


                        showSuccessToast(
                            "Selection Removed",
                            "Please choose another candidate."
                        );

                    }
                );

            });


        /* ==================================================
           REOPEN SELECTED CARD
        ================================================== */

        candidateCards.forEach((card) => {

            card.addEventListener(
                "click",
                (event) => {

                    if (
                        !card.classList.contains(
                            "selected"
                        )
                    ) {

                        return;

                    }


                    if (
                        event.target.closest(
                            ".deselectCandidate"
                        )
                    ) {

                        return;

                    }


                    flipOpen(card);

                    activeCard =
                        card;

                }
            );

        });


        /* ==================================================
           BUILD CANDIDATE PHOTO URL
           
           IMPORTANT:
           Candidate photos are loaded through the secure
           backend endpoint instead of directly exposing
           the uploads folder.
        ================================================== */

        function getCandidatePhotoUrl(
            candidate
        ) {

            if (!candidate) {

                return "";

            }


            /* ==========================================
               NOTA
            ========================================== */

            if (
                String(candidate.id)
                    .toUpperCase() === "NOTA"
            ) {

                return "../../assets/images/nota.png";

            }


            /* ==========================================
               VALIDATE CANDIDATE ID
            ========================================== */

            const candidateId =
                String(
                    candidate.id || ""
                ).trim();


            if (
                !candidateId ||
                !/^\d+$/.test(candidateId)
            ) {

                console.error(
                    "VOTIFY: Invalid candidate ID for confirmation photo:",
                    candidateId
                );

                return "";

            }


            /* ==========================================
               CONFIRMATION PHOTO ENDPOINT
            ========================================== */

            return (
                "../../backend/candidate-photo.php?id=" +
                encodeURIComponent(
                    candidateId
                )
            );

        }


        /* ==================================================
           SET CONFIRMATION PHOTO
        ================================================== */

        function setConfirmationPhoto(
            candidate
        ) {

            const confirmationPhoto =
                document.getElementById(
                    "confirmationCandidatePhoto"
                );


            if (!confirmationPhoto) {

                console.error(
                    "VOTIFY: Confirmation photo element not found."
                );

                return;

            }


            const photoUrl =
                getCandidatePhotoUrl(
                    candidate
                );


            console.log(
                "VOTIFY: Confirmation photo details:",
                {
                    candidateId:
                        candidate?.id,

                    photoName:
                        candidate?.photo,

                    photoUrl:
                        photoUrl
                }
            );


            /* ==========================================
               CLEAR PREVIOUS EVENTS
            ========================================== */

            confirmationPhoto.onload =
                null;

            confirmationPhoto.onerror =
                null;


            /* ==========================================
               NO PHOTO URL
            ========================================== */

            if (!photoUrl) {

                confirmationPhoto.removeAttribute(
                    "src"
                );


                confirmationPhoto.alt =
                    candidate?.name ||
                    "Selected Candidate";


                return;

            }


            /* ==========================================
               IMAGE LOAD SUCCESS
            ========================================== */

            confirmationPhoto.onload =
                () => {

                    console.log(
                        "VOTIFY: Confirmation photo loaded successfully.",
                        confirmationPhoto.currentSrc ||
                        confirmationPhoto.src
                    );

                };


            /* ==========================================
               IMAGE LOAD ERROR
            ========================================== */

            confirmationPhoto.onerror =
                () => {

                    console.error(
                        "VOTIFY: Confirmation photo failed to load.",
                        {
                            url:
                                confirmationPhoto.src,

                            candidateId:
                                candidate?.id,

                            photo:
                                candidate?.photo
                        }
                    );

                };


            /* ==========================================
               ALT TEXT
            ========================================== */

            confirmationPhoto.alt =
                String(candidate?.id)
                    .toUpperCase() === "NOTA"
                    ? "NOTA"
                    : candidate?.name ||
                      "Selected Candidate";


            /* ==========================================
               CACHE BUSTING
            ========================================== */

            const separator =
                photoUrl.includes("?")
                    ? "&"
                    : "?";


            confirmationPhoto.src =
                photoUrl +
                separator +
                "v=" +
                Date.now();

        }


        /* ==================================================
           GO TO CANDIDATE CONFIRMATION
        ================================================== */

        function goToCandidateConfirmation() {

            if (!selectedCandidate) {

                showErrorToast(
                    "Please select one candidate."
                );

                return;

            }


            const selectionSection =
                document.getElementById(
                    "candidateSelectionSection"
                );


            const confirmationSection =
                document.getElementById(
                    "candidateConfirmationSection"
                );


            if (
                !selectionSection ||
                !confirmationSection
            ) {

                console.error(
                    "VOTIFY: Confirmation sections not found."
                );

                return;

            }


            /* ==========================================
               HIDE SELECTION
            ========================================== */

            selectionSection.classList.add(
                "hidden"
            );


            /* ==========================================
               SHOW CONFIRMATION
            ========================================== */

            confirmationSection.classList.remove(
                "hidden"
            );


            /* ==========================================
               NAME
            ========================================== */

            const confirmationName =
                document.getElementById(
                    "confirmationCandidateName"
                );


            if (confirmationName) {

                confirmationName.textContent =
                    selectedCandidate.name;

            }


            /* ==========================================
               DEPARTMENT
            ========================================== */

            const confirmationDepartment =
                document.getElementById(
                    "confirmationCandidateDepartment"
                );


            if (confirmationDepartment) {

                confirmationDepartment.textContent =
                    selectedCandidate.department;

            }


            /* ==========================================
               YEAR
            ========================================== */

            const confirmationYear =
                document.getElementById(
                    "confirmationCandidateYear"
                );


            if (confirmationYear) {

                confirmationYear.textContent =
                    selectedCandidate.year;

            }


            /* ==========================================
               MANIFESTO
            ========================================== */

            const confirmationManifesto =
                document.getElementById(
                    "confirmationCandidateManifesto"
                );


            if (confirmationManifesto) {

                confirmationManifesto.textContent =
                    selectedCandidate.manifesto;

            }


            /* ==========================================
               MODAL CANDIDATE NAME
            ========================================== */

            const modalCandidateName =
                document.getElementById(
                    "modalCandidateName"
                );


            if (modalCandidateName) {

                modalCandidateName.textContent =
                    selectedCandidate.name;

            }


            /* ==========================================
               CONFIRMATION PHOTO
            ========================================== */

            setConfirmationPhoto(
                selectedCandidate
            );


            /* ==========================================
               RESET CONFIRMATION CHECKBOX
            ========================================== */

            const confirmationCheckbox =
                document.getElementById(
                    "confirmationCheckbox"
                );


            if (confirmationCheckbox) {

                confirmationCheckbox.checked =
                    false;

            }


            /* ==========================================
               CONFIRM BUTTON
            ========================================== */

            const confirmVoteButton =
                document.getElementById(
                    "confirmVoteButton"
                );


            if (confirmVoteButton) {

                confirmVoteButton.disabled =
                    false;


                confirmVoteButton.classList.remove(
                    "cursor-not-allowed",
                    "bg-slate-700",
                    "text-slate-400"
                );


                confirmVoteButton.classList.add(
                    "bg-gradient-to-r",
                    "from-blue-600",
                    "to-cyan-500"
                );

            }


            /* ==========================================
               SCROLL TOP
            ========================================== */

            window.scrollTo({

                top: 0,

                behavior: "smooth"

            });

        }


        /* ==================================================
           CONTINUE BUTTON
        ================================================== */

        continueButton.addEventListener(
            "click",
            () => {

                console.log(
                    "VOTIFY: Continue clicked."
                );


                /* ==========================================
                   VALIDATION
                ========================================== */

                if (
                    selectedCandidate === null
                ) {

                    console.log(
                        "VOTIFY: No candidate selected."
                    );


                    showErrorToast(
                        "Please select one candidate."
                    );


                    return;

                }


                console.log(
                    "VOTIFY: Browser candidate data:",
                    selectedCandidate
                );


                /* ==========================================
                   BUTTON LOADING
                ========================================== */

                continueButton.disabled =
                    true;


                continueButton.innerHTML = `
                    <i class="ri-loader-4-line animate-spin"></i>
                    Processing...
                `;


                /* ==========================================
                   SAVE SELECTION TO PHP SESSION
                ========================================== */

                fetch(
                    "../../backend/student/save_selection.php",
                    {

                        method: "POST",

                        headers: {

                            "Content-Type":
                                "application/x-www-form-urlencoded"

                        },

                        body:
                            "candidate_id=" +
                            encodeURIComponent(
                                selectedCandidate.id
                            )

                    }
                )


                .then(async (response) => {

                    console.log(
                        "VOTIFY: save_selection status:",
                        response.status
                    );


                    const text =
                        await response.text();


                    console.log(
                        "VOTIFY: save_selection response:",
                        text
                    );


                    let data;


                    try {

                        data =
                            JSON.parse(text);

                    }

                    catch (error) {

                        console.error(
                            "VOTIFY: Invalid JSON response.",
                            error
                        );


                        throw new Error(
                            "Invalid server response."
                        );

                    }


                    return data;

                })


                .then((data) => {

                    /* ======================================
                       SERVER SUCCESS
                    ====================================== */

                    if (
                        data &&
                        data.success
                    ) {

                        /* ==================================
                           USE TRUSTED SERVER DATA
                        ================================== */

                        if (
                            data.candidate
                        ) {

                            selectedCandidate = {

                                id:
                                    String(
                                        data.candidate.id ?? ""
                                    ),

                                name:
                                    data.candidate.name ?? "",

                                department:
                                    data.candidate.department ?? "",

                                year:
                                    data.candidate.year ?? "",

                                photo:
                                    data.candidate.photo ?? "",

                                manifesto:
                                    data.candidate.manifesto ?? ""

                            };


                            console.log(
                                "VOTIFY: Trusted candidate data received:",
                                selectedCandidate
                            );

                        }


                        /* ==================================
                           OPEN CONFIRMATION
                        ================================== */

                        goToCandidateConfirmation();

                        return;

                    }


                    /* ======================================
                       SERVER ERROR
                    ====================================== */

                    resetContinueButton();


                    showErrorToast(
                        data?.message ||
                        "Unable to save candidate selection."
                    );

                })


                .catch((error) => {

                    console.error(
                        "VOTIFY: Candidate selection error:",
                        error
                    );


                    resetContinueButton();


                    showErrorToast(

                        error.message ===
                        "Invalid server response."

                            ? "Server returned an invalid response."

                            : "Unable to connect to the server."

                    );

                });

            }
        );


        /* ==================================================
           LOGOUT MODAL
        ================================================== */

        if (
            desktopLogout &&
            logoutModal
        ) {

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


        if (
            cancelLogout &&
            logoutModal
        ) {

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
           CLICK OUTSIDE LOGOUT MODAL
        ================================================== */

        if (logoutModal) {

            logoutModal.addEventListener(
                "click",
                (event) => {

                    if (
                        event.target ===
                        logoutModal
                    ) {

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

                if (
                    event.key !== "Escape"
                ) {

                    return;

                }


                /* ======================================
                   CLOSE LOGOUT
                ====================================== */

                if (
                    logoutModal &&
                    logoutModal.classList.contains(
                        "flex"
                    )
                ) {

                    logoutModal.classList.remove(
                        "flex"
                    );


                    logoutModal.classList.add(
                        "hidden"
                    );

                }


                /* ======================================
                   CLOSE CARD
                ====================================== */

                if (
                    activeCard &&
                    !activeCard.classList.contains(
                        "selected"
                    )
                ) {

                    flipClose(
                        activeCard
                    );


                    activeCard =
                        null;

                }

            }
        );


        /* ==================================================
           INITIAL CLEANUP
        ================================================== */

        candidateCards.forEach((card) => {

            card.classList.remove(
                "selected",
                "disabled"
            );


            flipClose(card);


            const ribbon =
                getRibbon(card);


            if (ribbon) {

                ribbon.classList.add(
                    "hidden"
                );

            }


            const deselectButton =
                card.querySelector(
                    ".deselectCandidate"
                );


            if (deselectButton) {

                deselectButton.classList.add(
                    "hidden"
                );

            }

        });


        selectedCandidate =
            null;


        activeCard =
            null;


        disableContinueButton();

    }
);


/* ==========================================================
   END OF FILE
========================================================== */