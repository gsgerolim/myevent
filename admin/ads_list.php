<?php
// php/admin/ads_list.php
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$pdo = getPDO();

// Carrega todas as propagandas
$stmt = $pdo->query("
    SELECT id, title, image, link, active, display_time, created_at
    FROM ads
    ORDER BY created_at DESC
");

$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-striped align-middle" id="adsTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Prévia</th>
                    <th>Título</th>
                    <th>Link</th>
                    <th>Ativa</th>
                    <th>Duração (s)</th>
                    <th>Criada em</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($ads) > 0): ?>
                    <?php foreach ($ads as $ad): ?>
                        <tr data-id="<?= htmlspecialchars($ad['id']) ?>">
                            <td><?= $ad['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($ad['image']) ?>" alt="ad" style="height: 50px; border-radius: 6px;">
                            </td>
                            <td><?= htmlspecialchars($ad['title']) ?></td>
                            <td>
                                <?php if ($ad['link']): ?>
                                    <a href="<?= htmlspecialchars($ad['link']) ?>" target="_blank">Visitar</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="checkbox" class="form-check-input toggle-ad-active"
                                       <?= $ad['active'] ? 'checked' : '' ?>>
                            </td>
                            <td><?= htmlspecialchars($ad['display_time']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ad['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary btnEditAd">Editar</button>
                                <button class="btn btn-sm btn-danger btnDeleteAd">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Nenhuma propaganda cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
