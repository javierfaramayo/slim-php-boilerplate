<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Middlewares\TrailingRoute;

// IF USER SENDS A REQUEST users/ THIS MIDDLEWARE WILL DELETE THAT "/" AT THE END OF THE URL, THIS WILL BE CONVERTED TO users(WITHOUT "/") TO PREVENT ROUTING ERRORS
$app->add( new TrailingRoute() );

// SETTING COOKIES MIDDLEWARE
$app->add($container->get('csrf'));