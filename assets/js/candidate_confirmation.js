document.addEventListener(

    "DOMContentLoaded",

    () => {

        /* ==================================================
           ELEMENTS
        ================================================== */

        const backButton = document.getElementById(

            "backButton"

        );

        const confirmVoteButton = document.getElementById(

            "confirmVoteButton"

        );

        const confirmationModal = document.getElementById(

            "confirmationModal"

        );

        const cancelConfirmation = document.getElementById(

            "cancelConfirmation"

        );

        const confirmationCheckbox = document.getElementById(

            "confirmationCheckbox"

        );

        const submitVoteButton = document.getElementById(

            "submitVoteButton"

        );

        /* ==================================================
           BACK BUTTON
        ================================================== */

        backButton.addEventListener(

            "click",

            () => {

                window.location.href =

                "candidate_selection.php";

            }

        );

        /* ==================================================
           OPEN MODAL
        ================================================== */

        confirmVoteButton.addEventListener(

            "click",

            () => {

                confirmationModal.classList.remove(

                    "hidden"

                );

                confirmationModal.classList.add(

                    "flex"

                );

            }

        );

        /* ==================================================
           CLOSE MODAL
        ================================================== */

        function closeModal(){

            confirmationModal.classList.remove(

                "flex"

            );

            confirmationModal.classList.add(

                "hidden"

            );

        }

        cancelConfirmation.addEventListener(

            "click",

            closeModal

        );

        /* ==================================================
           OUTSIDE CLICK
        ================================================== */

        confirmationModal.addEventListener(

            "click",

            (event)=>{

                if(

                    event.target === confirmationModal

                ){

                    closeModal();

                }

            }

        );

        /* ==================================================
           ESC KEY
        ================================================== */

        document.addEventListener(

            "keydown",

            (event)=>{

                if(

                    event.key==="Escape"

                ){

                    closeModal();

                }

            }

        );

        /* ==================================================
           CHECKBOX ENABLE
        ================================================== */

        confirmationCheckbox.addEventListener(

            "change",

            () => {

                if(

                    confirmationCheckbox.checked

                ){

                    submitVoteButton.disabled = false;

                    submitVoteButton.classList.remove(

                        "opacity-50",

                        "cursor-not-allowed"

                    );

                }

                else{

                    submitVoteButton.disabled = true;

                    submitVoteButton.classList.add(

                        "opacity-50",

                        "cursor-not-allowed"

                    );

                }

            }

        );

        /* ==================================================
           SUBMIT BUTTON
        ================================================== */

        submitVoteButton.addEventListener(

            "click",

            () => {

                if(

                    !confirmationCheckbox.checked

                ){

                    return;

                }

                /* Disable Multiple Click */

                submitVoteButton.disabled = true;

                submitVoteButton.innerHTML = `

                    <i class="ri-loader-4-line animate-spin"></i>

                    Submitting Vote...

                `;

                /* ==========================================
                   REDIRECT
                   (Backend Part 5)
                ========================================== */

setTimeout(() => {

    const form = document.createElement("form");

    form.method = "POST";

    form.action = "../../backend/student/submit_vote.php";

    document.body.appendChild(form);

    form.submit();

}, 800);

            }

        );

    }

);
