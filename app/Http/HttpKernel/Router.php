<?php

namespace App\Http\HttpKernel;

use App\Support\Arr;
use Closure;

/**
 * @method prefix($prefix)
 * @method middleware($name)
 */
class Router
{

  /**
   * Var used to store all the registered routes
   * with there corresponding method
   * @var array|array[]
   */
  private array $routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
    'PATCH' => [],
    'OPTIONS' => [],
    'HEAD' => []
  ];

  /**
   * var used to keep track of the current prefix
   * @var string
   */
  private string $prefix = '';

  /**
   * Var used to keep track of current middleware stack
   * of a group and its children
   * @var array
   */
  private array $middleware = [];

  private array $groups = [];

  private array $groupContext = [];

  /**
   * This function will try and match all the routes
   * with the current path from the request
   * if it has found a match it will return the route class with all the parameters in the route
   * if not it will return false
   * @param Request $request
   * @return array|false
   */
  public function match(Request $request): array | false
  {
    foreach ($this->routes[$request->getMethod()] as $url => $route) {

      // Replace all forward slashes with escaped forward slashes
      $url = preg_replace('/\//', '\/', $url);

      // Replace any route parameters (placeholders in curly braces)
      // with a regular expression that will match an alphanumeric string
      $url = preg_replace('/\{[a-zA-Z0-9]*\}/', '([a-zA-Z0-9-]+)', $url);

      // Check if the modified url matches the path of the current request
      if (preg_match('/^' . $url . '$/', $request->getUri()->getPath(), $matches)) {

          // remove the first element of the array
          // since that is the full matched string
          array_shift($matches);

          // return the route with matches (parameters)
          return [$route, $matches];
      }
    }
    // if no route has been found return false
    return false;
  }

  /**
   * This function will load all the routes from the web.php and api.php folder
   * The routes in api.php will by default have the api prefix
   * @return void
   */
  public function boot(): void
  {
    // Load all the routes from api.php
    // with the api prefix
    $this->prefix('api')
      ->middleware('api')
      ->group(function () {
      require_once dirname(__DIR__, 3) . '/routes/api.php';
    });

    // Load all the routes from web.php
    // and its needed middleware to function properly
    $this->middleware("use.session")
    ->group(function () {
      require_once dirname(__DIR__, 3) . '/routes/web.php';
    });
  }

  /**
   * This function will add a new route to the $routes array
   * So it can be used
   * @param string $method
   * @param string $url
   * @param array|callable $action
   * @return Route
   */
  public function addRoute(string $method, string $url, array|callable $action): Route
  {
    // instantiate a new route class with $action
    $route = new Route($action);
    $route->middleware($this->getMiddleware());
    $prefix = $this->getPrefix();
    // register the route to the $routes array
    if(!empty($prefix) && $url === '/') $url = '';
    $this->routes[$method][$prefix .$url] = [
      "callable" => $route,
      "middleware" => []
    ];

    // return the route class so that middleware can be added
    return $route;
  }

  /**
   * This function will return the route by the given path
   * @param $path
   * @return array
   */
  public function getRoute($path): array
  {
    return array_column($this->routes, $path);
  }

  /**
   * This function will group all the routes in the callback
   * So that you can add a prefix to them
   * and put middleware on all the routes in the callback
   * and nested groups
   * @param Closure|array $attributes
   * @param Closure|null $callback
   * @return RouteGroup
   */
  public function group(Closure | array $attributes, Closure $callback = null): RouteGroup
  {
    // Set the attributes given as the second parameter
//    $this->setAttribute($attributes);
    $group = new RouteGroup();
    $this->groups[] = $group->group($attributes, $callback);
    // return this
    return $group;
  }

  /**
   * This function will return all the routes registered in the $routes array
   * @return array|array[]
   */
  public function getRoutes(): array
  {
    return $this->routes;
  }

  /**
   * if you call a function that does not exist this function will run
   * @param $method
   * @param $params
   * @return RouteGroup|null
   */
  public function __call($method, $params)
  {
    // if the called function is "middleware"
    if($method === 'middleware') {
      $var = (new RouteGroup())->middleware($params);
      // set the middleware attribute to the $params
      $this->groups[] = $var;
    }

    // if the called function is 'prefix'
    if($method === 'prefix') {
      // set the prefixes attribute to the $params
      $var = (new RouteGroup())->prefix(...$params);
      $this->groups[] = $var;
    }

    // return $this
    return $var ?? null;
  }

  /**
   * This function will set the attributes
   * for middleware and prefix
   * @param $attributes
   * @return void
   */
  private function setAttribute($attributes): void
  {
    // if middleware isset in the $attributes param
    if(isset($attributes["middleware"])) {
      // add attributes as new index in middleware array
      $this->middleware[] = $attributes["middleware"];
    }

    // if prefix isset in the $attributes param
    if(isset($attributes["prefix"][0])) {
      // format the prefix, so it can be used in the application
      if(!str_starts_with($attributes["prefix"][0], '/')) $attributes["prefix"][0] = '/'. $attributes["prefix"][0];
      if(str_ends_with($attributes["prefix"][0], '/')) $attributes["prefix"][0] = rtrim($attributes["prefix"][0], '/');

      // set the outcome of the formatted string to the prefix property
      $this->prefix = $this->prefix . $attributes["prefix"][0];
    }
  }

  public function removeOldGroup()
  {
    array_pop($this->groupContext);
  }

  public function updateGroupContext($group)
  {
    $this->groupContext[] = $group;
  }

  private function getPrefix(): string
  {
    $prefix = '';
    foreach ($this->groupContext as $group) {
      $prefix .= $group->prefix;
    }
    return $prefix;
  }

  private function getMiddleware()
  {
    $middleware = [];
    foreach ($this->groupContext as $group) {
      $middleware = array_merge($middleware, $group->middleware);
    }
    return $middleware;
  }
}
