<?php

\App\Application::make(
  \App\Support\Configs\SessionConfig::class,
  fn() => new \App\Support\Configs\SessionConfig(
    "",
    true,
    true,
    "lax"
  ));