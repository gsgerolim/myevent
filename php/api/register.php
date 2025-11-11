<?php
// php/api/register.php
require_once __DIR__ . '/../lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');

if (!$username || !$password || !$name) {
    json_response(['success' => false, 'message' => 'Dados incompletos'], 400);
}

$pdo = getPDO();

// Verifica se já existe usuário
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
if ($stmt->fetch()) {
    json_response(['success' => false, 'message' => 'Usuário já existe'], 409);
}

// Cria hash da senha
$password_hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    INSERT INTO users (username, password_hash, name, email, type)
    VALUES (:username, :password_hash, :name, :email, 'participant')
    RETURNING id, name, email, type
");
$stmt->execute([
    'username' => $username,
    'password_hash' => $password_hash,
    'name' => $name,
    'email' => $email
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
json_response(['success' => true, 'user' => $user]);
