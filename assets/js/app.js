/* ==========================================================
   VOTIFY
   Main Application Script
   File : assets/js/app.js
========================================================== */

"use strict";

/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    initializeApp();

});


/* ==========================================================
   INITIALIZE APPLICATION
========================================================== */

async function initializeApp() {

    await initializeLoader();

    await initializeComponents();

    initializeUI();

    initializeEffects();

    /* Page Specific */

    initializePageScripts();

}


/* ==========================================================
   COMPONENT LOADER
========================================================== */

async function loadComponent(id, file) {

    const container = document.getElementById(id);

    if (!container) return;

    try {

        const response = await fetch(file);

        if (!response.ok) {

            throw new Error("Unable to load : " + file);

        }

        container.innerHTML = await response.text();

    }

    catch (error) {

        console.error(error);

    }

}


/* ==========================================================
   LOAD COMMON COMPONENTS
========================================================== */

async function initializeComponents() {

    const basePath =
        window.location.pathname.includes("/pages/")
            ? "../../"
            : "";

    await loadComponent(
        "header",
        basePath + "components/header.html"
    );

    await loadComponent(
        "footer",
        basePath + "components/footer.html"
    );

}


/* ==========================================================
   LOADER
========================================================== */

async function initializeLoader() {

    const loaderContainer =
        document.getElementById("loader-container");

    if (!loaderContainer) return;

    const basePath =
        window.location.pathname.includes("/pages/")
            ? "../../"
            : "";

    fetch(basePath + "components/loader.html")

        .then(response => {

            if (!response.ok) {

                throw new Error("Loader not found");

            }

            return response.text();

        })

        .then(html => {

            loaderContainer.innerHTML = html;

            showLoader();

        })

        .catch(error => {

            console.error(error);

        });

}


/* ==========================================================
   SHOW LOADER
========================================================== */

function showLoader() {

    const loader = document.getElementById("loader");

    if (!loader) return;

    const loadingText =
        document.getElementById("loadingText");

    const progressBar =
        document.getElementById("loadingProgress");

    const texts = [

        "Preparing Secure Election...",

        "Loading Secure Components...",

        "Encrypting Session...",

        "Loading Dashboard...",

        "Almost Ready..."

    ];

    let progress = 0;

    let index = 0;

    /* Change Loading Text */

    const textTimer = setInterval(() => {

        if (loadingText) {

            loadingText.innerText = texts[index];

        }

        index++;

        if (index >= texts.length) {

            index = 0;

        }

    }, 700);

    /* Progress */

    const progressTimer = setInterval(() => {

        progress += 5;

        if (progressBar) {

            progressBar.style.width = progress + "%";

        }

        if (progress >= 100) {

            clearInterval(progressTimer);

        }

    }, 90);

    /* Hide Loader */

    setTimeout(() => {

        clearInterval(textTimer);

        clearInterval(progressTimer);

        loader.style.transition =
            "opacity .6s ease";

        loader.style.opacity = "0";

        loader.style.pointerEvents = "none";

        setTimeout(() => {

            loader.remove();

        }, 600);

    }, 2200);

}

/* ==========================================================
   SMOOTH SCROLL
========================================================== */

function initializeSmoothScroll() {

    document.querySelectorAll('a[href^="#"]').forEach(link => {

        link.addEventListener("click", function (e) {

            const targetId = this.getAttribute("href");

            if (targetId === "#") return;

            const target = document.querySelector(targetId);

            if (!target) return;

            e.preventDefault();

            target.scrollIntoView({

                behavior: "smooth",
                block: "start"

            });

        });

    });

}


/* ==========================================================
   SCROLL REVEAL
========================================================== */

function initializeRevealAnimation() {

    const revealElements = document.querySelectorAll(".reveal");

    if (!revealElements.length) return;

    const observer = new IntersectionObserver((entries) => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                entry.target.classList.add("active");

                observer.unobserve(entry.target);

            }

        });

    }, {

        threshold: .15

    });

    revealElements.forEach(element => {

        observer.observe(element);

    });

}


/* ==========================================================
   HERO ANIMATION
========================================================== */

function initializeHeroAnimation() {

    const hero = document.querySelector(".hero");

    if (!hero) return;

    hero.classList.add("fade-up");

}


/* ==========================================================
   FLOATING ELEMENTS
========================================================== */

function initializeFloatingElements() {

    document.querySelectorAll(".float").forEach((element, index) => {

        element.style.animationDelay =
            `${index * .3}s`;

    });

}


/* ==========================================================
   ACTIVE NAVIGATION
========================================================== */

function initializeActiveNavigation() {

    const currentPage =
        window.location.pathname.split("/").pop();

    document.querySelectorAll("nav a").forEach(link => {

        const href = link.getAttribute("href");

        if (!href) return;

        if (href.includes(currentPage)) {

            link.classList.add(

                "text-blue-400",

                "font-semibold"

            );

        }

    });

}


/* ==========================================================
   PAGE ENTRANCE
========================================================== */

function initializePageEntrance() {

    document.body.style.opacity = "0";

    document.body.style.transition =
        "opacity .5s ease";

    requestAnimationFrame(() => {

        document.body.style.opacity = "1";

    });

}


/* ==========================================================
   INITIALIZE UI
========================================================== */

function initializeUI() {

    initializeSmoothScroll();

    initializeRevealAnimation();

    initializeHeroAnimation();

    initializeFloatingElements();

    initializeActiveNavigation();

    initializePageEntrance();

}

/* ==========================================================
   COUNTER ANIMATION
========================================================== */

function initializeCounters() {

    const counters = document.querySelectorAll("[data-count]");

    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {

        entries.forEach(entry => {

            if (!entry.isIntersecting) return;

            const counter = entry.target;

            const target = Number(counter.dataset.count);

            let current = 0;

            const increment = Math.ceil(target / 80);

            const timer = setInterval(() => {

                current += increment;

                if (current >= target) {

                    current = target;

                    clearInterval(timer);

                }

                counter.innerText = current;

            }, 20);

            observer.unobserve(counter);

        });

    }, {

        threshold: .5

    });

    counters.forEach(counter => {

        observer.observe(counter);

    });

}


/* ==========================================================
   BUTTON RIPPLE EFFECT
========================================================== */

function initializeRippleEffect() {

    document.querySelectorAll(".btn-primary").forEach(button => {

        button.addEventListener("click", function (event) {

            const ripple = document.createElement("span");

            ripple.className = "ripple";

            const diameter = Math.max(

                this.clientWidth,

                this.clientHeight

            );

            ripple.style.width = diameter + "px";

            ripple.style.height = diameter + "px";

            ripple.style.left =
                (event.offsetX - diameter / 2) + "px";

            ripple.style.top =
                (event.offsetY - diameter / 2) + "px";

            this.appendChild(ripple);

            setTimeout(() => {

                ripple.remove();

            }, 600);

        });

    });

}


/* ==========================================================
   CARD HOVER EFFECT
========================================================== */

function initializeCardEffects() {

    document.querySelectorAll(".hover-card").forEach(card => {

        card.addEventListener("mousemove", function (event) {

            const rect = this.getBoundingClientRect();

            const x = event.clientX - rect.left;

            const y = event.clientY - rect.top;

            this.style.setProperty("--mouse-x", x + "px");

            this.style.setProperty("--mouse-y", y + "px");

        });

    });

}


/* ==========================================================
   BACK TO TOP
========================================================== */

function initializeBackToTop() {

    const button = document.getElementById("backToTop");

    if (!button) return;

    window.addEventListener("scroll", () => {

        if (window.scrollY > 500) {

            button.classList.remove("hidden");

        }

        else {

            button.classList.add("hidden");

        }

    });

    button.addEventListener("click", () => {

        window.scrollTo({

            top: 0,

            behavior: "smooth"

        });

    });

}


/* ==========================================================
   SCROLL PROGRESS BAR
========================================================== */

function initializeScrollProgress() {

    const progressBar =
        document.getElementById("scrollProgress");

    if (!progressBar) return;

    window.addEventListener("scroll", () => {

        const totalHeight =
            document.documentElement.scrollHeight -
            document.documentElement.clientHeight;

        const progress =
            (window.scrollY / totalHeight) * 100;

        progressBar.style.width =
            progress + "%";

    });

}


/* ==========================================================
   INITIALIZE EFFECTS
========================================================== */

function initializeEffects() {

    initializeCounters();

    initializeRippleEffect();

    initializeCardEffects();

    initializeBackToTop();

    initializeScrollProgress();

}

/* ==========================================================
   DEBOUNCE
========================================================== */

function debounce(callback, delay = 300) {

    let timer;

    return (...args) => {

        clearTimeout(timer);

        timer = setTimeout(() => {

            callback(...args);

        }, delay);

    };

}


/* ==========================================================
   THROTTLE
========================================================== */

function throttle(callback, limit = 200) {

    let waiting = false;

    return (...args) => {

        if (waiting) return;

        callback(...args);

        waiting = true;

        setTimeout(() => {

            waiting = false;

        }, limit);

    };

}


/* ==========================================================
   WINDOW RESIZE
========================================================== */

window.addEventListener(

    "resize",

    debounce(() => {

        console.log("Window Resized");

    }, 250)

);


/* ==========================================================
   WINDOW SCROLL
========================================================== */

window.addEventListener(

    "scroll",

    throttle(() => {

        /* Reserved for future */

    }, 120)

);


/* ==========================================================
   PAGE VISIBILITY
========================================================== */

document.addEventListener(

    "visibilitychange",

    () => {

        if (document.hidden) {

            console.log("Page Hidden");

        }

        else {

            console.log("Page Active");

        }

    }

);


/* ==========================================================
   NETWORK STATUS
========================================================== */

window.addEventListener("offline", () => {

    console.warn("Internet Disconnected");

});

window.addEventListener("online", () => {

    console.log("Internet Connected");

});


/* ==========================================================
   GLOBAL ERROR
========================================================== */

window.addEventListener(

    "error",

    (event) => {

        console.error(

            "Application Error :",

            event.message

        );

    }

);


/* ==========================================================
   GLOBAL UTILITIES
========================================================== */

const App = {

    qs(selector) {

        return document.querySelector(selector);

    },

    qsa(selector) {

        return document.querySelectorAll(selector);

    },

    addClass(element, className) {

        if (element)

            element.classList.add(className);

    },

    removeClass(element, className) {

        if (element)

            element.classList.remove(className);

    },

    toggleClass(element, className) {

        if (element)

            element.classList.toggle(className);

    },

    scrollTop() {

        window.scrollTo({

            top:0,

            behavior:"smooth"

        });

    }

};


/* ==========================================================
   APPLICATION READY
========================================================== */

window.addEventListener("load", () => {

    document.body.classList.add("loaded");

    console.log(

        "%cVOTIFY Ready",

        "color:#3B82F6;font-size:15px;font-weight:bold"

    );

});

/* ==========================================================
   PAGE SPECIFIC INITIALIZER
========================================================== */

function initializePageScripts() {

    const page = window.location.pathname;

    /* Register Page */

    if (page.includes("register")) {

        console.log("Register Page Loaded");

    }

}