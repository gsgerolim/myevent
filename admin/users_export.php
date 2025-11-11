<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';
requireAdmin();

require_once __DIR__ . '/../php/lib/pdf_export.php';
require_once __DIR__ . '/../php/lib/csv_export.php';
require_once __DIR__ . '/../php/lib/xls_export.php';

$input = json_decode(file_get_contents('php://input'), true);
$format = strtolower(trim($input['format'] ?? 'pdf'));
$fields = $input['fields'] ?? ['all'];

$pdo = getPDO();
$stmt = $pdo->query("SELECT id, username, name, email, type, active, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$users) json_response(['success' => false, 'message' => 'Nenhum usuário encontrado'], 404);

if ($fields !== ['all']) {
    $users = array_map(fn($u) => array_intersect_key($u, array_flip($fields)), $users);
}

$filename = 'usuarios_export_' . date('Ymd_His');

switch ($format) {
    case 'csv':
        generateCSVExport("Lista de Usuários", $users, "$filename.csv");
        break;
    case 'xls':
        generateXLSExport("Lista de Usuários", $users, "$filename.xlsx");
        break;
    case 'pdf':
    default:
        generatePDFExport("Lista de Usuários", $users, "$filename.pdf");
        break;
}
