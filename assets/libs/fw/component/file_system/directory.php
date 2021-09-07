<?php
/*
 * load class requires
 */
require_once('file_system.php');
/*
 * TDirectory
 * Child class for directory operations
 * @author Anthony Mays
 * @category Framework File System
 */
class TDirectory extends TFileSystem
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