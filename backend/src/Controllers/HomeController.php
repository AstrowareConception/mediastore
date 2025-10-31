<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\View;

class HomeController
{
    public function index(Request $request, Response $response)
    {
        $appEnv = htmlspecialchars(getenv('APP_ENV') ?: 'dev');
        $dbInfo = [
            'host' => getenv('DB_HOST') ?: '—',
            'name' => getenv('DB_NAME') ?: '—',
            'user' => getenv('DB_USER') ?: '—',
        ];
        return View::render($response, 'home', [
            'appEnv' => $appEnv,
            'dbInfo' => $dbInfo,
        ]);
    }
}
