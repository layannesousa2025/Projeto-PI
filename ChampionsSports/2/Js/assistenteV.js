document.addEventListener("DOMContentLoaded", () => {
  const sendBtn = document.getElementById("send-btn");
  const userInput = document.getElementById("user-input");
  const messagesContainer = document.getElementById("chatbot-messages");

  // Função para adicionar mensagens
  function addMessage(text, sender) {
    const message = document.createElement("div");
    message.classList.add("message", sender === "bot" ? "bot-message" : "user-message");
    message.innerText = text;
    messagesContainer.appendChild(message);

    // Scroll automático para a última mensagem
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Função de resposta do bot
  function botResponse(userText) {
    let text = userText.toLowerCase();
    let response = "Desculpe, não entendi. Pode reformular?";

    if (text.includes("evento")) {
      response = "Temos vários eventos acontecendo! Você gostaria de ver Academia, Voleibol, Natação ou Futebol?";
    } else if (text.includes("academia")) {
      response = "O Próximo Evento acontecerá na SmatFlit no sábado às 18h.";
    } else if (text.includes("voleibol")) {
      response = "O Próximo Evento de Voleibol acontecerá em Brasília Vôlei Esporte Clube nesta sexta às 20h.";
    } else if (text.includes("natação")) {
      response = "O Próximo Evento de Natação acontecerá nesta segunda-feira às 15h.";
    } else if (text.includes("oi") || text.includes("olá")) {
      response = "Olá! Sou o assistente do ChampionsSports. Como posso te ajudar a encontrar eventos?";
    } else if (
      text.includes("saúde") ||
      text.includes("saude") ||
      text.includes("dúvida de saúde") ||
      text.includes("duvida de saúde")
    ) {
      response = "Se você tem dúvidas sobre saúde, recomendamos procurar um profissional qualificado. Para dúvidas gerais, posso ajudar com informações sobre atividades físicas e bem-estar!";
    }

    setTimeout(() => addMessage(response, "bot"), 800);
  }

  // Evento do botão
  sendBtn.addEventListener("click", () => {
    const userText = userInput.value.trim();
    if (userText !== "") {
      addMessage(userText, "user");
      botResponse(userText);
      userInput.value = "";
    }
  });

  // Enter envia a mensagem
  userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      sendBtn.click();
    }
  });
});

 
