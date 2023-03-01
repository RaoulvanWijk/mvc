<?php

namespace App\Contracts\Commands;
interface CommandInterface
{
  public function getUsage(): string;


  public function getOptions(): array;


  public function getName(): string;


  public function getDescription(): string;


  public function getArgs(): array;

}