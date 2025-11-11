<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';
requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if (!$id) json_response(['success' => false, 'message' => 'ID inválido'], 400);

$pdo = getPDO();
$stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
$stmt->execute(['id' => $id]);

json_response(['success' => true, 'message' => 'Evento excluído com sucesso']);
