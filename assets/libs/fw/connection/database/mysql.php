<?php
/*
 * load class requires
 */
require_once('pdo.php');
/*
 * TMySQL
 * Child class for making and executing MySQL connections and queries
 * @author Anthony Mays
 * @category Framework Database Connection
 */
class TMySQL extends TPDO
{
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
        $this->construct_dns();
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
     * construct_dns
     * construct a dns string to use with MySQL connections
     * @param None
     * @return None
     */
    final protected function construct_dns()
    {
        try
        {
            $this->dns = 'mysql:dbname=' . $this->db_name . ';host=' . $this->host;
            if ($this->auto_connect)
            {
                $this->connect();
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>
