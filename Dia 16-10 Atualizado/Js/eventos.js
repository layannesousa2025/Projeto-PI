 // --- Dados de exemplo (substitua por sua API) ---
    const events = [
      { id: 1, title: "Caminhada", lugar: "circuito praça do Buriti", cidade: "Brasília/DF", venue: "PRAÇA DO BURITI , Brasília, DF, Brasil ", date: "2025-11-30", lat: -70655-775,},

      { id: 2, title: "Academia", lugar: "SmatFlit", cidade: "Brasilia/DF", venue: "St. N QNN, 09 Ceilândia Norte - Ceilândia, Brasília - DF ", date: "2025-08-15", lat: -72225-90, },

      { id: 3, title: "Ciclimismo", lugar: "BRASÍLIA BIKE CAMP ", cidade: "Brasilia/DF", venue: " SIT Torre de TV Digital de Brasília", date: "2025-05-13", lat: -70297-400,  },

      { id: 4, title: "Natacao", lugar: "Manta - natação 610 sul", cidade: "Brasilia/DF", venue: " Dentro do Sindilegis - 610, lote 70", date: "2025-06-25", lat: -70200-700,  },

      { id: 5, title: "Voleibol", lugar: "Brasília Vôlei Esporte Clube", cidade: "Brasilia/DF", venue: "St. F Norte - Taguatinga, Brasília ", date: "2025-09-21", lat: -72135-120, },
      
      { id: 6, title: "PCD", lugar: "#", cidade: "#", venue: "#", date: "2025-09-30", lat: -22.909938,  }
    ];

 

    // --- Estado ---
    let userLoc = null; // {lat, lon}

    // --- Utilidades ---
    const $ = sel => document.querySelector(sel);
    const km = n => `${n.toFixed(1)} km`;

    function toDate(s) { const d = new Date(s + 'T00:00:00'); d.setHours(12); return d; }

    // Haversine
    function distKm(a, b) {
      if (!a || !b) return Infinity;
      const R = 6371;
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
        .filter(ev => !q || `${ev.title} ${ev.city} ${ev.game}`.toLowerCase().includes(q))
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

      for (const ev of filtered) {
        const card = document.createElement('article');
        card.className = 'card';
        card.innerHTML = `
          <div class="title">${ev.title}</div>
          <div class="muted">🗓️ ${new Date(ev.date).toLocaleDateString('pt-BR')} · 📌 ${ev.venue} · ${ev.city}</div>
          <div class="inline">
            <a class="btn" href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(ev.venue + ' ' + ev.city)}" target="_blank" rel="noopener">Ver no mapa</a>
            <a class="btn" href="#" onclick="alert('Conecte a página do evento aqui.'); return false;">Detalhes</a>
          </div>`;
        res.appendChild(card);
      }

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

    // Inicializa
    render();