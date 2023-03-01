<?php

namespace App\Models;

use App\Support\Database;
use App\Support\QueryBuilder;

class Model
{
  /**
   * Var used to store the database Table of the model
   */
  protected static ?string $databaseTable = null;

  /**
   * Var used to keep track of tables primary key
   */
  protected static string $primaryKey = 'id';

  /**
   * Var used to store all the columns that are allowed to be inserted
   */
  protected static array $fillable = [];

  /**
   * Var used to store the query
   */
  protected string $query;

  /**
   * Var used to store all the binds for the query
   */
  protected array $binds = [];

  /**
   * Var used to store Database instance
   */
  protected Database $db;

  private $attributes = [];

  private $hidden = [];

  protected static ?string $databaseName = null;

  public function __construct(string $dbName = null)
  {
    $this->setTable();
    self::$databaseName = $dbName;
  }

  public function __get(string $name)
  {
    return $this->attributes[$name];
  }

  public static function select($columns = ['*']): QueryBuilder
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))->select($columns);
  }

  /**
   * Add a part of a query to the query without query-builder
   * @param $query
   * @param $binds
   * @return QueryBuilder
   */
  public function query($query, $binds): QueryBuilder
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))->query($query, $binds);
  }

  /**
   * Execute a query without query-builder
   * @param $query
   * @param $binds
   * @return mixed
   */
  public function exec($query, $binds): mixed
  {
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))->exec($query, $binds);
  }

  public static function find($id): static
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    $res = (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))->find($id);
    return static::toModel($res);
  }

  public static function all(): array | bool
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))
              ->select()
              ->get();
  }

  public static function delete($id, $column = null): bool
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))
              ->delete($id, $column);
  }

  public static function insert(array $data)
  {
    if(!self::$databaseTable) {
      self::setTable();
    }
    foreach ($data as $key => $value) {
      if (!in_array($key, static::$fillable)) {
        die("The column {$key} is not mass assignable or does not exist");
      }
    }
    return (new QueryBuilder(static::$databaseTable, static::$primaryKey, static::$databaseName))
              ->insert($data);
  }

  // This function sets the database table name. The name is determined by the getTableName() function.
  protected static function setTable(): void
  {
    static::$databaseTable = static::getTableName();
  }

  // Get table name by using the class name of the object
  // and converting it to a pluralized, lower case string
  // and appending an 's' to the end
  protected static function getTableName(): string
  {
    $className = explode('\\', static::class);
    $className = end($className);
    $className = preg_replace('/(?<!^)[A-Z]/', '_$0', $className);
    return strtolower($className) . 's';
  }

  public static function toModel($res)
  {
    $model = new Static();
    $model->setAttributes($res);
    return $model;
  }

  public function setAttributes($attributes)
  {
    foreach ($attributes as $key => $attribute)
    {
      if(in_array($key, static::$fillable)) {
        $this->attributes[$key] = $attribute;
      } else {
        $this->hidden[$key] = $attribute;
      }
    }
  }
}