<?php
namespace App\Controllers;
use Psr\Container\ContainerInterface;
use App\Models\User;
use App\Utils\Auth;
use App\Utils\Email;
use App\Models\DB;

class AuthController
{
  protected $container;

  // constructor receives container instance
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function index($request, $response, $args)
  {
    $current_route = $request->getAttribute('route')->getName();

    $template = explode('.', $current_route)[0];

    return $this->container->view->render($response, $template . '.twig', [
      'title' => ucwords($template)
    ]);
  }

  public function signIn($request, $response, $args)
  {
    extract($request->getParsedBody());

    $user = new User();
    $user = $user->where('email', $email)->first();

    if($user && password_verify($pass, $user->pass)) {
      session_destroy();
      unset($_COOKIE[SESSION_NAME]);
      Auth::startSession();

      $_SESSION['name'] = $user->name;
      $_SESSION['last_name'] = $user->last_name;

      return $response->withRedirect(APP_PUBLIC_URL.'/', 301);

    } else {

      return $this->container->view->render($response, 'signin.twig', [
        'error_message' => 'The credentials provided are incorrect. Please try again.',
        'old_fields' => $request->getParsedBody(),
        'title' => 'Sign In'
      ]);
    }
  }

  public function logOut($request, $response, $args)
  {
    session_destroy();
    unset($_COOKIE[SESSION_NAME]);
    return $response->withRedirect(APP_PUBLIC_URL.'/', 301);
  }

  public function forgotPass($request, $response, $args)
  {
    return $this->container->view->render($response, 'forgotpass.twig', [
      'title' => 'Forgot Pass'
    ]);
  }

  public function sendToken($request, $response, $args)
  {
    extract($request->getParsedBody());
    $user = new User();
    $user = $user->where('email', $email)->first();

    if($user){

      $userData = [
        'id' => $user->id,
        'email' => $user->email
      ];

      $token = Auth::createToken($userData);

      $url = APP_PUBLIC_URL ."/resetpass/". $token;

      $emailData = [
        'subject' => 'Reset your password',
        'to' => [
          $email
        ],
        'reply_to' => $email,
        'cc' => [
          $email
        ],
        'bcc' => [
          $email
        ],
        'path_template' => '../resources/emailTemplates/resetPassword.html',
        'data' => [
          '%URL%' => $url
        ]
      ];

      $result = Email::sendMail($emailData);

      if($result['response'] == 'ok'){

        $query = "INSERT INTO reset_pass VALUES (:email, :token, null)";
        $params = [
          ':email' => $email,
          ':token' => $token
        ];

        DB::insertRawQuery($query, $params);

        $res = ['success_message' => 'The message was sent, go to your mail box to reset your password.'];

      } else {

        $res = [
          'error_message' => 'Failed to send the mail. Please try again.',
          'old_fields' => $request->getParsedBody()
        ];
      }
    } else {

      $res = [
        'error_message' => 'The email provided is incorrect. Please enter a valid one.',
        'old_fields' => $request->getParsedBody()
      ];
    }

    $res['title'] = 'Forgot Pass';
    return $this->container->view->render($response, 'forgotpass.twig', $res);
  }

  public function verifyToken($request, $response, $args)
  {
    extract($args);
    $decoded = Auth::decodeToken($token);

    if($decoded['response'] == 'ok'){

      $query = "SELECT * FROM reset_pass WHERE token = :token AND email = :email";
      $params = [
        ':token' => $token,
        ':email' => $decoded['data']->data->email
      ];
      $stored = DB::getRawQuery($query, $params, 'fetch');

      if($stored){
        $res = ['response' => 'tokenValid', 'token' => $token];
      } else {
        $res = ['response' => 'tokenInvalid', 'error_message' => 'The link is invalid or expired.'];
      }

    } else {

      $res = ['response' => 'tokenInvalid', 'error_message' => 'The link is invalid or expired.'];
    }

    $res['title'] = 'Reset Password';

    return $this->container->view->render($response, 'resetpass.twig', $res);
  }

  public function resetPass($request, $response, $args)
  {
    extract($args);
    $decoded = Auth::decodeToken($token);

    if($decoded['response'] == 'ok'){

      $query = "SELECT * FROM reset_pass WHERE token = :token AND email = :email";
      $params = [
        ':token' => $token,
        ':email' => $decoded['data']->data->email
      ];
      $stored = DB::getRawQuery($query, $params, 'fetch');

      if($stored){

        extract($request->getParsedBody());

        if($pass === $newPass){

          $user = new User();
          $update = $user->update(['pass' => password_hash($pass, PASSWORD_BCRYPT)], $decoded['data']->data->id);

          if($update['response'] == 'ok'){

            $query = "DELETE FROM reset_pass WHERE token = :token AND email = :email";
            $params = [
              ':token' => $token,
              ':email' => $decoded['data']->data->email
            ];
            DB::setRawQuery($query, $params);

            return $this->container->view->render($response, 'signin.twig', [
              'success_message' => 'Your password has been changed. Now can Sign In again.'
            ]);
          } else {
            $res = [
              'response' => 'tokenValid',
              'error_message' => 'A problem ocurred while updating. Please try again',
              'token' => $token
            ];
          }

        } else {
          $res = [
            'response' => 'tokenValid',
            'error_message' => 'Confirm password failed. Both passwords are not the same.',
            'token' => $token
          ];
        }
      } else {
        $res = ['response' => 'tokenInvalid', 'error_message' => 'The link is invalid or expired.'];
      }

    } else {

      $res = ['response' => 'tokenInvalid', 'error_message' => 'The link is invalid or expired.'];
    }

    return $this->container->view->render($response, 'resetpass.twig', $res);
  }
}