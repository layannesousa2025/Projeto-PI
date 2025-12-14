document.addEventListener("DOMContentLoaded", () => {
  /* ==========================================================
     1. Scroll suave para links internos
  ========================================================== */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const href = this.getAttribute("href");

      if (!href || href === "#") return;

      try {
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        } else {
          console.warn(`Elemento não encontrado para o seletor: ${href}`);
        }
      } catch (error) {
        console.error(`Erro ao rolar até '${href}':`, error);
      }
    });
  });

  /* ==========================================================
     2. Envio assíncrono do formulário de contato
  ========================================================== */
  const contactForm = document.querySelector(".contact-form");

  if (contactForm) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.textContent;

      submitButton.disabled = true;
      submitButton.textContent = "Enviando...";

      try {
        const response = await fetch(this.action, {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          let serverMessage = `Erro no servidor: ${response.status} ${response.statusText}`;

          try {
            const errorData = await response.json();
            if (errorData?.message) {
              serverMessage = errorData.message;
            }
          } catch (_) {}

          throw new Error(serverMessage);
        }

        const userName = formData.get("nome") || "Usuário";
        alert(`Obrigado, ${userName}! Sua mensagem foi enviada com sucesso.`);
        this.reset();
      } catch (error) {
        console.error("Erro ao enviar o formulário:", error);
        alert(`Erro ao enviar: ${error.message}. Tente novamente mais tarde.`);
      } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
      }
    });
  }

  /* ==========================================================
     3. Animação fade-in ao rolar
  ========================================================== */
  const elementsToAnimate = document.querySelectorAll(".feature-card, .contact-item");

  if (elementsToAnimate.length > 0) {
    const observer = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
      }
    );

    elementsToAnimate.forEach((el) => {
      el.classList.add("fade-in-element");
      observer.observe(el);
    });
  }
});