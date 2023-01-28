<?php

namespace App\Support;

use Twig\TwigFunction;

class GlobalFunctionsTwig extends \Twig\Extension\AbstractExtension
{
  public function getFunctions(): array
  {
    return [
      new TwigFunction('method', [$this, 'method']),
      new TwigFunction('session', [$this, 'session'])
    ];
  }

  public function method($name)
  {
    return method($name);
  }


  public function session($key = null)
  {
    return $key ? app(Session::class)->get($key) : app(Session::class);
  }
}