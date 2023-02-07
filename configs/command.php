<?php

return [
  new \App\Console\Commands\Serve,

  "\e[33m make\e[39m\n",
  new \App\Console\Commands\MakeCommand(),
  new \App\Console\Commands\CreateController(),
  new \App\Console\Commands\MakeMiddleware(),
  new \App\Console\Commands\MakeModel(),
  new \App\Console\Commands\MakeComponent(),
];
