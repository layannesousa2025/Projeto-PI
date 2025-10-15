document.addEventListener("DOMContentLoaded", () => {
  const sendBtn = document.getElementById("send-btn");
  const userInput = document.getElementById("user-input");
  const messagesContainer = document.getElementById("chatbot-messages");

  // Função para o bot falar
  function falar(texto) {
    const msg = new SpeechSynthesisUtterance(texto);
    msg.lang = "pt-BR";
    msg.rate = 1;
    window.speechSynthesis.speak(msg);
  }

  // Função para adicionar mensagens no chat
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
    let response = "Desculpe, não entendi. Pode reformular a pergutar?";

    if (text.includes("evento")) {
      response = "Temos vários eventos acontecendo em BRASILIA-DF! Você gostaria de ver Academia, Voleibol, Natação ou Futebol?";
    } else if (text.includes("academia")) {
      response = "O próximo evento acontecerá na SmartFlit Localizadado em St. N QNN, 09 Ceilândia Norte - Ceilândia, Brasília - DF, 72225-090 no Sábado às 18h.";
    } else if (text.includes("voleibol")) {
      response = "O próximo evento de Voleibol acontecerá em Brasília Vôlei Esporte Clube nesta sexta às 20h.";
    } else if (text.includes("natação") || text.includes("natacao")) {
      response = "O próximo evento de Natação acontecerá nesta segunda-feira às 15h.";
    } else if (text.includes("futebol")) {
      response = "O próximo evento de Futebol será no estádio Central no domingo às 16h.";
    } else if (text.includes("oi") || text.includes("olá") || text.includes("ola")) {
      response = "Olá! Sou o assistente do ChampionsSports. Como posso te ajudar a encontrar eventos mais proximos de sua cidade";
    } else if (text.includes("saúde") || text.includes("saude")) {
      response = "Se você tem dúvidas sobre saúde, recomendamos procurar um profissional qualificado. Posso ajudar com informações sobre atividades físicas e bem-estar!";
    }

    // Adiciona a mensagem do bot com pequeno delay
    setTimeout(() => {
      addMessage(response, "bot");
      falar(response); // bot fala a resposta
    }, 500);
  }

  // Função para enviar mensagem
  function enviarMensagem() {
    const userText = userInput.value.trim();
    if (userText === "") return;

    addMessage(userText, "user"); // adiciona mensagem do usuário
    botResponse(userText);         // resposta do bot
    userInput.value = "";          // limpa input
  }

  // Evento do botão
  sendBtn.addEventListener("click", enviarMensagem);

  // Enter envia a mensagem
  userInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") enviarMensagem();
  });
});

 
