<?php
namespace App\Controllers;
use Psr\Container\ContainerInterface;
use App\Models\User;
use App\Models\DB;

class UsersController
{
  protected $container;
  protected $user;

  // constructor receives container instance
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
    $this->user = new User();
  }

  public function index($request, $response, $args)
  {
  }

  public function store($request, $response, $args)
  {
    extract($request->getParsedBody());

    if($pass === $confirmPass){

      if($this->user->where('email', $email)->notExists()){

        $this->user->name = $name;
        $this->user->last_name = $lastName;
        $this->user->email = $email;
        $this->user->pass = password_hash($pass, PASSWORD_BCRYPT);
        $this->user->status_id = 1;
        $res = $this->user -> create();

      } else {

        $res['response'] = 'error';
        $res['message'] = 'The email is not available, another user took it';
      }
    } else {

      $res['response'] = 'error';
      $res['message'] = 'Confirm password failed. Both passwords are not the same.';
    }

    if($res['response'] === 'ok') return $this->container->view->render($response, 'signin.twig', [
      'success_message' => 'Your user have been created successfully, now you can Sign in.'
    ]);

    return $this->container->view->render($response, 'signup.twig', [
      'error_message' => $res['message'],
      'old_fields' => $request->getParsedBody()
    ]);
  }
}