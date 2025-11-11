<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';
require_once __DIR__ . '/../php/lib/image_utils.php';

requireAdmin();
header('Content-Type: application/json');

$input = $_POST;
$id = isset($input['id']) ? intval($input['id']) : null;
if (!$id) json_response(['success' => false, 'message' => 'ID inválido'], 400);

$pdo = getPDO();

// Se for apenas carregar dados de um evento
if (!isset($input['update'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) json_response(['success' => false, 'message' => 'Evento não encontrado'], 404);
    json_response(['success' => true, 'event' => $event]);
}

// Campos permitidos
$fields = [
    'name', 'summary', 'date_start', 'date_end',
    'address', 'city', 'latitude', 'longitude',
    'capacity', 'unlimited', 'cost', 'image'
];

$update = [];
$params = ['id' => $id];

// Atualiza campos do POST
foreach ($fields as $f) {
    if (array_key_exists($f, $input)) {
        if ($f === 'unlimited') {
            $params[$f] = ($input[$f] === true || $input[$f] === 'true' || $input[$f] === 1 || $input[$f] === '1') ? 't' : 'f';
        } elseif ($f === 'capacity') {
            $params[$f] = intval($input[$f]);
        } else {
            $params[$f] = $input[$f];
        }
        $update[] = "$f = :$f";
    }
}

// Se enviou nova imagem, salva e atualiza
if (isset($_FILES['cropped_image']) && $_FILES['cropped_image']['error'] === UPLOAD_ERR_OK) {
    $imagePath = saveImageUpload($_FILES['cropped_image'], 'events', 'evt_');
    $params['image'] = $imagePath;
    if (!in_array('image = :image', $update)) $update[] = 'image = :image';
}

if (!$update) json_response(['success' => false, 'message' => 'Nenhum dado para atualizar'], 400);

$sql = "UPDATE events SET " . implode(', ', $update) . " WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'message' => 'Evento atualizado com sucesso!']);
