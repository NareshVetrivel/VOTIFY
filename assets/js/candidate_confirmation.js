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

        const modalConfirmationCheckbox = document.getElementById(
            "modalConfirmationCheckbox"
        );

        const submitVoteButton = document.getElementById(

            "submitVoteButton"

        );

if (

    !backButton ||

    !confirmVoteButton ||

    !confirmationModal ||

    !cancelConfirmation ||

    !modalConfirmationCheckbox ||

    !submitVoteButton

){
    return;
}

        /* ==================================================
           BACK BUTTON
        ================================================== */

backButton.addEventListener(

    "click",

    () => {

        closeModal();

        /* Reset Confirmation */

        modalConfirmationCheckbox.checked = false;

        submitVoteButton.disabled = true;

        submitVoteButton.classList.add(

            "opacity-50",

            "cursor-not-allowed"

        );

const continueButton = document.getElementById("continueButton");

if (continueButton) {

    continueButton.disabled = false;

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

        document
            .getElementById("candidateConfirmationSection")
            .classList.add("hidden");

        document
            .getElementById("candidateSelectionSection")
            .classList.remove("hidden");

        window.scrollTo({

            top: 0,

            behavior: "smooth"

        });

    }

);

        /* ==================================================
           OPEN MODAL
        ================================================== */

        confirmVoteButton.addEventListener(

            "click",

            async () => {

                await requestFullscreen();

                if (!document.fullscreenElement) {

                    return;

                }

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
           CHECKBOX ENABLE
        ================================================== */

        modalConfirmationCheckbox.addEventListener(

            "change",

            () => {

                if(

                    modalConfirmationCheckbox.checked

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

                    !modalConfirmationCheckbox.checked

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
