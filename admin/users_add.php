<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin(); // Apenas admins podem adicionar usuários

// Receber os dados JSON
$data = json_decode(file_get_contents('php://input'), true);
$pdo = getPDO();

// Validar campos obrigatórios
if (!isset($data['username'], $data['name'], $data['email'], $data['type'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Sanitizar inputs
$username = trim($data['username']);
$name = trim($data['name']);
$email = trim($data['email']);
$type = in_array($data['type'], ['participant', 'admin']) ? $data['type'] : 'participant';
$active = isset($data['active']) && $data['active'] ? 1 : 0;

// Validar campos
if (!$username || !$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

// Checar se o usuário já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Usuário ou email já cadastrado.']);
    exit;
}

// Criar senha padrão (pode ser alterada pelo usuário depois)
$defaultPassword = '123456'; // ou gere aleatória
$hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);

// Inserir usuário
$stmt = $pdo->prepare("INSERT INTO users (username, name, email, type, active, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
if ($stmt->execute([$username, $name, $email, $type, $active, $hashedPassword])) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuário adicionado com sucesso!',
        'defaultPassword' => $defaultPassword // opcional, para mostrar ao admin
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar usuário.']);
}
