<?php
// Mostrar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Teste de configuração PHP e PostgreSQL</h2>";

// Testar se arquivos existem
$files_to_check = [
    'php/config.php',
    'php/lib/db.php',
    'php/lib/functions.php',
    'php/lib/auth.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "Arquivo encontrado: $file ✅<br>";
    } else {
        echo "Arquivo **NÃO encontrado**: $file ❌<br>";
    }
}

// Testar conexão com PostgreSQL
try {
    $config = include __DIR__ . '/php/config.php';

    $dsn = sprintf(
        'pgsql:host=%s;port=%d;dbname=%s',
        $config['db']['host'],
        $config['db']['port'],
        $config['db']['dbname']
    );
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<br>Conexão com PostgreSQL OK ✅<br>";

    // Testar uma query simples
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Query teste retornou: " . implode(", ", $result) . "<br>";

} catch (Exception $e) {
    echo "<br><strong>Erro ao conectar com o banco:</strong> " . $e->getMessage() . " ❌<br>";
}

// Testar se funções do auth.php funcionam
try {
    require_once __DIR__ . '/php/lib/auth.php';
    if (function_exists('getUser')) {
        echo "Função getUser() disponível ✅<br>";
    } else {
        echo "Função getUser() **NÃO encontrada** ❌<br>";
    }
} catch (Exception $e) {
    echo "Erro ao incluir auth.php: " . $e->getMessage() . "<br>";
}

echo "<br>Teste completo finalizado.";
