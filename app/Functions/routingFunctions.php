<?php

function route(string $name, array $params = [])
{
  \App\Http\Route::route($name, $params);
}

function back()
{
  
  return $_SERVER['HTTP_REFERER'];
}