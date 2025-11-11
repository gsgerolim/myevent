<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if (!$id) json_response(['success' => false, 'message' => 'ID inválido'], 400);

$pdo = getPDO();

// impede exclusão do próprio admin
if ($id == $_SESSION['user_id']) {
    json_response(['success' => false, 'message' => 'Você não pode excluir sua própria conta!'], 403);
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

json_response(['success' => true, 'message' => 'Usuário excluído com sucesso!']);
