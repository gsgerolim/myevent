<?php
// php/api/events_search.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = getPDO();
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$q = trim($input['q'] ?? '');
$date_from = $input['date_from'] ?? null;
$date_to = $input['date_to'] ?? null;
$city = trim($input['city'] ?? '');

$where = [];
$params = [];

if ($q) {
    $where[] = "(name ILIKE :q OR summary ILIKE :q)";
    $params[':q'] = "%$q%";
}

if ($date_from) {
    $where[] = "date_start >= :date_from";
    $params[':date_from'] = $date_from;
}

if ($date_to) {
    $where[] = "date_end <= :date_to";
    $params[':date_to'] = $date_to;
}

if ($city) {
    $where[] = "city ILIKE :city";
    $params[':city'] = "%$city%";
}

$sql = "SELECT e.id, e.name, e.summary, e.image, e.address, e.city, e.date_start, e.date_end, e.latitude, e.longitude, e.capacity, e.unlimited, e.cost,
        (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) AS subscribed_count
        FROM events e";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY e.date_start ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response(['events' => $events]);
