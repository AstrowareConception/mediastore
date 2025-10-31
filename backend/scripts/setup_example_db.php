<?php
// Simple CLI script to create and seed the example database using Medoo
// Usage inside the container:
//   php scripts/setup_example_db.php

require __DIR__ . '/../vendor/autoload.php';

use Medoo\Medoo;

function db_conn() {
    static $db = null;
    if ($db instanceof Medoo) { return $db; }
    $db = new Medoo([
        'type' => 'mysql',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'database' => getenv('DB_NAME') ?: 'test',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'charset' => 'utf8mb4',
        'error' => PDO::ERRMODE_EXCEPTION,
    ]);
    return $db;
}

try {
    $pdo = db_conn()->pdo;
    $pdo->exec("CREATE TABLE IF NOT EXISTS users_demo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $count = db_conn()->count('users_demo');
    if ($count === 0) {
        db_conn()->insert('users_demo', [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
        ]);
        echo "Seeded 2 rows into users_demo\n";
    } else {
        echo "users_demo already has $count rows.\n";
    }
    echo "Setup OK.\n";
} catch (Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
