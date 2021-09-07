<?php
/*
 * load class requires
 */
require_once('fw/connection/connection.php');
/*
 * TLDAP
 * Base class for providing LDAP services
 * This class will use the ../assets/libs/defines.php configuration information for LDAP
 * @author Anthony Mays
 * @category Framework Database Connection
 */
class TLDAP extends TConnection
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
        $this->disconnect();
        parent::__destruct();
    }

    /*
     * is_configured
     * returns true if the USE_LDAP define is set to YES; otherwise, returns false
     * @param None
     * @return None
     */
    public function is_configured()
    {
        return (SITE_USE_LDAP == 'YES');
    }

    /*
     * connect
     * makes a connection to the desired resource and sets the handle property appropriately.  The handle property
     * will remain null until set
     * @param None
     * @return None
     */
    public function connect()
    {
        if ($this->is_configured())
        {
            if ($this->is_valid_ip_address(SITE_PRIMARY_LDAP_SERVER))
            {
                $this->handle = ldap_connect(SITE_LDAP_PROTOCOL.SITE_PRIMARY_LDAP_SERVER, SITE_LDAP_PORT);
                if ($this->handle === false)
                {
                    if ($this->is_valid_ip_address(SITE_BACKUP_LDAP_SERVER))
                    {
                        $this->handle = ldap_connect(SITE_LDAP_PROTOCOL.SITE_BACKUP_LDAP_SERVER, SITE_LDAP_PORT);
                    }
                }
            }
        }
        return !($this->handle === false || $this->handle === null);
    }

    /*
     * disconnect
     * disconnects from the desired resource
     * @param None
     * @return None
     */
    public function disconnect()
    {
        if ($this->handle !== null)
        {
            ldap_close($this->handle);
        }
    }

    /*
     * bind
     * determines if the specified user credentials has account on the site
     * @param string $user_name - the user name part of the credentials to validate
     * @param string $password - the password part of the credentials to validate
     * @return None
     */
    public function bind($user_name, $password)
    {
        $result = true;
        if ($this->is_configured())
        {
            if ($this->connect())
            {
                ldap_set_option($this->handle, LDAP_OPT_PROTOCOL_VERSION, 3);
                $result = @ldap_bind($this->handle, $user_name.SITE_LDAP_USER_DOMAIN, $password);
            }
        }
        return $result;
    }
}
?>