<?php

if(!function_exists("view")) {
  /**
   * This function will load a view
   * by putting it in the body of the stream
   * and returning a response
   * @param $file
   * @param array $data
   * @return \App\Http\HttpKernel\Response
   */
    function view($file, array $data = []): \App\Http\HttpKernel\Response
    {
        $twig = \App\Application::$container->get('twig');
        $twig->addGlobal("methods", methods());
        $resource = fopen("php://temp", 'r+');
        $response = new \App\Http\HttpKernel\Response();
        $response->getBody()->write($twig->render("$file.twig", $data));
        return $response;
    }
}

if(!function_exists("app")) {
  /**
   * This function will try and get the class from the container
   * @param $id
   * @return mixed|object|string|null
   */
  function app($id): mixed
  {
    try {
      return \App\Application::$container->get($id);
    } catch(Exception | \App\Exceptions\Container\ContainerException $exception) {
      error($exception);
    }
  }
}

if(!function_exists("redirect")) {
  /**
   * This function will return a response
   * with an added header('Location')
   * to the given location
   * @param string $location
   * @param int $status
   * @param array $headers
   * @return \App\Http\HttpKernel\Response
   */
  function redirect(string $location, int $status = 302, array $headers = []): \App\Http\HttpKernel\Response
  {
    return app(\App\Http\HttpKernel\Redirector::class)->to($location, $status, $headers);
  }
}

if(!function_exists("methods")) {
  function methods(): array
  {
    $methods = [];
    foreach (\App\Http\HttpKernel\Route::$allowedMethods as $method) {
      $methods[$method] = "<input type='hidden' name='_method' value='$method'>";
    }
    return $methods;
  }
}

if(!function_exists("method")) {
  function method($method): string
  {
      return "<input type='hidden' name='_method' value='$method'>";
  }
}

if(!function_exists("error")) {
  /**
   * This function will send any error to a log file
   * And die with the error message
   * @param $error
   * @param $custom
   * @return void
   */
  function error($error, $custom = null): void
  {
    if(!is_null($custom)) {
      error_log($custom. "\n", 3, dirname(__DIR__, 2). '/storage/logs/app.log');
    }
    error_log($error. "\n", 3, dirname(__DIR__, 2). '/storage/logs/app.log');
    dd($error);
  }
}

if(!function_exists("message_log")) {
  /**
   * This function will send any error to a log file
   * And die with the error message
   * @param $error
   * @param $custom
   * @return void
   */
  function message_log($message): void
  {
    error_log($message. "\n", 3, dirname(__DIR__, 2). '/storage/logs/app.log');

  }
}