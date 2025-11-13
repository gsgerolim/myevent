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

$newPassword = bin2hex(random_bytes(4)); // gera senha aleatória curta
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$pdo = getPDO();
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
$stmt->execute([$hash, $data['id']]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'newPassword' => $newPassword]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
}
