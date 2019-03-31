<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\UsersController;
use App\Middlewares\VerifySession;

$app->get('/', HomeController::class . ':index')->add( new VerifySession() )->setName('home');

$app->get('/signin', AuthController::class . ':index')->setName('signin.show');

$app->post('/signin', AuthController::class . ':signIn')->setName('signin.exec');

$app->get('/signup', AuthController::class . ':index')->setName('signup.show');

$app->post('/signup', UsersController::class . ':store')->setName('signup.store');

$app->get('/logout', AuthController::class . ':logOut')->setName('logout');

$app->get('/forgotpass', AuthController::class . ':forgotPass')->setName('forgotpass.show');

$app->post('/forgotpass', AuthController::class . ':sendToken')->setName('forgotpass.sendtoken');

$app->get('/resetpass/{token}', AuthController::class . ':verifyToken')->setName('resetpass.show');

$app->post('/resetpass/{token}', AuthController::class . ':resetPass')->setName('resetpass.exec');

$app->group('/users', function () use ($app) {

  $app->get('', function (Request $request, Response $response) {

    return $this->view->render($response, 'home.html', [
      'name' => 'Lista de usuarios',
      'app_name' => APP_NAME
    ]);
  })->setName('users');

  $app->post('/add', function (Request $request, Response $response) {

    return $this->view->render($response, 'home.html', [
      'name' => 'Lista de usuarios',
      'app_name' => APP_NAME
    ]);

  })->setName('users.add');

});

$app->get('/testdb', UsersController::class .':index');

$app->get('/services', function (Request $request, Response $response) {
  return $this->view->render($response, 'services.twig');
})->add( new VerifySession() )->setName('services');