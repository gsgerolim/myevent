<?php
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$active = $_POST['active'] ?? null;

if (!$id || !isset($active)) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("UPDATE ads SET active = :active WHERE id = :id");
    $stmt->execute([':active' => filter_var($active, FILTER_VALIDATE_BOOLEAN), ':id' => $id]);
    echo json_encode(['success' => true, 'message' => 'Status atualizado']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
