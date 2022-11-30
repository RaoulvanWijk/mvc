<?php
namespace App\Http;

use App\Http\Controllers\Controller;

class HttpResponseCodeHandler extends Controller
{
  public function NotFound()
  {
    $this->view('responses/notFound');
  }
}