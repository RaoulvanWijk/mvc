<?php

namespace App\Support;

use App\Contracts\SessionInterface;
use App\Exceptions\Session\SessionException;
use App\Support\Configs\SessionConfig;

class Session implements SessionInterface
{
  public function __construct(private SessionConfig $config)
  {
  }

  /**
   * @throws SessionException
   */
  public function start(): void
  {
    if($this->isActive()) throw new SessionException("Session has already been started");

    if(headers_sent($file, $line)) throw new SessionException("Headers already sent by $file at line $line");

    session_set_cookie_params([
        "secure" => $this->config->secure,
        "httponly" => $this->config->httpOnly,
        "samesite" => $this->config->sameSite
      ]);

    if(!empty($this->config->name)) session_name($this->config->name);

    if(!session_start()) throw new SessionException("Unable to start session");

    if(!array_key_exists('_flash', $_SESSION) || !isset($_SESSION['_flash'])) {
    $_SESSION['_flash'] = [];
    }
  }


  public function save(): void
  {
    session_write_close();
  }

  public function isActive(): bool
  {
    return session_status() === PHP_SESSION_ACTIVE;
  }

  public function regenerate(): bool
  {
    return session_regenerate_id();
  }

  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function put($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function get($key, $default = '')
  {
    return $this->has($key) ? $_SESSION[$key] : $default;
  }

  public function flash($key, $value)
  {
    $_SESSION["_flash"][$key] = $value;
  }

  public function error($values)
  {
    $this->flash("errors", $values);
  }

  public function has($key): bool
  {
    return array_key_exists($key, $_SESSION);
  }

  public function hasFlash($key): bool
  {
    return array_key_exists($key, $_SESSION["_flash"]);
  }
  public function getFlash(string $key)
  {
    if($this->hasFlash($key)) {
      $value = $_SESSION["_flash"][$key];
      unset($_SESSION["_flash"][$key]);

      return $value;
    }
  }
  public function errors()
  {
    return $this->getFlash("errors");
  }

  public function forget($key)
  {
    unset($_SESSION[$key]);
  }
}