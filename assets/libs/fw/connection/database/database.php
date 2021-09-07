<?php
/*
 * load class requires
 */
require_once('fw/connection/connection.php');
/*
 * TDatabase
 * Abstract base class for providing database connectivity services
 * @author Anthony Mays
 * @category Framework Database Connection
 */
abstract class TDatabase extends TConnection
{
    /*
     * host to connect to
     * @protected string $host
     */
    protected $host;

    /*
     * port to connect to
     * @protected integer port
     */
    protected $port;

    /*
     * database name to connect to
     * @protected string $db_name
     */
    protected $db_name;

    /*
     * user name to connect with
     * @protected string $user_name
     */
    protected $user_name;

    /*
     * password to connect with
     * @protected string $password
     */
    protected $password;

    /*
     * automatically connect on instantiation
     * @protected $bool $auto_connect
     */
    protected $auto_connect;

    /*
     * commit on disconnect if true; otherwise, rollback on disconnect if false
     * @protected bool commit_on_disconnect
     */
    protected $commit_on_disconnect;

    /*
     * Class constructor
     * @param string $host - the host service ip address used to make the connection
     * @param integer $port - the connection port used to make the connection
     * @param string $db_name - the database name used to make the connection
     * @param string $user_name - the user name used to make the connection
     * @param string $password - the password used to make the connection
     * @param bool $auto_connect - connect on instantiation or not
     * @param bool $commit_on_disconnect - commit on disconnect if true; otherwise, rollback
     * @return None
     */
    public function __construct($host, $port, $db_name, $user_name, $password, $auto_connect = false, $commit_on_disconnect = false)
    {
        parent::__construct();
        $this->host = $host;
        $this->port = $port;
        $this->db_name = $db_name;
        $this->user_name = $user_name;
        $this->password = $password;
        $this->auto_connect = $auto_connect;
        $this->commit_on_disconnect = $commit_on_disconnect;
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->disconnect();
        $this->host = null;
        $this->port = null;
        $this->db_name = null;
        $this->user_name = null;
        $this->password = null;
        $this->auto_connect = null;
        $this->commit_on_disconnect = null;
        parent::__destruct();
    }

    /*
     * commit
     * commits a transaction
     * @param None
     * @return None
     */
    abstract public function commit();

    /*
     * in_transaction
     * returns true if in a transaction; otherwise, returns false
     * @param None
     * @return None
     */
    abstract public function in_transaction();

    /*
     * rollback
     * rollbacks a transaction
     * @param None
     * @return None
     */
    abstract public function rollback();

    /*
     * start_transaction
     * @param None
     * @return None
     */
    abstract public function start_transaction();

    /*
     * query
     * execute the given sql statement
     * @param string $sql - the sql statement to execute
     * @return None
     */
    abstract public function query($sql);

    /*
     * get_auto_connect
     * returns the auto_connect property
     * @param None
     * @return bool
     */
    final public function get_auto_connect()
    {
        try
        {
            return $this->auto_connect;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_commit_on_disconnect
     * returns the commit on disconnect property  associated with the current connection
     * @param None
     * @return bool
     */
    final public function get_commit_on_disconnect()
    {
        try
        {
            return $this->commit_on_disconnect;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_db_name
     * returns the database name associated with the current connection
     * @param None
     * @return string
     */
    final public function get_db_name()
    {
        try
        {
            return $this->db_name;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_host
     * returns the host associated with the current connection
     * @param None
     * @return string
     */
    final public function get_host()
    {
        try
        {
            return $this->host;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_port
     * returns the port associated with the current connection
     * @param None
     * @return integer
     */
    final public function get_port()
    {
        try
        {
            return $this->port;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_user_name
     * returns the user name associated with the current connection
     * @param None
     * @return string
     */
    final public function get_user_name()
    {
        try
        {
            return $this->user_name;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * refresh
     * refreshes a connection by disconnecting and connecting
     * @param None
     * @return bool|None returns true on success; otherwise, throws an error
     */
    public function refresh()
    {
        try
        {
            $this->disconnect();
            $this->connect();
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_auto_connect
     * set the auto connect property - does not cause a refresh
     * @param bool $value
     * @return None
     */
    final public function set_auto_connect($value)
    {
        try
        {
            $this->auto_connect = $value;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_commit_on_disconnect
     * set the commit on disconnect property - does not cause a refresh
     * @param bool $value
     * @return None
     */
    final public function set_commit_on_disconnect($value)
    {
        try
        {
            $this->commit_on_disconnect = $value;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_db_name
     * sets the database name property - will cause a disconnect
     * @param string $value - name of the database
     * @return potential connection error
     */
    final public function set_db_name($value)
    {
        try
        {
            $this->db_name = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_host
     * set the connection host property - will cause a disconnect
     * @param string $value - name of the host
     * @return potention connection error
     */
    final public function set_host($value)
    {
        try
        {
            $this->host = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_password
     * set the password property - will cause a disconnect
     * @param string $value - password used to make the connection
     * @return
     */
    final public function set_password($value)
    {
        try
        {
            $this->password = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_port
     * set the connection port - will cause a disconnect
     * @param integer $value - the port number used to establish the connection
     * @return potential connection error
     */
    final public function set_port($value)
    {
        try
        {
            $this->port = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * set_user_name
     * set the user name property - will cause a disconnect
     * @param string $value
     * @return potential connection error
     */
    final public function set_user_name($value)
    {
        try
        {
            $this->user_name = $value;
            $this->disconnect();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>
