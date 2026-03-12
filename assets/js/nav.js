const menuBtn = document.getElementById('menuBtn');
const closeBtn = document.getElementById('closeBtn');
const modalMenu = document.getElementById('modalMenu');
const modalOverlay = document.getElementById('modalOverlay');
const navbar = document.getElementById('navbar');

function openMenu() {
    modalMenu.classList.add('active');
    modalOverlay.classList.add('active');
    navbar.style.visibility = 'hidden';
    document.body.style.overflow = 'hidden';
}

function closeMenu() {
    modalMenu.classList.remove('active');
    modalOverlay.classList.remove('active');
    navbar.style.visibility = 'visible';
    document.body.style.overflow = 'auto';
}

menuBtn.addEventListener('click', openMenu);
closeBtn.addEventListener('click', closeMenu);
modalOverlay.addEventListener('click', closeMenu);

const modalLinks = document.querySelectorAll('.modal-nav a');
modalLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        closeMenu();
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalMenu.classList.contains('active')) {
        closeMenu();
    }
});

function updateNavbarOnScroll() {
    const scrollY = window.scrollY;
    if (scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
}

window.addEventListener('scroll', updateNavbarOnScroll);
