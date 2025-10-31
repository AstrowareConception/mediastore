<?php
// Slim 4 front controller — bootstraps the app and delegates routes to src/routes.php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Register routes (controllers + views live under src/)
// This file expects $app to be in scope
require __DIR__ . '/../src/routes.php';

$app->run();
