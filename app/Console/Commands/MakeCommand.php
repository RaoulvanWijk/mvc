<?php

namespace App\Console\Commands;

class MakeCommand
{
  public string $defaultName = "make:command";
  public string $defaultDescription = "Create a new command";

  public function execute($name): void
  {
    $template =  $this->commandTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    $file = fopen(__DIR__. "/$name.php", "w");
    fwrite($file, $template);
    fclose($file);
  }

  public function getUsage(): string
  {
    return "make:command <name>";
  }

  private function commandTemplate(): string
  {
    return '<?php

namespace App\Console\Commands;

class {{ name }}
{
  public string $defaultName = "";
  public string $defaultDescription = "";

  public function execute(): void
  {
    
  }

  public function getUsage()
  {
    return "";
  }

  public function getArgs()
  {
    return [

    ];
  }
}';
  }

  public function getArgs(): array
  {
    return [
      "name" => "The name of the command",
    ];
  }
}