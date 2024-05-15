feather.replace();


// Fonction pour copier le menu pour mobile
function copyMenu() {
    // Copier à l'intérieur de .dpt-cat dans .departments
    var dptCategory = document.querySelector('.dpt-cat');
    var dptPlace = document.querySelector('.departments');
    dptPlace.innerHTML = dptCategory.innerHTML;

    // Copier à l'intérieur de nav dans nav
    var mainNav = document.querySelector('.header-nav nav');
    var navPlace = document.querySelector('.off-canvas nav');
    navPlace.innerHTML = mainNav.innerHTML;

    // Copier .header-top .wrapper dans .thetop-nav
    var topNav = document.querySelector('.header-top .wrapper');
    var topPlace = document.querySelector('.thetop-nav');
    topPlace.innerHTML = topNav.innerHTML;
}
copyMenu();

// Afficher le menu mobile
const menuButton = document.querySelector('.trigger'),
    closeButton = document.querySelector('.t-close'),
    addclass = document.querySelector('.site');
menuButton.addEventListener('click', function() {
    addclass.classList.toggle('showmenu')
})
closeButton.addEventListener('click', function() {
    addclass.classList.remove('showmenu')
})

// Afficher le sous-menu sur mobile
const submenu = document.querySelectorAll('.has-child .icon-small');
submenu.forEach((menu) => menu.addEventListener('click', toggle));

function toggle(e) {
    e.preventDefault();
    submenu.forEach((item) => item != this ? item.closest('.has-child').classList.remove('expand') : null);
    if (this.closest('.has-child').classList != 'expand');
    this.closest('.has-child').classList.toggle('expand');
}

// Initialiser le slider
const swiper = new Swiper('.swiper', {
    loop: true,
    pagination: {
        el: '.swiper-pagination',
    },
});

// Afficher la barre de recherche
const searchButton = document.querySelector('.t-search'),
    tClose = document.querySelector('.search-close'),
    showClass = document.querySelector('.site');

searchButton.addEventListener('click', function() {
    showClass.classList.toggle('showsearch')
})
tClose.addEventListener('click', function() {
    showClass.classList.remove('showsearch')
})

// Afficher le menu de département
const dptButton = document.querySelector('.dpt-cat .dpt-trigger'),
    dptClass = document.querySelector('.site');
dptButton.addEventListener('click', function() {
    dptClass.classList.toggle('showdpt')
})

// Initialiser le slider d'image de produit
var productThumb = new Swiper ('.small-image', {
    loop: true,
    spaceBetween: 10,
    slidesPerView: 3,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
        481: {
            spaceBetween: 32,
        }
    }
});
var productBig = new Swiper ('.big-image', {
    loop: true,
    autoHeight: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    thumbs: {
        swiper: productThumb,
    }
})

// Régler la largeur de la barre de produits en stock en pourcentage
var stocks = document.querySelectorAll('.products .stock');
for (let x = 0; x < stocks.length; x++) {
    let stock = stocks[x].dataset.stock,
        available = stocks[x].querySelector('.qty-available').innerHTML,
        sold = stocks[x].querySelector('.qty-sold').innerHTML,
        percent = sold * 100 / stock;

    stocks[x].querySelector('.available').style.width = percent + '%';
}

// Afficher le panier lors du clic
const divtoShow = '.mini-cart';
const divPopup = document.querySelector(divtoShow);
const divTrigger = document.querySelector('.cart-trigger');

divTrigger.addEventListener('click', () => {
    setTimeout(() => {
        if(!divPopup.classList.contains('show')) {
            divPopup.classList.add('show');
        }
    }, 250)
})

// Fermer en cliquant à l'extérieur
document.addEventListener('click', (e) => {
    const isClosest = e.target.closest(divtoShow);
    if(!isClosest && divPopup.classList.contains('show')) {
        divPopup.classList.remove('show')
    }
})

// Afficher le modal lors du chargement
window.onload = function() {
    document.querySelector('.site').classList.toggle('showmodal')
}

document.querySelector('.modalclose').addEventListener('click', function() {
    document.querySelector('.site').classList.remove('showmodal')
})


