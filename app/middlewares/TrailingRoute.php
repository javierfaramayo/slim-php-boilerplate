<?php
namespace App\Middlewares;

class TrailingRoute
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
      $uri = $request->getUri();
      $path = $uri->getPath();
      if ($path != '/' && substr($path, -1) == '/') {
          // permanently redirect paths with a trailing slash
          // to their non-trailing counterpart
          $uri = $uri->withPath(substr($path, 0, -1));

          if($request->getMethod() == 'GET') {
              return $response->withRedirect((string)$uri, 301);
          }
          else {
              return $next($request->withUri($uri), $response);
          }
      }

      return $next($request, $response);
    }
}