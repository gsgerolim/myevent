<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
requireAdmin();

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Propaganda excluída com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
