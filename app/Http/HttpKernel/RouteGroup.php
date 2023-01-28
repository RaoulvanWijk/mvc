<?php

namespace App\Http\HttpKernel;

use Closure;

class RouteGroup
{
  public array $middleware = [];
  public string $prefix = '';

  private Closure $callback;
  public function group(Closure | array $attributes, Closure $callback = null)
  {
    if(!is_callable($attributes)) {
      if(array_key_exists('middleware', $attributes)) {
        $this->middleware($attributes['middleware']);
      } elseif (array_key_exists('prefix', $attributes)) {
        $this->prefix($attributes['prefix']);
      }
    } else {
      $callback = $attributes;
    }
    $this->callback = $callback;

    $this->applyToRoutes();
    return $this;
  }

  public function middleware(string | array $middleware): static
  {
    $this->middleware = is_array($middleware) ? $middleware : [$middleware];
    
    return $this;
  }

  public function prefix(string $prefix): static
  {
    $this->prefix = str_starts_with($prefix, '/') ? $prefix : '/'.$prefix;
    
    return $this;
  }

  public function applyToRoutes()
  {
    app(Router::class)->updateGroupContext($this);
    ($this->callback)();
    app(Router::class)->removeOldGroup();
  }
}