<?php
/**
 * This registers the autoloader for the application.
 */
spl_autoload_register(function($className) {
  $className = str_replace('\\', '/', $className);
  if(file_exists(dirname(__DIR__). '/' . $className . '.php')) {
      require_once dirname(__DIR__). '/' . $className . '.php';
  } elseif($className != 'App/Libraries/PDO' || $className != 'App/Libraries/PDOException')  {
      die('Class does not exists: '. $className);
  }
});