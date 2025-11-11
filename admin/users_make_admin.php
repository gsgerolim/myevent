<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$id = intval($input['id'] ?? 0);
if (!$id) json_response(['success'=>false,'message'=>'ID inválido'],400);

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT type FROM users WHERE id = ?");
$stmt->execute([$id]);
$current = $stmt->fetchColumn();
if ($current === false) json_response(['success'=>false,'message'=>'Usuário não encontrado'],404);

$new = ($current === 'admin') ? 'participant' : 'admin';
$stmt = $pdo->prepare("UPDATE users SET type = :new WHERE id = :id");
$stmt->execute(['new'=>$new,'id'=>$id]);
json_response(['success'=>true,'message'=> $new === 'admin' ? 'Usuário promovido a admin' : 'Usuário removido do grupo admin']);
