<?php

namespace App\Http\HttpKernel;

use App\Support\Session;

class RedirectResponse extends Response
{
  public function with($key, $message)
  {
    app(Session::class)->flash($key, $message);
  }

  public function withErrors($errors)
  {
    app(Session::class)->error($errors);
  }
}