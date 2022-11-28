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
  public function registerRoute(string $method, string $path, Closure | array $callable, string $name): void
  {
    if(str_ends_with($path, '/') && $path !== '/') {
      $path = substr($path, 0, -1);
    }
    $this->routes[$method][$path] = [
      'callable' => $callable,
      'name' => $name
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

    // check if the route exists without params
    if (isset($this->routes[$method][$url])) {
      return [$this->routes[$method][$url]['callable'], []];
    }

    // check if the route exists with params
    return self::verifyRouteWithParams($url, $method);
  }

  /**
   * This method will be used to verify if the route exists with params
   * @param string $url
   * @param string $method
   * @return array|Closure
   */
  private function verifyRouteWithParams(string $url, string $method): array|Closure
  {
    // check if the route exists with params
    foreach ($this->routes[$method] as $route => $callable) {
      $validUrl = $route;
      $route = preg_replace('/\//', '\/', $route);
      $route = preg_replace('/\{[a-zA-Z0-9]*\}/', '([a-zA-Z0-9-]+)', $route);
      if (preg_match('/^' . $route . '$/', $url, $matches)) {
        array_shift($matches);
        // $this->routes[$method][$url]['params'] = $matches;
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

  public function middleware(string $name, $callable)
  {
    if(isset($this->routeMiddleware[$name])) {
      $t = new $this->routeMiddleware[$name];
      $t->handle("test", $callable);
    }
  }
}
