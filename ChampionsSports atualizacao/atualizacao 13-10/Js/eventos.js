const events = [ // Renomeado para 'events' para consistência com a lógica de filtragem
  {
    id: 1, // Adicionado ID único
    title: "Ciclismo", // Renomeado de 'evento' para 'title'
    game: "Ciclismo", // Adicionado 'game' para o filtro de categoria
    lugar: "SIT Torre de TV Digital", // Mantido 'lugar' para referência, mas 'venue' será usado para exibição
    venue: "SIT Torre de TV Digital", // Adicionado 'venue' para exibição na tabela
    cidade: "Brasília", // Cidade
    date: "2025-05-12", // Formato YYYY-MM-DD para compatibilidade com `toDate`
    lat: -15.722, // Latitude aproximada para cálculo de distância
    lon: -47.884, // Longitude aproximada para cálculo de distância
    queryMapa: "SIT Torre de TV Digital de Brasília" // Termo de busca para o mapa
  },
  {
    id: 2, // Adicionado ID único
    title: "Ciclismo",
    game: "Ciclismo",
    lugar: "Ponte JK",
    venue: "Ponte JK",
    cidade: "Brasília",
    date: "2025-06-24",
    lat: -15.815, // Latitude aproximada da Ponte JK
    lon: -47.835, // Longitude aproximada da Ponte JK
    queryMapa: "Ponte JK Brasília"
  },
  {
    id: 3, // Adicionado ID único
    title: "Ciclismo",
    game: "Ciclismo",
    lugar: "Parque da Cidade",
    venue: "Parque da Cidade",
    cidade: "Brasília",
    date: "2025-07-18",
    lat: -15.800, // Latitude aproximada
    lon: -47.900, // Longitude aproximada
    queryMapa: "Parque da Cidade Sarah Kubitschek Brasília"
  },
  {
    id: 4, // Adicionado ID único
    title: "Ciclismo",
    game: "Ciclismo",
    lugar: "Eixão do Lazer (Sul)",
    venue: "Eixão do Lazer (Sul)",
    cidade: "Brasília",
    date: "2025-08-05",
    lat: -15.810, // Latitude aproximada
    lon: -47.900, // Longitude aproximada
    queryMapa: "Eixão Sul Brasília"
  }
];

// --- Estado ---
let userLoc = null; // {lat, lon}

// --- Utilidades ---
const $ = sel => document.querySelector(sel);
const km = n => `${n.toFixed(1)} km`;

function toDate(s) { const d = new Date(s + 'T00:00:00'); d.setHours(12); return d; }

// Haversine formula for distance calculation
function distKm(a, b) {
  if (!a || !b || a.lat === undefined || a.lon === undefined || b.lat === undefined || b.lon === undefined) return Infinity;
  const R = 6371; // Raio da Terra em quilômetros
  const dLat = (b.lat - a.lat) * Math.PI / 180;
  const dLon = (b.lon - a.lon) * Math.PI / 180;
  const s1 = Math.sin(dLat/2) ** 2 + Math.cos(a.lat * Math.PI/180) * Math.cos(b.lat * Math.PI/180) * Math.sin(dLon/2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(s1));
}

function render() {
  const q = $('#q').value.trim().toLowerCase();
  const game = $('#game').value;
  const from = $('#from').value ? toDate($('#from').value) : null;
  const to = $('#to').value ? toDate($('#to').value) : null;

  const filtered = events
    .filter(ev => !q || `${ev.title} ${ev.cidade} ${ev.venue}`.toLowerCase().includes(q))
    .filter(ev => !game || ev.game === game)
    .filter(ev => !from || toDate(ev.date) >= from)
    .filter(ev => !to || toDate(ev.date) <= to)
    .map(ev => ({
      ...ev,
      distance: userLoc ? distKm(userLoc, {lat: ev.lat, lon: ev.lon}) : null
    }));

  filtered.sort((a, b) => {
    // Ordenação padrão: por proximidade, com desempate por data
    const da = a.distance ?? Infinity, db = b.distance ?? Infinity;
    if (da === db) return toDate(a.date) - toDate(b.date);
    return da - db;
  });

  $('#count').textContent = filtered.length;
  $('#empty').style.display = filtered.length ? 'none' : 'block';

  const res = $('#results');
  res.innerHTML = '';

  filtered.forEach(evento => {
    const tr = document.createElement("tr");
    // Codifica o local para ser usado na URL do Google Maps
    const linkMapa = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(evento.queryMapa)}`;

    tr.innerHTML = `
      <td>${evento.title}</td>
      <td>${evento.venue}</td>
      <td>${evento.cidade}</td>
      <td>${evento.data}</td>
      <td>${evento.distance ? km(evento.distance) : 'N/A'}</td>
      <td>
        <a href="${linkMapa}" target="_blank" rel="noopener" class="btn primary">Ver no mapa</a>
        <button class="btn" onclick="alert('Página de detalhes em construção!')">Detalhes</button>
      </td>
    `;
    res.appendChild(tr);
  });

  // Dica de localização
  $('#locHint').textContent = userLoc
    ? `Sua localização está ativa.`
    : 'Dica: ative sua localização para ver distâncias';
}

// --- Eventos UI ---
['q','game','from','to'].forEach(id => {
  document.getElementById(id).addEventListener('input', render);
  document.getElementById(id).addEventListener('change', render);
});

$('#clearFilters').addEventListener('click', () => {
  $('#q').value = '';
  $('#game').value = '';
  $('#from').value = '';
  $('#to').value = '';
  render();
});

$('#useLoc').addEventListener('click', () => {
  if (!('geolocation' in navigator)) {
    alert('Geolocalização não suportada neste navegador. Use a opção manual.');
    return;
  }
  navigator.geolocation.getCurrentPosition((pos) => {
    userLoc = { lat: pos.coords.latitude, lon: pos.coords.longitude };
    render();
  }, (err) => {
    console.warn('Geo erro', err);
    alert('Não foi possível obter sua localização. Verifique permissões do navegador.');
  }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
});

// --- Inicialização ---

// Pega o parâmetro 'game' da URL quando a página carrega
const urlParams = new URLSearchParams(window.location.search);
const gameFromUrl = urlParams.get('game');

// Se encontrou um 'game' na URL, define o valor do dropdown de filtro
if (gameFromUrl) {
    $('#game').value = gameFromUrl;
}

render(); // Renderiza os eventos na inicialização