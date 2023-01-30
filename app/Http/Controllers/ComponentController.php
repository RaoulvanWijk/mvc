<?php

namespace App\Http\Controllers;

use App\Http\HttpKernel\Request;

class ComponentController
{
  public function handle($type)
  {
    dd($_SERVER);
    $type = str_replace(' ', '',ucwords(str_replace('.', "\\", $type)));
    return app('App\\Components\\'. $type)->render(...Request::capture()->getParsedBody());
  }
}