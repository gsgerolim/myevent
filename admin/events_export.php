<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);

$eventId = intval($input['event_id'] ?? 0);
$format = strtolower(trim($input['format'] ?? 'csv'));
$fields = $input['fields'] ?? ['all'];

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID do evento inválido']);
    exit;
}

$pdo = getPDO();

// Busca informações do evento
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute(['id' => $eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Evento não encontrado']);
    exit;
}

// Busca inscritos
$sql = "SELECT u.id AS user_id, u.name, u.email, u.username, u.created_at AS user_created_at, ep.subscribed_at
        FROM event_participants ep
        JOIN users u ON u.id = ep.user_id
        WHERE ep.event_id = :event_id
        ORDER BY ep.subscribed_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['event_id' => $eventId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => 'Nenhum inscrito encontrado']);
    exit;
}

// Filtra campos
if (!in_array('all', $fields)) {
    $rows = array_map(fn($row) => array_intersect_key($row, array_flip($fields)), $rows);
}

$filenameBase = 'inscritos_evento_' . preg_replace('/\s+/', '_', strtolower($event['name']));

switch ($format) {
    case 'pdf':
        require_once __DIR__ . '/../php/lib/pdf_export.php';
        generatePDFExport($event['name'], $rows, $filenameBase . '.pdf');
        break;

    case 'xls':
        require_once __DIR__ . '/../php/lib/xls_export.php';
        generateXLSExport($event['name'], $rows, $filenameBase . '.xls');
        break;

    default:
        require_once __DIR__ . '/../php/lib/csv_export.php';
        generateCSVExport($event['name'], $rows, $filenameBase . '.csv');
        break;
}
