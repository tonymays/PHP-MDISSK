<?php
/*
 * TObject
 * Base class for all framework classes
 * @author Anthony Mays
 * @category Base Framework
 */
class TObject
{
    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
    }

    /*
     * get_time
     * returns the current time
     * @param None
     * @return float
     */
    public function get_time()
    {
        try
        {
            list($usec, $sec) = explode(' ', microtime());
            return floatval($sec) + $usec;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * stack_backtrace
     * returns the php debug backtrace
     * @param None
     * @return array
     */
    public function stack_trace()
    {
        try
        {
            return debug_backtrace();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * is_valid_ip_address
     * returns true if the ip address specified is valid; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function is_valid_ip_address($ip_address)
    {
        return $this->filter_var($ip_address, FILTER_VALIDATE_IP);
    }

    /*
     * is_valid_mac_address
     * returns true if the mac address specified is valid; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function is_valid_mac_address($mac_address)
    {
        // note filter_var added FILTER_VALIDATE_MAC in version 5.5.0 of php so had to use a regular expression
        // strip colons if passed
        $mac_address = str_replace(':','',$mac_address);
        return (bool)preg_match('/^([0-9a-f][0-9a-f]){5}([0-9a-f][0-9a-f])$/', $mac_address);
    }

    /*
     * is_valid_email_address
     * returns true if the email address specified is valid; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function is_valid_email_address($email_address, $strip_after_dot = false)
    {
        if ($strip_after_dot)
        {
            $email_address = $this->string->slice_before($email_address, '.');
        }
        return $this->filter_var($email_address, FILTER_VALIDATE_EMAIL);
    }

    /*
     * is_valid_integer
     * returns true if the integer specified is valid; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function is_valid_integer($integer)
    {
        return $this->filter_var($integer,FILTER_VALIDATE_INT);
    }

    /*
     * filter_var
     * wrapper function for PHP filter_var function
     * @param None
     * @return bool
     */
    final public function filter_var($value, $validate_as)
    {
        $result = false;
        if (filter_var($value, $validate_as) !== false)
        {
            $result = true;
        }
        return $result;
    }
}
?>