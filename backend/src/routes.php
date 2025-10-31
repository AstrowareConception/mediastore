<?php
use Slim\App;
use App\Controllers\HomeController;
use App\Controllers\Api\StatusController;
use App\Controllers\Api\DbController;
use App\Controllers\Api\UsersController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* @var App $app */

// Home page (HTML view)
$app->get('/', [new HomeController(), 'index']);

// Simple API routes
$app->get('/api/time', [new StatusController(), 'time']);
$app->get('/api/hello/{name}', [new StatusController(), 'hello']);
$app->get('/api/diagnostics', [new StatusController(), 'diagnostics']);

// DB utilities
$app->get('/api/db/ping', [new DbController(), 'ping']);
$app->get('/api/db/setup', [new DbController(), 'setup']);

// Users examples
$app->get('/api/examples/users', [new UsersController(), 'index']);
$app->post('/api/examples/user', [new UsersController(), 'create']);
