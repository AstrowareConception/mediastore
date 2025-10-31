<?php
namespace App\Infrastructure;

use Medoo\Medoo;
use PDO;

class Database
{
    /** @var Medoo|null */
    private static $instance = null;

    /**
     * @return Medoo
     */
    public static function get()
    {
        if (self::$instance instanceof Medoo) {
            return self::$instance;
        }
        self::$instance = new Medoo([
            'type' => 'mysql',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'database' => getenv('DB_NAME') ?: 'test',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'port' => (int)(getenv('DB_PORT') ?: 3306),
            'charset' => 'utf8mb4',
            'error' => PDO::ERRMODE_EXCEPTION,
            'option' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ]);
        return self::$instance;
    }
}
