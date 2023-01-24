<?php
namespace App;

use App\Exceptions\Container\ContainerException;
use App\Http\HttpKernel\Router;
use App\Support\Container;
use Exception;

class Application
{
  /**
   * Var used to store the container of the application
   * @var Container
   */
  public static Container $container;

  private bool $booted = false;

  /**
   * Instantiate the Container for this application
   */
  public function __construct()
  {
    static::$container = new Container();
  }

  /**
   * Call all the boot methods of necessary classes
   * @return void
   * @throws Exception
   */
  public function boot(): void
  {
    require_once dirname(__DIR__). "/configs/container.php";
    app(Router::class)->boot();
    $this->booted = true;
  }

  /**
   * This function will return weather or not the application has booted
   * @return bool
   */
  public function isBooted(): bool
  {
    return $this->booted;
  }

  /**
   * Register a new binding in the container
   * @param string $id
   * @param string|callable $concrete
   * @return mixed
   */
  public static function make(string $id, string|callable $concrete): mixed
  {
    return static::$container->bind($id, $concrete);
  }

  /**
   * Register a new binding in the container as a singleton
   * @param string $id
   * @param string|callable|object $concrete
   * @return mixed
   */
  public function singleton(string $id, string|callable|object $concrete): mixed
  {
    return static::$container->bind($id, $concrete, true);
  }

  /**
   * Get the value of a binding from the container
   * @param string $id
   * @return mixed
   */
  public function get(string $id): mixed
  {
    try {
      return static::$container->get($id);
    } catch (ContainerException | Exception $exception) {
      error($exception, "ERROR: Something went wrong when resolving the dependency's");
    }
  }
}