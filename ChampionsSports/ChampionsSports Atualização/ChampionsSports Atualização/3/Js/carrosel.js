// Seletores principais
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.indicator');
let currentSlide = 0;
const totalSlides = slides.length;

// Atualiza o carrossel
function updateCarousel() {
  // Move os slides
  const slidesContainer = document.querySelector('.carousel-slides');
  slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;

  // Atualiza classes "active"
  slides.forEach((slide, index) => {
    slide.classList.toggle('active', index === currentSlide);
  });

  indicators.forEach((indicator, index) => {
    indicator.classList.toggle('active', index === currentSlide);
  });
}

// Avançar
function nextSlide() {
  currentSlide = (currentSlide + 1) % totalSlides;
  updateCarousel();
}

// Voltar
function prevSlide() {
  currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  updateCarousel();
}



