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
$stmt = $pdo->prepare("SELECT active FROM users WHERE id = ?");
$stmt->execute([$data['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    exit;
}

$newStatus = $user['active'] ? 0 : 1;

$update = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
$update->execute([$newStatus, $data['id']]);

echo json_encode([
    'success' => true,
    'newStatus' => $newStatus
]);
