<?php
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();
?>
<div class="container mt-4">
  <h4 class="mb-3">📢 Gestão de Propagandas</h4>

  <div class="d-flex justify-content-between mb-3">
    <button id="btnAddAd" class="btn btn-success">Adicionar Propaganda</button>
    <button id="btnReloadAds" class="btn btn-outline-primary">Recarregar Lista</button>
  </div>

  <div id="adsContainerAdmin" class="table-responsive">
    <div class="text-center text-muted mt-3">Carregando propagandas...</div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="adModal" tabindex="-1" aria-labelledby="adModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adModalLabel">Adicionar Propaganda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="adForm" enctype="multipart/form-data">
          <input type="hidden" id="adId" name="id">
          <input type="hidden" id="adCroppedImage" name="cropped_image">

          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control" id="adTitle" name="title" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Link</label>
            <input type="text" class="form-control" id="adLink" name="link">
          </div>

          <div class="mb-3">
            <label class="form-label">Tempo de Exibição (segundos)</label>
            <input type="number" class="form-control" id="adDisplayTime" name="display_time" min="1" value="5">
          </div>

          <div class="mb-3">
            <label class="form-label">Imagem da Propaganda</label>
            <input type="file" class="form-control" id="adImage" name="image" accept="image/*">
            <small class="text-muted">Proporção fixa 1200x600 (2:1)</small>

            <div class="mt-3 text-center" id="cropContainer" style="display:none;">
              <img id="adCropper" src="" alt="Crop Preview" style="max-width:100%; border-radius:8px;">
            </div>

            <div class="mt-3 text-center">
              <img id="adPreview" src="" alt="Preview" style="max-width:100%; max-height:300px; display:none; border-radius:8px;">
            </div>
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="adActive" name="active" checked>
            <label class="form-check-label" for="adActive">Ativa</label>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" id="saveAdBtn" class="btn btn-primary">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
