<?php
/*
 * load class requires
 */
require_once('file_system.php');
/*
 * TBackup
 * Child class for file/directory backup and restore operations
 * @author Anthony Mays
 * @category Framework File System
 */
class TBackup extends TFileSystem
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