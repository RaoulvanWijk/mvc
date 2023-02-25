<?php

namespace App\Support;

use App\Http\HttpKernel\Request;
use App\Models\Model;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use App\Exceptions\Container\NotFoundException;
use App\Exceptions\Container\ContainerException;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionFunction;

class Container implements ContainerInterface
{
  private array $bindings = [];

  /**
   * @param string $id
   * @return mixed|object|string|null
   */
  public function get(string $id): mixed
  {
    if($this->has($id)) {
      return $this->bindings[$id]["concrete"]($this);
    }
    try {
      return $this->resolve($id);
    } catch (ContainerException | Exception $exception) {
      error($exception, "ERROR: Something went wrong when resolving the dependency's from the class");
    }
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
  public function bind(string $id, string | callable | object $concrete, bool $singleton = false)
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

  /**
   * Resolve the parameters from the given method
   * @throws ReflectionException
   * @throws ContainerException
   */
  public function resolveMethod($method, $params = [], $request = null)
  {
    $newParams = $params;
    /**
     * Check if the method is a function or class and method
     * if not throw exception
     */
    if(is_callable($method)) {
      $method = new ReflectionFunction($method);
    } elseif (class_exists($method[0])) {
      $method = new \ReflectionMethod($method[0], $method[1]);
    } else {
      throw new ContainerException("The given callable of route is not a class or function");
    }
    /**
     * Get the parameters from the method or function
     */
    $methodParams = $method->getParameters();

    /**
     * If there are no parameters return empty array
     */
    if (empty($methodParams)) return [];

    // Initialize $idx var
    $idx = 0;

    /**
     * Loop over all the parameters
     * to try and resolve the type
     */
    foreach ($methodParams as $param) {
      $type = $param->getType();

      // If no type move to next param
      if (!$type) {
        $idx++;
        continue;
      }

      // If type is builtin or is UnionType move on
      if($type->isBuiltin() || $type instanceof \ReflectionUnionType) {
        $idx++;
        continue;
      }

      /**
       * If type is Request class put the current request at current idx
       * and move the other vars in array to the right
       */
      if($type->getName() === Request::class) {
        array_splice($newParams, $idx, 0, [$request]);
        $idx++;
        continue;
      }

      /**
       * If type is subclass of model
       * find the row from the database
       */
      if (is_subclass_of($type->getName(), Model::class)) {
        $id = ($type->getName())::find($newParams[$idx]);
        $newParams[$idx] = $id;
        $idx++;
        continue;
      }

      /**
       * If other if statements are false
       * try to resolve the class from the container
       * if it fails to resolve
       * it throws exception
       */
      $newParams[$idx] = $this->resolve($type);

    }
    /**
     * return the newParams array to the router,
     * so it can be parsed to the method
     */
    return $newParams;
  }
}