<?php
require_once __DIR__ . '/lib/auth.php';
$user = getUser();
$isAdmin = $user && ($user['type'] ?? '') === 'admin';
?>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="loginForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Usuário</label>
          <input type="text" class="form-control" id="loginUsername" required>
        </div>
        <div class="mb-3">
          <label>Senha</label>
          <input type="password" class="form-control" id="loginPassword" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary w-100" type="submit">Entrar</button>
      </div>
      <!-- Botão dentro do modal de login -->
      <div class="text-center mt-2">
        <a href="#" id="openRegisterModal">Cadastre-se</a>
      </div>

    </form>
  </div>
</div>

<!-- Modal Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="registerForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cadastro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Nome</label><input type="text" class="form-control" id="registerName" required></div>
        <div class="mb-3"><label>Usuário</label><input type="text" class="form-control" id="registerUsername" required></div>
        <div class="mb-3"><label>Email</label><input type="email" class="form-control" id="registerEmail"></div>
        <div class="mb-3"><label>Senha</label><input type="password" class="form-control" id="registerPassword" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary w-100" type="submit">Cadastrar</button>
      </div>
    </form>
  </div>
</div>