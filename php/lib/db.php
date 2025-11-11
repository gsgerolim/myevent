<?php
// php/lib/db.php

function getPDO() {
    static $pdo;

    if ($pdo) {
        return $pdo;
    }

    $config = include __DIR__ . '/../config.php';

    $host = $config['db']['host'] ?? 'localhost';
    $port = $config['db']['port'] ?? 5432;
    $dbname = $config['db']['dbname'] ?? 'eventhub';
    $user = $config['db']['user'] ?? 'postgres';
    $pass = $config['db']['pass'] ?? '';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

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
