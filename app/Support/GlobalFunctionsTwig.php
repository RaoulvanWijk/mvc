<?php

namespace App\Support;

use Twig\TwigFunction;

class GlobalFunctionsTwig extends \Twig\Extension\AbstractExtension
{
  public function getFunctions(): array
  {
    return [
      new TwigFunction('method', [$this, 'method']),
      new TwigFunction('session', [$this, 'session']),
      new TwigFunction('component', [$this, 'component'])
    ];
  }

  public function method($name): string
  {
    return method($name);
  }


  public function session($key = null): mixed
  {
    return $key ? app(Session::class)->get($key) : app(Session::class);
  }

  public function component($name, $params = []): string
  {
    $id = uniqid();
    return "<div id='".$id."'  data-component-type='".$name."' data-component-params='".json_encode($params)."'></div>";
  }
}