<?php

namespace App\Support;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use App\Exceptions\Container\NotFoundException;
use App\Exceptions\Container\ContainerException;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class Container implements ContainerInterface
{
  private array $bindings = [];

  /**
   * @param string $id
   * @return mixed|object|string|null
   * @throws Exception
   */
  public function get(string $id): mixed
  {
    if($this->has($id)) {
      return $this->bindings[$id]["concrete"]($this);
    }

    return $this->resolve($id);
  }

  /**
   * @param string $id
   * @return bool
   */
  public function has(string $id) : bool
  {
    return isset($this->bindings[$id]);
  }

  /**
   * @param string $id
   * @param string|callable|object $concrete
   * @param bool $singleton
   * @return array|void
   */
  public function bind(string $id, string | callable | object $concrete, bool $singleton = false): mixed
  {
    if($singleton) {
        if(!is_object($concrete)) {
            $class = new $concrete();
        } else {
            $class = $concrete;
        }

      $concrete = fn () => $class;
      $this->bindings[$id] = compact("concrete", "singleton");
      return;
    } else {
      if(is_callable($concrete)) {
        $this->bindings[$id] = compact("concrete", "singleton");
      } else {
        $concrete = fn () => new $concrete();
        $this->bindings[$id] = compact("concrete", "singleton");
      }
    }

    return $this->bindings[$id];
  }

  /**
   * resolve the class
   * @param string $id
   * @return mixed|object|string|null
   * @throws ContainerException
   * @throws ReflectionException
   */
  public function resolve(string $id): mixed
  {
    // Instantiate the ReflectionClass 
    // to view the Class of the $id
    $ref = new \ReflectionClass($id);

    // Check if the class is instantiable
    // If not, throw an exception
    if(!$ref->isInstantiable()) {
      throw new Exception("Class {$id} is not instantiable");
    }

    // get the method if $method is not null
    // else get the constructor
    $method = $ref->getConstructor();

    // If the method is not null, get the parameters
    // else return a new instance of the class
    if(!$method) {
      return new $id;
    }

    // Get the parameters of the method
    $params = $method->getParameters();
    
    // If the parameters are empty, return a new instance of the class
    
    if(empty($params)) {
      return new $id;
    }
    
    // Get the dependencies of the parameters
    $dependencies = $this->getDependencies($id, $params);

    return $ref->newInstanceArgs($dependencies);
  }

  /**
   * This function will get all the dependencies from a class that is being resolved
   * from the constructor
   * @param string $id
   * @param array $params
   * @return array
   * @throws ContainerException
   */
  public function getDependencies(string $id, array $params): array
  {
    $dependencies = array_map(
    /**
     * @throws ContainerException
     */
    function (\ReflectionParameter $param) use ($id) {
        $name = $param->getName();
        $type = $param->getType();

        if (! $type) {
          // If there is no type move to the next parameter
          return null;
        }

        if ($type instanceof \ReflectionUnionType) {
          throw new ContainerException(
            'Failed to resolve class "' . $id . '" because of union type for param "' . $name . '"'
          );
        }

        if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
          return $this->get($type->getName());
        }

        throw new ContainerException(
          'Failed to resolve class "' . $id . '" because invalid param "' . $name . '"'
        );
      },
      $params
    );
    return $dependencies;
  }
}