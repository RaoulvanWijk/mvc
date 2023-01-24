<?php

namespace App\Console\Commands;

use App\Console\Commands\Command;

class Serve extends Command
{
  protected string $defaultName = "serve";
  protected string $usage = "serve [options]";
  protected array $arguments = [];
  protected array $options = [
    "--port" => "The port the application will serve on",
    "--host" => "The host the application will serve on",
    "--clear" => "This will clear the webserver log"
  ];
  protected string $defaultDescription = "Serve the application on the PHP web server";

  public function execute(): void
  {
    $descriptorspec = array(
          0 => array("pipe", "r"),
          1 => array("pipe", "w"),
          2 => array("pipe", "w"),
      );
    echo "\e[32mStarting the webserver on:\e[39m http://" .($inputOptions["--host"] ?? "localhost").":". ($inputOptions["--port"] ?? "8080")."\n";
    echo "\e[33mPress Ctrl+C to stop the webserver.\e[39m\n";

    $proc = proc_open("php -S ". ($args["options"]["--host"] ?? "localhost"). ":".($args["options"]["--port"] ?? "8080"). " -t public 2>&1 &", $descriptorspec, $pipes);
    if(array_key_exists("--clear", $this->inputOptions)) {
      $this->clear();
    }
    fgets($pipes[1]);
    while ($s = fgets($pipes[1])) {
          echo $s;
          file_put_contents("./storage/logs/webserver.log", $s, FILE_APPEND);
      }
      proc_close($proc);
  }

    private function clear()
    {
        if(file_exists("./storage/logs/webserver.log")) {
            file_put_contents("./storage/logs/webserver.log", "");
        }
    }
}