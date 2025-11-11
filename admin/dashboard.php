<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin(); // garante que apenas admins acessem

?>
<script src="admin/admin.js"></script>

<div class="d-flex flex-wrap gap-3 mt-3">
    <button class="btn btn-primary" id="btnManageEvents">Gestão de Eventos</button>
    <button class="btn btn-secondary" id="btnManageUsers">Gestão de Usuários</button>
    <button class="btn btn-warning" id="btnManageAds">Gestão de Propagandas</button>
</div>
<div id="adminContent" class="mt-4"></div>