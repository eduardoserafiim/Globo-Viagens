document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('section');
    const links = document.querySelectorAll('.desktop-nav ul li a');

    function highlightMenu() {
        let currentSection = '';
        
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
                currentSection = section.id;
            }
        });

        links.forEach(link => {
            link.classList.remove('active');
        });

        if (currentSection) {
            const activeLink = document.querySelector(`a[href="#${currentSection}"]`);
            if (activeLink && currentSection !== 'hero') {
                activeLink.classList.add('active');
            }
        }
    }

    window.addEventListener('scroll', highlightMenu);
    window.addEventListener('load', highlightMenu);
});
