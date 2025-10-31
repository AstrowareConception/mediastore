<?php
namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Infrastructure\Database;
use Exception;

class DbController
{
    public function ping(Request $request, Response $response)
    {
        try {
            $r = Database::get()->query('SELECT 1 as ok')->fetch();
            $payload = ['db' => 'connected', 'result' => $r];
        } catch (Exception $e) {
            $payload = ['db' => 'error', 'error' => $e->getMessage()];
        }
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function setup(Request $request, Response $response)
    {
        try {
            $db = Database::get();
            $pdo = $db->pdo;
            $pdo->exec("CREATE TABLE IF NOT EXISTS users_demo (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $count = $db->count('users_demo');
            if ((int)$count === 0) {
                $db->insert('users_demo', [
                    ['name' => 'Alice', 'email' => 'alice@example.com'],
                    ['name' => 'Bob', 'email' => 'bob@example.com'],
                ]);
            }
            $payload = ['setup' => 'ok', 'rows' => (int)$db->count('users_demo')];
        } catch (Exception $e) {
            $payload = ['setup' => 'error', 'error' => $e->getMessage()];
        }
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
