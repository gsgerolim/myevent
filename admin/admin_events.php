<!-- admin_events.php -->
<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Gestão de Eventos</h2>
    <button id="btnAddEvent" class="btn btn-success">Adicionar Evento</button>
  </div>

  <div id="adminEventsContainer">
    <!-- Tabela de eventos será carregada aqui pelo admin.js -->
  </div>

</div>

<!-- Modal de Edição de Evento -->
<!-- Modal de Edição de Evento -->
<div class="modal fade" id="eventModalAdmin" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="eventFormAdmin">
        <div class="modal-header">
          <h5 class="modal-title">Gerenciar Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="eventId" name="id">

          <div class="mb-3">
            <label>Nome</label>
            <input type="text" id="eventName" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Resumo</label>
            <textarea id="eventSummary" name="summary" class="form-control"></textarea>
          </div>

          <div class="row">
            <div class="col">
              <label>Data Início</label>
              <input type="datetime-local" id="eventDateStart" name="date_start" class="form-control">
            </div>
            <div class="col">
              <label>Data Fim</label>
              <input type="datetime-local" id="eventDateEnd" name="date_end" class="form-control">
            </div>
          </div>

          <div class="mt-3">
            <label>Endereço</label>
            <input type="text" id="eventAddress" name="address" class="form-control">
          </div>

          <div class="mt-3">
            <label>Cidade</label>
            <input type="text" id="eventCity" name="city" class="form-control">
          </div>

          <div class="mt-3">
            <label>Capacidade</label>
            <input type="number" id="eventCapacity" name="capacity" class="form-control">
            <div class="form-check mt-2">
              <input type="checkbox" id="eventUnlimited" name="unlimited" class="form-check-input">
              <label for="eventUnlimited" class="form-check-label">Ilimitado</label>
            </div>
          </div>

          <div class="mt-3">
            <label>Custo</label>
            <input type="text" id="eventCost" name="cost" class="form-control">
          </div>

          <div class="mt-3">
            <label>Latitude</label>
            <input type="text" id="eventLatitude" name="latitude" class="form-control">
          </div>

          <div class="mt-3">
            <label>Longitude</label>
            <input type="text" id="eventLongitude" name="longitude" class="form-control">
          </div>

          <div id="eventMap" style="height: 300px; border-radius: 10px; margin-top: 10px;"></div>

          <!-- IMAGEM DO EVENTO -->
          <div class="mt-3">
            <label>Imagem</label>
            <input type="text" id="eventImage" name="image" class="form-control mb-2" readonly placeholder="URL da imagem">
            <input type="file" id="eventImageFile" class="form-control">
            <img id="eventImagePreview" style="max-width:100%; margin-top:10px; display:none;">
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Exportação -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="exportForm">
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Exportar Inscritos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="exportEventId" name="id" />

          <div class="mb-3">
            <label class="form-label">Formato do arquivo</label>
            <select class="form-select" id="exportFormat" name="format" required>
              <option value="pdf">PDF</option>
              <option value="csv">CSV</option>
              <option value="xls">XLS</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Campos para exportar</label>
            <select class="form-select" id="exportFields" name="fields[]" multiple required>
              <option value="all" selected>Todos os campos</option>
              <option value="name">Nome</option>
              <option value="email">E-mail</option>
              <option value="phone">Telefone</option>
              <option value="registered_at">Data de Inscrição</option>
            </select>
            <small class="text-muted">Use CTRL ou CMD para selecionar vários</small>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Exportar</button>
        </div>
      </form>
    </div>
  </div>
</div>