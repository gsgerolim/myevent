document.addEventListener("DOMContentLoaded", () => {
  loadUsersList();

  //document.getElementById("btnRefreshUsers").addEventListener("click", loadUsersList);

  document.body.addEventListener("click", (e) => {
    if (e.target && e.target.id === "saveUserBtn") saveUser();
  });
});


let currentUserId = null; // null = novo usuário

async function loadUsersList() {
  const container = document.getElementById("adminUsersContainer");
  //container.innerHTML = "<p>Carregando usuários...</p>";

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
            <th>ID</th>
            <th>Usuário</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Ativo</th>
            <th>Criado em</th>
            <th>Ações</th>
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
            <button class="btn btn-sm btn-secondary me-1" onclick="toggleActive(${u.id}, ${u.active})">${u.active ? "Desativar" : "Ativar"}</button>
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
}

// Placeholder das ações (vamos preencher depois)
async function editUser(id) {
  currentUserId = id;
  const modal = new bootstrap.Modal(document.getElementById("userModalAdmin"));
  try {
    const res = await fetch("admin/users_edit.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ fetch: 1, id })
    });
    const data = await res.json();
    if (!data.success) return alert(data.message || "Erro ao carregar usuário.");

    const u = data.user;
    document.getElementById("editUserId").value = u.id;
    document.getElementById("editUserUsername").value = u.username;
    document.getElementById("editUserName").value = u.name ?? "";
    document.getElementById("editUserEmail").value = u.email ?? "";
    document.getElementById("editUserType").value = u.type;
    document.getElementById("editUserActive").checked = !!u.active;
    modal.show();
  } catch (err) {
    console.error(err);
    alert("Erro ao carregar usuário.");
  }
}




async function deleteUser(id) {
  if (!confirm("Tem certeza que deseja excluir este usuário?")) return;

  try {
    const res = await fetch("admin/users_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });

    const data = await res.json();
    console.log("Delete response:", data);

    if (!data.success) return alert(data.message || "Erro ao excluir usuário.");

    alert("Usuário excluído com sucesso!");
    loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao excluir usuário.");
  }
}


async function resetPassword(id) {
  if (!confirm("Deseja realmente resetar a senha deste usuário?")) return;

  try {
    const res = await fetch("admin/users_resetpass.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    console.log("Reset response:", data);

    if (!data.success) return alert(data.message || "Erro ao resetar senha.");

    alert(`Senha resetada com sucesso!\nNova senha: ${data.newPassword}`);
  } catch (err) {
    console.error(err);
    alert("Erro ao resetar senha.");
  }
}


async function toggleActive(id, active) {
  const action = active ? "desativar" : "ativar";
  if (!confirm(`Deseja realmente ${action} este usuário?`)) return;

  try {
    const res = await fetch("admin/users_toggle.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    console.log("Toggle response:", data);

    if (!data.success) return alert(data.message || "Erro ao atualizar status.");

    alert(`Usuário ${data.newStatus ? "ativado" : "desativado"} com sucesso.`);
    loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao alterar status.");
  }
}

async function viewSubscriptions(id) {
  const modalEl = document.getElementById("userSubscriptionsModal");
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

    if (!data.success) {
      body.innerHTML = "<p>Erro ao carregar inscrições.</p>";
      return;
    }

    if (data.events.length === 0) {
      body.innerHTML = "<p>Nenhuma inscrição encontrada.</p>";
      return;
    }

    let html = `
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Evento</th>
            <th>Cidade</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Inscrito em</th>
          </tr>
        </thead>
        <tbody>
    `;

    data.events.forEach(ev => {
      html += `
        <tr>
          <td>${ev.id}</td>
          <td>${ev.name}</td>
          <td>${ev.city ?? "-"}</td>
          <td>${ev.date_start}</td>
          <td>${ev.date_end}</td>
          <td>${ev.subscribed_at}</td>
        </tr>
      `;
    });

    html += "</tbody></table>";
    body.innerHTML = html;
  } catch (err) {
    console.error(err);
    body.innerHTML = "<p>Erro ao carregar inscrições.</p>";
  }

  modal.show();
}

function addUser() {
  currentUserId = null;
  const modal = new bootstrap.Modal(document.getElementById("userModalAdmin"));
  document.getElementById("editUserId").value = "";
  document.getElementById("editUserUsername").value = "";
  document.getElementById("editUserName").value = "";
  document.getElementById("editUserEmail").value = "";
  document.getElementById("editUserType").value = "participant";
  document.getElementById("editUserActive").checked = true;
  modal.show();
}

async function saveUser() {
  const id = document.getElementById("editUserId").value.trim();
  const username = document.getElementById("editUserUsername").value.trim();
  const name = document.getElementById("editUserName").value.trim();
  const email = document.getElementById("editUserEmail").value.trim();
  const type = document.getElementById("editUserType").value;
  const active = document.getElementById("editUserActive").checked;

  const url = currentUserId ? "admin/users_edit.php" : "admin/users_add.php";
  const payload = currentUserId
    ? { id, username, name, email, type, active }
    : { username, name, email, type, active };

  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (!data.success) return alert(data.message || "Erro ao salvar usuário.");

    alert(currentUserId ? "Usuário atualizado com sucesso!" : "Usuário adicionado com sucesso!");
    bootstrap.Modal.getInstance(document.getElementById("userModalAdmin")).hide();
    loadUsersList();
  } catch (err) {
    console.error(err);
    alert("Erro ao salvar usuário.");
  }
}