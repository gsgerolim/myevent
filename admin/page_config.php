<?php
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();
require_once __DIR__ . '/../php/lib/db.php';
$pdo = getPDO();
$config = $pdo->query("SELECT * FROM global_config WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<div class="container p-4">
    <h2 class="mb-4">🎨 Configuração do Sistema</h2>

    <div class="card shadow config-card mb-4">
        <div class="card-body">
            <h5>Informações Gerais</h5>
            <div class="mb-3">
                <label class="form-label">Título do site</label>
                <input type="text" id="siteTitle" class="form-control"
                       value="<?= htmlspecialchars($config['site_title'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nome da plataforma</label>
                <input type="text" id="pageTitle" class="form-control"
                       value="<?= htmlspecialchars($config['page_title'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="card shadow config-card mb-4">
        <div class="card-body">
            <h5>Logotipo e Ícone</h5>
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" id="logoUpload" accept="image/*" class="form-control">
                    <img src="<?= htmlspecialchars($config['logo_path'] ?? '') ?>" id="logoPreview"
                         class="upload-preview mt-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Favicon</label>
                    <input type="file" id="faviconUpload" accept="image/*" class="form-control">
                    <img src="<?= htmlspecialchars($config['favicon_path'] ?? '') ?>" id="faviconPreview"
                         class="upload-preview mt-2">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow config-card mb-4">
        <div class="card-body">
            <h5>Paleta de Cores</h5>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Tema Claro</h6>
                    <div id="themeLightColors" class="d-flex flex-wrap gap-3"></div>
                </div>
                <div class="col-md-6">
                    <h6>Tema Escuro</h6>
                    <div id="themeDarkColors" class="d-flex flex-wrap gap-3"></div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <h6 class="m-0">Prévia do Tema</h6>
                <button id="togglePreviewTheme" class="btn btn-outline-secondary btn-sm">Simular Tema Escuro</button>
            </div>
            <div id="themePreview" class="mt-3 border p-3 rounded">
                <h5>Prévia do Tema</h5>
                <p>As alterações de cor serão refletidas aqui.</p>
                <button class="btn btn-primary me-2">Botão primário</button>
                <button class="btn btn-secondary">Botão secundário</button>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button id="saveConfig" class="btn btn-primary px-4">💾 Salvar Alterações</button>
    </div>
</div>
