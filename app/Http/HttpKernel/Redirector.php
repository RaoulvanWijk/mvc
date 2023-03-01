<?php

namespace App\Http\HttpKernel;

class Redirector
{
  private RedirectResponse $response;

  public function to($location, $status = 302, $headers = []): Response
  {
    $this->response = new RedirectResponse();
    $this->response = $this->response->withAddedHeader("Location", $location);
    $this->response = $this->response->withStatus($status);
    foreach ($headers as $header => $value) {
      $this->response = $this->response->withAddedHeader($header, $value);
    }
    return $this->response;
  }
}