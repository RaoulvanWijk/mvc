<?php

namespace App\Http\Middleware;

use App\Contracts\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Closure;

class Middleware implements MiddlewareInterface
{
  public function process(Request $request, Closure $next): Response
  {
    return $next($request);
  }
}