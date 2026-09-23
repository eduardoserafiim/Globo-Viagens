document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll(".mobile-link");

    function onScroll() {
        let scrollY = window.pageYOffset;

        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 100; // ajustar para compensar header fixo se necessário
            const sectionId = current.getAttribute("id");

            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove("active");
                    if (link.getAttribute("href").includes(sectionId)) {
                        link.classList.add("active");
                    }
                });
            }
        });
    }

    window.addEventListener("scroll", onScroll);
    onScroll(); // Executa ao carregar para marcar a seção inicial
});
