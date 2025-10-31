<?php
namespace App\Models;

use App\Infrastructure\Database;
use Exception;

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function createTableIfNotExists()
    {
        $pdo = $this->db->pdo;
        $pdo->exec("CREATE TABLE IF NOT EXISTS users_demo (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function seedIfEmpty()
    {
        $count = $this->db->count('users_demo');
        if ((int)$count === 0) {
            $this->db->insert('users_demo', [
                ['name' => 'Alice', 'email' => 'alice@example.com'],
                ['name' => 'Bob', 'email' => 'bob@example.com'],
            ]);
        }
    }

    public function listUsers()
    {
        return $this->db->select('users_demo', ['id','name','email','created_at']);
    }

    public function insertUser($name, $email)
    {
        return $this->db->insert('users_demo', [
            'name' => $name,
            'email' => $email,
        ])->id;
    }
}
