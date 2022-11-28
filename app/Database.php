<?php
namespace App;

use \PDO;
use \PDOException;
    class Database
    {
        private $dbHost = DB_HOST;
        private $dbName = DB_NAME;
        private $dbUser = DB_USER;
        private $dbPass = DB_PASS;

        private $statement;
        private $dbHandler;
        private $error;

        public function __construct(string $dbName = null)
        {
            if(!is_null($dbName)) {
                $this->dbName = $dbName;
            }
            $this->ConnectionMySql();
        }

        private function ConnectionMySql()
        {
            // For mysql
            $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
            $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
            try 
            {
                $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
            } 
            catch(PDOException $ex) 
            {
                error_log("ERROR : Failed to connect mySql database!", 0);
                die('ERROR : Failed to connect mySql database '. $ex->getMessage());
            }
        }

        private function ConnectionSqlServer()
        {
            // For SqlServer
            $conn = 'sqlsrv:Server=' . $this->dbHost . 'Database=' . $this->dbName;
            $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

            try 
            {
                $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass);
                error_log("INFO : APP has been connected with SqlServer database!", 0);
            } 
            catch(PDOException $ex) 
            {
                error_log("ERROR : Failed to connect SqlServer database!", 0);
                die('ERROR : Failed to connect SqlServer database! '. $ex->getMessage());
            }
        }

        //Allows us to write queries
        public function query($sql)
        {
            try
            {
                $this->statement = $this->dbHandler->prepare($sql);
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to create sql query in Database class method query!", 0);
                die('ERROR : Failed to create sql query in Database class method query! '. $ex->getMessage());
            }   
        }

        //Bind values
        public function bind($parameter, $value, $type = null)
        {
            try
            {
                switch (is_null($type)) 
                {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
                $this->statement->bindValue($parameter, $value, $type);
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to bind sql data type in Database class method bind!", 0);
                die('ERROR : Failed to bind sql data type in Database class method bind! '. $ex->getMessage());
            }   
        }

        //Execute the prepared statement
        public function execute()
        {
            try
            {
                return $this->statement->execute();
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to execute sql query in Database class method execute!", 0);
                die('ERROR : Failed to execute sql query in Database class method execute! '. $ex->getMessage());
            }
        }

        //Return an array
        public function resultSet()
        {
            try
            {
                $this->execute();
                return $this->statement->fetchAll(PDO::FETCH_OBJ);
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to get result set in Database class method resultSet!", 0);
                die('ERROR : Failed to get result set in Database class method resultSet! '. $ex->getMessage());
            }
        }

        //Return a specific row as an object
        public function single()
        {
            try
            {
                $this->execute();
                return $this->statement->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to get result row in Database class method single!", 0);
                die('ERROR : Failed to get result row in Database class method single! '. $ex->getMessage());
            }
        }

        //Get's the row count
        public function rowCount()
        {
            try
            {
                return $this->statement->rowCount();
            }
            catch (PDOException $ex) 
            {
                error_log("ERROR : Failed to get a count of rows in Database class method rowCount!", 0);
                die('ERROR : Failed to get a count of rows in Database class method rowCount! '. $ex->getMessage());
            }
        }
    }
?>
