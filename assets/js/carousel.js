let slides = [
    { title: "", desc: "", img: "/assets/landingpage-images/Carousel1.jpg" },
    { title: "", desc: "", img: "/assets/landingpage-images/Carousel2.jpg" },
    { title: "", desc: "", img: "/assets/landingpage-images/Carousel3.jpg" },
    { title: "", desc: "", img: "/assets/landingpage-images/Carousel4.jpg" }
];

const mainBg = document.getElementById('main-bg');
const titleEl = document.getElementById('slide-title');
const descEl = document.getElementById('slide-desc');
const fullscreenModal = document.getElementById('fullscreen-modal');
const fullscreenImg = document.getElementById('fullscreen-img');
const closeModal = document.getElementById('close-modal');

function render() {
    const current = slides[0];

    mainBg.style.opacity = '0';
    
    setTimeout(() => {
        mainBg.src = current.img;
        titleEl.innerText = current.title;
        descEl.innerText = current.desc;
        mainBg.style.opacity = '1';
    }, 400);
}

document.getElementById('next-btn').addEventListener('click', () => {
    const first = slides.shift();
    slides.push(first);
    render();
});

document.getElementById('prev-btn').addEventListener('click', () => {
    const last = slides.pop();
    slides.unshift(last);
    render();
});

document.querySelector('.carousel').addEventListener('click', (e) => {
    if (!e.target.closest('.nav-btn')) {
        fullscreenImg.src = mainBg.src;
        fullscreenModal.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }
});

closeModal.addEventListener('click', (e) => {
    e.stopPropagation(); 
    fullscreenModal.classList.remove('active');
    document.body.style.overflow = ''; 
});

fullscreenModal.addEventListener('click', (e) => {
    if (e.target === fullscreenModal) {
        fullscreenModal.classList.remove('active');
        document.body.style.overflow = '';
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && fullscreenModal.classList.contains('active')) {
        fullscreenModal.classList.remove('active');
        document.body.style.overflow = '';
    }
});

render();