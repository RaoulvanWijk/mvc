<?php

namespace App\Http\Requests;

class Request 
{
  public array $parameters = [];

  public array $request_errors;

  public string $redirect_if_failed;

  public function __set($key, $val)
  {
    $this->parameters[$key] = $val;
  }

  public function __get($key)
  {
    return $this->parameters[$key];
  }

  public function __construct(array $data = null, $useCSRF = true)
  {
    if(is_null($data)) {
      $data = array_merge($_POST, $_GET);
    }
    if($_SERVER["REQUEST_METHOD"] == "GET") {
      $this->validate($data, useCSRF: false);
    } else {
      $this->validate($data, useCSRF: $useCSRF);
    }
  }

  /**
   * Validate the request
   * @param array $data
   * @param array $rules
   * @param bool $useCSRF
   */
  public function validate(array $data, array $rules = [], bool $useCSRF = true)
  {
    if(count($rules) == 0) return;
    if(!$this->authorize()) {
      throw new \Exception('Not authorized');
    }
    if($useCSRF) {
      if(!isset($data['_token']) || !isset($_SESSION['_token'])) {
        throw new \Exception('CSRF token mismatch');
      }
      if(!hash_equals($_SESSION['_token'], $data['_token'])) {
        throw new \Exception('CSRF token mismatch');
      }
    }

    // Validate the data
    // Specify by the rules
    // If the data is not valid, throw an exception

    $rules = $rules ? $rules : $this->rules();
    foreach($rules as $key => $rule)
    {
      $rule = explode('|', $rule);
      foreach($rule as $r)
      {
        if($r === 'required')
        {
          if(!isset($data[$key]))
          {
            self::$request_errors[] = "The $key field is required";
          }
        } elseif($r === "int") {
          if(!is_int($data[$key]))
          {
            self::$request_errors[] = "The $key field must be an integer";
          }
        } elseif($r === "string") {
          if(!is_string($data[$key]))
          {
            self::$request_errors[] = "The $key field must be a string";
          }
        } elseif($r === "array") {
          if(!is_array($data[$key]))
          {
            self::$request_errors[] = "The $key field must be an array";
          }
        } elseif($r === "bool") {
          if(!is_bool($data[$key]))
          {
            self::$request_errors[] = "The $key field must be a boolean";
          }
        } elseif($r === "float") {
          if(!is_float($data[$key]))
          {
            self::$request_errors[] = "The $key field must be a float";
          }
        } elseif($r === "numeric") {
          if(!is_numeric($data[$key]))
          {
            self::$request_errors[] = "The $key field must be a numeric";
          }
        } elseif($r === "email") {
          if(!filter_var($data[$key], FILTER_VALIDATE_EMAIL))
          {
            self::$request_errors[] = "The $key field must be a valid email";
          }
        } elseif($r === "url") {
          if(!filter_var($data[$key], FILTER_VALIDATE_URL))
          {
            self::$request_errors[] = "The $key field must be a valid url";
          }
        } elseif($r === "ip") {
          if(!filter_var($data[$key], FILTER_VALIDATE_IP))
          {
            self::$request_errors[] = "The $key field must be a valid ip address";
          }
        } elseif($r === "mac") {
          if(!filter_var($data[$key], FILTER_VALIDATE_MAC))
          {
            self::$request_errors[] = "The $key field must be a valid mac address";
          }
        } elseif(str_contains($r, ':')) {
          $r = explode(':', $r);
          if($r[0] === "min")
          {
            if(strlen($data[$key]) < $r[1])
            {
              self::$request_errors[] = "The $key field must be at least $r[1] characters";
            }
          } elseif($r[0] === "max") {
            if(strlen($data[$key]) > $r[1])
            {
              self::$request_errors[] = "The $key field must be at most $r[1] characters";
            }
          } elseif($r[0] === "between") {
            if(strlen($data[$key]) < $r[1] || strlen($data[$key]) > $r[2])
            {
              self::$request_errors[] = "The $key field must be between $r[1] and $r[2] characters";
            }
          } elseif($r[0] === "min_num") {
            if($data[$key] < $r[1])
            {
              self::$request_errors[] = "The $key field must be at least $r[1]";
            }
          } elseif($r[0] === "max_num") {
            if($data[$key] > $r[1])
            {
              self::$request_errors[] = "The $key field must be at most $r[1]";
            }
          } elseif($r[0] === "between_num") {
            if($data[$key] < $r[1] || $data[$key] > $r[2])
            {
              self::$request_errors[] = "The $key field must be between $r[1] and $r[2]";
            }
          } elseif($r[0] === "min_float") {
            if($data[$key] < $r[1])
            {
              self::$request_errors[] = "The $key field must be at least $r[1]";
            }
          } elseif($r[0] === "max_float") {
            if($data[$key] > $r[1])
            {
              self::$request_errors[] = "The $key field must be at most $r[1]";
            }
          } elseif($r[0] === "between_float") {
            if($data[$key] < $r[1] || $data[$key] > $r[2])
            {
              self::$request_errors[] = "The $key field must be between $r[1] and $r[2]";
            }
          }
        }
      }
      if(count(self::$request_errors) > 0) {
        $_SESSION[$_SERVER["REQUEST_URI"]]["request_errors"] = self::$request_errors;
        $redirect = $this->redirect_if_failed ? : $_SERVER["HTTP_REFERER"];
        header("Location: $redirect");
      }
    }
    $this->setProperties($data);
  }

  /**
   * Set the properties of the class so that they can be accessed by the controller
   * @param array $data
   */
  public function setProperties(array $data)
  {
    foreach($data as $key => $value) {
      $this->__set($key, $value);
    }
  }

  /**
   * In this method you can set the rules for the validation
   * @return array
   */
  public function rules()
  {
    return [];
  }

  /**
   * In this method you can check if a user is authorized to access the request
   */
  public function authorize()
  {
    return true;
  }
}
