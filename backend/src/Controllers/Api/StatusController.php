<?php
namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Infrastructure\Database;
use Exception;

class StatusController
{
    public function time(Request $request, Response $response)
    {
        $payload = ['time' => date('c')];
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function hello(Request $request, Response $response, array $args)
    {
        $name = isset($args['name']) ? $args['name'] : 'World';
        $payload = [
            'message' => "Bonjour, $name !",
            'tip' => 'Utilisez {name} dans l’URL pour personnaliser la réponse.'
        ];
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function diagnostics(Request $request, Response $response)
    {
        $diagnostics = [
            'timestamp' => date('c'),
            'app_env' => getenv('APP_ENV') ?: 'dev',
            'php_version' => PHP_VERSION,
            'vendor_exists' => is_dir(dirname(__DIR__, 3) . '/vendor'),
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'openssl' => extension_loaded('openssl'),
            ],
            'packages' => [
                'slim/slim' => null,
                'catfan/medoo' => null,
            ],
            'db' => [
                'host' => getenv('DB_HOST') ?: null,
                'name' => getenv('DB_NAME') ?: null,
                'user' => getenv('DB_USER') ?: null,
                'port' => getenv('DB_PORT') ?: null,
                'status' => 'unknown',
                'result' => null,
                'error' => null,
            ],
        ];

        if (class_exists('Composer\\InstalledVersions')) {
            try { $diagnostics['packages']['slim/slim'] = \Composer\InstalledVersions::getPrettyVersion('slim/slim'); } catch (Exception $e) {}
            try { $diagnostics['packages']['catfan/medoo'] = \Composer\InstalledVersions::getPrettyVersion('catfan/medoo'); } catch (Exception $e) {}
        }

        try {
            $row = Database::get()->query('SELECT 1 AS ok')->fetch();
            $diagnostics['db']['status'] = 'connected';
            $diagnostics['db']['result'] = $row;
        } catch (Exception $e) {
            $diagnostics['db']['status'] = 'error';
            $diagnostics['db']['error'] = $e->getMessage();
        }

        $response->getBody()->write(json_encode($diagnostics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
