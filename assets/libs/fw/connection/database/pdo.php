<?php
/*
 * load class requires
 */
require_once('database.php');
/*
 * TPDO
 * Abstract base class for providing PDO database connectivity and query services
 * @author Anthony Mays
 * @category Framework Database Connection
 */
abstract class TPDO extends TDatabase
{
    /*
     * dns string used to make pdo connection
     * @protected string $dns
     */
    protected $dns;

    /*
     * Class constructor
     * @param string $host - the host service ip address used to make the connection
     * @param integer $port - the connection port used to make the connection
     * @param string $db_name - the database name used to make the connection
     * @param string $user_name - the user name used to make the connection
     * @param string $password - the password used to make the connection
     * @param bool $auto_connect - connect on instantiation or not
     * @para, bool $commit_on_disconnect - commit on disconnect if true; otherwise, rollback
     * @return None
     */
    public function __construct($host, $port, $db_name, $user_name, $password, $auto_connect = false, $commit_on_disconnect = false)
    {
        parent::__construct($host, $port, $db_name, $user_name, $password, $auto_connect, $commit_on_disconnect);
        $this->dns = null;
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->dns = null;
        parent::__destruct();
    }

    /*
     * construct_dns
     * construct the dns string used to make a connection
     * @param None
     * @return None
     */
    abstract protected function construct_dns();

    /*
     * connect
     * make a connection and set the handle property to the resource returned
     * @param None
     * @return None
     */
    public function connect()
    {
        try
        {
            $this->handle = new PDO($this->dns, $this->user_name, $this->password);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * commits
     * commits an open transaction
     * @param None
     * @return bool|error returns true on success; otherwise, throws an error
     */
    public function commit()
    {
        try
        {
            if ($this->in_transaction())
            {
                return $this->handle->commit();
            }
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * disconnect
     * disconnect the current connection.  commit or rollback a transaction -
     * depending on the commit on disconnect property
     * @param None
     * @return bool|error returns true on success; otherwise, throws an error
     */
    public function disconnect()
    {
        try
        {
            if ($this->handle !== null)
            {
                if ($this->commit_on_disconnect)
                {
                    $this->commit();
                }
                else
                {
                    $this->rollback();
                }
                $this->handle = null;
            }
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * dml
     * execute a dml statement
     * @param string $sql - to sql statement to execute
     * @return None
     */
    public function dml($sql)
    {
        try
        {
            $this->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $set = $this->handle->query($sql);
            $set->closeCursor();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_dns
     * return the current dns string
     * @param None
     * @return string
     */
    final public function get_dns()
    {
        try
        {
            return $this->dns;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * in_transaction
     * returns true if the session is in a transaction; otherwise, returns false
     * @param None
     * @return bool
     */
    public function in_transaction()
    {
        try
        {
            if ($this->handle !== null)
            {
                return $this->handle->inTransaction();
            }
            return false;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * prepare
     * prepares a sql statement with given driver options for execution
     * @param string $sql - the sql statement to execute
     * @param array $driver_options - the driver option to apply
     * @return PDOStatement|error returns a PDOStatement object on success;
     * otherwise, throws an preparation error
     */
    public function prepare($sql, $driver_options = array())
    {
        try
        {
            $result = $this->handle->prepare($sql, $driver_options);
            if ($result === false)
            {
                throw new Exception('[ERROR] SQL Statement ' . $sql . ' failed to prepare');
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * execute
     * executes a prepare statement given input parameters
     * @param PDOStatement $statement_obj - the prepared PDOStatement object
     * @params array $input_parameters - the execution input parameters
     * @return bool|error returns true on success; otherwise, throws and error.
     * Note: all transactions to this point will be rolled back on error.
     */
    public function execute($statement_obj, $input_parameters = array())
    {
        try
        {
            if (!$statement_obj->execute($input_parameters))
            {
                $error = $statement_obj->errorInfo();
                throw new Exception('[ERROR] An execution error has occurred: ' . $error[2] . ' rolling back transaction');
            }
            return true;
        }
        catch (Exception $e)
        {
            $this->rollback();
            throw $e;
        }
    }

    /*
     * fetch_all
     * fetches all rows contained on a pdo statement
     * @param PDOStatement $statement_obj - the prepared PDOStatement object
     * @return array
    */
    public function fetch_all($statement_obj)
    {
        try
        {
            $result = $statement_obj->fetchAll(PDO::FETCH_ASSOC);
            $statement_obj->closeCursor();
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * query
     * execute a sql query
     * @param string $sql - to sql statement to execute
     * @return PDOStatement object on success; otherwise, throws an execution
     * error
     */
    public function query($sql)
    {
        try
        {
            $this->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $set = $this->handle->query($sql);
            $result = $set->fetchAll(PDO::FETCH_ASSOC);
            $set->closeCursor();
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * rollback
     * rollback a transaction
     * @param None
     * @return bool|error returns true on success; otherwise, throws an error
     */
    public function rollback()
    {
        try
        {
            if ($this->in_transaction())
            {
                return $this->handle->rollBack();
            }
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_dns
     * sets a new dns string for the current connection - will cause a disconnect
     * @param string $value - contains the new dns string
     * @return potential connection errors
     */
    public function set_dns($value)
    {
        try
        {
            $this->dns = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * start_transaction
     * starts a new transaction
     * @param None
     * @return bool|error return true on success; otherwise, returns false.
     * Could throw potential connection related errors
     */
    public function start_transaction()
    {
        try
        {
            return $this->handle->beginTransaction();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>