<?php

namespace App\Console\Commands;

class MakeMiddleware extends Command
{
    protected string $defaultName = "make:middleware";
    protected string $usage = "make:middleware [arguments] [options]";
    protected array $arguments = [
        "<name>" => "The name of the middleware"
    ];
    protected array $options = [
        //
    ];
    protected string $defaultDescription = "Create a new middleware class";

  public function execute($name): void
  {
    $template =  $this->middlewareTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    if(!file_exists(dirname(__DIR__, 2). "/Http/Middleware/$name.php")) {
      $file = fopen(dirname(__DIR__, 2). "/Http/Middleware/$name.php", "w");
      fwrite($file, $template);
      fclose($file);
      echo "\e[32mSuccessfully created a middleware at \e[39m".realpath(dirname(__DIR__, 2). "/Http/Middleware/$name.php");
    } else echo "\e[31mFile already exists at \e[39m".realpath(dirname(__DIR__, 2). "/Http/Middleware/$name.php");
  }

  private function middlewareTemplate(): string
  {
    return '<?php

namespace App\Http\Middleware;

use App\Contracts\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Contracts\MiddlewareInterface;
use Closure;

class {{ name }} implements MiddlewareInterface
{
  public function process(Request $request, Closure $next): Response
  {
    return $next($request);
  }
}';
  }
}