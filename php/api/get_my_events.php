<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';

$user = getUser();
if (!$user) {
    json_response(['success'=>false,'message'=>'Usuário não logado'], 401);
}

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT e.id, e.name, e.date_start, e.date_end, e.image, e.summary, e.address, e.city, e.latitude, e.longitude
    FROM events e
    INNER JOIN event_participants ep ON ep.event_id = e.id
    WHERE ep.user_id = :user_id
    ORDER BY e.date_start DESC
");
$stmt->execute(['user_id'=>$user['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response(['success'=>true,'events'=>$events]);
