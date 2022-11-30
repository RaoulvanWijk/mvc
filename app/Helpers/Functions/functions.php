<?php

function csrf()
{
  $_SESSION['_token'] = bin2hex(random_bytes(32));
  return '<input type="hidden" name="_token" value="' . $_SESSION['_token'] . '">';
}

function method($method)
{
  return '<input type="hidden" name="_method" value="' . $method . '">';
}

function route(string $name, array $params = [])
{
  \App\Http\Route::route($name, $params);
}

function back()
{
  return $_SERVER['HTTP_REFERER'];
}