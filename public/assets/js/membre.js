const cardsContainer = document.querySelector('.cards');
const scrollbarThumb = document.querySelector('.scrollbar-thumb');
const scrollbarArrowUp = document.querySelector('.scrollbar-arrow.up');
const scrollbarArrowDown = document.querySelector('.scrollbar-arrow.down');
const paginationContainer = document.querySelector('.pagination');

let selectedCardIndex = 0;
let isThumbDragging = false;
let thumbDragOffset = 0;

let currentPage = 1;
const itemsPerPage = 10;

function updateSelectedCard() {
  const cards = document.querySelectorAll('.card-membre');
  cards.forEach((card, index) => {
    if (index === selectedCardIndex) {
      card.classList.add('selecte');
    } else {
      card.classList.remove('selecte');
    }
  });
}

function scrollToSelectedCard() {
  const cards = document.querySelectorAll('.card-membre');
  const selectedCard = cards[selectedCardIndex];
  if (selectedCard) {
    selectedCard.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }
}

function updateScrollbarThumb() {
  const containerHeight = cardsContainer.clientHeight;
  const contentHeight = cardsContainer.scrollHeight;
  const thumbHeight = (containerHeight / contentHeight) * containerHeight;
  scrollbarThumb.style.height = `${thumbHeight}px`;

  if (cardsContainer.scrollTop === 0) {
    scrollbarThumb.classList.remove('bottom');
    scrollbarThumb.classList.add('top');
  } else if (
    cardsContainer.scrollTop + cardsContainer.clientHeight ===
    cardsContainer.scrollHeight
  ) {
    scrollbarThumb.classList.remove('top');
    scrollbarThumb.classList.add('bottom');
  } else {
    scrollbarThumb.classList.remove('top', 'bottom');
  }
}

function handleArrowClick(direction) {
  const cards = document.querySelectorAll('.card-membre');
  if (direction === 'previous') {
    if (selectedCardIndex === 0) {
      selectedCardIndex = cards.length - 1;
    } else {
      selectedCardIndex = selectedCardIndex - 1;
    }
  } else if (direction === 'next') {
    if (selectedCardIndex === cards.length - 1) {
      selectedCardIndex = 0;
    } else {
      selectedCardIndex = selectedCardIndex + 1;
    }
  }

  updateSelectedCard();
  scrollToSelectedCard();
}

function handleThumbDrag(event) {
  if (!isThumbDragging) return;

  const containerHeight = cardsContainer.clientHeight;
  const contentHeight = cardsContainer.scrollHeight;
  const trackHeight =
    containerHeight - parseFloat(scrollbarThumb.style.height);
  const maxThumbTranslate =
    trackHeight - parseFloat(scrollbarThumb.style.height);

  const offsetY = event.clientY - thumbDragOffset;
  const scrollPercentage = (offsetY / trackHeight) * 100;
  const thumbTranslate = Math.max(
    0,
    Math.min(maxThumbTranslate, offsetY - 20)
  );
  const contentTranslate =
    (scrollPercentage / 100) * (contentHeight - containerHeight);

  scrollbarThumb.style.transform = `translateY(${thumbTranslate}px)`;
  cardsContainer.scrollTop = contentTranslate;
}

function handleThumbDragStart(event) {
  const thumbRect = scrollbarThumb.getBoundingClientRect();
  thumbDragOffset = event.clientY - thumbRect.top;
  isThumbDragging = true;
}

function handleThumbDragEnd() {
  isThumbDragging = false;
}

scrollbarArrowUp.addEventListener('click', () => {
  handleArrowClick('previous');
});

scrollbarArrowDown.addEventListener('click', () => {
  handleArrowClick('next');
});

scrollbarThumb.addEventListener('mousedown', handleThumbDragStart);
document.addEventListener('mousemove', handleThumbDrag);
document.addEventListener('mouseup', handleThumbDragEnd);

cardsContainer.addEventListener('scroll', () => {
  const scrollPercentage =
    (cardsContainer.scrollTop /
      (cardsContainer.scrollHeight - cardsContainer.clientHeight)) *
    100;
  const thumbTranslate =
    (scrollPercentage / 100) *
    (cardsContainer.clientHeight - parseFloat(scrollbarThumb.style.height));
  scrollbarThumb.style.transform = `translateY(${thumbTranslate}px)`;
});

// Génération des cartes depuis les données fournies par le fichier Twig
function generateCards() {
  const cards = document.querySelectorAll('.card-membre');

  cards.forEach((card, index) => {
    card.addEventListener('click', () => {
      selectedCardIndex = index;
      updateSelectedCard();
    });
  });

  updateSelectedCard();
}

function generatePagination() {
  paginationContainer.innerHTML = '';

  const numberOfPages = Math.ceil(cardData.length / itemsPerPage);

  for (let i = 1; i <= numberOfPages; i++) {
    const button = document.createElement('button');
    button.textContent = i;
    button.addEventListener('click', () => {
      currentPage = i;
      generateCards();
      generatePagination();
    });

    if (i === currentPage) {
      button.classList.add('active');
    }

    paginationContainer.appendChild(button);
  }
}

generateCards();
generatePagination();
updateScrollbarThumb();
