// Smooth scroll para links internos
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Formulário de contato
document.querySelector(".contact-form").addEventListener("submit", function (e) {
  e.preventDefault()

  const nome = document.getElementById("nome").value
  const email = document.getElementById("email").value
  const telefone = document.getElementById("telefone").value
  const mensagem = document.getElementById("mensagem").value

  // Aqui você pode adicionar a lógica para enviar o formulário
  alert(`Obrigado, ${nome}! Sua mensagem foi enviada com sucesso. Entraremos em contato em breve.`)

  // Limpar formulário
  this.reset()
})

// Animação ao scroll (fade in)
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
}

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1"
      entry.target.style.transform = "translateY(0)"
    }
  })
}, observerOptions)

// Aplicar animação aos cards
document.querySelectorAll(".feature-card, .contact-item").forEach((el) => {
  el.style.opacity = "0"
  el.style.transform = "translateY(20px)"
  el.style.transition = "opacity 0.6s ease, transform 0.6s ease"
  observer.observe(el)
})