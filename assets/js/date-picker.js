/* ==========================================================
   VOTIFY
   Reusable DOB Date Picker
   File : assets/js/date-picker.js

   Features:
   - Tailwind CSS only
   - Year -> Month -> Date flow
   - Minimum age: 18
   - Maximum age: 25
   - Automatically adapts every year
   - Mobile + Desktop responsive
   - Keeps original input name/value for backend
   - No external date-picker library
========================================================== */

(function () {

    "use strict";


    /* ======================================================
       CONFIGURATION
    ====================================================== */

    const MIN_AGE = 18;
    const MAX_AGE = 25;


    /* ======================================================
       MONTHS
    ====================================================== */

    const MONTHS = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ];


    /* ======================================================
       WEEKDAYS
    ====================================================== */

    const WEEKDAYS = [
        "Su",
        "Mo",
        "Tu",
        "We",
        "Th",
        "Fr",
        "Sa"
    ];


    /* ======================================================
       DATE HELPERS
    ====================================================== */

    function pad(value) {

        return String(value).padStart(2, "0");

    }


    function createDate(year, month, day) {

        return new Date(
            year,
            month,
            day,
            12,
            0,
            0,
            0
        );

    }


    function getToday() {

        const now = new Date();

        return createDate(
            now.getFullYear(),
            now.getMonth(),
            now.getDate()
        );

    }


    /* ======================================================
       FORMAT BACKEND
       YYYY-MM-DD
    ====================================================== */

    function formatISO(date) {

        return (
            date.getFullYear() +
            "-" +
            pad(date.getMonth() + 1) +
            "-" +
            pad(date.getDate())
        );

    }


    /* ======================================================
       FORMAT USER DISPLAY
       DD-MM-YYYY
    ====================================================== */

    function formatDisplay(date) {

        return (
            pad(date.getDate()) +
            "-" +
            pad(date.getMonth() + 1) +
            "-" +
            date.getFullYear()
        );

    }


    /* ======================================================
       PARSE ISO DATE
    ====================================================== */

    function parseISO(value) {

        if (
            !value ||
            !/^\d{4}-\d{2}-\d{2}$/.test(value)
        ) {

            return null;

        }


        const parts = value.split("-");

        const year = Number(parts[0]);
        const month = Number(parts[1]) - 1;
        const day = Number(parts[2]);


        const date = createDate(
            year,
            month,
            day
        );


        /* Prevent invalid date conversion */

        if (
            date.getFullYear() !== year ||
            date.getMonth() !== month ||
            date.getDate() !== day
        ) {

            return null;

        }


        return date;

    }


    /* ======================================================
       SAME DATE CHECK
    ====================================================== */

    function isSameDate(first, second) {

        if (!first || !second) {

            return false;

        }


        return (
            first.getFullYear() === second.getFullYear() &&
            first.getMonth() === second.getMonth() &&
            first.getDate() === second.getDate()
        );

    }


    /* ======================================================
       AGE RANGE
    ====================================================== */

    function getDateRange() {

        const today = getToday();


        /*
         * Example:
         *
         * Today: 20-09-2026
         *
         * Minimum age: 18
         * Maximum age: 25
         *
         * Valid DOB:
         *
         * 20-09-2001
         *       to
         * 20-09-2008
         *
         * Automatically updates every year.
         */

        const minDate = createDate(
            today.getFullYear() - MAX_AGE,
            today.getMonth(),
            today.getDate()
        );


        const maxDate = createDate(
            today.getFullYear() - MIN_AGE,
            today.getMonth(),
            today.getDate()
        );


        return {
            minDate,
            maxDate
        };

    }


    /* ======================================================
       CHECK DATE ALLOWED
    ====================================================== */

    function isDateAllowed(date, range) {

        return (
            date >= range.minDate &&
            date <= range.maxDate
        );

    }


    /* ======================================================
       CHECK MONTH ALLOWED
    ====================================================== */

    function isMonthAllowed(
        year,
        month,
        range
    ) {

        const firstDate =
            createDate(
                year,
                month,
                1
            );


        const lastDate =
            createDate(
                year,
                month + 1,
                0
            );


        return (
            lastDate >= range.minDate &&
            firstDate <= range.maxDate
        );

    }


    /* ======================================================
       VOTIFY DATE PICKER CLASS
    ====================================================== */

    class VotifyDatePicker {

        constructor(input) {

            this.input = input;

            this.range =
                getDateRange();


            this.selectedDate =
                parseISO(
                    this.input.value
                );


            /*
             * Ignore existing DOB if outside
             * current allowed age range.
             */

            if (
                this.selectedDate &&
                !isDateAllowed(
                    this.selectedDate,
                    this.range
                )
            ) {

                this.selectedDate = null;

            }


            this.viewYear =
                this.selectedDate
                    ? this.selectedDate.getFullYear()
                    : this.range.maxDate.getFullYear();


            this.viewMonth =
                this.selectedDate
                    ? this.selectedDate.getMonth()
                    : this.range.maxDate.getMonth();


            /*
             * Picker flow:
             *
             * year
             *   ↓
             * month
             *   ↓
             * date
             */

            this.step = "year";

            this.isOpen = false;


            this.build();

            this.bindEvents();

            this.syncDisplay();

        }


        /* ==================================================
           BUILD
        ================================================== */

        build() {

            /*
             * Keep original input.
             *
             * Backend still receives:
             *
             * name="dob"
             * value="YYYY-MM-DD"
             */

            this.input.type = "text";

            this.input.readOnly = true;

            this.input.autocomplete = "bday";

            this.input.setAttribute(
                "inputmode",
                "none"
            );


            /* ----------------------------------------------
               WRAPPER
            ---------------------------------------------- */

            this.wrapper =
                document.createElement("div");


            this.wrapper.className =
                "relative w-full";


            this.input.parentNode.insertBefore(
                this.wrapper,
                this.input
            );


            this.wrapper.appendChild(
                this.input
            );


            /* ----------------------------------------------
               INPUT
            ---------------------------------------------- */

            this.input.classList.add(
                "pr-14",
                "cursor-pointer"
            );


            this.input.placeholder =
                "dd-mm-yyyy";


            /* ----------------------------------------------
               CALENDAR BUTTON
            ---------------------------------------------- */

            this.calendarButton =
                document.createElement("button");


            this.calendarButton.type =
                "button";


            this.calendarButton.className = `
                absolute
                right-3
                top-1/2
                -translate-y-1/2
                w-10
                h-10
                flex
                items-center
                justify-center
                rounded-xl
                text-slate-400
                hover:text-blue-400
                hover:bg-blue-500/10
                transition-all
                duration-200
                focus:outline-none
                focus:ring-2
                focus:ring-blue-500/30
            `;


            this.calendarButton.setAttribute(
                "aria-label",
                "Open date picker"
            );


            this.calendarButton.innerHTML =
                '<i class="ri-calendar-line text-xl"></i>';


            this.wrapper.appendChild(
                this.calendarButton
            );


            /* ----------------------------------------------
               PICKER PANEL
            ---------------------------------------------- */

            this.panel =
                document.createElement("div");


            this.panel.className = `
                hidden
                absolute
                z-[100]
                left-0
                right-0
                mt-3
                rounded-2xl
                border
                border-white/10
                bg-[#111827]
                shadow-2xl
                overflow-hidden
                backdrop-blur-xl
            `;


            this.wrapper.appendChild(
                this.panel
            );


            /* ----------------------------------------------
               DISPLAY OVERLAY
            ---------------------------------------------- */

            this.displayOverlay =
                document.createElement("div");


            this.displayOverlay.className = `
                hidden
                absolute
                top-0
                h-14
                flex
                items-center
                pointer-events-none
                text-white
                rounded-xl
                font-normal
            `;


            this.wrapper.appendChild(
                this.displayOverlay
            );


            /*
             * IMPORTANT
             *
             * The original input may have different
             * left/right padding depending on the page.
             *
             * Example:
             *
             * Login:
             *   pl-12 pr-14
             *
             * Registration:
             *   px-5 + pr-14
             *
             * Therefore we do NOT hard-code:
             *
             * left: 0
             * left: 12
             *
             * Instead, use the actual computed input
             * padding so the visible DOB text always
             * starts exactly where the input text starts.
             */

            this.syncOverlayPosition();

        }


        /* ==================================================
           SYNC OVERLAY POSITION
        ================================================== */

        syncOverlayPosition() {

            if (!this.input || !this.displayOverlay) {

                return;

            }


            const styles =
                window.getComputedStyle(
                    this.input
                );


            const paddingLeft =
                styles.paddingLeft;


            const paddingRight =
                styles.paddingRight;


            this.displayOverlay.style.left =
                paddingLeft;


            this.displayOverlay.style.right =
                paddingRight;


            this.displayOverlay.style.paddingLeft =
                "0";


            this.displayOverlay.style.paddingRight =
                "0";

        }


        /* ==================================================
           EVENTS
        ================================================== */

        bindEvents() {

            /* ----------------------------------------------
               INPUT CLICK
            ---------------------------------------------- */

            this.input.addEventListener(
                "click",
                (event) => {

                    event.preventDefault();
                    event.stopPropagation();

                    this.open();

                }
            );


            /* ----------------------------------------------
               CALENDAR BUTTON
            ---------------------------------------------- */

            this.calendarButton.addEventListener(
                "click",
                (event) => {

                    event.preventDefault();
                    event.stopPropagation();

                    this.open();

                }
            );


            /* ----------------------------------------------
               PANEL CLICK PROTECTION
            ---------------------------------------------- */

            this.panel.addEventListener(
                "click",
                (event) => {

                    event.stopPropagation();

                }
            );


            /* ----------------------------------------------
               OUTSIDE CLICK
            ---------------------------------------------- */

            document.addEventListener(
                "click",
                (event) => {

                    if (
                        this.isOpen &&
                        !this.wrapper.contains(
                            event.target
                        )
                    ) {

                        this.close();

                    }

                }
            );


            /* ----------------------------------------------
               ESCAPE
            ---------------------------------------------- */

            document.addEventListener(
                "keydown",
                (event) => {

                    if (
                        event.key === "Escape" &&
                        this.isOpen
                    ) {

                        event.preventDefault();

                        this.close();

                    }

                }
            );


            /* ----------------------------------------------
               FORM RESET
            ---------------------------------------------- */

            const form =
                this.input.closest("form");


            if (form) {

                form.addEventListener(
                    "reset",
                    () => {

                        setTimeout(
                            () => {

                                this.selectedDate =
                                    null;

                                this.syncDisplay();

                            },
                            0
                        );

                    }
                );

            }


            /* ----------------------------------------------
               WINDOW RESIZE
            ---------------------------------------------- */

            window.addEventListener(
                "resize",
                () => {

                    this.syncOverlayPosition();

                }
            );

        }


        /* ==================================================
           OPEN PICKER
        ================================================== */

        open() {

            this.range =
                getDateRange();


            /*
             * Make sure overlay position is always
             * synchronized before opening.
             */

            this.syncOverlayPosition();


            const current =
                parseISO(
                    this.input.value
                );


            if (
                current &&
                isDateAllowed(
                    current,
                    this.range
                )
            ) {

                this.selectedDate =
                    current;

                this.viewYear =
                    current.getFullYear();

                this.viewMonth =
                    current.getMonth();

            } else {

                this.selectedDate = null;

                this.viewYear =
                    this.range.maxDate.getFullYear();

                this.viewMonth =
                    this.range.maxDate.getMonth();

            }


            /*
             * ALWAYS START WITH YEAR.
             */

            this.step = "year";

            this.isOpen = true;


            this.panel.classList.remove(
                "hidden"
            );


            this.render();

        }


        /* ==================================================
           CLOSE PICKER
        ================================================== */

        close() {

            this.isOpen = false;

            this.panel.classList.add(
                "hidden"
            );

        }


        /* ==================================================
           RENDER
        ================================================== */

        render() {

            this.panel.innerHTML = "";


            if (this.step === "year") {

                this.renderYears();

                return;

            }


            if (this.step === "month") {

                this.renderMonths();

                return;

            }


            this.renderCalendar();

        }


        /* ==================================================
           HEADER
        ================================================== */

        createHeader(
            title,
            subtitle
        ) {

            const header =
                document.createElement("div");


            header.className = `
                px-4
                sm:px-5
                pt-4
                pb-3
                border-b
                border-white/10
            `;


            header.innerHTML = `

                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-[10px]
                                uppercase
                                tracking-[0.18em]
                                text-slate-500
                                font-bold
                            "
                        >
                            Date of Birth
                        </p>


                        <h3
                            class="
                                mt-1
                                text-base
                                sm:text-lg
                                font-bold
                                text-white
                            "
                        >
                            ${title}
                        </h3>

                    </div>


                    <span
                        class="
                            shrink-0
                            px-3
                            py-1
                            rounded-full
                            bg-blue-500/10
                            border
                            border-blue-500/20
                            text-[10px]
                            font-bold
                            text-blue-400
                        "
                    >
                        18–25 yrs
                    </span>

                </div>


                <p
                    class="
                        mt-2
                        text-xs
                        text-slate-500
                    "
                >
                    ${subtitle}
                </p>

            `;


            this.panel.appendChild(
                header
            );

        }


        /* ==================================================
           YEAR SELECTOR
        ================================================== */

        renderYears() {

            const currentYear =
                getToday().getFullYear();


            const minYear =
                currentYear - MAX_AGE;


            const maxYear =
                currentYear - MIN_AGE;


            this.createHeader(
                "Select Year",
                `${minYear} – ${maxYear}`
            );


            const container =
                document.createElement("div");


            container.className = `
                p-4
                sm:p-5
                max-h-64
                overflow-y-auto
            `;


            const grid =
                document.createElement("div");


            grid.className = `
                grid
                grid-cols-3
                sm:grid-cols-4
                gap-2
            `;


            for (
                let year = minYear;
                year <= maxYear;
                year++
            ) {

                const button =
                    document.createElement("button");


                button.type =
                    "button";


                button.textContent =
                    year;


                const selected =
                    this.selectedDate &&
                    this.selectedDate.getFullYear() ===
                    year;


                button.className = `
                    h-11
                    rounded-xl
                    border
                    text-sm
                    font-semibold
                    transition-all
                    duration-200
                    ${
                        selected
                            ? "bg-blue-600 border-blue-500 text-white shadow-lg shadow-blue-600/20"
                            : "bg-white/5 border-white/10 text-slate-300 hover:bg-blue-500/10 hover:border-blue-500/40 hover:text-white"
                    }
                `;


                button.addEventListener(
                    "click",
                    (event) => {

                        event.preventDefault();
                        event.stopPropagation();


                        this.viewYear =
                            year;


                        if (
                            this.selectedDate &&
                            this.selectedDate.getFullYear() ===
                            year
                        ) {

                            this.viewMonth =
                                this.selectedDate.getMonth();

                        } else {

                            this.viewMonth =
                                this.getFirstAllowedMonth(
                                    year
                                );

                        }


                        this.step =
                            "month";


                        this.render();

                    }
                );


                grid.appendChild(
                    button
                );

            }


            container.appendChild(
                grid
            );


            this.panel.appendChild(
                container
            );

        }


        /* ==================================================
           FIND FIRST ALLOWED MONTH
        ================================================== */

        getFirstAllowedMonth(year) {

            for (
                let month = 0;
                month < 12;
                month++
            ) {

                if (
                    isMonthAllowed(
                        year,
                        month,
                        this.range
                    )
                ) {

                    return month;

                }

            }


            return 0;

        }


        /* ==================================================
           MONTH SELECTOR
        ================================================== */

        renderMonths() {

            this.createHeader(
                String(this.viewYear),
                "Choose your birth month"
            );


            const container =
                document.createElement("div");


            container.className = `
                p-4
                sm:p-5
            `;


            const grid =
                document.createElement("div");


            grid.className = `
                grid
                grid-cols-3
                sm:grid-cols-4
                gap-2
            `;


            MONTHS.forEach(
                (month, index) => {

                    const allowed =
                        isMonthAllowed(
                            this.viewYear,
                            index,
                            this.range
                        );


                    const button =
                        document.createElement("button");


                    button.type =
                        "button";


                    button.textContent =
                        month.slice(0, 3);


                    button.disabled =
                        !allowed;


                    const selected =
                        this.selectedDate &&
                        this.selectedDate.getFullYear() ===
                        this.viewYear &&
                        this.selectedDate.getMonth() ===
                        index;


                    button.className = `
                        h-11
                        rounded-xl
                        border
                        text-sm
                        font-semibold
                        transition-all
                        duration-200
                        ${
                            selected
                                ? "bg-blue-600 border-blue-500 text-white shadow-lg shadow-blue-600/20"
                                : allowed
                                    ? "bg-white/5 border-white/10 text-slate-300 hover:bg-blue-500/10 hover:border-blue-500/40 hover:text-white"
                                    : "bg-white/[0.02] border-white/5 text-slate-700 cursor-not-allowed"
                        }
                    `;


                    if (allowed) {

                        button.addEventListener(
                            "click",
                            (event) => {

                                event.preventDefault();
                                event.stopPropagation();


                                this.viewMonth =
                                    index;


                                this.step =
                                    "date";


                                this.render();

                            }
                        );

                    }


                    grid.appendChild(
                        button
                    );

                }
            );


            container.appendChild(
                grid
            );


            const back =
                document.createElement("button");


            back.type =
                "button";


            back.className = `
                mt-4
                w-full
                h-10
                rounded-xl
                border
                border-white/10
                bg-white/5
                text-xs
                font-semibold
                text-slate-400
                hover:text-white
                hover:bg-white/10
                transition
            `;


            back.innerHTML =
                '<i class="ri-arrow-left-line mr-1"></i> Change Year';


            back.addEventListener(
                "click",
                (event) => {

                    event.preventDefault();
                    event.stopPropagation();


                    this.step =
                        "year";


                    this.render();

                }
            );


            container.appendChild(
                back
            );


            this.panel.appendChild(
                container
            );

        }


        /* ==================================================
           DATE CALENDAR
        ================================================== */

        renderCalendar() {

            this.createHeader(
                `${MONTHS[this.viewMonth]} ${this.viewYear}`,
                "Choose your birth date"
            );


            const container =
                document.createElement("div");


            container.className = `
                p-4
                sm:p-5
            `;


            /* ----------------------------------------------
               TOP NAVIGATION
            ---------------------------------------------- */

            const top =
                document.createElement("div");


            top.className = `
                flex
                items-center
                justify-between
                gap-2
                mb-4
            `;


            const back =
                document.createElement("button");


            back.type =
                "button";


            back.className = `
                inline-flex
                items-center
                gap-1
                px-3
                h-9
                rounded-lg
                border
                border-white/10
                bg-white/5
                text-xs
                font-semibold
                text-slate-400
                hover:text-white
                hover:bg-white/10
                transition
            `;


            back.innerHTML =
                '<i class="ri-arrow-left-line"></i> Month';


            back.addEventListener(
                "click",
                (event) => {

                    event.preventDefault();
                    event.stopPropagation();


                    this.step =
                        "month";


                    this.render();

                }
            );


            top.appendChild(
                back
            );


            const label =
                document.createElement("span");


            label.className = `
                text-sm
                font-bold
                text-blue-400
            `;


            label.textContent =
                `${MONTHS[this.viewMonth]} ${this.viewYear}`;


            top.appendChild(
                label
            );


            container.appendChild(
                top
            );


            /* ----------------------------------------------
               WEEKDAYS
            ---------------------------------------------- */

            const weekdayGrid =
                document.createElement("div");


            weekdayGrid.className = `
                grid
                grid-cols-7
                gap-1
                mb-2
            `;


            WEEKDAYS.forEach(
                (day) => {

                    const item =
                        document.createElement("div");


                    item.className = `
                        h-8
                        flex
                        items-center
                        justify-center
                        text-[10px]
                        sm:text-xs
                        font-bold
                        text-slate-500
                    `;


                    item.textContent =
                        day;


                    weekdayGrid.appendChild(
                        item
                    );

                }
            );


            container.appendChild(
                weekdayGrid
            );


            /* ----------------------------------------------
               DATE GRID
            ---------------------------------------------- */

            const dateGrid =
                document.createElement("div");


            dateGrid.className = `
                grid
                grid-cols-7
                gap-1
            `;


            const firstDay =
                createDate(
                    this.viewYear,
                    this.viewMonth,
                    1
                ).getDay();


            const daysInMonth =
                createDate(
                    this.viewYear,
                    this.viewMonth + 1,
                    0
                ).getDate();


            /* ----------------------------------------------
               EMPTY CELLS
            ---------------------------------------------- */

            for (
                let i = 0;
                i < firstDay;
                i++
            ) {

                const empty =
                    document.createElement("div");


                empty.className =
                    "h-9 sm:h-10";


                dateGrid.appendChild(
                    empty
                );

            }


            /* ----------------------------------------------
               ACTUAL DATES
            ---------------------------------------------- */

            for (
                let day = 1;
                day <= daysInMonth;
                day++
            ) {

                const date =
                    createDate(
                        this.viewYear,
                        this.viewMonth,
                        day
                    );


                const allowed =
                    isDateAllowed(
                        date,
                        this.range
                    );


                const selected =
                    isSameDate(
                        date,
                        this.selectedDate
                    );


                const button =
                    document.createElement("button");


                button.type =
                    "button";


                button.textContent =
                    day;


                button.disabled =
                    !allowed;


                button.className = `
                    h-9
                    sm:h-10
                    w-full
                    rounded-lg
                    text-xs
                    sm:text-sm
                    font-semibold
                    transition-all
                    duration-200
                    ${
                        selected
                            ? "bg-blue-600 text-white shadow-lg shadow-blue-600/20"
                            : allowed
                                ? "text-slate-300 hover:bg-blue-500/15 hover:text-white"
                                : "text-slate-700 cursor-not-allowed"
                    }
                `;


                if (allowed) {

                    button.addEventListener(
                        "click",
                        (event) => {

                            event.preventDefault();
                            event.stopPropagation();


                            this.selectDate(
                                date
                            );

                        }
                    );

                }


                dateGrid.appendChild(
                    button
                );

            }


            container.appendChild(
                dateGrid
            );


            /* ----------------------------------------------
               SELECTED DOB PREVIEW
            ---------------------------------------------- */

            const preview =
                document.createElement("div");


            preview.className = `
                mt-4
                flex
                items-center
                justify-between
                gap-3
                px-4
                py-3
                rounded-xl
                bg-blue-500/10
                border
                border-blue-500/20
            `;


            const previewLabel =
                document.createElement("span");


            previewLabel.className = `
                text-xs
                text-slate-400
                font-medium
            `;


            previewLabel.textContent =
                "Selected DOB";


            const previewValue =
                document.createElement("span");


            previewValue.className = `
                text-xs
                sm:text-sm
                font-bold
                text-blue-400
            `;


            previewValue.textContent =
                this.selectedDate
                    ? formatDisplay(
                        this.selectedDate
                    )
                    : "Select a date";


            preview.appendChild(
                previewLabel
            );


            preview.appendChild(
                previewValue
            );


            container.appendChild(
                preview
            );


            this.panel.appendChild(
                container
            );

        }


        /* ==================================================
           SELECT DATE
        ================================================== */

        selectDate(date) {

            if (
                !isDateAllowed(
                    date,
                    this.range
                )
            ) {

                return;

            }


            this.selectedDate =
                date;


            /*
             * Backend value:
             *
             * YYYY-MM-DD
             */

            this.input.value =
                formatISO(date);


            /*
             * User display:
             *
             * DD-MM-YYYY
             */

            this.syncDisplay();


            /*
             * Keep compatibility with
             * register.js / login.js.
             */

            this.input.dispatchEvent(
                new Event(
                    "input",
                    {
                        bubbles: true
                    }
                )
            );


            this.input.dispatchEvent(
                new Event(
                    "change",
                    {
                        bubbles: true
                    }
                )
            );


            this.close();

        }


        /* ==================================================
           DISPLAY SYNC
        ================================================== */

        syncDisplay() {

            /*
             * Keep overlay aligned with the actual
             * input padding.
             */

            this.syncOverlayPosition();


            if (!this.input.value) {

                this.displayOverlay.classList.add(
                    "hidden"
                );


                this.input.classList.remove(
                    "text-transparent",
                    "caret-transparent"
                );


                this.input.placeholder =
                    "dd-mm-yyyy";


                return;

            }


            const date =
                parseISO(
                    this.input.value
                );


            if (!date) {

                this.displayOverlay.classList.add(
                    "hidden"
                );


                this.input.classList.remove(
                    "text-transparent",
                    "caret-transparent"
                );


                this.input.placeholder =
                    "dd-mm-yyyy";


                return;

            }


            this.displayOverlay.textContent =
                formatDisplay(date);


            this.displayOverlay.classList.remove(
                "hidden"
            );


            this.input.classList.add(
                "text-transparent",
                "caret-transparent"
            );


            this.input.placeholder = "";

        }

    }


    /* ======================================================
       INITIALIZE ALL VOTIFY DATE PICKERS
    ====================================================== */

    function initDatePickers() {

        const inputs =
            document.querySelectorAll(
                "[data-votify-date-picker]"
            );


        inputs.forEach(
            (input) => {

                if (
                    input.dataset.datePickerInitialized ===
                    "true"
                ) {

                    return;

                }


                input.dataset.datePickerInitialized =
                    "true";


                new VotifyDatePicker(
                    input
                );

            }
        );

    }


    /* ======================================================
       PUBLIC API
    ====================================================== */

    window.VotifyDatePicker = {

        init: initDatePickers

    };


    /* ======================================================
       AUTO INITIALIZE
    ====================================================== */

    if (
        document.readyState ===
        "loading"
    ) {

        document.addEventListener(
            "DOMContentLoaded",
            initDatePickers
        );

    } else {

        initDatePickers();

    }

})();