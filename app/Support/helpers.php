<?php

if(!function_exists("view")) {
  /**
   * This function will load a view
   * by putting it in the body of the stream
   * and returning a response
   * @param $file
   * @param array $data
   * @return \App\Http\HttpKernel\Response
   * @throws Exception
   */
    function view($file, array $data = []): \App\Http\HttpKernel\Response
    {
        $twig = \App\Application::$container->get('twig');
        $resource = fopen("php://temp", 'r+');
        $response = new \App\Http\HttpKernel\Response();
        $response->getBody()->write($twig->render("index.twig", $data));
        return $response;
    }
}

if(!function_exists("app")) {
  /**
   * This function will try and get the class from the container
   * @param $id
   * @return mixed|object|string|null
   * @throws Exception
   */
  function app($id): mixed
  {
    return \App\Application::$container->get($id);
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
   * @throws Exception
   */
  function redirect(string $location, int $status, array $headers = []): \App\Http\HttpKernel\Response
  {
    return app(\App\Http\HttpKernel\Redirector::class)->to($location, $status, $headers);
  }
}
