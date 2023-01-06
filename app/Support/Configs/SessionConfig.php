<?php

namespace App\Support\Configs;

class SessionConfig
{
  public function __construct(
    public string $name,
    public bool $secure,
    public bool $httpOnly,
    public string $sameSite
  )
  {}
}