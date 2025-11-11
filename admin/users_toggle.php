<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$id = intval($input['id'] ?? 0);
if (!$id) json_response(['success'=>false,'message'=>'ID inválido'],400);

$pdo = getPDO();
$pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS active boolean DEFAULT true");
$stmt = $pdo->prepare("UPDATE users SET active = NOT COALESCE(active, true) WHERE id = :id RETURNING active");
$stmt->execute(['id'=>$id]);
$val = $stmt->fetchColumn();
json_response(['success'=>true,'message'=>$val ? 'Usuário ativado' : 'Usuário desativado']);
