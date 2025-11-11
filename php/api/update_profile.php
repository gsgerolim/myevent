<?php
// php/api/update_profile.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success'=>false,'message'=>'Método não permitido'], 405);
}

$user = getUser();
if (!$user) json_response(['success'=>false,'message'=>'Não autenticado'], 401);

$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$name = trim($body['name'] ?? '');
$email = trim($body['email'] ?? '');
$phone = trim($body['phone'] ?? '');
$password = trim($body['password'] ?? '');

if (!$name || !$email) json_response(['success'=>false,'message'=>'Nome e e-mail são obrigatórios'], 400);

$pdo = getPDO();

try {
    if ($user['type'] === 'admin') {
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET username = :name, password_hash = :password WHERE id = :id");
            $stmt->execute(['name'=>$name, 'password'=>$password_hash, 'id'=>$user['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE admins SET username = :name WHERE id = :id");
            $stmt->execute(['name'=>$name, 'id'=>$user['id']]);
        }
    } else { // participant
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE attendees SET name = :name, email = :email, phone = :phone, password_hash = :password WHERE id = :id");
            $stmt->execute(['name'=>$name, 'email'=>$email, 'phone'=>$phone, 'password'=>$password_hash, 'id'=>$user['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE attendees SET name = :name, email = :email, phone = :phone WHERE id = :id");
            $stmt->execute(['name'=>$name, 'email'=>$email, 'phone'=>$phone, 'id'=>$user['id']]);
        }
    }
    json_response(['success'=>true]);
} catch (Exception $e) {
    error_log("update_profile error: " . $e->getMessage());
    json_response(['success'=>false,'message'=>'Erro interno'], 500);
}
