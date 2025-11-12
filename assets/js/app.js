// torna função acessível no escopo global pro botão do popup funcionar
window.eventsData = [];


window.showEventDetail = function(id) {
  const ev = window.eventsData.find(e => e.id == id);
  if (!ev) return;
  const modal = new bootstrap.Modal(document.getElementById("eventModal"));
  const title = document.getElementById("eventModalLabel");
  const body = document.getElementById("eventModalBody");

  title.textContent = ev.name;
  body.innerHTML = `
    <img src="${ev.image || 'assets/default.jpg'}" class="img-fluid rounded mb-3">
    <p><strong>Data:</strong> ${ev.date_start}</p>
    <p><strong>Local:</strong> ${ev.address}</p>
    <p>${ev.summary || ''}</p>
    <p><strong>Custo:</strong> ${ev.cost}</p>
  `;
  modal.show();
};


document.addEventListener("DOMContentLoaded", () => {
  const eventsContainer = document.getElementById("eventsContainer");
  const mapContainer = document.getElementById("mapContainer");
  const adsContainer = document.getElementById("adsInner");
  const sidebar = document.getElementById("sidebarOffcanvas");
  const sidebarBS = new bootstrap.Offcanvas(sidebar);
  const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
  const eventModalTitle = document.getElementById("eventModalLabel");
  const eventModalBody = document.getElementById("eventModalBody");
window.eventsData = [];
  let viewGrid = true;
  let pendingEventSubscription = null;

  function showToast(msg, type = "info") {
    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show();
    setTimeout(() => toast.remove(), 5000);
  }

  async function fetchJSON(url, opt = {}) {
    try {
      const res = await fetch(url, opt);
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || "Erro");
      return data;
    } catch (e) { showToast(e.message, "danger"); throw e; }
  }

async function loadAds() {
  try {
    const res = await fetchJSON("php/api/ads_active.php");
    const ads = res.data || res.ads || [];
    if (!ads.length) return;
    const adsInner = document.getElementById("adsInner");
    adsInner.innerHTML = "";

    ads.forEach((ad, i) => {
      adsInner.innerHTML += `
        <div class="carousel-item ${i === 0 ? 'active' : ''}" 
             data-id="${ad.id}" 
             data-title="${ad.title || ''}" 
             data-link="${ad.link || '#'}" 
             data-image="${ad.image}">
          <img src="${ad.image}" class="d-block w-100 rounded ad-slide" alt="${ad.title || 'Propaganda'}">
        </div>`;
    });

    const carouselEl = document.getElementById("adsContainerPublic");
    new bootstrap.Carousel(carouselEl, { interval: 5000, ride: "carousel" });

    // Clique direto na imagem abre o modal
    const modal = new bootstrap.Modal(document.getElementById("adDetailModal"));
    const titleEl = document.getElementById("adDetailTitle");
    const imageEl = document.getElementById("adDetailImage");
    const linkEl = document.getElementById("adDetailLink");

    adsInner.querySelectorAll(".ad-slide").forEach(img => {
      img.addEventListener("click", () => {
        const parent = img.closest(".carousel-item");
        titleEl.textContent = parent.dataset.title || "Propaganda";
        imageEl.src = parent.dataset.image;
        linkEl.href = parent.dataset.link;
        modal.show();
      });
    });
  } catch (err) {
    console.error(err);
  }
}



  async function loadEvents() {
    eventsContainer.innerHTML = `<div class="text-center text-muted mt-4">Carregando...</div>`;
    mapContainer.style.display = "none";
    try {
      const res = await fetchJSON("php/api/get_events.php");
      window.eventsData = res.events || [];
renderEvents(window.eventsData);

    } catch {
      eventsContainer.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar eventos.</div>`;
    }
  }

  function renderEvents(list) {
    mapContainer.style.display = "none";
    eventsContainer.style.display = "flex";
    eventsContainer.className = viewGrid ? "row view-grid" : "row view-list";
    if (list.length === 0) {
      eventsContainer.innerHTML = `<div class="text-center text-muted mt-4">Nenhum evento ativo.</div>`;
      return;
    }
    eventsContainer.innerHTML = "";
    list.forEach(ev => {
      const card = document.createElement("div");
      card.className = "col-md-4 mb-4";
      card.innerHTML = `
        <div class="card h-100 shadow-sm">
                    ${ev.isSubscribed ? '<span class="badge bg-success position-absolute top-0 end-0 m-2">✔ Inscrito</span>' : ''}

          <img src="${ev.image || 'assets/default.jpg'}" class="card-img-top" alt="${ev.name}">
          <div class="card-body d-flex flex-column">
            <h5>${ev.name}</h5>
            <p class="flex-grow-1 small text-muted">${ev.summary || ''}</p>
            <p class="text-secondary small">${ev.address}</p>
            <button class="btn btn-primary mt-auto" data-id="${ev.id}">Ver detalhes</button>
          </div>
        </div>`;
      eventsContainer.appendChild(card);
    });
    eventsContainer.querySelectorAll("button[data-id]").forEach(btn => {
      btn.addEventListener("click", () => showEventDetail(btn.dataset.id));
    });
  }

  function showEventDetail(id) {
    const ev = eventsData.find(e => e.id == id);
    if (!ev) return;
    eventModalTitle.textContent = ev.name;
    eventModalBody.innerHTML = `
  <img src="${ev.image || 'assets/default.jpg'}" class="img-fluid rounded mb-3">
  <p><strong>Data:</strong> ${ev.date_start}</p>
  <p><strong>Local:</strong> ${ev.address}</p>
  <p>${ev.summary || ''}</p>
  <p><strong>Custo:</strong> ${ev.cost}</p>
  <div id="mapEvent" style="height:250px;" class="rounded mb-3"></div>
  <div class="d-flex justify-content-between align-items-center">
    <span class="text-muted small">${ev.unlimited ? 'Vagas ilimitadas' : ev.capacity}</span>
    <div class="d-flex gap-2">
      <button id="subscribeBtn" class="btn ${ev.isSubscribed ? 'btn-danger' : 'btn-success'}">
        ${ev.isSubscribed ? 'Cancelar inscrição' : 'Inscrever-se'}
      </button>
      <button id="navigateBtn" class="btn btn-sm btn-success">Como chegar</button>
    </div>
  </div>
`;

   
    if (ev.latitude && ev.longitude) {
  const map = L.map("mapEvent").setView([ev.latitude, ev.longitude], 15);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution: "© OpenStreetMap" }).addTo(map);
  L.marker([ev.latitude, ev.longitude]).addTo(map).bindPopup(ev.name);

  document.getElementById("navigateBtn").onclick = () => {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const url = isIOS
      ? `http://maps.apple.com/?daddr=${ev.latitude},${ev.longitude}`
      : `https://www.google.com/maps/dir/?api=1&destination=${ev.latitude},${ev.longitude}`;
    window.open(url, "_blank");
  };
}

    eventModal.show();
    document.getElementById("subscribeBtn").onclick = () => subscribeToEvent(ev.id);

    
  }

  

  async function subscribeToEvent(id) {
    if (!window.isLoggedIn) {
      pendingEventSubscription = id;
      new bootstrap.Modal(document.getElementById("loginModal")).show();
      return;
    }
    try {
      const btn = document.getElementById("subscribeBtn");
      const action = btn.textContent.includes("Cancelar") ? "unsubscribe" : "subscribe";

      const res = await fetchJSON("php/api/subscribe.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event_id: id, action })
      });

      showToast(res.message || (action === "subscribe" ? "Inscrição confirmada!" : "Inscrição cancelada!"), "success");
      await loadEvents();
      eventModal.hide();
    } catch (err) {
      showToast(err.message || "Erro ao processar inscrição", "danger");
    }
  }


  document.getElementById("btnToggleView").onclick = () => { viewGrid = !viewGrid; renderEvents(eventsData); };

  document.querySelectorAll('.offcanvas-body a[data-action]').forEach(link => {
    link.onclick = e => {
      e.preventDefault();
      sidebarBS.hide();
      const action = link.dataset.action; // Corrigido aqui
      if (action === "inicio" || action === "eventos") renderEvents(eventsData);
      if (action === "mapa") showMap();
      if (action === "meus-eventos") loadMyEvents();
    };
  });

  const loginBtn = document.getElementById("menuLogin");
  if (loginBtn) loginBtn.onclick = e => {
    e.preventDefault();
    sidebarBS.hide();
    new bootstrap.Modal(document.getElementById("loginModal")).show();
  };
  const logoutBtn = document.getElementById("menuLogout");
  if (logoutBtn) {
    logoutBtn.onclick = async (e) => {
      e.preventDefault(); // evita reload da página
      try {
        const res = await fetch("php/api/logout.php", { method: "POST" });
        const data = await res.json();
        if (data.success) {
          window.isLoggedIn = false;
          window.currentUserName = null;
          showToast("Logout realizado com sucesso!", "success");
          location.reload(); // atualiza a página para refletir usuário deslogado
        } else {
          showToast(data.message || "Erro ao sair", "danger");
        }
      } catch (err) {
        console.error(err);
        showToast("Erro ao sair", "danger");
      }
    };
  }

  async function showMap() {
  eventsContainer.style.display = "none";
  mapContainer.style.display = "block";

  // se já existir mapa, remove
  if (mapContainer._leaflet_id) {
    mapContainer._leaflet_id = null;
  }

  mapContainer.innerHTML = ""; 
  mapContainer.style.height = "500px";

  const map = L.map(mapContainer).setView([-20.425, -51.365], 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap"
  }).addTo(map);

  // define URL conforme login
  const url = window.isLoggedIn ? "php/api/get_my_events.php" : "php/api/get_events.php";

  try {
    const res = await fetchJSON(url);
    const eventsToShow = res.events || [];

    if (!eventsToShow.length) {
      showToast("Nenhum evento para exibir no mapa", "info");
      return;
    }

    // adiciona marcadores
    eventsToShow.forEach(ev => {
  if (ev.latitude && ev.longitude) {
    const marker = L.marker([ev.latitude, ev.longitude]).addTo(map);

    // cria o popup com dois botões
    marker.bindPopup(`
      <div style="min-width:150px">
        <strong>${ev.name}</strong><br>
        <button class="btn btn-sm btn-primary mt-1" onclick="window.showEventDetail(${ev.id})">
          Ver mais
        </button>
        <button class="btn btn-sm btn-success mt-1" onclick="window.openNavigation(${ev.latitude}, ${ev.longitude})">
          Como chegar
        </button>
      </div>
    `);
  }
});

window.openNavigation = function(lat, lng) {
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  const url = isIOS
    ? `http://maps.apple.com/?daddr=${lat},${lng}`
    : `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
  window.open(url, "_blank");
};

    // ajusta zoom para mostrar todos
    const bounds = eventsToShow
      .filter(e => e.latitude && e.longitude)
      .map(e => [e.latitude, e.longitude]);
    if (bounds.length) map.fitBounds(bounds);
  } catch (err) {
    console.error(err);
    showToast("Erro ao carregar eventos no mapa", "danger");
  }
}

// Abrir modal de registro a partir do login
const openRegister = document.getElementById('openRegisterModal');
if (openRegister) {
  openRegister.addEventListener('click', e => {
    e.preventDefault();
    bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
    new bootstrap.Modal(document.getElementById('registerModal')).show();
  });
}


// Submissão do cadastro
document.getElementById('registerForm').addEventListener('submit', async e => {
  e.preventDefault();

  const name = document.getElementById('registerName').value.trim();
  const username = document.getElementById('registerUsername').value.trim();
  const email = document.getElementById('registerEmail').value.trim();
  const password = document.getElementById('registerPassword').value;

  try {
    const res = await fetch('php/api/register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, username, email, password })
    });

    const data = await res.json();
    if (!data.success) {
      showToast(data.message || 'Erro ao cadastrar', 'danger');
      return;
    }

    // Login automático após cadastro
    const loginRes = await fetch('php/api/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });

    const loginData = await loginRes.json();
    if (loginData.success) {
      window.isLoggedIn = true;
      window.currentUserName = loginData.name || username;
      showToast('Cadastro realizado! Você já está logado.', 'success');
      bootstrap.Modal.getInstance(document.getElementById("registerModal")).hide();
      location.reload();
    } else {
      showToast('Cadastro realizado, mas falha no login automático.', 'warning');
    }

  } catch (err) {
    console.error(err);
    showToast('Erro ao cadastrar', 'danger');
  }
});


  document.getElementById("loginForm").onsubmit = async e => {
    e.preventDefault();
    const username = document.getElementById("loginUsername").value.trim();
    const password = document.getElementById("loginPassword").value;
    try {
      const r = await fetchJSON("php/api/login.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ username, password }) });
      window.isLoggedIn = true;
      window.currentUserName = r.name;
      showToast("Login efetuado com sucesso!", "success");
      bootstrap.Modal.getInstance(document.getElementById("loginModal")).hide();
      if (pendingEventSubscription) { subscribeToEvent(pendingEventSubscription); pendingEventSubscription = null; }
    } catch { }
  };


  async function loadMyEvents() {
    eventsContainer.innerHTML = `<div class="text-center text-muted mt-4">Carregando seus eventos...</div>`;
    mapContainer.style.display = "none";
    eventsContainer.style.display = "flex";
    try {
      const res = await fetchJSON("php/api/get_my_events.php");
      const myEvents = res.events || [];
      renderMyEvents(myEvents);
    } catch (err) {
      eventsContainer.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar seus eventos.</div>`;
    }
  }

  function renderMyEvents(events) {
    eventsContainer.innerHTML = "";
    if (events.length === 0) {
      eventsContainer.innerHTML = `<div class="text-center text-muted mt-4">Você não está inscrito em nenhum evento.</div>`;
      return;
    }

    const table = document.createElement("table");
    table.className = "table table-striped table-hover";

    const thead = document.createElement("thead");
    thead.innerHTML = `<tr>
        <th>Evento</th>
        <th>Data</th>
        <th>Ações</th>
    </tr>`;
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    events.forEach(ev => {
      const date = new Date(ev.date_start);
      const monthNames = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
      const dateStr = `${monthNames[date.getMonth()]}/${date.getFullYear()}`;

      const tr = document.createElement("tr");
      tr.innerHTML = `
            <td>${ev.name}</td>
            <td>${dateStr}</td>
            <td>
                <button class="btn btn-sm btn-primary me-1" data-id="${ev.id}" data-action="view">Ver Evento</button>
                <button class="btn btn-sm btn-secondary" data-id="${ev.id}" data-action="certificate">Certificado</button>
            </td>
        `;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    eventsContainer.appendChild(table);

    // Event buttons
    eventsContainer.querySelectorAll('button[data-action="view"]').forEach(btn => {
      btn.addEventListener("click", () => showEventDetail(btn.dataset.id));
    });

    // Certificado button apenas visual
    eventsContainer.querySelectorAll('button[data-action="certificate"]').forEach(btn => {
      btn.addEventListener("click", () => {
        showToast("Botão de certificado (PDF) clicado", "info");
      });
    });
  }
async function loadAdminPanel() {
  if (!window.isAdmin) {
    showToast("Acesso negado", "danger");
    return;
  }

  eventsContainer.style.display = "flex";
  mapContainer.style.display = "none";
  eventsContainer.innerHTML = `<div class="text-center text-muted mt-4">Carregando painel...</div>`;

  try {
    const res = await fetch("admin/dashboard.php");
    const html = await res.text();
    eventsContainer.innerHTML = html;

    // **INICIALIZA O admin.js APÓS O HTML SER INJETADO**
    if (window.admin && typeof window.admin.init === "function") {
      window.admin.init();
    }

  } catch (err) {
    eventsContainer.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar painel.</div>`;
  }
}





  document.querySelectorAll('.offcanvas-body a').forEach(link => {
    link.addEventListener('click', async e => {
      const action = link.dataset.action;
      if (!action) return;

      e.preventDefault();
      sidebarBS.hide();

      if (action === 'inicio') {
        await loadEvents();
      }
      if (action === 'eventos') {
        await loadEvents();
      }
      if (action === 'mapa') {
        await showMap();
      }
      if (action === 'meus-eventos') {
        await loadMyEvents();
      }
      if (action === 'painel') {
        await loadAdminPanel();
      }
    });
  });



  loadAds();
  loadEvents();


});
