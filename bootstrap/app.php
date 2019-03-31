<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utils\Auth;

// IMPORT CONFIGS FILE, THIS CONTAINS GLOBAL CONSTANTS TO USE IN ALL OF APP
require_once('../config/app.php');

// SETTINGS BEFORE CREATING APP
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['determineRouteBeforeAppMiddleware'] = true;

// CREATING THE APP
$app = new \Slim\App(['settings' => $config]);

// STARTING SESSION
Auth::startSession();

// IMPORT FILE WITH CONTAINER CONFIGS AND DEPENDENCIES
require_once('../bootstrap/container.php');
/* $app->add($container->get('csrf')); */

// IMPORT FILE WITH GLOBAL MIDDLEWARES
require_once('../bootstrap/middlewares.php');

// WEB ROUTES
require_once('../routes/web.php');

// API ROUTES
require_once('../routes/api.php');

// START APPLICATION
$app->run();