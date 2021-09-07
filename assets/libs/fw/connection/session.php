<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TSession
 * Abstract base class for providing default services for all sessions
 * @author Anthony Mays
 * @category Framework Session
 */
abstract class TSession extends TObject
{
    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /*
     * start
     * starts a session
     * @param None
     * @return None
     */
    abstract public function start();

    /*
     * end
     * terminates a session
     * @param None
     * @return None
     */
    abstract public function end();
}
?>