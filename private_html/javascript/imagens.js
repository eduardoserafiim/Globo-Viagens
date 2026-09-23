document.querySelectorAll('.image-carousel').forEach(carousel => {
    const images = JSON.parse(carousel.dataset.images);
    const imgElement = carousel.querySelector('.carousel-image');
    let index = 0;

    const updateImage = () => {
        imgElement.src = images[index];
    };

    carousel.querySelector('.next-button').addEventListener('click', () => {
        index = (index + 1) % images.length;
        updateImage();
    });

    carousel.querySelector('.prev-button').addEventListener('click', () => {
        index = (index - 1 + images.length) % images.length;
        updateImage();
    });
});

