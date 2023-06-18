const cards = Array.from(document.querySelectorAll('.card'));

// Fonction pour sélectionner une carte
const selectCard = (card) => {
    // Supprimer la classe "selected" de toutes les cartes
    cards.forEach((card) => {
        card.classList.remove('selected');
    });

    // Ajouter la classe "selected" à la carte sélectionnée
    card.classList.add('selected');
};

// Gérer l'événement de clic sur une carte
cards.forEach((card) => {
    card.addEventListener('click', () => {
        selectCard(card);
    });
    card.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            selectCard(card);
        }
    });
});

const sliderContainer = document.querySelector('.slider-container');
const prevButton = document.querySelector('.prev-btn');
const nextButton = document.querySelector('.next-btn');
const dotsContainer = document.querySelector('.dots');
// Slide to the selected card
const slideToCard = (cardIndex) => {
sliderContainer.style.transform = `translateX(-${
cardIndex * (cards[0].offsetWidth + 70)
}px)`;

// Remove "selected" class from all cards
cards.forEach((card) => {
card.classList.remove('selected');
});

// Add "selected" class to the current card
cards[cardIndex].classList.add('selected');

// Update active dot
dotsContainer.querySelectorAll('.dot').forEach((dot) => {
dot.classList.remove('active');
});
dotsContainer.querySelectorAll('.dot')[cardIndex].classList.add('active');
};


// Previous button click event
prevButton.addEventListener('click', () => {
const selectedCardIndex = cards.findIndex((card) => card.classList.contains('selected'));
const prevCardIndex = (selectedCardIndex - 1 + cards.length) % cards.length;
slideToCard(prevCardIndex);
});

// Next button click event
nextButton.addEventListener('click', () => {
const selectedCardIndex = cards.findIndex((card) => card.classList.contains('selected'));
const nextCardIndex = (selectedCardIndex + 1) % cards.length;
slideToCard(nextCardIndex);
});

// Generate dots
for (let i = 0; i < cards.length; i++) {
const dot = document.createElement('div');
dot.classList.add('dot');
if (i === 0) {
dot.classList.add('active');
}
dotsContainer.appendChild(dot);
}

// Dot click event
dotsContainer.querySelectorAll('.dot').forEach((dot, index) => {
dot.addEventListener('click', () => {
slideToCard(index);
});
});