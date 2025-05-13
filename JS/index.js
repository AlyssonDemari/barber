document.querySelectorAll('.itens a').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.itens i').forEach(icon => {
            icon.classList.remove('active');
        });
        this.querySelector('i').classList.add('active');
    });
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
 

const items = document.querySelectorAll('.container-itens .item');

items.forEach(item => {
    item.addEventListener('click', () => {
        // Primeiro, desativa todos os outros itens
        items.forEach(otherItem => {
            if (otherItem !== item) { // Não mexa no item que está sendo clicado
                const otherIcon = otherItem.querySelector('i');
                const otherHidden = otherItem.querySelector('.hidden');
                
                otherIcon.classList.remove('fa-toggle-on');
                otherIcon.classList.add('fa-toggle-off');
                otherHidden.style.display = 'none';
                otherItem.style.backgroundColor = '';
            }
        });

        // Agora, alterna o estado do item clicado
        const icon = item.querySelector('i');
        const hidden = item.querySelector('.hidden');
        const color = "2222227c";
        
        icon.classList.toggle('fa-toggle-off');
        icon.classList.toggle('fa-toggle-on');
        
        hidden.style.display = (hidden.style.display === 'block') ? 'none' : 'block';
        
        if (icon.classList.contains('fa-toggle-on')) {
            item.style.backgroundColor = '#' + color;
        } else {
            item.style.backgroundColor = '';
        }
    });
});