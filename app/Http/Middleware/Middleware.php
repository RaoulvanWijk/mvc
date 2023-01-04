<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Closure;

class Middleware
{
  public function process(Request $request, Closure $next): Response
  {
    return $next($request);
  }
}