document.addEventListener('DOMContentLoaded', function () {

    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });


    const menuBtn = document.getElementById('menuBtn');
    const closeBtn = document.getElementById('closeBtn');
    const modalMenu = document.getElementById('modalMenu');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalLinks = document.querySelectorAll('.modal-nav a');

    const openMenu = () => {
        modalMenu.classList.add('active');
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    };

    const closeMenu = () => {
        modalMenu.classList.remove('active');
        modalOverlay.classList.remove('active');
        document.body.style.overflow = 'auto'; 
    };

    if(menuBtn) menuBtn.addEventListener('click', openMenu);
    if(closeBtn) closeBtn.addEventListener('click', closeMenu);
    if(modalOverlay) modalOverlay.addEventListener('click', closeMenu);

    modalLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });


    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            window.open('https://www.facebook.com/profile.php?id=61554345268520', '_blank');
        });
    });


    function initSlider(sliderId, prevId, nextId) {
        const slider = document.getElementById(sliderId);
        const prevBtn = document.getElementById(prevId);
        const nextBtn = document.getElementById(nextId);

        if (!slider || !prevBtn || !nextBtn) return;

        const getScrollAmount = () => {
            const firstCard = slider.querySelector('.col-6, .col-md-4, .col-lg-3');
            if (firstCard) {
                return firstCard.offsetWidth + 24;
            }
            return 300; 
        };

        nextBtn.addEventListener('click', () => {
            slider.scrollLeft += getScrollAmount();
        });

        prevBtn.addEventListener('click', () => {
            slider.scrollLeft -= getScrollAmount();
        });

        slider.addEventListener('scroll', () => {
            if (slider.scrollLeft <= 0) {
                prevBtn.style.opacity = "0.3";
                prevBtn.style.pointerEvents = "none";
            } else {
                prevBtn.style.opacity = "1";
                prevBtn.style.pointerEvents = "auto";
            }

            const maxScroll = slider.scrollWidth - slider.clientWidth;
            if (slider.scrollLeft >= maxScroll - 5) {
                nextBtn.style.opacity = "0.3";
                nextBtn.style.pointerEvents = "none";
            } else {
                nextBtn.style.opacity = "1";
                nextBtn.style.pointerEvents = "auto";
            }
        });

        const maxScrollLoad = slider.scrollWidth - slider.clientWidth;
        if (maxScrollLoad <= 0) {
            nextBtn.style.display = "none";
            prevBtn.style.display = "none";
        } else {
            prevBtn.style.opacity = "0.3";
            prevBtn.style.pointerEvents = "none";
        }
    }


    initSlider('partsSlider', 'prevBtn', 'nextBtn');
    initSlider('transSlider', 'transPrevBtn', 'transNextBtn');
    initSlider('coolSlider', 'coolPrevBtn', 'coolNextBtn');
    initSlider('oilSlider', 'oilPrevBtn', 'oilNextBtn');
    initSlider('timingSlider', 'timingPrevBtn', 'timingNextBtn');
    initSlider('brakeSlider', 'brakePrevBtn', 'brakeNextBtn');
    initSlider('coolExtraSlider', 'coolExtraPrevBtn', 'coolExtraNextBtn');


    const revealOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal');
                observer.unobserve(entry.target);
            }
        });
    }, revealOptions);

    const allCards = document.querySelectorAll('.part-card');
    allCards.forEach(card => {
        revealObserver.observe(card);
    });

});