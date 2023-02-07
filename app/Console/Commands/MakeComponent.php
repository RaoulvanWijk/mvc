<?php

namespace App\Console\Commands;

class MakeComponent extends Command
{
   protected string $usage = "make:component [arguments] [options]";

  protected array $options = [
    //
  ];

  protected string $defaultName = "make:component";

  protected string $defaultDescription = "Create a new component";

  protected array $arguments = [
    "<name>" => "The name of the component"
  ];

  public function execute($name): void
  {
    $template =  $this->componentTemplate();
    $template = str_replace("{{ name }}", $name, $template);
    if(!file_exists(dirname(__DIR__, 2). "/Http/Components/$name.php")) {
      $file = fopen(dirname(__DIR__, 2). "/Http/Components/$name.php", "w");
      fwrite($file, $template);
      fclose($file);
      echo "\e[32mSuccessfully created a Component at \e[39m [".__DIR__. "/$name.php]";
    } else echo "\e[31mFile already exists at \e[39m[".__DIR__. "/$name.php]";
  }

  public function componentTemplate()
  {
    return '<?php

namespace App\Http\Components;

class {{ name }} extends Component
{
  public function render()
  {
    
  }
}';
  }

}