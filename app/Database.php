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
            
            $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
        }

        private function ConnectionSqlServer()
        {
            // For SqlServer
            $conn = 'sqlsrv:Server=' . $this->dbHost . 'Database=' . $this->dbName;
            $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

            $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass);
            error_log("INFO : APP has been connected with SqlServer database!", 0);
        }

        //Allows us to write queries
        public function query($sql)
        {
            $this->statement = $this->dbHandler->prepare($sql); 
        }

        //Bind values
        public function bind($parameter, $value, $type = null)
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

        //Execute the prepared statement
        public function execute()
        {
            return $this->statement->execute();
        }

        //Return an array
        public function resultSet()
        {
            $this->execute();
            return $this->statement->fetchAll(PDO::FETCH_OBJ);
        }

        //Return a specific row as an object
        public function single()
        {
            $this->execute();
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }

        //Get's the row count
        public function rowCount()
        {
            return $this->statement->rowCount();
        }
    }
?>
