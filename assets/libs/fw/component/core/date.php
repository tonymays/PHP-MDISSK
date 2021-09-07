<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TDate extends TObject
 * Child class for handling non-native php date implementations
 * @author Anthony Mays
 * @category Component
 */
class TDate extends TObject
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
     * convert_epoch
     * Convert and epoch integer to the appropriate date string
     * @param integer $epoch - the epoch to convert
     * @param string $format - the format inwhich to convert the epoch to - defaulted to 'Y-m-d H:i:s' string
     * @return date
     */
    public function convert_epoch($epoch, $format = 'Y-m-d H:i:s')
    {
        try
        {
            return date($format, $epoch);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>