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
