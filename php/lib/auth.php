<?php
// php/lib/auth.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

session_start();

function login($username, $password) {
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT id, username, password_hash, name, email, type FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        return [
            'success' => true,
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'type' => $user['type']
        ];
    }
    return ['success' => false, 'message' => 'Usuário ou senha inválidos'];
}
function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    return true;
}
function getUser() {
    if (!isset($_SESSION['user_id'])) return null;

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, username, name, email, type FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireLogin() {
    $user = getUser();
    if (!$user) {
        json_response(['success'=>false,'message'=>'Não autorizado'], 401);
        exit;
    }
    return $user;
}

function requireAdmin() {
    $user = requireLogin();
    if ($user['type'] !== 'admin') {
        json_response(['success'=>false,'message'=>'Acesso negado'], 403);
        exit;
    }
    return $user;
}
