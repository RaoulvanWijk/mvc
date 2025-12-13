<?php

namespace App\Http\Middleware;

use App\Contracts\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Closure;

class Middleware implements MiddlewareInterface
{
  public function process(ServerRequestInterface $request, Closure $next): ResponseInterface
  {
    return $next($request);
  }
}