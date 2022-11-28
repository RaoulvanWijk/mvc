<?php
namespace App\Console;

class ArgvInput
{
  private array $argv;


  private string $template;
  private string $fileType;

  private array $options = [
    'help' => 'help',
    'h' => 'help',
    'serve' => 'serve',
    'make' => [
      "controller" => "controller",
      "model" => "model",
      "middleware" => "middleware",
      "request" => "request"
    ]
  ];

  public function __construct($argv)
  {
    $this->argv = $argv;
  }

  /**
   * This function will be used to handle the commands
   * @return void
   */
  public function handle(): void
  {
    if($this->argv[1] == $this->options['help'] || $this->argv[1] == $this->options['h']) {
      $this->help();
    } else if($this->argv[1] == $this->options['serve']) {
      $this->serve();
    } elseif(str_starts_with($this->argv[1], "make")) {
      $cmd = explode(":", $this->argv[1]);
      if(!isset($this->options["make"][$cmd[1]])) {
        echo "The command {$cmd[1]} does not exists";
        exit;
      }
      if(!isset($this->argv[2])) {
        echo "Please provide a name for the {$cmd[1]}";
        exit;
      }
      switch($cmd[1]) {
        case "controller":
          $this->fileType = "Http/Controllers";
          $this->template = self::controller();
          break;
        case "model":
          $this->fileType = "Http/Models";
          $this->template = self::model();
          break;
        case "middleware":
          $this->fileType = "Http/Middleware";
          $this->template = self::middleware();
          break;
        case "request":
          $this->fileType = "Http/Requests";
          $this->template = self::request();
          break;
        default:
          echo "The command {$cmd[1]} does not exists";
          exit;
      }
    }
    $this->template = str_replace("{{ name }}", $this->argv[2], $this->template);
    $this->createFile();
  }
  
  /**
   * Function used to start the php server of this application
   * @return void
   */
  public function serve(): void
  {
    echo "Starting server on port 8000";
    exec("php -S localhost:8000 -t public");
  }

  /**
   * Show the help message
   * @return void
   */
  public function help(): void
  {
    echo "Usage: php mvc [command] [options]". PHP_EOL
    . "Commands:". PHP_EOL
    . "  make:controller [name]". PHP_EOL
    . "  make:model [name]". PHP_EOL
    . "  make:middleware [name]". PHP_EOL
    . "  make:request [name]". PHP_EOL
    . "  serve  -runs this application from command line". PHP_EOL
    . "  help   -view all commands". PHP_EOL;
    exit;
  }

  /**
   * Set $this->template to the controller template
   * @return string
   */
  public function controller(): string
  {
    return file_get_contents(__DIR__ . "/Templates/Controller.txt");
  }

  /**
   * Set $this->template to the model template
   * @return string
   */
  public function model(): string
  {
    return file_get_contents(__DIR__ . "/Templates/Model.txt");
  }

  /**
   * Set $this->template to the middleware template
   * @return string
   */
  public function middleware(): string
  {
    return file_get_contents(__DIR__ . "/Templates/Middleware.txt");
  }

  /**
   * Set $this->template to the request template
   * @return string
   */
  public function request(): string
  {
    return file_get_contents(__DIR__ . "/Templates/Request.txt");
  }

  /**
   * Create the file based on the template
   * @return void
   */
  public function createFile(): void
  {
    $file = fopen(__DIR__ . "/../{$this->fileType}/{$this->argv[2]}.php", "w");
    fwrite($file, $this->template);
    fclose($file);
    echo "File created successfully at " . __DIR__ . "/../{$this->fileType}/{$this->argv[2]}.php";
    exit;
  }
}