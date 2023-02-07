<?php

namespace App\Console\Commands;

use App\Console\Commands\Command;

class MakeCommand extends Command
{
    protected string $defaultName = "make:command";
    protected string $usage = "make:command [arguments] [options]";
    protected array $arguments = [
        "<name>" => "The name of the command"
    ];
    protected array $options = [
        //
    ];
    protected string $defaultDescription = "Create a new command class";

  public function execute($name)
  {
    $template =  $this->commandTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    if(!file_exists(__DIR__. "/$name.php")) {
    $file = fopen(__DIR__. "/$name.php", "w");
    fwrite($file, $template);
    fclose($file);
    echo "\e[32mSuccessfully created a command at \e[39m".realpath(__DIR__. "/$name.php");
    } else echo "\e[31mFile already exists at \e[39m".realpath(__DIR__. "/$name.php");
  }

  private function commandTemplate(): string
  {
    return '<?php

namespace App\Console\Commands;

class {{ name }} extends Command
{
   protected string $usage = "";

  protected array $options = [
    //
  ];

  protected string $defaultName = "";

  protected string $defaultDescription = "";

  protected array $arguments = [
    //
  ];

  public function execute(): void
  {
    
  }

}';
  }
}