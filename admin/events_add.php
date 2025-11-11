<?php
require_once __DIR__ . '/../php/lib/auth.php';
require_once __DIR__ . '/../php/lib/functions.php';
require_once __DIR__ . '/../php/lib/image_utils.php';

requireAdmin();
header('Content-Type: application/json');

$input = $_POST;

$name = trim($input['name'] ?? '');
$summary = trim($input['summary'] ?? '');
$date_start = $input['date_start'] ?? null;
$date_end = $input['date_end'] ?? null;
$address = trim($input['address'] ?? '');
$city = trim($input['city'] ?? '');
$latitude = $input['latitude'] ?? null;
$longitude = $input['longitude'] ?? null;
$capacity = intval($input['capacity'] ?? 100);
//$unlimited = filter_var($input['unlimited'] ?? false, FILTER_VALIDATE_BOOLEAN);
$unlimited = isset($input['unlimited']) ? 1 : 0;
//$active = isset($_POST['active']) ? 1 : 0;
$cost = trim($input['cost'] ?? 'Gratuito');

if (!$name || !$date_start) {
    json_response(['success' => false, 'message' => 'Nome e data de início são obrigatórios'], 400);
}

try {
    // Lida com upload da imagem
    $imagePath = null;
    if (isset($_FILES['cropped_image']) && $_FILES['cropped_image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = saveImageUpload($_FILES['cropped_image'], 'events', 'evt_'); // pasta 'events', prefixo 'evt_'
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare("
        INSERT INTO events 
        (name, summary, date_start, date_end, address, city, latitude, longitude, capacity, unlimited, cost, image, created_by)
        VALUES 
        (:name, :summary, :date_start, :date_end, :address, :city, :latitude, :longitude, :capacity, :unlimited, :cost, :image, :created_by)
        RETURNING id
    ");

    $stmt->execute([
        'name' => $name,
        'summary' => $summary,
        'date_start' => $date_start,
        'date_end' => $date_end,
        'address' => $address,
        'city' => $city,
        'latitude' => $latitude ?: null,
        'longitude' => $longitude ?: null,
        'capacity' => $capacity,
        'unlimited' => $unlimited,
        'cost' => $cost,
        'image' => $imagePath,
        'created_by' => $_SESSION['user_id']
    ]);

    $id = $stmt->fetchColumn();
    json_response(['success' => true, 'id' => $id, 'message' => 'Evento criado com sucesso!']);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao inserir evento: ' . $e->getMessage()], 500);
}
