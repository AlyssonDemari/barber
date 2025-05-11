const menu = document.querySelector('.menu');

menu.addEventListener('click', () => {
    const menuMobile = document.querySelector('.menu-mobile');
    menuMobile.classList.toggle('active');
});