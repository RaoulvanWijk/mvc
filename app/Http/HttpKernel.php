<?php

namespace App\Http;

use Closure;

// This class will be used to store all the routes and middleware
class HttpKernel
{
  /**
   * This will be used to store all the routes
   */
  private $routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => []
  ];

  private $start;

  /**
   * This will be used to store all the routeMiddleware
   */
  private $routeMiddleware = [
    "demo" => \App\Http\Middleware\DemoMiddleware::class,
    "demo2" => \App\Http\Middleware\DemoMiddleware2::class
  ];

  /**
   * This method will be used to register a route to the routes array
   * @param string $method
   * @param string $path
   * @param Closure|array $callable
   * @param string $name
   * @return void
   */
  public function __construct()
  {
    $this->start = function () {
      //
    };
  }

  public function registerRoute(string $method, string $path, Closure | array $callable, string $name, array $middleware = []): void
  {
    if(str_ends_with($path, '/') && $path !== '/') {
      $path = substr($path, 0, -1);
    }
    $this->routes[$method][$path] = [
      'callable' => $callable,
      'name' => $name,
      'middleware' => $middleware
    ];
  }

  /**
   * This method will be used to handle the route requested by the user
   * @param string $url
   * @param string $method
   * @return array|Closure
   */
  public function handleRoute(string $url, string $method): array|Closure
  {
    // Remove query string variables from URL (if any).
    $url = parse_url($url, PHP_URL_PATH);
    
    // check if $mehtod is a valid method
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
      throw new \Exception("Invalid request method: $method");
    }
    

    // check if the route exists
    return self::verifyRoute($url, $method);
  }

  /**
   * This method will be used to verify if the route exists
   * @param string $url
   * @param string $method
   * @return array|Closure
   */
  private function verifyRoute(string $url, string $method): array|Closure
  {
    // check if the route exists with params
    foreach ($this->routes[$method] as $route => $callable) {
      $validUrl = $route;
      $route = preg_replace('/\//', '\/', $route);
      $route = preg_replace('/\{[a-zA-Z0-9]*\}/', '([a-zA-Z0-9-]+)', $route);
      if (preg_match('/^' . $route . '$/', $url, $matches)) {
        array_shift($matches);

        self::handleMiddleware($this->routes[$method][$validUrl]['middleware']);
        return [$this->routes[$method][$validUrl]['callable'], $matches];
      }
    }
    // route not found
    throw new \Exception("Route not found: $url");
  }

  /**
   * This method will return the route by name
   * @param string $name
   * @return string
   */
  public function getRouteByName(string $name): string
  {
    foreach ($this->routes as $method => $routes) {
      foreach ($routes as $route => $callable) {
        if ($callable['name'] == $name) {
          return $route;
        }
      }
    }
    return "";
  }



  private function handleMiddleware(array $middlewares)
  {
    foreach($middlewares as $middleware) {
      if(isset($this->routeMiddleware[$middleware]))
        self::addCurrentMiddleware(new $this->routeMiddleware[$middleware]);
      }
    return call_user_func($this->start);
  }

  private function addCurrentMiddleware($middleware)
  {
    $next = $this->start;
    $this->start = function () use ($middleware, $next) {
      return $middleware($next, new \App\Http\Requests\Request(useCSRF: false));
    };
  }


}
