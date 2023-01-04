<?php

namespace App\Support;

class Arr
{
  /**
   * This function can be used to flatten an array
   * with recursion
   * @param $array
   * @return false|array
   */
  public static function array_flatten($array): false|array
  {
    if (!is_array($array)) {
      return FALSE;
    }
    $result = array();
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $result = array_merge($result, self::array_flatten($value));
      }
      else {
        $result[$key] = $value;
      }
    }
    return $result;
  }
}