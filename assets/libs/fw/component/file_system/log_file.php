<?php
/*
 * load class requires
 */
require_once('file_system.php');
/*
 * TLogFile
 * Child class for log file like operations
 * @author Anthony Mays
 * @category Framework File System
 */
class TLogFile extends TFile
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
}
?>