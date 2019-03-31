<?php
namespace App\Utils;

use \Firebase\JWT\JWT;

class Auth
{
  public static function createToken($userData)
  {
    $data = array(
      'iat' => time(), // Tiempo que inició el token
      'exp' => time() + (60 * EMAIL_TOKEN_EXPIRATION_TIME), // Tiempo que expirará el token
      'data' => $userData
    );

    return JWT::encode($data, PRIVATE_KEY);
  }

  public static function decodeToken($token)
  {
    try {

      $tokenData = JWT::decode($token, PRIVATE_KEY, array('HS256'));
      $res = [
        'response' => 'ok',
        'data' => $tokenData
      ];

    } catch (\Exception $e) {

      $res = [
        'response' => 'error',
        'data' => $e->getMessage()
      ];
    }

    return $res;
  }

    public static function startSession()
  {
    ini_set('session.save_path', '../storage/sessions');
    ini_set('session.name', SESSION_NAME);
    ini_set('session.cookie_lifetime', EXPIRATION_TIME * 60);

    session_start();
  }
}