<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/php/lib/auth.php';
require_once __DIR__ . '/php/lib/config_global.php';
require_once __DIR__ . '/php/lib/db.php';

$user = getUser();
$isAdmin = $user && ($user['type'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($config['site_title'] ?? 'Eventos') ?></title>
  <link rel="icon" href="<?= htmlspecialchars($config['favicon_path'] ?? 'assets/uploads/sample1.jpg') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet">

</head>

<body class="theme-light">
  <!-- NAVBAR -->
  <nav class="navbar navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container-fluid">
      <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">☰</button>
      <span class="navbar-brand"><?= htmlspecialchars($config['site_title'] ?? 'Eventos') ?></span>
      <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#filtroOffcanvas">🔍</button>
    </div>
  </nav>

  <!-- SIDEBAR -->
  <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header border-bottom border-secondary">
      <img src="<?= htmlspecialchars($config['logo_path'] ?? 'assets/uploads/sample2.jpg') ?>" alt="Logo"
        style="height:40px;">
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
      <!--a href="#" class="nav-link text-white mb-2" data-action="inicio">🏠 Início</a -->
      <a href="#" class="nav-link text-white mb-2" data-action="eventos">📅 Eventos</a>
      <a href="#" class="nav-link text-white mb-2" data-action="mapa">🗺️ Meu Mapa</a>

      <?php if (!$user): ?>
        <a href="#" class="nav-link text-white mb-2" id="menuLogin">🔐 Login / Cadastro</a>
      <?php else: ?>
        <div class="border-top border-secondary mt-2 pt-2 mb-2"></div>
        <span class="text-secondary small mb-2">Participante</span>
        <a href="#" class="nav-link text-white mb-2" id="menuPerfil">👤 Meu Perfil</a>
        <a href="#" class="nav-link text-white mb-2" id="menuMeusEventos" data-action="meus-eventos">🧾 Meus Eventos</a>
        <?php if ($isAdmin): ?>
          <div class="border-top border-secondary mt-2 pt-2 mb-2"></div>
          <span class="text-secondary small mb-2">Administração</span>
          <a href="#" class="nav-link text-white mb-2" data-action="painel">⚙️ Painel</a>
          <a href="admin/config.php" class="nav-link text-white mb-2">🎨 Configuração</a>
        <?php endif; ?>
        <a href="#" id="menuLogout" class="nav-link text-white mt-3 mb-2">🚪 Sair</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- CONTEÚDO -->
  <main class="pt-5 mt-4">
    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Eventos Ativos</h2>
        <button class="btn btn-outline-secondary" id="btnToggleView" title="Alternar exibição">🔳</button>
      </div>
      <div id="adsContainerPublic" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner" id="adsInner"></div>

        <button class="carousel-control-prev" type="button" data-bs-target="#adsContainerPublic" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#adsContainerPublic" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>

       
      </div>

      <!-- Modal detalhes da propaganda -->
      <div class="modal fade" id="adDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="adDetailTitle"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              <img id="adDetailImage" src="" class="img-fluid rounded mb-3">
              <a id="adDetailLink" href="#" target="_blank" class="btn btn-success">Ir para o site</a>
            </div>
          </div>
        </div>
      </div>


      <div id="eventsContainer" class="row view-grid"></div>
      <div id="mapContainer" style="height:500px; display:none;"></div>
    </div>
  </main>

  <!-- FILTRO -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="filtroOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Filtros</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <form id="filterForm">
        <div class="mb-3"><label class="form-label">Nome</label><input class="form-control" id="q"
            placeholder="Pesquisar..."></div>
        <div class="mb-3"><label class="form-label">Data de Início</label><input type="date" id="date_from"
            class="form-control"></div>
        <div class="mb-3"><label class="form-label">Data de Fim</label><input type="date" id="date_to"
            class="form-control"></div>
        <div class="mb-3"><label class="form-label">Cidade</label><input class="form-control" id="city"
            placeholder="Digite a cidade"></div>
        <button type="button" id="btnFilter" class="btn btn-primary w-100">Filtrar</button>
      </form>
    </div>
  </div>

  <!-- MODAIS -->
  <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Detalhes do Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="eventModalBody"></div>
      </div>
    </div>
  </div>

  <?php include 'php/modals.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="assets/js/app.js"></script>
  <script src="assets/js/admin.js"></script>

  <script>
    window.isLoggedIn = <?= $user ? 'true' : 'false' ?>;
    window.isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
    window.currentUserName = <?= $user ? json_encode($user['name'] ?? $user['username']) : 'null' ?>;
  </script>

</body>

</html>