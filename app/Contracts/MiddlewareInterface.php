<?php

namespace App\Contracts;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Closure;

interface MiddlewareInterface
{
  public function process(Request $request, Closure $next): Response;
}