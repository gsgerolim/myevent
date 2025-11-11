<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if (!$id) json_response(['success' => false, 'message' => 'ID inválido'], 400);

$pdo = getPDO();

// limpa o hash de senha
$stmt = $pdo->prepare("UPDATE users SET password_hash = NULL WHERE id = ?");
$stmt->execute([$id]);

json_response(['success' => true, 'message' => 'Senha resetada. O usuário precisará redefinir ao entrar.']);
