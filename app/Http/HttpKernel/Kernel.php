<?php

namespace App\Http\HttpKernel;

use App\Exceptions\Http\MiddlewareNotFound;
use App\Http\Middleware\Middleware;
use App\Http\Middleware\UseSession;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Kernel implements RequestHandlerInterface
{
  private array $routeHandler = [];
    private array $middleware = [
      //
    ];

  /**
   * Var used to store the fully qualified classname
   * by an id, so it's easier to apply on a route
   * @var array|string[]
   */
  private array $routeMiddleware = [
    "use.session" => UseSession::class,
    "api" => Middleware::class
  ];

  /**
   * This function will be used to handle the route
   * @param ServerRequestInterface $request
   * @return Response
   * @throws ContainerExceptionInterface
   * @throws MiddlewareNotFound
   * @throws NotFoundExceptionInterface
   * @throws Exception
   */
  public function handle(ServerRequestInterface $request): Response
  {
    $route = app(Router::class)->match($request);

    // Check if a route was found
    if($route) {

      // check if the Route class has a closure or controller and method
      // and handle the route based on the outcome
      if(!$route[0]["callable"]->isClosure()) {
        $this->routeHandler = [[$route[0]["callable"]->getController(), $route[0]["callable"]->getMethod()], $route[1]];
        $response = $this->handleRoute($route[0], $request);
      } else {
        $this->routeHandler = [$route[0]["callable"]->getAction(), $route[1]];
        $response = $this->handleRoute($route[0], $request);
      }
    } else {
      // if no route was found return 404 response
      $response = new Response(404, reasonPhrase: "Page not found");
    }

    // Check if the $response that was returned is an actual response
    // If it is not replace with actual response
    if(!$response instanceof Response) $response = new Response();

    // return a response
    return $response;
  }

  /**
   * This function will first handle all the middleware
   * and after that call the route handler
   * @param array $route
   * @param $request
   * @return Response
   * @throws MiddlewareNotFound
   */
  private function handleRoute(array $route, $request) : Response
  {
    // attempts to get all the middleware classes that are registered in the $routeMiddleware array
    // if it is not found it will throw a MiddlewareNotFound exception
    $middlewares = $this->getMiddlewares($route["callable"]->getMiddlewares());

    // sets the idx to 0
    $idx = 0;
    // Creates an anonymous function that will run all the middleware
    // and at the end run the controller with method or Closure

    $next = function (ServerRequestInterface $request) use (&$middlewares, &$idx, &$next) {
      if ($idx >= count($middlewares)) {
        return $this->callRouteHandler($request);
      }
      $idx++;
      $middlewareInstance = app($middlewares[$idx-1]);
      return $middlewareInstance->process($request, $next);
    };

    // Start the chain
    return $next($request);
  }


  /**
   * This function wil return the fully qualified classnames
   * of all the middleware registered to the current route
   * @param array $middlewares
   * @return array
   * @throws MiddlewareNotFound
   */
  private function getMiddlewares(array $middlewares): array
  {
    return array_map(
      function ($middleware) {
        if(!$this->routeMiddleware[$middleware]) {
          throw new MiddlewareNotFound('Middleware '.$middleware.' does not exist in the $routeMiddleware[] array in app\Http\HttpKernel\Kernel.php');
        }
        return $this->routeMiddleware[$middleware];
      }, $middlewares);
  }

  /**
   * This function will call the routeHandler and return a response
   * if no response was returned by the routehandler or middleware
   * it will create a new empty Response
   * @param $request
   * @return Response
   * @throws Exception
   */
  private function callRouteHandler($request): Response
  {
    if(is_callable($this->routeHandler[0])) {
      $response = call_user_func_array($this->routeHandler[0], $this->routeHandler[1]);
    } else {
      $response = call_user_func_array([app($this->routeHandler[0][0]), $this->routeHandler[0][1]], $this->routeHandler[1]);
    }


    if(!$response instanceof Response) $response = new Response();
    return $response;
  }
}