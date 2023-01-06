<?php

namespace App\Contracts;

interface SessionInterface
{
  public function start(): void;

  public function save(): void;

  public function isActive();

  public function regenerate(): bool;

  public function set($key, $value);

  public function put($key, $value);

  public function get($key, $default = '');

  public function flash($key, $value);

  public function error($values);

  public function has($key): bool;

  public function hasFlash($key): bool;

  public function getFlash(string $key);

  public function errors();

  public function forget($key);
}