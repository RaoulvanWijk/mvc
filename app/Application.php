<?php

namespace App;

use App\Helpers\Support\Session;
use App\Http\HttpKernel;
/**
 * This class will be used to handle the application
 */
class Application
{
  // reference to the HttpKernel
  public static HttpKernel $httpKernel;

  // reference to the Session
  public static Session $session;

  // This will be used to store the Requestclass that is needed
  private object $currentRequestClass;
  // This will be used to store method that is needed
  private string $currentMethod;

  private array $params;

  // This method will be used to instantiate the HttpKernel class as singleton
  public function __construct()
  {
      self::$httpKernel = new HttpKernel();
      if(!isset(self::$session)) {
        self::$session = new Session();
      }
  }

  /**
   * This method will be used to start the application
   * @return void
   */
  public function start(): void
  {
    $request = self::$httpKernel->handleRoute($_SERVER['REQUEST_URI'], self::getRequestMethod());
    if(is_null($request)) exit();
    $this->params = $request[1];
    if(is_array($request[0])) {
      $this->currentRequestClass = new $request[0][0]();
      $this->currentMethod = $request[0][1];
      self::validateRequest();
      call_user_func_array([$this->currentRequestClass, $this->currentMethod], $this->params);
    } else {
      $request[0]($this->params);
    }
  }

  /**
   * This method will return the request method that is being used by the user
   * @return string
   */
  public function getRequestMethod(): string
  {
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_method'])) {
      return $_POST['_method'];
    } else {
      return $_SERVER['REQUEST_METHOD'];
    }
  }

  /**
   * This method will be used to validate the request
   * by checking if the method of the controller needs a request class
   * @return void
   */
  public function validateRequest() : void
  {
    $reflectionClass = new \ReflectionClass($this->currentRequestClass);
    $reflectionMethod = $reflectionClass->getMethod($this->currentMethod);
    if(count($reflectionMethod->getParameters()) > 0) {
      // Check if the first parameter needs a request class
      if(is_subclass_of($reflectionMethod->getParameters()[0]->getType()->getName(), 'App\Http\Requests\Request')) {
        $requestClass = $reflectionMethod->getParameters()[0]->getType()->getName();
        array_unshift($this->params, new $requestClass(array_merge($_GET, $_POST)));
      }
    }
  }
}
