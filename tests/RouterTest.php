<?php

namespace Tests;

use App\Http\HttpKernel\Request;
use App\Http\HttpKernel\Router;
use App\Http\HttpKernel\Route;
use App\Http\HttpKernel\Uri;
use App\Http\HttpKernel\Stream;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testAddRoute(): void
    {
        $route = $this->router->addRoute('GET', '/test', function () {
            return 'success';
        });

        $this->assertInstanceOf(Route::class, $route);

        // Mock a request
        $uri = new Uri('http', 'localhost', '80', '/test', '', '');
        $request = new Request('/test', 'GET', $uri, [], new Stream(fopen('php://temp', 'r+')));

        $match = $this->router->match($request);
        $this->assertIsArray($match);
        // The router returns the route configuration array, where 'callable' is the Route object
        $this->assertSame($route, $match[0]['callable']);
    }

    public function testRouteParameters(): void
    {
        $route = $this->router->addRoute('GET', '/user/{id}', function ($id) {
            return "User " . $id;
        });

        $uri = new Uri('http', 'localhost', '80', '/user/42', '', '');
        $request = new Request('/user/42', 'GET', $uri, [], new Stream(fopen('php://temp', 'r+')));

        $match = $this->router->match($request);
        $this->assertIsArray($match);
        $this->assertSame($route, $match[0]['callable']);
        $this->assertEquals(['42'], $match[1]);
    }

    public function testRouteNotFound(): void
    {
        $uri = new Uri('http', 'localhost', '80', '/not-found', '', '');
        $request = new Request('/not-found', 'GET', $uri, [], new Stream(fopen('php://temp', 'r+')));

        $match = $this->router->match($request);
        $this->assertFalse($match);
    }

    public function testRouteMethodMismatch(): void
    {
        $this->router->addRoute('GET', '/test', function () {});

        $uri = new Uri('http', 'localhost', '80', '/test', '', '');
        $request = new Request('/test', 'POST', $uri, [], new Stream(fopen('php://temp', 'r+')));

        $match = $this->router->match($request);
        $this->assertFalse($match);
    }
}
