<?php
namespace App\Middlewares;

class VerifySession
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
      $excluded = ['signin.show', 'signin.exec', 'signup.show', 'signup.store', 'forgotpass.show', 'forgotpass.sendtoken', 'resetpass.show', 'resetpass.exec'];
      $current_route = $request->getAttribute('route')->getName();

      if(!in_array($current_route, $excluded) && empty($_SESSION['name'])){
        return $response->withRedirect(APP_PUBLIC_URL.'/signin', 301);
      }

      return $next($request, $response);
    }
}