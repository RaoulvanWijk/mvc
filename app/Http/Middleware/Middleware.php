<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;

interface Middleware
{

  public function __invoke(Closure $next, Request $request);

} 