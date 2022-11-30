<?php
namespace App\Http;

use App\Application;
use Closure;

class Route
{

  private static string $prefix = "";
  private static array $middleware = [];

  /**
   * This method will be used to register a GET route
   * @param string $path
   * @param Closure|array $callable
   * @param string $name
   * @return void
   */
  public static function get(string $path, Closure|array $callable, string $name): void
  {
    Application::$httpKernel->registerRoute('GET', self::$prefix . $path, $callable, $name, self::$middleware);
  }

  /**
   * This method will be used to register a POST route
   * @param string $path
   * @param Closure|array $callable
   * @param string $name
   * @return void
   */
  public static function post(string $path, Closure|array $callable, string $name): void
  {
    Application::$httpKernel->registerRoute('POST', self::$prefix . $path, $callable, $name, self::$middleware);
  }

  /**
   * This method will be used to register a PUT route
   * @param string $path
   * @param Closure|array $callable
   * @param string $name
   * @return void
   */
  public static function put(string $path, Closure|array $callable, string $name): void
  {
    Application::$httpKernel->registerRoute('PUT', self::$prefix . $path, $callable, $name, self::$middleware);
  }

  /**
   * This method will be used to register a DELETE route
   * @param string $path
   * @param Closure|array $callable
   * @param string $name
   * @return void
   */
  public static function delete(string $path, Closure|array $callable, string $name): void
  {
    Application::$httpKernel->registerRoute('DELETE', self::$prefix . $path, $callable, $name, self::$middleware);
  }

  /**
   * This method will set the prefix for the routes in a group function
   * @param string $prefix
   * @return self
   */
  public static function prefix(string $prefix): self
  {
    self::$prefix = $prefix;
    return new self();
  }
  
  /**
   * This method will be used to register a group of routes
   * @param Closure $callable
   * @return self
   */
  public static function group(Closure $callable, array $attributes = []): self
  {
    if(isset($attributes["middleware"])) {
      if(is_string($attributes["middleware"])) $attributes["middleware"] = [$attributes["middleware"]];
      self::$middleware = $attributes["middleware"];
    }
    $callable();
    self::$prefix = "";
    self::$middleware = [];
    return new self();
  }
  
  // create a function that will register middleware to a route
  // public static function middleware(string $name, $callable)
  // {
  //   Application::$httpKernel->middleware($name, $callable);
  //   return new self;
  // }

  /**
   * This method will return the route by name
   * @param string $name
   * @return string
   */
  public static function route(string $name, array $params = []): string
  {
    $route = Application::$httpKernel->getRouteByName($name);
    foreach ($params as $key => $value) {
      $route = str_replace('{' . $key . '}', $value, $route);
    }
    return $route;
  }
}
