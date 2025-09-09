// Dados dos slides
const slidesData = [
  { img: "./imagem/ciclismo.jpg", title: "Ciclismo", description: "Explore trilhas e estradas com sua bicicleta." },
  { img: "./imagem/jiujitsu.jpg", title: "Jiu-Jitsu", description: "Arte suave e defesa pessoal." },
  { img: "./imagem/volei-praia.jpg", title: "Vôlei", description: "Cooperação e saltos impressionantes." },
  { img: "./imagem/tenis.jpg", title: "Tênis", description: "Precisão, elegância e estratégia." },
  { img: "./imagem/natacao.jpg", title: "Natação", description: "Movimento fluido na água." },
  { img: "./imagem/musculacao.jpg", title: "Musculação", description: "Força e resistência." },
  { img: "./imagem/parapente.jpg", title: "Parapente", description: "Liberdade no voo livre." },
  { img: "./imagem/remo.jpg", title: "Remo", description: "Força e sincronia em equipe." }
];

let currentSlide = 0;

// Cria os slides e indicadores dinamicamente
function createSlides() {
  const carousel = document.getElementById("carousel");
  const indicatorsContainer = document.getElementById("carousel-indicators");

  slidesData.forEach((slide, index) => {
    // Slide
    const slideDiv = document.createElement("div");
    slideDiv.classList.add("slide");
    if (index === 0) slideDiv.classList.add("active");

    slideDiv.innerHTML = `
      <img src="${slide.img}" alt="${slide.title}">
      <h3>${slide.title}</h3>
      <p>${slide.description}</p>
    `;
    carousel.appendChild(slideDiv);

    // Indicador
    const indicator = document.createElement("span");
    indicator.classList.add("indicator");
    if (index === 0) indicator.classList.add("active");
    indicator.addEventListener("click", () => goToSlide(index));
    indicatorsContainer.appendChild(indicator);
  });
}

// Atualiza o carrossel (posição + indicadores)
function updateCarousel() {
  const carousel = document.getElementById("carousel");
  const indicators = document.querySelectorAll(".indicator");
  const slides = document.querySelectorAll(".slide");

  carousel.style.transform = `translateX(-${currentSlide * 100}%)`;

  indicators.forEach((indicator, index) => {
    indicator.classList.toggle("active", index === currentSlide);
  });

  slides.forEach((slide, index) => {
    slide.classList.toggle("active", index === currentSlide);
  });
}

// Navegação
function nextSlide() {
  currentSlide = (currentSlide + 1) % slidesData.length;
  updateCarousel();
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + slidesData.length) % slidesData.length;
  updateCarousel();
}

function goToSlide(index) {
  currentSlide = index;
  updateCarousel();
}

// Inicialização
document.addEventListener("DOMContentLoaded", () => {
  createSlides();
  updateCarousel();

  // Troca automática a cada 5s
  setInterval(nextSlide, 5000);
});
