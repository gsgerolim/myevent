<?php
// php/api/events.php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';

$pdo = getPDO();

$stmt = $pdo->query("SELECT e.id, e.name, e.summary, e.image, e.address, e.city, e.date_start, e.date_end, e.latitude, e.longitude, e.capacity, e.unlimited, e.cost,
    (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) AS subscribed_count
    FROM events e
    ORDER BY e.date_start ASC");

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response(['events' => $events]);
