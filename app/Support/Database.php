<?php
namespace App\Support;

use \PDO;
use \PDOException;
class Database
{
  private string $dbHost;
  private string $dbName;
  private string $dbUser;
  private ?string $dbPass;

  private mixed $statement;
  private PDO $dbHandler;
  private mixed $error;

  public function __construct(string $dbName = null)
  {
    $this->dbHost = $_ENV["DB_HOST"] ?? 'localhost';
    $this->dbName = $_ENV["DB_NAME"] ?? 'mvcframework';
    $this->dbUser = $_ENV["DB_USER"] ?? 'root';
    $this->dbPass = $_ENV["DB_PASS"] ?? '';
    if(!is_null($dbName)) {
      $this->dbName = $dbName;
    }
    switch(strtolower($_ENV["DB_CONNECTION"] ?? 'mysql')) {
      case "sqlserver":
        $this->ConnectionSqlServer();
        break;
      case "mysql":
        $this->ConnectionMySql();
        break;
      default:
        dd("no sql engine specified");
        break;
    }
  }

  private function ConnectionMySql(): void
  {
    // For mysql
    $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
    $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

    $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);

    message_log("INFO : APP has been connected with mysqli database!");
  }

  private function ConnectionSqlServer(): void
  {
    // For SqlServer
    $conn = 'sqlsrv:Server=' . $this->dbHost . ';Database=' . $this->dbName;
    $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

    $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass);
    message_log("INFO : APP has been connected with SqlServer database!");
  }

  //Allows us to write queries
  public function query($sql): void
  {
    $this->statement = $this->dbHandler->prepare($sql);
  }

  //Bind values
  public function bind($parameter, $value, $type = null): void
  {
    $type = match (is_null($type)) {
      is_int($value) => PDO::PARAM_INT,
      is_bool($value) => PDO::PARAM_BOOL,
      is_null($value) => PDO::PARAM_NULL,
      default => PDO::PARAM_STR,
    };
    $this->statement->bindValue($parameter, $value, $type);
  }

  //Execute the prepared statement
  public function execute($values = null)
  {
    try {
      return $this->statement->execute($values);
    } catch(PDOException $e) {
      error($e, 'ERROR: Something went wrong when executing the sql query');
    }
  }

  //Return an array
  public function resultSet()
  {
    try {
      $this->execute();
      return $this->statement->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      error($e, 'ERROR: Something went wrong when executing the sql query');
    }
  }

  //Return a specific row as an object
  public function single()
  {
    try {
      $this->execute();
      return $this->statement->fetch(PDO::FETCH_OBJ);
    } catch(PDOException $e) {
      error($e, 'ERROR: Something went wrong when executing the sql query');
    }
  }

  //Gets the row count
  public function rowCount()
  {
    return $this->statement->rowCount();
  }
}

