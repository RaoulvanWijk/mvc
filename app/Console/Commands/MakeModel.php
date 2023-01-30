<?php

namespace App\Console\Commands;

class MakeModel extends Command
{
   protected string $usage = "make:model [arguments] [options]";

  protected array $options = [
    //
  ];

  protected string $defaultName = "make:model";

  protected string $defaultDescription = "Create a new model class";

  protected array $arguments = [
    "<name>" => "The name of the model"
  ];

  public function execute($name): void
  {
    $template = $this->modelTemplate();
    $template = str_replace("{{ name }}", $name, $template);

    if(!file_exists(dirname(__DIR__, 2). "/Models/$name.php")) {
      $file = fopen(dirname(__DIR__, 2). "/Models/$name.php", "w");
      fwrite($file, $template);
      fclose($file);
      echo "\e[32mSuccessfully created a middleware at \e[39m".realpath(dirname(__DIR__, 2). "/Models/$name.php");
    } else echo "\e[31mFile already exists at \e[39m".realpath(dirname(__DIR__, 2). "/Models/$name.php");
  }

  private function modelTemplate()
  {
    return '<?php

namespace App\Models;

class {{ name }} extends Model
{
   protected static array $fillable = [
      //
   ];
}';
  }
}