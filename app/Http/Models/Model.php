<?php
namespace App\Http\Models;

use App\Database;

class Model
{
  /**
   * Var used to store the database Table of the model
   */
  protected string $databaseTable;

  /**
   * Var used to keep track of tables primary key
   */
  protected string $primaryKey = 'id';

  /**
   * Var used to store all the columns that are allowed to be inserted
   */
  protected array $fillable = [];

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
  
  public function __construct(string $dbName = null)
  {
    $this->setTable();
    if(!is_null($dbName)) {
      $this->db = new Database($dbName);
    } else {
      $this->db = new Database;
    }
  }

  /**
   * Returns all records from the database table specified in the model
   * @return array
   */
  public function All(): array
  {
    $this->db->query('SELECT * FROM ' . $this->databaseTable);
    return $this->db->resultSet();
  }

  /**
   * Find a record by primary key
   * @param int $id
   * @return object
   */
  public function find(int $id): object
  {
    $this->db->query('SELECT * FROM ' . $this->databaseTable . ' WHERE '. $this->primaryKey .' = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  /**
   * Select the columns that you want to get from the database
   * @param array $columns
   * @return $this
   */
  public function select(array ...$columns):self
  {
    $this->query = 'SELECT ';
    if(count($columns) > 0) {
      foreach ($columns as $column) {
        $this->query .= $column . ', ';
      }
      $this->query = substr($this->query, 0, -2);
    } else {
      $this->query .= '*';
    }
    $this->query .= ' FROM ' . $this->databaseTable;
    return $this;
  }

  /**
   * This method is used for joins in the query builder
   * @param string $table
   * @param string $column1
   * @param string $column2
   * @param string $joinType
   * @return $this
   */
  public function join(string $table, string $column1, string $column2, string $joinType = 'INNER'): self
  {
    $this->query .= ' ' . $joinType . ' JOIN ' . $table . ' ON ' . $column1 . ' = ' . $column2;
    return $this;
  }

  /**
   * where
   * @param array $wheres
   * @return $this
   */
  public function where(array ...$wheres): self
  {
    $this->query .= ' WHERE ';
    foreach ($wheres as $where) {
      $this->query .= $where . ' AND ';
    }
    $this->query = substr($this->query, 0, -4);
    return $this;
  }

  /**
   * orWhere
   * @param array $wheres
   * @return $this
   */
  public function orWhere(...$wheres): self
  {
    $this->query .= ' OR ';
    foreach ($wheres as $where) {
      $this->query .= $where . ' OR ';
    }
    $this->query = substr($this->query, 0, -3);
    return $this;
  }

  /**
   * where with prepared statement
   * @param string $where
   * @param array $binds
   * @return $this
   */
  public function whereRaw(string $where, array $binds = []): self
  {
    $this->query .= ' WHERE ' . $where;
    $this->binds = $binds;
    return $this;
  }

  /**
   * orWhere with prepared statement
   * @param string $where
   * @param array $binds
   * @return $this
   */
  public function orWhereRaw(string $where, array $binds = []): self
  {
    $this->query .= ' OR ' . $where;
    $this->binds = $binds;
    return $this;
  }

  /**
   * having
   * @param array $havings
   * @return $this
   */
  public function having(array ...$havings): self
  {
    $this->query .= ' HAVING ';
    foreach ($havings as $having) {
      $this->query .= $having . ' AND ';
    }
    $this->query = substr($this->query, 0, -4);
    return $this;
  }

  /**
   * orHaving
   * @param array $havings
   * @return $this
   */
  public function orHaving(array ...$havings): self
  {
    $this->query .= ' OR ';
    foreach ($havings as $having) {
      $this->query .= $having . ' OR ';
    }
    $this->query = substr($this->query, 0, -3);
    return $this;
  }

  /**
   * having with prepared statement
   * @param string $having
   * @param array $binds
   * @return $this
   */
  public function havingRaw(string $having, array $binds = []): self
  {
    $this->query .= ' HAVING ' . $having;
    $this->binds = $binds;
    return $this;
  }

  /**
   * orHaving with prepared statement
   * @param string $having
   * @param array $binds
   * @return $this
   */
  public function orHavingRaw(string $having, array $binds = []): self
  {
    $this->query .= ' OR ' . $having;
    $this->binds = $binds;
    return $this;
  }

  /**
   * Order by column name
   * @param string $orderBy
   * @param string $order
   * @return $this
   */
  public function orderBy(string $orderBy, string $order): self
  {
    $this->query .= ' ORDER BY ' . $orderBy . ' ' . $order;
    return $this;
  }

  /**
   * Set the group by clause for the query.
   * @param array $columns
   * @return $this
   */
  public function groupBy(array ...$columns): self
  {
    $this->query .= ' GROUP BY ';
    foreach ($columns as $column) {
      $this->query .= $column . ', ';
    }
    $this->query = substr($this->query, 0, -2);
    return $this;
  }

  /**
   * Set the limit of the query
   * @param int $limit
   * @return $this
   */
  public function limit(int $limit): self
  {
    $this->query .= ' LIMIT ' . $limit;
    return $this;
  }


  /**
   * This method is used to execute the query built by the methods above
   * @return array
   */
  public function get(): array
  {
    $this->db->query($this->query);
    if(!empty($this->binds)) {
      foreach ($this->binds as $key => $value) {
        $this->db->bind($key, $value);
      }
    }
    return $this->db->resultSet();
  }

  /**
   * This method is used to insert data into the database
   * @param array $data
   * @return bool
   */
  public function create(array $data): bool
  {
    foreach ($data as $key => $value) {
      if(!in_array($key, $this->fillable)) {
        die("This column name is not mass assignable or does not exist");
      }
    }
    $this->db->query('INSERT INTO ' . $this->databaseTable . ' (' . implode(', ', array_keys($data)) . ') VALUES (:' . implode(', :', array_keys($data)) . ')');
    foreach ($data as $key => $value) {
      $this->db->bind(':' . $key, $value);
    }
    return $this->db->execute();
  }

  /**
   * Update a record in the database
   * @param array $data
   * @param int $id
   * @return bool
   */
  public function update(array $data, int $id): bool
  {
    $placeholders = '';
    foreach ($data as $key => $value) {
      if(!in_array($key, $this->fillable)) {
        die("This column name is not mass assignable or does not exist");
      }
      $placeholders .= $key . ' = :' . $key . ', ';
    }
    $this->db->query('UPDATE ' . $this->databaseTable . ' SET '. $placeholders .'  WHERE ' . $this->primaryKey . ' = ' . $id);
    foreach ($data as $key => $value) {
      $this->db->bind(':' . $key, $value);
    }
    return $this->db->execute();
  }
  
  /**
   * Delete a record from the database based on the primary key
   * @param  int $id
   * @return bool
   */
  public function delete(int $id): bool
  {
    $this->db->query('DELETE FROM ' . $this->databaseTable . ' WHERE ' . $this->primaryKey . ' = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  /**
   * This method is used to execute a query without the models query builder
   * @param string $query
   * @param array $binds
   * @return array
   */
  public function rawQuery(string $query, array $binds): array
  {
    $this->db->query($query);
    foreach ($binds as $key => $value) {
      $this->db->bind($key, $value);
    }
    return $this->db->resultSet();
  }

  // This function sets the database table name. The name is determined by the getTableName() function.
  protected function setTable()
  {
    $this->databaseTable = $this->getTableName();
  }

  // Get table name by using the class name of the object
  // and converting it to a pluralized, lower case string
  // and appending an 's' to the end
  protected function getTableName()
  {
    $className = explode('\\', get_class($this));
    $className = end($className);
    $className = preg_replace('/(?<!^)[A-Z]/', '_$0', $className);
    return strtolower($className) . 's';
  }

  /**
   * This method is used to execute a query without the models query builder
   * @param string $query
   * @param array $binds
   */
  protected function exec($query, $binds = [])
  {
    $this->db->query($query);
    if(!empty($binds)) {
      foreach ($binds as $key => $value) {
        $this->db->bind($key, $value);
      }
    }
    $this->db->execute();
  }

  /**
   * This method is used to execute a query and return the data without the models query builder
   * @param string $query
   * @param array $binds
   * @return array
   */
  protected function query($query, $binds = []) : mixed 
  {
    $this->db->query($query);
    if(!empty($binds)) {
      foreach ($binds as $key => $value) {
        $this->db->bind($key, $value);
      }
    }
    return $this->db->execute();
  }
}
