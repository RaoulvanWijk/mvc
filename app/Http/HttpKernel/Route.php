<?php

namespace App\Http\HttpKernel;

use App\Support\Arr;
use Exception;
use Closure;
use http\Exception\RuntimeException;
use Symfony\Component\VarDumper\Caster\ClassStub;


/**
 * @method static \App\Http\HttpKernel\Route get(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route post(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route put(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route delete(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route patch(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route options(string $path, array|callable $action)
 * @method static \App\Http\HttpKernel\Route head(string $path, array|callable $action)
 *
 * @see \App\Http\HttpKernel\Router addRoute()
 */
class Route
{

  /**
   * var used to store all the allowed http methods of which you can register a route to
   * @var array|string[]
   */
  public static array $allowedMethods = [
    'get',
    'post',
    'put',
    'delete',
    'patch',
    'options',
    'head'
  ];

  /**
   * var used to keep track of the controller registered to this route
   * @var string|mixed
   */
  private string $controller;

  /**
   * Var used to kee track of the method registered to this route
   * @var string|mixed
   */
  private string $method;

  /**
   * Var used to store all the middleware applied to this route
   * @var array
   */
  private array $middlewares = [];

  /**
   * @var bool this will keep track if this route has a closure or controller/method
   */
  private bool $isClosure = false;

  /**
   * Var used to keep track of the closure given
   * @var Closure
   */
  private Closure $action;

  /**
   * @var string Var used to store the name given to the route
   */
  private string $name = '';

  public function __construct($action)
  {
    // Check if the $action parameter is an array
    if(is_array($action)) {
      // if it is set the controller and method
      $this->controller = $action[0];
      $this->method = $action[1];
    } else {
      // a closure must have been given
      // so set isClosure to true
      // and set the closure
      $this->isClosure = true;
      $this->action = $action;
    }
  }

  /**
   * This function will return the controller registered to this routed
   * @return string
   */
  public function getController(): string
  {
    return $this->controller;
  }

  /**
   * This function will return the method of the controller class registered to this route
   * @return string
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * this function will return the closure registered to the route
   * @return Closure
   */
  public function getAction(): Closure
  {
    return $this->action;
  }

  /**
   * This function will return all the middleware registered to this route
   * excluding the middleware applied to the group its in
   * @return array
   */
  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }

  /**
   * This function will return the isClosure property
   * so that the HttpKernel\Kernel class can check weather or not it needs to resolve the controller
   * @return bool
   */
  public function isClosure(): bool
  {
    return $this->isClosure;
  }

  /**
   * This function can be used to give a name to a route
   * @param string $name
   * @return $this
   */
  public function name(string $name): static
  {
    $this->name = $name;
    return $this;
  }

  /**
   * This will return the name of the route
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * This function will apply middleware to a route
   * @param ...$middleware
   * @return RouteGroup|Route
   */
  public function middleware(...$middleware): RouteGroup | Route
  {
    $this->middlewares = Arr::array_flatten(array_merge($this->middlewares, $middleware));
    return $this;
  }

  /**
   * This functions sets the prefix for a route group
   * @param string $prefix
   * @return Router
   */
  public static function prefix(string $prefix): RouteGroup
  {
    return app(Router::class)->prefix($prefix);
  }

  public static function group(Closure | string $attributes, Closure $callback = null)
  {
    return app(Router::class)->group($attributes, $callback);
  }

  /**
   * This function returns all the routes from the router class
   * @return array
   */
  public static function getRoutes(): array
  {
    return app(Router::class)->getRoutes();
  }

  /**
   * This function can register new routes to the router class
   * @param string $method
   * @param array $params
   * @return Route
   * @throws \RuntimeException|Exception
   */
  public static function __callStatic(string $method, array $params): Route
  {
    if(in_array($method, self::$allowedMethods)) {
      $rc = \App\Application::$container->get(Router::class);
      return $rc->addRoute(strtoupper($method), $params[0], $params[1]);
    } else {
      throw new RuntimeException("The static method $method does not exist in the Route class");
    }
  }
}
