// Récupère l'URL de la page actuelle
const currentUrl = window.location.href;

// Sélectionne les éléments de menu
const homeButton = document.querySelector('.menu-first-item');
const memberButton = document.querySelector('.menu-next-item');
const profileButton = document.querySelector('.menu-last-item');


// Vérifie si l'URL de la page actuelle correspond à chaque bouton du menu
if (currentUrl.endsWith('/membre') || currentUrl.includes('/membre/')) {
  memberButton.setAttribute('class', 'menu-next-item menu-selected');
} else if (currentUrl.endsWith('/utilisateur') || currentUrl.includes('/utilisateur/')) {
  profileButton.classList.add('menu-selected');
} else {
  homeButton.classList.add('menu-selected');
};


