<?php

namespace App\Console;

class CliRunner
{
  private array $commands;
  private array $options = [
    "-h" => "help",
    "-help" => "help",
  ];

  public function __construct()
  {
    $this->commands = require_once __DIR__ . '/../../configs/command.php';
  }

  public function handle($argv): void
  {
    if(in_array($argv[1], ["list", "-h", "-help"])) {
      $this->showCommands();
    } else {
      $command = $this->getCommand($argv);
      if(!$command) echo "\e[31mThe command $argv[1] does not exist\e[39m\n";
      else $this->executeCommand($command, $argv);
    }
  }

  private function showCommands(): void
  {
    echo "\e[33mUsage:\e[39m\n";
    echo "  command [options] [arguments]\n";
    echo "\e[33mAvailable commands:\e[39m\n";
    echo "  \e[32m". sprintf("%-22s %03s", "list", "List commands"). "\e[39m\n";
    foreach ($this->commands as $command) {
      if(is_string($command)) echo $command;
      else echo "  \e[32m". sprintf("%-22s %03s", $command->getName(), $command->getDescription()). "\e[39m\n";
    }
  }

  private function getCommand($argv)
  {
    foreach ($this->commands as $command) {
      if(is_string($command)) continue;

      if($argv[1] === $command->getName()) {
        return $command;
      }
    }
    return false;
  }

  private function executeCommand(mixed $command, $argv): void
  {
    // remove "mvc" from array
    array_shift($argv);
    // remove command identifier from array
    array_shift($argv);

      if(isset($argv[0]) && array_key_exists($argv[0], $this->options)) {
        $this->showHelpOfCommand($command);
      } else {
        $args = $command->getArgs();
        $options = $command->getOptions();
        $argv = $this->formatArgs($argv, $options);
        $command->setOptions($argv['options']);
//        dd($command);
        if(count($args) != count($argv['params'])) $this->wrongAmountParams($command, count($args), $argv['params']);
        else $command->execute(...$argv['params']);
      }
  }

  private function wrongAmountParams($command, $commandCount, $args): void
  {
    echo "    Command \e[32m\"".$command->getName() . "\"\e[39m expects exactly \e[31m$commandCount ". ($commandCount == 1 ? "parameter " : "parameters "). "\e[39m" . count($args) ." given: \n";
    foreach ($args as $arg) {
      echo "\t\e[33m".$arg. "\e[39m\n";
    }
  }

    private function formatArgs($argv): array
    {
        // split the arguments into parameters and options
        $args = [
            "params" => [],
            "options" => []
        ];
        foreach ($argv as $arg) {
            if(str_starts_with($arg, "--")) {
                // check if the option has a value
                if(str_contains($arg, "=")) {
                    $option = explode("=", $arg);
                    $args["options"][$option[0]] = $option[1];
                } else {
                    $args["options"][$arg] = null;
                }
            } else {
                $args["params"][] = $arg;
            }
        }
        return $args;
    }


    public function showHelpOfCommand($command): void
    {
      echo "\e[33mDescription:\e[39m\n";
      echo "{$command->getDescription()}\n\n";
      echo "\e[33mUsage:\e[39m\n";
      echo $command->getUsage() . "\n\n";
      $args = $command->getArgs();
        if(!empty($args)) {
            echo "\e[33mArguments:\e[39m\n";
        foreach ($args as $name => $desc) {
          echo "  \e[32m". sprintf("%-22s \e[39m%03s", $name, $desc). "\n\n";
        }
      }
      $options = $command->getOptions();
        if(!empty($options)) {
            echo "\e[33mOptions:\e[39m\n";
            foreach ($this->options as $name => $desc) {
                echo "  \e[32m". sprintf("%-22s \e[39m%03s", $name, $desc). "\n";
            }
            foreach ($options as $name => $desc) {
                echo "  \e[32m". sprintf("%-22s \e[39m%03s", $name, $desc). "\n";
            }
        }
    }
}