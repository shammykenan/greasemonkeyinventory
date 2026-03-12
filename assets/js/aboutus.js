gsap.registerPlugin(ScrollTrigger);

window.addEventListener("DOMContentLoaded", () => {
    
    const aboutSplit = new SplitType('.text-about', { types: 'words' });
    const missionSplit = new SplitType('.text-mission', { types: 'words' });
    const visionSplit = new SplitType('.text-vision', { types: 'words' });

    const container = document.querySelector(".horizontal-content");

    let tl = gsap.timeline({
        scrollTrigger: {
            trigger: ".horizontal-section",
            pin: true,           
            scrub: 1,            
            start: "top top",
            end: "+=5000",       
        }
    });

    tl.to(aboutSplit.words, {
        color: "#d4af37",      
        stagger: 0.1,          
        duration: 2
    });

    tl.to(container, {
        x: "-100vw",
        duration: 3,
        ease: "power2.inOut"
    });

    tl.to(missionSplit.words, {
        color: "#d4af37",      
        stagger: 0.1,
        duration: 2
    });

    tl.to(container, {
        x: "-200vw",
        duration: 3,
        ease: "power2.inOut"
    });

    tl.to(visionSplit.words, {
        color: "#d4af37",      
        stagger: 0.1,
        duration: 2
    });
});