// =======================
// Listar usuários
// =======================
window.loadUsersList = async function() {
  const container = document.getElementById("adminUsersContainer");
  if (!container) return; // evita erro se container ainda não existe

  container.innerHTML = "<p>Carregando usuários...</p>";

  try {
    const res = await fetch("admin/users_get.php");
    const data = await res.json();

    if (!data.success) {
      container.innerHTML = "<p>Erro ao carregar usuários.</p>";
      return;
    }

    if (!data.users || data.users.length === 0) {
      container.innerHTML = "<p>Nenhum usuário encontrado.</p>";
      return;
    }

    let html = `
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th><th>Usuário</th><th>Nome</th><th>Email</th>
            <th>Tipo</th><th>Ativo</th><th>Criado em</th><th>Ações</th>
          </tr>
        </thead>
        <tbody>
    `;

    data.users.forEach(u => {
      html += `
        <tr>
          <td>${u.id}</td>
          <td>${u.username}</td>
          <td>${u.name ?? "-"}</td>
          <td>${u.email ?? "-"}</td>
          <td>${u.type}</td>
          <td>${u.active ? "✅" : "❌"}</td>
          <td>${u.created_at}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1" onclick="editUser(${u.id})">Editar</button>
            <button class="btn btn-sm btn-danger me-1" onclick="deleteUser(${u.id})">Excluir</button>
            <button class="btn btn-sm btn-warning me-1" onclick="resetPassword(${u.id})">Resetar Senha</button>
            <button class="btn btn-sm btn-secondary me-1" onclick="toggleActive(${u.id}, ${u.active})">
              ${u.active ? "Desativar" : "Ativar"}
            </button>
            <button class="btn btn-sm btn-info me-1" onclick="viewSubscriptions(${u.id})">Inscrições</button>
          </td>
        </tr>
      `;
    });

    html += "</tbody></table>";
    container.innerHTML = html;
  } catch (err) {
    console.error(err);
    container.innerHTML = "<p>Erro ao carregar usuários.</p>";
  }
};

// =======================
// Adicionar / Editar
// =======================
window.addUser = function() {
  window.currentUserId = null;
  const modalEl = document.getElementById("userModalAdmin");
  if (!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);

  const fields = ["editUserId","editUserUsername","editUserName","editUserEmail","editUserType","editUserActive"];
  fields.forEach(f => {
    const el = document.getElementById(f);
    if (!el) return;
    if (f === "editUserType") el.value = "participant";
    else if (f === "editUserActive") el.checked = true;
    else el.value = "";
  });

  const saveBtn = document.getElementById("saveUserBtn");
  if (saveBtn) saveBtn.onclick = window.saveUser; // liga listener aqui

  modal.show();
};

window.editUser = async function(id) {
  window.currentUserId = id;
  const modalEl = document.getElementById("userModalAdmin");
  if (!modalEl) return;
  const modal = new bootstrap.Modal(modalEl);

  try {
    const res = await fetch("admin/users_edit.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ fetch: 1, id })
    });
    const data = await res.json();
    if (!data.success) return alert(data.message || "Erro ao carregar usuário.");

    const u = data.user;
    const fields = {
      editUserId: u.id,
      editUserUsername: u.username,
      editUserName: u.name ?? "",
      editUserEmail: u.email ?? "",
      editUserType: u.type,
      editUserActive: !!u.active
    };

    Object.keys(fields).forEach(f => {
      const el = document.getElementById(f);
      if (!el) return;
      if (f === "editUserActive") el.checked = fields[f];
      else el.value = fields[f];
    });

    const saveBtn = document.getElementById("saveUserBtn");
    if (saveBtn) saveBtn.onclick = window.saveUser;

    modal.show();
  } catch (err) {
    console.error(err);
    alert("Erro ao carregar usuário.");
  }
};

// =======================
// Salvar usuário
// =======================
window.saveUser = async function() {
  const fields = ["editUserId","editUserUsername","editUserName","editUserEmail","editUserType","editUserActive"];
  const data = {};
  fields.forEach(f => {
    const el = document.getElementById(f);
    if (!el) return;
    if (f === "editUserActive") data[f] = el.checked;
    else data[f] = el.value.trim();
  });

  const url = window.currentUserId ? "admin/users_edit.php" : "admin/users_add.php";
  const payload = window.currentUserId
    ? { id: data.editUserId, username: data.editUserUsername, name: data.editUserName, email: data.editUserEmail, type: data.editUserType, active: data.editUserActive }
    : { username: data.editUserUsername, name: data.editUserName, email: data.editUserEmail, type: data.editUserType, active: data.editUserActive };

  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    if (!result.success) return alert(result.message || "Erro ao salvar usuário.");

    alert(window.currentUserId ? "Usuário atualizado!" : "Usuário adicionado!");
    const modalEl = document.getElementById("userModalAdmin");
    bootstrap.Modal.getInstance(modalEl)?.hide();
    window.loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao salvar usuário.");
  }
};

// =======================
// Excluir / Reset / Ativar
// =======================
window.deleteUser = async function(id) {
  if (!confirm("Tem certeza que deseja excluir este usuário?")) return;
  try {
    const res = await fetch("admin/users_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (!data.success) return alert(data.message || "Erro ao excluir usuário.");
    alert("Usuário excluído!");
    window.loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao excluir usuário.");
  }
};

window.resetPassword = async function(id) {
  if (!confirm("Deseja realmente resetar a senha deste usuário?")) return;
  try {
    const res = await fetch("admin/users_resetpass.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (!data.success) return alert(data.message || "Erro ao resetar senha.");
    alert(`Senha resetada com sucesso!\nNova senha: ${data.newPassword}`);
  } catch (err) {
    console.error(err);
    alert("Erro ao resetar senha.");
  }
};

window.toggleActive = async function(id, active) {
  const action = active ? "desativar" : "ativar";
  if (!confirm(`Deseja realmente ${action} este usuário?`)) return;
  try {
    const res = await fetch("admin/users_toggle.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (!data.success) return alert(data.message || "Erro ao atualizar status.");
    alert(`Usuário ${data.newStatus ? "ativado" : "desativado"}!`);
    window.loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao alterar status.");
  }
};

window.viewSubscriptions = async function(id) {
  const modalEl = document.getElementById("userSubscriptionsModal");
  if (!modalEl) return;
  const modal = new bootstrap.Modal(modalEl);
  const body = document.getElementById("subscriptionsBody");
  const title = document.getElementById("subscriptionsTitle");

  body.innerHTML = "<p>Carregando...</p>";
  title.textContent = "Inscrições do Usuário ID " + id;

  try {
    const res = await fetch("admin/users_subscriptions.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();

    if (!data.success) { body.innerHTML = "<p>Erro ao carregar inscrições.</p>"; return; }
    if (!data.events.length) { body.innerHTML = "<p>Nenhuma inscrição encontrada.</p>"; return; }

    let html = `<table class="table table-bordered table-sm">
      <thead><tr><th>ID</th><th>Evento</th><th>Cidade</th><th>Início</th><th>Fim</th><th>Inscrito em</th></tr></thead><tbody>`;

    data.events.forEach(ev => {
      html += `<tr>
        <td>${ev.id}</td>
        <td>${ev.name}</td>
        <td>${ev.city ?? "-"}</td>
        <td>${ev.date_start}</td>
        <td>${ev.date_end}</td>
        <td>${ev.subscribed_at}</td>
      </tr>`;
    });

    html += "</tbody></table>";
    body.innerHTML = html;
  } catch (err) {
    console.error(err);
    body.innerHTML = "<p>Erro ao carregar inscrições.</p>";
  }

  modal.show();
};
