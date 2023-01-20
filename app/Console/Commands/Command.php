<?php

namespace App\Console\Commands;

use App\Contracts\Commands\CommandInterface;


class Command implements CommandInterface
{
  protected string $usage;

  protected array $options;

  protected string $defaultName;

  protected string $defaultDescription;

  protected array $arguments;

  protected array $inputOptions;

  public function getUsage(): string
  {
    return $this->usage;
  }

  public function getOptions(): array
  {
    return $this->options;
  }

  public function getName(): string
  {
    return $this->defaultName;
  }

  public function getDescription(): string
  {
    return $this->defaultDescription;
  }

  public function getArgs(): array
  {
    return $this->arguments;
  }

  public function setOptions($options)
  {
    $this->inputOptions = $options;
  }
}