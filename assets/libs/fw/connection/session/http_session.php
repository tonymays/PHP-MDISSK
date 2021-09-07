<?php
/*
 * load class requires
 */
require_once('fw/connection/session.php');
/*
 * THTTPSession
 * Child class for handling all HTTP Session services
 * @author Anthony Mays
 * @category Framework Session HTTP
 */
class THTTPSession extends TSession
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
     * cookies
     * retrieve the cookie global array
     * @param None
     * @return array
     */
    final public function cookies()
    {
        try
        {
            return isset($_COOKIE) ? $_COOKIE : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * end
     * conclude http session
     * @param None
     * @return None
     */
    final public function end()
    {
        try
        {
            if (isset($_SESSION))
            {
                session_unset();
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * enviornment
     * retrieve the environment global array
     * @param None
     * @return array
     */
    final public function environment()
    {
        try
        {
            return isset($_ENV) ? $_ENV : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * exists
     * returns true if the key can be found in the session haystack; otherwise, returns false
     * @param string $haystack - the haystack within $_SESSION to search
     * @param string $key - the key sought within the haystack
     * @return bool
     */
    public function exists($haystack, $key)
    {
        try
        {
            if (!isset($_SESSION[$haystack]))
            {
                return false;
            }
            return array_key_exists($key, $_SESSION[$haystack]);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * files
     * return the files global array
     * @param None
     * @return array
     */
    final public function files()
    {
        try
        {
            return isset($_FILES) ? $_FILES : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * gets
     * return the gets global array
     * @param None
     * @return array
     */
    final public function gets()
    {
        try
        {
            return isset($_GET) ? $_GET : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * posts
     * return the posts global array
     * @param None
     * @return array
     */
    final public function posts()
    {
        try
        {
            return isset($_POST) ? $_POST : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * requests
     * return the requests global array
     * @param None
     * @return array
     */
    final public function requests()
    {
        try
        {
            return isset($_REQUEST) ? $_REQUEST : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * server
     * return the server global array
     * @param None
     * @return array
     */
    final public function server()
    {
        try
        {
            return isset($_SERVER) ? $_SERVER : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * session
     * return the session global array
     * @param None
     * @return array
     */
    final public function session()
    {
        try
        {
            return isset($_SESSION) ? $_SESSION : array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * session_exists
     * returns true if a session has been started; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function session_exists()
    {
        try
        {
            return isset($_SESSION);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * start
     * start an HTTP Session
     * @param None
     * @return bool|error returns true on success; otherwise, throws an error
     */
    final public function start()
    {
        try
        {
            if (!isset($_SESSION))
            {
                if (!session_start())
                {
                    throw new Exception('[ERROR] Session failed to start');
                }
            }
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>