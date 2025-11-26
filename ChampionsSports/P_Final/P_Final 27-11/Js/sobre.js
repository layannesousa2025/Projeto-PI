document.addEventListener("DOMContentLoaded", () => {
  // ===== 1. Scroll suave para links internos =====
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const href = this.getAttribute("href");

      // Ignora links vazios (#)
      if (!href || href === "#") return;

      try {
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        } else {
          console.warn(`Elemento alvo não encontrado para o seletor: ${href}`);
        }
      } catch (error) {
        console.error(`Erro ao tentar rolar até '${href}':`, error);
      }
    });
  });

  // ===== 2. Envio assíncrono do formulário de contato =====
  const contactForm = document.querySelector(".contact-form");
  if (contactForm) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.textContent;

      try {
        // Feedback visual
        submitButton.disabled = true;
        submitButton.textContent = "Enviando...";

        const response = await fetch(this.action, {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          throw new Error(`Erro no servidor: ${response.status} ${response.statusText}`);
        }

        const userName = formData.get("nome") || "usuário";
        alert(`Obrigado, ${userName}! Sua mensagem foi enviada com sucesso.`);
        this.reset();
      } catch (error) {
        console.error("Erro ao enviar o formulário:", error);
        alert("Desculpe, ocorreu um erro ao enviar sua mensagem. Tente novamente mais tarde.");
      } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
      }
    });
  }

  // ===== 3. Animação com fade-in ao rolar =====
  const elementsToAnimate = document.querySelectorAll(".feature-card, .contact-item");
  if (elementsToAnimate.length > 0) {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target); // Para de observar após animar
        }
      });
    }, observerOptions);

    elementsToAnimate.forEach((el) => {
      el.classList.add("fade-in-element");
      observer.observe(el);
    });
  }
});