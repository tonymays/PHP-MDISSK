<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TConnection
 * Abstract base class for providing default services for all connections
 * @author Anthony Mays
 * @category Framework Connection
 */
abstract class TConnection extends TObject
{
    /*
     * the connection handle
     * @protected resource $handle
     */
    protected $handle;

    /*
     * String Class Object
     * @protected object $string
     */
    public $String;

    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->handle = null;
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->handle = null;
        parent::__destruct();
    }

    /*
     * connect
     * makes a connection to the desired resource
     * @param None
     * @return None
     */
    abstract public function connect();

    /*
     * disconnect
     * disconnects from the desired resource
     * @param None
     * @return None
     */
    abstract public function disconnect();

    /*
     * get_handle
     * returns the handle, or null if one does not exists, for the connected resource
     * @param None
     * @return resource
     */
    final public function get_handle()
    {
        return $this->handle;
    }
}
?>