
const CONFIG = {
    desktop: {
        startOffset: 200,
        scrollDistance: 700,
        subtextTrigger: 0.5
    },
    mobile: {
        startOffset: 150,
        scrollDistance: 500,
        subtextTrigger: 0.6
    }
};

function isMobile() {
    return window.innerWidth <= 768;
}

function getCurrentConfig() {
    return isMobile() ? CONFIG.mobile : CONFIG.desktop;
}

const scrollHeading = document.getElementById('scrollHeading');
if (scrollHeading) {
    const text = scrollHeading.getAttribute('data-text');

    scrollHeading.innerHTML = '';

    let letterIndex = 0;
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        const span = document.createElement('span');
        
        if (char === ' ') {
            span.className = 'letter space';
            span.innerHTML = '&nbsp;';
        } else {
            span.className = 'letter';
            span.textContent = char;
            span.setAttribute('data-index', letterIndex);
            letterIndex++;
        }
        
        scrollHeading.appendChild(span);
    }

    const letters = document.querySelectorAll('#scrollHeading .letter:not(.space)');
    const totalLetters = letters.length;

    let lastScrollTop = 0;
    const scrollSubtext = document.getElementById('scrollSubtext');
    const textSection = document.querySelector('.textfill-on-scroll');

    function handleTextScroll() {
        const config = getCurrentConfig();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const sectionTop = textSection.offsetTop;
        const windowHeight = window.innerHeight;
        
        const elementInView = sectionTop - windowHeight + config.startOffset;
        
        const startPoint = Math.max(0, elementInView);
        const endPoint = startPoint + config.scrollDistance;
        
        let fillProgress = 0;
        
        if (scrollTop < startPoint) {
            fillProgress = 0;
        } else if (scrollTop > endPoint) {
            fillProgress = 1;
        } else {
            fillProgress = (scrollTop - startPoint) / config.scrollDistance;
        }
        
        const lettersToFill = Math.round(fillProgress * totalLetters);
        
        letters.forEach((letter, index) => {
            if (index < lettersToFill) {
                letter.classList.add('filled');
            } else {
                letter.classList.remove('filled');
            }
        });
        
        if (scrollSubtext) {
            if (fillProgress > config.subtextTrigger) {
                scrollSubtext.classList.add('visible');
            } else {
                scrollSubtext.classList.remove('visible');
            }
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }

    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(handleTextScroll);
    });
    
    window.addEventListener('resize', handleTextScroll);
}



const CONFIG_SERVICES = {
    desktop: {
        startOffset: 100,
        scrollDistance: 500
    },
    mobile: {
        startOffset: 80,
        scrollDistance: 400
    }
};

function getCurrentServicesConfig() {
    return isMobile() ? CONFIG_SERVICES.mobile : CONFIG_SERVICES.desktop;
}

const servicesHeading = document.getElementById('servicesHeading');
if (servicesHeading) {
    const servicesText = servicesHeading.getAttribute('data-text');
    servicesHeading.innerHTML = '';
    
    let letterIndex = 0;
    for (let i = 0; i < servicesText.length; i++) {
        const char = servicesText[i];
        const span = document.createElement('span');
        
        if (char === ' ') {
            span.className = 'letter space';
            span.innerHTML = '&nbsp;';
        } else {
            span.className = 'letter';
            span.textContent = char;
            span.setAttribute('data-index', letterIndex);
            letterIndex++;
        }
        
        servicesHeading.appendChild(span);
    }
    
    const servicesLetters = servicesHeading.querySelectorAll('.letter:not(.space)');
    const totalServicesLetters = servicesLetters.length;
    
    function handleServicesTextScroll() {
        const config = getCurrentServicesConfig();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const servicesSection = document.querySelector('.services-section');
        if (!servicesSection) return;
        
        const sectionTop = servicesSection.offsetTop;
        const windowHeight = window.innerHeight;
        
        const elementInView = sectionTop - windowHeight + config.startOffset;
        const startPoint = Math.max(0, elementInView);
        const endPoint = startPoint + config.scrollDistance;
        
        let fillProgress = 0;
        
        if (scrollTop < startPoint) {
            fillProgress = 0;
        } else if (scrollTop > endPoint) {
            fillProgress = 1;
        } else {
            fillProgress = (scrollTop - startPoint) / config.scrollDistance;
        }
        
        const lettersToFill = Math.round(fillProgress * totalServicesLetters);
        
        servicesLetters.forEach((letter, index) => {
            if (index < lettersToFill) {
                letter.classList.add('filled');
            } else {
                letter.classList.remove('filled');
            }
        });
    }
    
    let servicesScrollTimeout;
    window.addEventListener('scroll', function() {
        if (servicesScrollTimeout) {
            window.cancelAnimationFrame(servicesScrollTimeout);
        }
        servicesScrollTimeout = window.requestAnimationFrame(handleServicesTextScroll);
    });
    
    window.addEventListener('resize', handleServicesTextScroll);
}



function revealCards() {
    const cards = document.querySelectorAll('.part-card');
    const triggerPoint = isMobile() ? 150 : 100; 
    
    cards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (cardTop < windowHeight - triggerPoint) {
            card.classList.add('reveal');
        }
    });
}

window.addEventListener('scroll', revealCards);
window.addEventListener('load', revealCards);



const partsSlider = document.getElementById('partsSlider');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

if (prevBtn && nextBtn && partsSlider) {
    const getScrollDistance = () => isMobile() ? 250 : 300;
    
    prevBtn.addEventListener('click', () => {
        partsSlider.scrollBy({
            left: -getScrollDistance(),
            behavior: 'smooth'
        });
    });

    nextBtn.addEventListener('click', () => {
        partsSlider.scrollBy({
            left: getScrollDistance(),
            behavior: 'smooth'
        });
    });
    
    if (isMobile()) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        partsSlider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        partsSlider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    partsSlider.scrollBy({
                        left: getScrollDistance(),
                        behavior: 'smooth'
                    });
                } else {
                    partsSlider.scrollBy({
                        left: -getScrollDistance(),
                        behavior: 'smooth'
                    });
                }
            }
        }
    }
}