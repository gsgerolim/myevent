<?php
// php/lib/db.php

function getPDO() {
    static $pdo;

    if ($pdo) {
        return $pdo;
    }

$config = include __DIR__ . '/../config.php';

$host = $config['db']['host'];
$port = $config['db']['port'];
$dbname = $config['db']['dbname'];
$user = $config['db']['user'];
$pass = $config['db']['pass'];
$sslmode = $config['db']['sslmode'];

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslmode";


    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }

    return $pdo;
}
