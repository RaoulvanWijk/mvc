<?php

namespace Tests;

use App\Application;
use App\Http\HttpKernel\Kernel;
use App\Http\HttpKernel\Request;
use App\Http\HttpKernel\Response;
use App\Http\HttpKernel\Router;
use App\Http\HttpKernel\Stream;
use App\Http\HttpKernel\Uri;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/Support/helpers.php';

class HttpMethodsTest extends TestCase
{
    private Application $app;
    private Kernel $kernel;
    private Router $router;

    protected function setUp(): void
    {
        $this->app = new Application();
        $this->router = new Router();
        $this->app->singleton(Router::class, $this->router);
        $this->kernel = new Kernel();
        $this->app->singleton(Kernel::class, $this->kernel);
        // App\Application::$container is already set by new Application()
    }

    /**
     * @dataProvider httpMethodProvider
     */
    public function testHttpMethods(string $method): void
    {
        $path = '/test-' . strtolower($method);

        $this->router->addRoute($method, $path, function () use ($method) {
            return "Response for $method";
        });

        $uri = new Uri('http', 'localhost', '80', $path, '', '');
        $request = new Request($path, $method, $uri, [], new Stream(fopen('php://temp', 'r+')));

        $response = $this->kernel->handle($request);

        $this->assertEquals(200, $response->getStatusCode(), "Failed for method $method");
        $this->assertEquals("Response for $method", (string)$response->getBody());
    }

    public static function httpMethodProvider(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
            ['OPTIONS'],
            ['HEAD'],
        ];
    }
}
