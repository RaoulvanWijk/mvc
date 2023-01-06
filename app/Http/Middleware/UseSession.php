<?php

namespace App\Http\Middleware;

use App\Contracts\MiddlewareInterface;
use App\Support\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Closure;

class UseSession implements MiddlewareInterface
{
  public function process(Request $request, Closure $next): Response
  {
    $session = app(Session::class);
    $session->start();
    $_SESSION["test"] = "test";
    $response = $next($request);
    $session->save();
    $_SESSION["test"] = "TETET";
    return $response;
  }
}