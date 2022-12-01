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
  return \App\Http\Route::route($name, $params);
}

function back()
{
  return $_SERVER['HTTP_REFERER'];
}


function session($key)
{
  return $_SESSION[$key];
}

function view(string $view, array $data = [])
{
  foreach($data as $key => $val) {
  ${$key} = $val;
  }
  require_once dirname(dirname(dirname(__DIR__))). "/resources/views/". $view .".php"; 
}