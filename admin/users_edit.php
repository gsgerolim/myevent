<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../php/lib/db.php';
require_once __DIR__ . '/../php/lib/auth.php';
requireAdmin();

$data = json_decode(file_get_contents('php://input'), true);
$pdo = getPDO();

// Validar JSON
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Buscar dados do usuário
if (!empty($data['fetch']) && !empty($data['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$data['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user['active'] = (bool)$user['active'];
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
    exit;
}

// Atualização do usuário
if (!empty($data['id']) && !empty($data['username'])) {
    $stmt = $pdo->prepare("
        UPDATE users SET 
            username = :username, 
            name = :name, 
            email = :email, 
            type = :type, 
            active = :active 
        WHERE id = :id
    ");
    $stmt->execute([
        ':username' => $data['username'],
        ':name' => $data['name'] ?? null,
        ':email' => $data['email'] ?? null,
        ':type' => $data['type'] ?? 'participant',
        ':active' => !empty($data['active']) ? 1 : 0,
        ':id' => $data['id']
    ]);

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes']);
