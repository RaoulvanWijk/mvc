<?php

use App\Application;
use App\Helpers\Support\Session;

/**
 * This function will be used to return a html input with the csrf token
 */
function csrf()
{
  $token = bin2hex(random_bytes(32));
  session()->set('_token', $token);
  return '<input type="hidden" name="_token" value="' . $token . '">';
}

/**
 * This function will be used to return a html input with the method
 */
function method($method)
{
  return '<input type="hidden" name="_method" value="' . $method . '">';
}

/**
 * This function will be used to get the route by name
 */
function route(string $name, array $params = [])
{
  return \App\Http\Route::route($name, $params);
}

/**
 * This function will return the previous url
 * @return string
 */
function back()
{
  return $_SERVER['HTTP_REFERER'];
}

/**
 * This function will be used to get the session
 * @param string $key
 * @return Session | string | null
 */
function session($key = null) : Session | string | null
{
  if (is_null($key)) {
    return Application::$session;
  }
  return Application::$session->get($key);
}

/**
 * This function will be used to load a view
 * @param string $view
 * @param array $data
 * @return void
 */
function view(string $view, array $data = [])
{
  foreach($data as $key => $val) {
  ${$key} = $val;
  }
  if(!file_exists(dirname(dirname(dirname(__DIR__))). "/resources/views/". $view .".php")) {
    throw new Exception("View not found at ". dirname(dirname(dirname(__DIR__))). "/resources/views/". $view .".php");
  }
  require_once dirname(dirname(dirname(__DIR__))). "/resources/views/". $view .".php"; 
}