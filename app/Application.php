<?php
namespace App;

use App\Http\HttpKernel\Router;
use App\Support\Container;

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
   * @throws \Exception
   */
  public function boot(): void
  {
    require_once dirname(__DIR__). "/configs/container.php";
    app(Router::class)->boot();
    $this->booted = true;
  }

  /**
   * This function will return wether or not the application has booted
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
  * @return void
  */
  public static function make(string $id, string|callable $concrete)
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
  public function get(string $id)
  {
    return static::$container->get($id);
  }
}