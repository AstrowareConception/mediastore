<?php
namespace App\Support;

use Psr\Http\Message\ResponseInterface as Response;

class View
{
    public static function render(Response $response, $template, array $data = [], $status = 200)
    {
        $viewsDir = __DIR__ . '/../Views/';
        $file = $viewsDir . $template . '.php';
        if (!is_file($file)) {
            $response->getBody()->write("View not found: {$template}");
            return $response->withStatus(500);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withStatus($status)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
