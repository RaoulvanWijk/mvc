<?php

namespace App\Console\Commands;

class CreateController
{
  public string $defaultName = "make:controller";
  public string $defaultDescription = "Create a new controller";

  public function execute($name): void
  {
    $template =  $this->controllerTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    if(!file_exists(__DIR__. "/../../Http/Controllers/$name.php")) {
      $file = fopen(__DIR__. "/../../Http/Controllers/$name.php", "w");
      fwrite($file, $template);
      fclose($file);
      echo "\e[32mSuccessfully created a controller at \e[39m".realpath(dirname(__DIR__, 2). "/Http/Controllers/$name.php");
    } else echo "\e[31mFile already exists at \e[39m".realpath(dirname(__DIR__, 2). "/Http/Controllers/$name.php");

  }

  public function getUsage()
  {
    return "make:controller <name>";
  }

  public function getArgs()
  {
    return [
      "name" => "The name of the Controller",
    ];
  }

  private function controllerTemplate(): string
  {
    return '<?php

namespace App\Http\Controllers;

class {{ name }}
{
  public function index()
  {
    
  }
}';
  }
}