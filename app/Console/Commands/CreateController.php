<?php

namespace App\Console\Commands;

class CreateController extends Command
{
    protected string $defaultName = "make:controller";
    protected string $usage = "make:controller [arguments] [options]";
    protected array $arguments = [
        "<name>" => "The name of the controller"
    ];
    protected array $options = [

    ];
    protected string $defaultDescription = "Serve the application on the PHP web server";

  public function execute($name): void
  {
    $template =  $this->controllerTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    if(!file_exists(__DIR__. "/../../Http/Controllers/$name.php")) {
      $file = fopen(__DIR__. "/../../Http/Controllers/$name.php", "w");
      fwrite($file, $template);
      fclose($file);
      echo "\e[32mSuccessfully created a controller at \e[39m[".dirname(__DIR__, 2). "/Http/Controllers/$name.php]";
    } else echo "\e[31mFile already exists at \e39m[".dirname(__DIR__, 2). "/Http/Controllers/$name.php]";

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