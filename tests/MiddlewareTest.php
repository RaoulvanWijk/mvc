<?php

namespace Tests;

use App\Application;
use App\Contracts\MiddlewareInterface;
use App\Http\HttpKernel\Kernel;
use App\Http\HttpKernel\Request;
use App\Http\HttpKernel\Response;
use App\Http\HttpKernel\Router;
use App\Http\HttpKernel\Stream;
use App\Http\HttpKernel\Uri;
use PHPUnit\Framework\TestCase;
use Closure;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/../app/Support/helpers.php';
use Psr\Http\Message\ServerRequestInterface;

class TestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, Closure $next): ResponseInterface
    {
        $response = $next($request);
        return $response->withHeader('X-Test-Middleware', 'working');
    }
}

class MiddlewareTest extends TestCase
{
    private Application $app;
    private Kernel $kernel;
    private Router $router;

    protected function setUp(): void
    {
        // Setup application and container
        $this->app = new Application();

        // Bind Router
        $this->router = new Router();
        $this->app->singleton(Router::class, $this->router);

        // Bind Kernel
        $this->kernel = new Kernel();
        $this->app->singleton(Kernel::class, $this->kernel);

        // Register TestMiddleware in the container
        // App\Application does not have bind method, use singleton or proxy to container
        Application::$container->bind(TestMiddleware::class, TestMiddleware::class);

        // We need to inject the middleware into the Kernel's private $routeMiddleware array
        // using Reflection because there is no public API to do so.
        $reflection = new \ReflectionClass($this->kernel);
        $property = $reflection->getProperty('routeMiddleware');
        $property->setAccessible(true);
        $middlewares = $property->getValue($this->kernel);
        $middlewares['test.middleware'] = TestMiddleware::class;
        $property->setValue($this->kernel, $middlewares);

        // Set the static container instance for helpers like app()
        // App\Application::$container is set by new Application()
    }

    public function testMiddlewareIsExecuted(): void
    {
        // Define a route that uses the middleware
        $this->router->addRoute('GET', '/middleware-test', function () {
            return new Response(200, [], new Stream(fopen('php://temp', 'r+')));
        })->middleware(['test.middleware']);

        // Create a request
        $uri = new Uri('http', 'localhost', '80', '/middleware-test', '', '');
        $request = new Request('/middleware-test', 'GET', $uri, [], new Stream(fopen('php://temp', 'r+')));

        // Handle the request
        $response = $this->kernel->handle($request);

        // Assert the middleware added the header
        $this->assertTrue($response->hasHeader('X-Test-Middleware'));
        $this->assertEquals(['working'], $response->getHeader('X-Test-Middleware'));
    }
}
