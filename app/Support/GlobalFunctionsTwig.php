<?php

namespace App\Support;

use Twig\TwigFunction;

class GlobalFunctionsTwig extends \Twig\Extension\AbstractExtension
{
  public function getFunctions(): array
  {
    return [
      new TwigFunction('method', [$this, 'method']),
    ];
  }

  public function method($name)
  {
    return method($name);
  }


}