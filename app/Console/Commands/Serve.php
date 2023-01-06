<?php

namespace App\Console\Commands;

class Serve
{
  public string $defaultName = "serve";
  public string $defaultDescription = "Serve the application on the PHP web server";

  public function execute(): void
  {
    echo "\e[32mStarting the webserver on:\e[39m http://localhost:8080\n";
    exec("php -S localhost:8080 -t public");
  }

  public function getUsage()
  {
    return "serve";
  }

  public function getArgs()
  {
    return [

    ];
  }
}