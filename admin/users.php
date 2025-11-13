<?php

require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin(); // Garante que só administradores acessem
?>

<div class="container mt-4">
    <h4 class="mb-3">👥 Gestão de Usuários</h4>

    <div class="d-flex justify-content-between mb-3">
<button class="btn btn-success" onclick="addUser()">Adicionar Usuário</button>
        <button id="btnExportUsers" class="btn btn-outline-primary">Exportar Lista</button>
    </div>

    <div id="adminUsersContainer">
        <div class="text-center text-muted mt-3">Carregando usuários...</div>
    </div>
</div>

<!-- Modal de Usuário -->
<div class="modal fade" id="userModalAdmin" tabindex="-1" aria-labelledby="userModalAdminLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalAdminLabel">Editar Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editUserId">
        <div class="mb-3">
          <label>Usuário</label>
          <input type="text" id="editUserUsername" class="form-control">
        </div>
        <div class="mb-3">
          <label>Nome</label>
          <input type="text" id="editUserName" class="form-control">
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" id="editUserEmail" class="form-control">
        </div>
        <div class="mb-3">
          <label>Tipo</label>
          <select id="editUserType" class="form-select">
            <option value="participant">Participante</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="editUserActive">
          <label class="form-check-label" for="editUserActive">Conta Ativa</label>
        </div>
      </div>
      <div class="modal-footer">
<button id="saveUserBtn" class="btn btn-success">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal de Exportação -->
<div class="modal fade" id="exportUsersModal" tabindex="-1" aria-labelledby="exportUsersModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="exportUsersForm">
        <div class="modal-header">
          <h5 class="modal-title" id="exportUsersModalLabel">Exportar Lista de Usuários</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label for="exportUsersFormat" class="form-label">Formato</label>
          <select id="exportUsersFormat" class="form-select mb-3" required>
            <option value="pdf">PDF</option>
            <option value="csv">CSV</option>
            <option value="xls">XLS</option>
          </select>

          <label for="exportUsersFields" class="form-label">Campos a exportar</label>
          <select id="exportUsersFields" class="form-select" multiple size="6">
            <option value="all" selected>Todos os campos</option>
            <option value="id">ID</option>
            <option value="username">Usuário</option>
            <option value="name">Nome</option>
            <option value="email">E-mail</option>
            <option value="type">Tipo</option>
            <option value="created_at">Criado em</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Exportar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Inscrições -->
<div class="modal fade" id="userSubscriptionsModal" tabindex="-1" aria-labelledby="subscriptionsTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subscriptionsTitle">Inscrições do Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="subscriptionsBody">
        <p>Carregando...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
