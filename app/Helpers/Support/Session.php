<?php
namespace App\Helpers\Support;

class Session {
  // Constructor function
  public function __construct() {
    // Start the session if it has not already been started
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
      session_gc();
    }
  }

  // Set a session variable
  public function set($key, $value, $flash = false, $error = false) {
    if ($flash) {
      $_SESSION['_flash'][$key] = $value;
    } elseif($error) {
      $_SESSION['_error'][$key] = $value;
    } else {
      $_SESSION[$key] = $value;
    }
  }



  // Set a flash message
  public function flash($key, $value) {
    // Set the flash message as a session variable
    self::set($key, $value, flash: true);
  }

  // Check if a session variable exists
  public function has($key) {
    return isset($_SESSION[$key]);
  }

  public function hasFlash($key) {
    return isset($_SESSION['_flash'][$key]);
  }

  // Get a session variable
  public function get($key, $flash = false) {
    if ($flash) {
      if (isset($_SESSION['_flash'][$key])) {
        return $_SESSION['_flash'][$key];
      }
      return false;
    }
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    }
    return false;
  }

  /**
   * Get the errors from the session
   * @return array | string | false
   */
  public function errors()
  {
    return $this->getFlash('errors');
  }


  // Remove a session variable
  public function remove($key, $flash = false) {
    if ($flash) {
      unset($_SESSION['_flash'][$key]);
      return;
    } else {
      unset($_SESSION[$key]);
      return;
    }
  }

  // Destroy the entire session
  public function destroy() {
    session_destroy();
  }



  // Get a flash message and remove it from the session
  public function getFlash($key) {
    // Check if a flash message with this key exists
    if (self::hasFlash($key)) {
      // Get the flash message
      $value = self::get($key, flash: true);
      // Remove the flash message and the flag
      self::remove($key, flash: true);
      // Return the flash message
      return $value;
    }
    return false;
  }

  // Return an array of formatted session variables
  public function all() {
  return $_SESSION;
  }
}