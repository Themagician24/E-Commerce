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


// Just a collection of modern UI effects. 
// I like to keep these kind of things "handy" (in codepen) to show clients/employers what I'm talking about when I say things like "gradient animation"

// If I ever get around to it, I'll clean this all up (lots of extra
// unecessary markup + CSS). For now,
// I'm just using Codepen as my personal "effect holder" :)

// Inspiration: https://portal.griflan.com/
// Inspiration: https://github.com/cruip/cruip-tutorials/tree/main/spotlight-effect

// Cards spotlight
class Spotlight {
	constructor(containerElement) {
		this.container = containerElement;
		this.cards = Array.from(this.container.children);
		this.mouse = {
			x: 0,
			y: 0,
		};
		this.containerSize = {
			w: 0,
			h: 0,
		};
		this.initContainer = this.initContainer.bind(this);
		this.onMouseMove = this.onMouseMove.bind(this);
		this.init();
	}

	initContainer() {
		this.containerSize.w = this.container.offsetWidth;
		this.containerSize.h = this.container.offsetHeight;
	}

	onMouseMove(event) {
		const { clientX, clientY } = event;
		const rect = this.container.getBoundingClientRect();
		const { w, h } = this.containerSize;
		const x = clientX - rect.left;
		const y = clientY - rect.top;
		const inside = x < w && x > 0 && y < h && y > 0;
		if (inside) {
			this.mouse.x = x;
			this.mouse.y = y;
			this.cards.forEach((card) => {
				const cardX = -(card.getBoundingClientRect().left - rect.left) + this.mouse.x;
				const cardY = -(card.getBoundingClientRect().top - rect.top) + this.mouse.y;
				card.style.setProperty('--mouse-x', `${cardX}px`);
				card.style.setProperty('--mouse-y', `${cardY}px`);
			});
		}
	}

	init() {
		this.initContainer();
		window.addEventListener('resize', this.initContainer);
		window.addEventListener('mousemove', this.onMouseMove);
	}
}

// Init Spotlight
const spotlights = document.querySelectorAll('[data-spotlight]');
spotlights.forEach((spotlight) => {
	new Spotlight(spotlight);
});

// GSAP ("parallax section reveal
document.addEventListener('DOMContentLoaded', function () {
	gsap.registerPlugin(ScrollTrigger);

	gsap.from(".dx-fixed-background__media-wrapper", {
		scale: 0.55,
		scrollTrigger: {
			trigger: ".dx-fixed-background__media-wrapper",
			start: "top bottom", 
			end: "center 75%", 
			scrub: true
		}
	});
	gsap.from(".dx-fixed-background__media", {
		borderRadius: "300px",
		scrollTrigger: {
			trigger: ".dx-fixed-background__media-wrapper",
			start: "top bottom", // Same start as above
			end: "center 75%", // Same end point as above
			scrub: true
		}
	});
});

document.addEventListener("DOMContentLoaded", function() {
    var allProducts = document.getElementById("allProducts");
    var categoryList = document.getElementById("categoryList");

    allProducts.addEventListener("mouseenter", function() {
      categoryList.style.display = "block";
    });

    allProducts.addEventListener("mouseleave", function() {
      categoryList.style.display = "none";
    });
  });