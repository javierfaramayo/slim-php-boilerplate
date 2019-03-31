<?php
use Slim\Views\TwigExtension;
use App\Views\CsrfExtension;

$container = $app->getContainer();

// SETTING CSRF AUTHENTICATION TOKEN

$container['csrf'] = function ($c) {
  return new \Slim\Csrf\Guard;
};

// SETTING TWIG LIKE TEMPLATE ENGINE

$container['view'] = function ($container) {
  $view = new \Slim\Views\Twig('../resources/views', [
    'cache' => false
  ]);

  $view->getEnvironment()->addGlobal("appName", APP_NAME);
  $view->getEnvironment()->addGlobal("session", $_SESSION);

  $view->addExtension(new CsrfExtension($container['csrf']));

  $router = $container->get('router');
  $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
  $view->addExtension(new TwigExtension($router, $uri));

  return $view;
};

// SETTING MONOLOG TO MANAGE LOGS

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger(SESSION_NAME);
  $file_handler = new \Monolog\Handler\StreamHandler('../storage/logs/'.date('y-m-d').'.log');
  $logger->setTimezone(new DateTimeZone(TIME_ZONE));
  $logger->pushHandler($file_handler);
  return $logger;
};

// UNSET SLIM AND PHP ERROR HANDLERS TO USE CUSTOM HANDLERS

unset($container['errorHandler']);
unset($container['phpErrorHandler']);

$container['errorHandler'] = function ($container) {
  return function ($request, $response, $exception) use ($container) {
      $container -> logger -> error($exception->getMessage());
      return $response->withStatus(500)
          ->withHeader('Content-Type', 'text/html')
          ->write('Something went wrong!'.$exception->getMessage());
  };
};

$container['phpErrorHandler'] = function ($container) {
  return function ($request, $response, $exception) use ($container) {
      $container -> logger -> error($exception->getMessage());
      return $response->withStatus(500)
          ->withHeader('Content-Type', 'text/html')
          ->write('Something went wrong!'.$exception->getMessage());
  };
};

// SET CUSTOM NOTICE HANDLER TO THROW ERROR EXCEPTIONS AND MANAGE IN TRY/CATCH
set_error_handler(function ($severity, $message, $file, $line) use ($container){
  if (!(error_reporting() & $severity)) {
      return;
  }
  $container -> logger -> error($message);
  throw new \ErrorException($message, 0, $severity, $file, $line);
});

// SET UPLOADS DIRECTORIES FOR FILES AND IMAGES

$container['uploadFiles'] = APP_PUBLIC_URL.'/uploads/files';
$container['uploadImages'] = APP_PUBLIC_URL.'/uploads/images';

// SETTING SESSION HELPER
// Register globally to app
$container['session'] = function ($c) {
  return new \SlimSession\Helper;
};
