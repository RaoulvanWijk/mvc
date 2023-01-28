<?php

namespace App\Http\Middleware;

use App\Contracts\MiddlewareInterface;
use App\Support\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Closure;
class UseCsrf implements MiddlewareInterface
{
  public function process(Request $request, Closure $next): Response
  {
    if($request->getMethod() !== 'GET') {
      $session_token = app(Session::class)->get('_token', null);
      $body = $request->getParsedBody();
      if(!$session_token || !isset($body['_token']) || $session_token !== $body['_token']) {
        return new \App\Http\HttpKernel\Response(403, reasonPhrase: "Forbidden");
      }
      $this->generateToken();
    } else {
      $this->generateToken();
    }
    return $next($request);
  }

  private function generateToken()
  {
    $token = bin2hex(random_bytes(32));
    app(Session::class)->put('_token', $token);
    app('twig')->addGlobal('csrf', "<input name='_token' type='hidden' value=".$token.">");
  }
}