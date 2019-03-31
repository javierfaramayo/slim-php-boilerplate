<?php
namespace App\Middlewares;

class VerifyJWT
{
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        try {

/*             $decoded = JWT::decode($jwt, $key, array('HS256'));
            return $next($request, $response); */

        } catch(Exception $e) {
            var_dump($e);
        }
    }
}