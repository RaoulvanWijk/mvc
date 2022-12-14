<?php

namespace App\Http\Controllers;

use App\Http\Route;
abstract class Controller 
{
  /**
   * Var used to store the next route
   */
  protected string $nextUrl;

  /**
   * This method will be used to load a view
   */
  protected function view($view, $data = []): void
  {
    if (file_exists('../resources/views/' . $view . '.php')) 
    {
      foreach ($data as $key => $value) 
      {
        $$key = $value;
      }
      require_once '../resources/views/' . $view . '.php';
    } else {
      die("View does not exists.");
    }
  }

  /**
   * This method will be used to redirect the user to another route
   * @param string $route
   * @return $this
   */
  protected function redirect(string $route): self
  {
    $this->nextUrl = Route::route($route);
    header('Location: ' . $this->nextUrl);
    return $this;
  }

  /**
   * This method will be used to redirect the user to the previous route
   * @return $this
   */
  protected function back(): self
  {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    return $this;
  }

  /**
   * This method will be used to redirect the user with a message stored in the session
   * @param string $key
   * @param string $value
   * @return $this
   */
  protected function with($key, $value = null): self
  {
    session()->flash($key, $value);
    return $this;
  }

  /**
   * This method will be used to redirect the user with errors stored in the session
   * @param array $errors
   * @return $this
   */
  protected function withErrors($errors): self
  {
    session()->flash('errors', $errors);
    return $this;
  } 



}
