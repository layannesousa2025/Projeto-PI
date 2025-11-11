document.addEventListener('DOMContentLoaded', () => {
  // Seletores principais
  const slides = document.querySelectorAll('.carousel-slide');
  const slidesContainer = document.querySelector('.carousel-slides');
  const indicatorsContainer = document.querySelector('.carousel-indicators');
  
  if (slides.length === 0) return; // Não faz nada se não houver slides

  let currentSlide = 0;
  const totalSlides = slides.length;
  let autoPlayInterval;

  // --- Cria os indicadores dinamicamente ---
  indicatorsContainer.innerHTML = ''; // Limpa indicadores existentes
  for (let i = 0; i < totalSlides; i++) {
    const indicator = document.createElement('button');
    indicator.classList.add('indicator');
    indicator.dataset.index = i;
    indicator.addEventListener('click', () => {
      goToSlide(i);
      resetAutoPlay(); // Reinicia o timer ao clicar
    });
    indicatorsContainer.appendChild(indicator);
  }
  // Pega a referência dos indicadores recém-criados
  const indicators = document.querySelectorAll('.indicator');

  // --- Funções do Carrossel ---
  function updateCarousel() {
    // Move os slides
    slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;

    // Atualiza classes "active" dos slides e indicadores
    slides.forEach((slide, index) => slide.classList.toggle('active', index === currentSlide));
    indicators.forEach((indicator, index) => indicator.classList.toggle('active', index === currentSlide));
  }

  // Funções de navegação (expostas globalmente para o HTML)
  window.nextSlide = function() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
    resetAutoPlay();
  }

  window.prevSlide = function() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
    resetAutoPlay();
  }

  function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
  }

  // --- Troca automática ---
  function resetAutoPlay() {
    clearInterval(autoPlayInterval);
    autoPlayInterval = setInterval(() => window.nextSlide(), 5000);
  }

  // Inicializa
  updateCarousel();
  resetAutoPlay();
});
