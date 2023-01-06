<?php

namespace App\Http\HttpKernel;

class Redirector
{
  private Response $response;

  public function to($location, $status = 200, $headers = []): Response
  {
    $this->response = new Response();
    $this->response = $this->response->withAddedHeader("Location", $location);
    $this->response = $this->response->withStatus($status);
    foreach ($headers as $header => $value) {
      $this->response = $this->response->withAddedHeader($header, $value);
    }
    return $this->response;
  }
}