<?php
// php/api/get_profile.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

$user = getUser();
if (!$user) {
    json_response(['success' => false, 'message' => 'Usuário não logado'], 401);
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT id, username, name, email, type, active FROM users WHERE id = :uid");
$stmt->execute([':uid' => $user['id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    json_response(['success' => false, 'message' => 'Perfil não encontrado'], 404);
}

json_response(['success' => true, 'profile' => $profile]);
