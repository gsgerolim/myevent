<?php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido'], 405);
}

$user = getUser();
if (!$user) {
    json_response(['success' => false, 'message' => 'Não autenticado'], 401);
}

$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$id = $user['id'];
$name = trim($body['name'] ?? '');
$email = trim($body['email'] ?? '');
$password = trim($body['password'] ?? '');
$type = $body['type'] ?? $user['type'];
$active = isset($body['active']) ? (int)$body['active'] : $user['active'];

if (!$name || !$email) {
    json_response(['success' => false, 'message' => 'Nome e e-mail são obrigatórios'], 400);
}

$pdo = getPDO();

try {
    // Admin pode alterar type e active
    if ($user['type'] === 'admin') {
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, type = :type, active = :active, password_hash = :password WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'type' => $type,
                'active' => $active,
                'password' => $password_hash,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, type = :type, active = :active WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'type' => $type,
                'active' => $active,
                'id' => $id
            ]);
        }
    } else {
        // Usuário comum: só nome, email e senha
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password_hash = :password WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $password_hash,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'id' => $id
            ]);
        }
    }

    json_response(['success' => true]);
} catch (Exception $e) {
    error_log("update_profile error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'Erro interno'], 500);
}
