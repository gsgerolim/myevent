<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não informado']);
    exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$data['id']]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado ou já removido']);
}
