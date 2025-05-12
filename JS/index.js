const menu = document.querySelector('.menu');

menu.addEventListener('click', () => {
    const menuMobile = document.querySelector('.menu-mobile');
    menuMobile.classList.toggle('active');
});

const images = [
    { 'id': '1', 'url': 'corte1.jpg' },
    { 'id': '2', 'url': 'corte2.jpg' },
    { 'id': '3', 'url': 'corte3.jpg' },
    { 'id': '4', 'url': 'corte1.jpg' },
    { 'id': '5', 'url': 'corte2.jpg' },
];

const containerItens = document.querySelector('#container-item');
let currentIndex = 0;

const loadImages = (images, container) => {
    images.forEach(image => {
        container.innerHTML += `
            <div class='item'>
                <img src='JS/${image.url}'/>
            </div>
        `;
    });
};

loadImages(images, containerItens);

const nextSlide = () => {
    currentIndex = (currentIndex + 1) % images.length;
    updateCarousel();
};

const updateCarousel = () => {
    containerItens.style.transform = `translateX(-${currentIndex * 100}%)`;
};

setInterval(nextSlide, 2000);
 

const toggle = document.querySelector('.toggle');

toggle.addEventListener('click', () => {
    const item = document.querySelector('.item i');
    item.classList.toggle('fa-toggle-on');
    item.classList.toggle('fa-toggle-off');
});