<!-- ==========================================================
CANDIDATE SELECTION SECTION
========================================================== -->

<section id="candidateSelectionSection" class="px-6 py-10">

    <div class="max-w-7xl mx-auto">

        <!-- ==========================================================
        TOPBAR
        ========================================================== -->

        <div
            class="glass
            rounded-3xl
            px-8
            py-6
            mb-10
            flex
            items-center
            justify-between">

            <div>

                <h1
                    class="text-3xl md:text-4xl font-bold">

                    Candidate Selection

                </h1>

                <p
                    class="mt-2 text-slate-400">

                    Welcome,

                    <span
                        class="text-blue-400 font-semibold">

                        <?php
                        echo htmlspecialchars($_SESSION["student_name"]);
                        ?>

                    </span>

                </p>

            </div>

            <button

                id="desktopLogout"

                class="hidden md:flex
                items-center
                gap-3
                px-6
                py-3
                rounded-2xl
                font-semibold
                text-white
                bg-gradient-to-r
                from-red-500
                to-pink-600
                hover:scale-105
                transition-all">

                <i class="ri-logout-box-r-line text-xl"></i>

                Logout

            </button>

        </div>

        <!-- ==========================================================
        LOGOUT MODAL
        ========================================================== -->

        <div

            id="logoutModal"

            class="fixed
            inset-0
            hidden
            items-center
            justify-center
            bg-black/70
            backdrop-blur-sm
            z-[9999]">

            <div
                class="glass
                rounded-3xl
                w-[420px]
                max-w-[90%]
                p-8">

                <div class="text-center">

                    <i
                        class="ri-logout-box-r-line
                        text-6xl
                        text-red-400">
                    </i>

                    <h2
                        class="text-3xl
                        font-bold
                        mt-5">

                        Logout

                    </h2>

                    <p
                        class="mt-3
                        text-slate-400">

                        Are you sure you want to logout?

                    </p>

                    <div
                        class="flex gap-4 mt-8">

                        <button

                            id="cancelLogout"

                            class="flex-1
                            py-4
                            rounded-2xl
                            bg-white/10
                            hover:bg-white/20">

                            Cancel

                        </button>

                        <a

                            href="../../backend/student/logout.php"

                            class="flex-1
                            text-center
                            py-4
                            rounded-2xl
                            font-semibold
                            text-white
                            bg-gradient-to-r
                            from-red-500
                            to-pink-600">

                            Logout

                        </a>

                    </div>

                </div>

            </div>

        </div>

        <!-- ==========================================================
        RULES
        ========================================================== -->

        <div
            class="glass
            rounded-3xl
            p-8
            mb-12">

            <div
                class="flex
                items-center
                gap-5
                mb-8">

                <div
                    class="w-16
                    h-16
                    rounded-2xl
                    bg-blue-500/20
                    flex
                    items-center
                    justify-center">

                    <i
                        class="ri-shield-check-line
                        text-3xl
                        text-blue-400">
                    </i>

                </div>

                <div>

                    <h2
                        class="text-2xl
                        font-bold">

                        Voting Instructions

                    </h2>

                    <p
                        class="text-slate-400 mt-1">

                        Please read all instructions before selecting your candidate.

                    </p>

                </div>

            </div>

            <div
                class="grid
                grid-cols-1
                md:grid-cols-2
                gap-5">

                <div class="flex gap-3">

                    <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

                    <p>Only one candidate can be selected.</p>

                </div>

                <div class="flex gap-3">

                    <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

                    <p>Your vote cannot be changed after confirmation.</p>

                </div>

                <div class="flex gap-3">

                    <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

                    <p>Review every candidate before voting.</p>

                </div>

                <div class="flex gap-3">

                    <i class="ri-checkbox-circle-fill text-green-400 mt-1"></i>

                    <p>NOTA is always available.</p>

                </div>

            </div>

        </div>

        <!-- ==========================================================
        TITLE
        ========================================================== -->

        <div
            class="text-center mb-14">

            <h2
                class="text-5xl font-bold gradient-text">

                Choose Your Candidate

            </h2>

            <p
                class="mt-5
                text-slate-400
                max-w-3xl
                mx-auto">

                Click any candidate card to view details, read the manifesto and cast your vote.

            </p>

        </div>

        <!-- ==========================================================
        CANDIDATE GRID
        ========================================================== -->

        <div

            id="candidateGrid"

            class="grid
            grid-cols-1
            md:grid-cols-2
            xl:grid-cols-3
            gap-10
            justify-items-center">

<!-- ==========================================================
CANDIDATE LOOP
========================================================== -->

<?php

if (
    isset($result) &&
    $result instanceof mysqli_result
) {

    while (
        $candidate = mysqli_fetch_assoc($result)
    ) {

?>

<!-- ==========================================================
CANDIDATE CARD
========================================================== -->

<div
class="candidate-card
w-full
max-w-[340px]
h-[560px]">

    <div class="candidate-inner relative w-full h-full">

        <!-- ==================================================
        FRONT
        =================================================== -->

        <div
class="candidate-front glass absolute inset-0">

            <!-- PHOTO -->

            <div class="candidate-photo relative overflow-hidden">

                <img

                src="../../uploads/candidates/<?php

                echo htmlspecialchars(

                $candidate["photo"]

                );

                ?>"

                alt="<?php

                echo htmlspecialchars(

                $candidate["full_name"]

                );

                ?>"

                class="candidate-image w-full h-full object-cover">

            </div>

            <!-- DETAILS -->

            <div
            class="candidate-details">

                <div
                class="selectedRibbon">

                    ✔ YOUR CHOICE

                </div>

                <h3
                class="candidate-name">

                    <?php

                    echo htmlspecialchars(

                    $candidate["full_name"]

                    );

                    ?>

                </h3>

                <p
                class="candidate-department">

                    <?php

                    echo htmlspecialchars(

                    $candidate["department"]

                    );

                    ?>

                </p>

                <p
                class="candidate-year">

                    <?php

                    echo htmlspecialchars(

                    $candidate["year"]

                    );

                    ?>

                </p>

            </div>

            <!-- FOOTER -->

            <div
            class="candidate-footer">

                <i
                class="ri-cursor-line">
                </i>

                Click To View

            </div>

        </div>

        <!-- ==================================================
        BACK
        =================================================== -->

        <div
class="candidate-back glass absolute inset-0">

            <!-- HEADER -->

            <div
            class="candidate-back-header">

                <div>

                    <h3
                    class="candidate-name">

                        <?php

                        echo htmlspecialchars(

                        $candidate["full_name"]

                        );

                        ?>

                    </h3>

                    <p
                    class="candidate-department">

                        <?php

                        echo htmlspecialchars(

                        $candidate["department"]

                        );

                        ?>

                    </p>

                    <p
                    class="candidate-year">

                        <?php

                        echo htmlspecialchars(

                        $candidate["year"]

                        );

                        ?>

                    </p>

                </div>

                <button

                type="button"

                class="closeCard">

                    <i
                    class="ri-close-line">
                    </i>

                </button>

            </div>

            <!-- MANIFESTO -->

            <div
            class="candidate-manifesto">

                <h4>

                    Manifesto

                </h4>

                <p>

                    <?php

                    echo nl2br(

                        htmlspecialchars(

                            $candidate["manifesto"]

                        )

                    );

                    ?>

                </p>

            </div>

            <!-- BUTTONS -->

            <div
class="candidate-buttons mt-auto">

<button
type="button"
class="selectCandidate btn-primary"

data-id="<?php echo $candidate["id"]; ?>"

data-name="<?php echo htmlspecialchars($candidate["full_name"]); ?>"

data-department="<?php echo htmlspecialchars($candidate["department"]); ?>"

data-year="<?php echo htmlspecialchars($candidate["year"]); ?>"

data-photo="<?php echo htmlspecialchars($candidate["photo"]); ?>"

data-manifesto="<?php echo htmlspecialchars($candidate["manifesto"]); ?>">

                    Select Candidate

                </button>

                <button

                type="button"

                class="deselectCandidate">

                    Deselect Candidate

                </button>

            </div>

        </div>

    </div>

</div>

<?php

}
}
?>

<!-- ==========================================================
NOTA CARD
========================================================== -->

<div
class="candidate-card
w-full
max-w-[340px]
h-[560px]">

    <div
    class="candidate-inner">

        <!-- FRONT -->

        <div
class="candidate-front glass absolute inset-0">

            <div
            class="candidate-photo
            flex
            items-center
            justify-center
            bg-gradient-to-br
            from-red-500/20
            to-pink-500/20">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 128 128"
                    class="w-28 h-28"
                    fill="none">

                    <rect
                        x="38"
                        y="18"
                        width="52"
                        height="92"
                        rx="4"
                        stroke="#FF6B6B"
                        stroke-width="4"/>

                    <line
                        x1="50"
                        y1="38"
                        x2="76"
                        y2="38"
                        stroke="#FF6B6B"
                        stroke-width="4"
                        stroke-linecap="round"/>

                    <circle
                        cx="82"
                        cy="38"
                        r="3"
                        fill="#FF6B6B"/>

                    <line
                        x1="50"
                        y1="56"
                        x2="76"
                        y2="56"
                        stroke="#FF6B6B"
                        stroke-width="4"
                        stroke-linecap="round"/>

                    <circle
                        cx="82"
                        cy="56"
                        r="3"
                        fill="#FF6B6B"/>

                    <line
                        x1="50"
                        y1="74"
                        x2="76"
                        y2="74"
                        stroke="#FF6B6B"
                        stroke-width="4"
                        stroke-linecap="round"/>

                    <circle
                        cx="82"
                        cy="74"
                        r="3"
                        fill="#FF6B6B"/>

                    <line
                        x1="24"
                        y1="28"
                        x2="104"
                        y2="100"
                        stroke="#FF6B6B"
                        stroke-width="10"
                        stroke-linecap="round"/>

                    <line
                        x1="104"
                        y1="28"
                        x2="24"
                        y2="100"
                        stroke="#FF6B6B"
                        stroke-width="10"
                        stroke-linecap="round"/>

                </svg>

            </div>

            <div class="candidate-details">

                <div class="selectedRibbon">

                    ✔ YOUR CHOICE

                </div>

                <h3 class="candidate-name text-red-300">

                    NOTA

                </h3>

                <p class="candidate-department">

                    None Of The Above

                </p>

                <p class="candidate-year text-red-400">

                    Reject All Candidates

                </p>

            </div>

            <div class="candidate-footer">

                <i class="ri-cursor-line"></i>

                Click To View

            </div>

        </div>

        <!-- BACK -->

        <div
class="candidate-back glass absolute inset-0">

            <div
            class="candidate-back-header">

                <div>

                    <h3
                    class="candidate-name text-red-300">

                        NOTA

                    </h3>

                    <p
                    class="candidate-department">

                        None Of The Above

                    </p>

                </div>

                <button
                type="button"
                class="closeCard">

                    <i class="ri-close-line"></i>

                </button>

            </div>

            <div
            class="candidate-manifesto">

                <h4>

                    About NOTA

                </h4>

                <p>

                    Select NOTA if you believe none of the available candidates deserve your vote. Your vote will still be counted as a valid vote.

                </p>

            </div>

            <div
            class="candidate-buttons">

                <button

                type="button"

                class="selectCandidate btn-primary"

data-id="NOTA"

data-name="NOTA"

data-department="None Of The Above"

data-year="Reject All Candidates"

data-photo=""

data-manifesto="Select NOTA if you believe none of the available candidates deserve your vote. Your vote will still be counted as a valid vote.">

                    Select NOTA

                </button>

                <button

                type="button"

                class="deselectCandidate">

                    Deselect Candidate

                </button>

            </div>

        </div>

    </div>

</div>

</div>

<!-- ==========================================================
CONTINUE BUTTON
========================================================== -->

<div
class="mt-14
flex
justify-center">

    <button

    id="continueButton"

    type="button"

    disabled

    class="
    w-full
    md:w-[420px]
    py-4
    rounded-2xl
    font-semibold
    text-lg
    bg-slate-700
    text-slate-400
    cursor-not-allowed">

        Continue

        <i
        class="ri-arrow-right-line ml-2">
        </i>

    </button>

</div>

<!-- ==========================================================
SUCCESS TOAST
========================================================== -->

<div

id="successToast"

class="toast-success">

    <div class="flex items-center gap-3">

        <i
        class="ri-checkbox-circle-fill text-2xl">
        </i>

        <div>

            <p class="font-semibold">

                Candidate Selected

            </p>

            <p class="text-sm">

                You can continue now.

            </p>

        </div>

    </div>

</div>

<!-- ==========================================================
ERROR TOAST
========================================================== -->

<div

id="errorToast"

class="toast-error">

    <div class="flex items-center gap-3">

        <i
        class="ri-error-warning-fill text-2xl">
        </i>

        <div>

            <p class="font-semibold">

                Selection Error

            </p>

            <p
            id="errorToastMessage"
            class="text-sm">

                Only one candidate can be selected.

            </p>

        </div>

    </div>

</div>

</div>

</section>