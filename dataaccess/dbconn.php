<?php
namespace fwk\dataaccess;

/**
* This file includes all the classes required to manage connections and queries against the DB
* @author Pablo Bossi
*/
include_once($_SERVER["DOCUMENT_ROOT"]."/config/dbconf.php");

/**
* This class is a DB connection manager, every query to the DB will go through this class.
* Suggested way to use it is through classes on the dataaccess folder were you can capsulate caching and DB behaviour
* @author Pablo Bossi
*/
class dbConnProvider
{
  private static $connections = array();

  /**
  * Get a DB conexion, if there isn't an instance try to connect, if there is just return the active one
  * @param String connType connection name as defined in dbconf
  * @returns dbConn object
  */
  public static function getConnection($connType)
  {
    if (! isset(self::$connections[$connType]))
    {
      eval ("\$host   = ".$connType."_HOST;");
      eval ("\$user   = ".$connType."_USER;");
      eval ("\$pass   = ".$connType."_PASS;");
      eval ("\$dbName = ".$connType."_DB;");
      $mysqlConn = new \mysqli($host, $user, $pass, $dbName);
      if ($mysqlConn->connect_error)
      {
        error_log('Connect Error (' . $mysqlConn->connect_errno . ') '. $mysqlConn->connect_error);
        return false;
      }
      $dbConnection = new dbConn($mysqlConn);
      self::$connections[$connType] = $dbConnection;
    }
    return self::$connections[$connType];
  }
}

/**
* This class represents a DB connection, connections should not created straigh but the dbConnProvider should be used to get a connection instead.
* The class works as is only for Mysql servers, for other DBs this class should be replaced
* @author Pablo Bossi
*/
class dbConn
{
  private $dbConnection;

  /**
  * Constructor for the class
  * @param mysqliConnection mysqlConnection is a mysqli connection established, this class will capsulate the usage of this connection
  * @return dbConn object instance
  */
  public function __construct($mysqlConnection)
  {
    $this->dbConnection = $mysqlConnection;
    $this->dbConnection->query("SET NAMES 'utf8'");
    $this->dbConnection->query("SET CHARSET 'utf8'");
  }

  /**
  * The queries to the DB works through connection statements this method is used to "prepare" the requested query
  * @param string sql is the query to be prepared where the parameters for the query holds ? instead of the values (Read PHP prepared statements for further information)
  * @link http://php.net/manual/en/mysqli.prepare.php Mysqli prepared statements documentation
  * @return dbConn object instance
  */
  private function prepare($sql)
  {
    $preparedStatement = $this->dbConnection->prepare($sql);
    if (! $preparedStatement)
      error_log('Connect Error (' . $this->dbConnection->errno . ') '. $this->dbConnection->error. PHP_EOL. $sql);

    return $preparedStatement;
  }

  /**
  * The queries to the DB works through connection statements this method is used to "prepare" the requested query and bind the parameters to it
  * @param string sqlStatement sql to be queried
  * @param dbParameters $parameters instance of dbParameters holding the types and values to replace in the prepared statement
  * @link http://php.net/manual/en/mysqli.prepare.php Mysqli prepared statements documentation
  * @return mysqli_stmt mysqli prepared statement ready to be executed
  */
  private function getStatement($sqlStatement, dbParameters $parameters)
  {
    $preparedStatement = $this->prepare($sqlStatement);
    $types = "";
    $values = array();

    if ($preparedStatement)
    {
      $parameters->resetPointer();
      $parametersCount = $parameters->getParametersCount();
      if ($parametersCount > 0)
      {
        $iCounter = 0;
        while ($iCounter < $parametersCount)
        {
          $param = $parameters->getParameter();
          $types .= $param[0];
          array_push($values, $param[1]);
          $iCounter++;
        }
        
        $bind = call_user_func_array(array(&$preparedStatement, "bind_param"), array_merge(array($types), $this->refValues($values)));

        if (! $bind)
        {
          $preparedStatement->close();
          error_log('DB Bind Param Error (' . $preparedStatement->errno . ') '. $preparedStatement->error);
          return false;
        }
      }
      return $preparedStatement;
    }
    else
    {
      return false;
    }
  }

  /**
  * Since PHP 5.3 bind_param function from mysql required the parameters to be passed by reference, this function converts an array of values into it's reference values
  * @param mixed[] $values array of parameter values
  * @return mixed[] array of references to values
  */
  private function refValues($values) {
    $refs = array();

    foreach ($values as $key => $value)
    {
      $refs[$key] = &$values[$key]; 
    }

    return $refs; 
  }
  
  /**
  * Converts mysqli query results into arrays for standardization and capsulating the DB in use
  * @param mysqli_stmt $preparedStatement mysqli prepared statement already executed (with results attached)
  * @return mixed[] results of the query converted into an associative array, were the keys are the names of the fields on the select list converted into CAPS
  */
  private function buildResult($preparedStatement)
  {
    $metaData = $preparedStatement->result_metadata();
    $fields = array();
    $fieldsRef = array();
    $result = array();

    while ($field = $metaData->fetch_field())
    {
      $fields[$field->name] = 1;
      $fieldsRef[] = &$fields[$field->name];
    }
    $bind = call_user_func_array(array(&$preparedStatement, "bind_result"), $fieldsRef);

    while ($preparedStatement->fetch())
    {
      foreach($fields as $key => $value)
      {
        $row[strtoupper($key)] = $value;
      }
      array_push($result, $row);
    }
    return $result;
  }

  /**
  * Executes a select query into the DB (Queries for which a result set is expected)
  * @param string $sqlStatement sql to be executed in the form of a prepared statement (? instead of the parameter values)
  * @param dbParameters $parameters instance of dbParameters object holding the type and value of the parameters for the query
  * @example $dbConn->executeQuery('SELECT * FROM users WHERE id = ?', $params); Where params includes an int parameter holding the user Id
  * @return mixed[] results of the query converted into an associative array, were the keys are the names of the fields on the select list converted into CAPS
  */
  public function executeQuery($sqlStatement, dbParameters $parameters)
  {
      $preparedStatement = $this->getStatement($sqlStatement, $parameters);
      
      if (! $preparedStatement)
        return false;

      if ($preparedStatement->execute())
      {
        $preparedStatement->store_result();
        $result = $this->buildResult($preparedStatement);
        $preparedStatement->close();
        return $result;
      }
      else
      {
        error_log('DB Execute prepared Error (' . $preparedStatement->errno . ') '. $preparedStatement->error);
        return false;
      }
  }

  /**
  * Executes a query which does not expect to retrieve any data (Insert, Update, Delete, ...)
  * @param string $sqlStatement sql to be executed in the form of a prepared statement (? instead of the parameter values)
  * @param dbParameters $parameters instance of dbParameters object holding the type and value of the parameters for the query
  * @example $dbConn->executeQuery('UPDATE users set username = ? WHERE id = ?', $params); Where params includes a string and an int parameter holding the username and the user Id
  * @return bool result of the execution true if success, false on error
  */
  public function execute($sqlStatement, dbParameters $parameters)
  {
      $preparedStatement = $this->getStatement($sqlStatement, $parameters);

      if (! $preparedStatement)
        return false;

      $result = $preparedStatement->execute();
      if (! $result) {
        error_log('DB Execute prepared for statement '.$sqlStatement.PHP_EOL.'Error (' . $preparedStatement->errno . ') '. $preparedStatement->error);
      }

      $preparedStatement->close();
      return $result;
  }
  
  /**
  * Sometimes after executing an insert the Inserted Id is required, this method is used for that purpose and should be called after a call to execute.
  * @return int id of the last Id inserted in a table with Autoincrement PK
  */
  public function getLastId() {
    return $this->dbConnection->insert_id;
  }

  /**
  * Starts a transaction on this connection
  * @return bolean indicating the result of the operation
  */
  public function startTransaction() {
    return $this->dbConnection->begin_transaction();
  }
  
  /**
  * Rollbacks a started transaction on this connection
  * @return bolean indicating the result of the operation
  */
  public function rollback() {
    return $this->dbConnection->rollback();
  }

  /**
  * Commits a started transaction on this connection
  * @return bolean indicating the result of the operation
  */
  public function commit() {
    return $this->dbConnection->commit();
  }
}

/**
* This class is to send parameters to the database it provides magic methods to add properties as needed
* @author Pablo Bossi
*/

class dbParameters
{
  private static $validTypes = array("INT", "STRING", "TEXT", "DOUBLE");
  private static $typesTranslations = array("INT" => "i", "STRING" => "s", "DOUBLE" => "d", "TEXT" => "b");
  private $paramPointer;
  private $parameters;

  /**
  * Constructor for the class
  * @return dbParameters instance of the class
  */
  public function __construct()
  {
    $this->paramPointer = 0;
    $this->parameters = array();
  }

  /**
  * Gets the value of a parameter based on the name of the parameter, if the parameter is not set in the class then it returns null
  * @param string $propName name of the parameter to get
  * @return mixed[] type and value of the parameter with the specified name or null if doesn't exists
  */
  private function _get($propName)
  {
    $value = null;
    if (isset($this->parameters[$propName]))
      $value = $this->parameters[$propName];

    return $value;
  }

  /**
  * Sets the value of a parameter if exists or create a new one if it doesn't
  * @param string $propName name of the parameter to be set
  * @param string $propValue new value for the parameter
  */
  private function _set($propName, $propValue)
  {
    $this->parameters[$propName] = $propValue;
  }

  /**
  * Sets a new attribute for the class
  * @param string $type only valid values are INT, STRING, TEXT or DOUBLE, which are the accepted types for prepared statements
  * @param mixed $value value that the parameter should take
  * @return bool success status of adding the new parameter
  */
  public function addParameter($type, $value)
  {
    if (in_array($type, self::$validTypes))
    {
      $this->_set("parameter_".count($this->parameters), array(self::$typesTranslations[$type], $value));
      return true;
    }
    return false;      
  }

  /**
  * Gets a parameter, it works sequentially, meaning there is a pointer, pointing to the last returned parameter and this function will return the next one
  * if all parameters has been already returned then it returns null.
  * @return mixed[] array holding the type and value for the parameter
  */
  public function getParameter()
  {
    $value = null;
    if ($this->paramPointer < count($this->parameters))
    {
      $value = $this->_get("parameter_".$this->paramPointer);
      $this->paramPointer++;
    }
    return $value;
  }

  /**
  * Resets the parameter pointer to the first parameter in order to be able to get the parameters with getParameter function
  */
  public function resetPointer()
  {
    $this->paramPointer = 0;
  }

  /**
  * Gets the count of parameters being hold in the class
  * @return int count of parameters being hold in the class
  */
  public function getParametersCount()
  {
    return count($this->parameters);
  }
}
?>