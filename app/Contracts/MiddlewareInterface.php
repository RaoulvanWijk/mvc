<?php

namespace App\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Closure;

interface MiddlewareInterface
{
  public function process(ServerRequestInterface $request, Closure $next): ResponseInterface;
}