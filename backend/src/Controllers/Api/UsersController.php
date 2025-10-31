<?php
namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserRepository;
use Exception;

class UsersController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function index(Request $request, Response $response)
    {
        try {
            $users = $this->repo->listUsers();
            $payload = ['count' => count($users), 'items' => $users];
        } catch (Exception $e) {
            $payload = ['error' => $e->getMessage()];
        }
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode((string)$request->getBody(), true);
        if (!is_array($data)) { $data = []; }
        $name = trim(isset($data['name']) ? $data['name'] : '');
        $email = trim(isset($data['email']) ? $data['email'] : '');
        if ($name === '' || $email === '') {
            $response->getBody()->write(json_encode([
                'error' => 'name et email sont requis'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        try {
            $id = $this->repo->insertUser($name, $email);
            $payload = ['inserted_id' => $id];
            $status = 201;
        } catch (Exception $e) {
            $payload = ['error' => $e->getMessage()];
            $status = 400;
        }
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
