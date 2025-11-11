<!-- php/admin/ads_modal.php -->
<div class="modal fade" id="adModal" tabindex="-1" aria-labelledby="adModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adModalLabel">Nova Propaganda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="adForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="adId">

          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control" name="title" id="adTitle" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Link (opcional)</label>
            <input type="url" class="form-control" name="link" id="adLink">
          </div>

          <div class="mb-3">
            <label class="form-label">Imagem da propaganda</label>
            <input type="file" class="form-control" id="adImageInput" accept="image/*" <?= $isAdmin ? '' : 'disabled' ?>>
            <div class="mt-3 text-center">
              <img id="adPreview" style="max-width: 100%; border-radius: 8px; display:none;">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Duração do slide (segundos)</label>
            <input type="number" class="form-control" name="display_time" id="adDisplayTime" value="5" min="1" required>
          </div>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="adActive" checked>
            <label class="form-check-label" for="adActive">Ativa</label>
          </div>

          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
