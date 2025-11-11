document.addEventListener("DOMContentLoaded", () => {
  loadUsersList();

  document.getElementById("btnRefreshUsers").addEventListener("click", loadUsersList);
});

async function loadUsersList() {
  const container = document.getElementById("adminUsersContainer");
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

    // Monta tabela
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
function editUser(id) {
  alert("Editar usuário ID: " + id);
}

function deleteUser(id) {
  alert("Excluir usuário ID: " + id);
}

function resetPassword(id) {
  alert("Resetar senha do usuário ID: " + id);
}

function toggleActive(id, active) {
  alert(`${active ? "Desativar" : "Ativar"} usuário ID: ${id}`);
}

function viewSubscriptions(id) {
  alert("Ver inscrições do usuário ID: " + id);
}
