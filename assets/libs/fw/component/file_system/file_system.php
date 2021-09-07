<?php
/*
 * load class requires
 */
require_once('fw/object.php');
require_once('fw/component/core/command.php');
require_once('fw/component/core/string.php');
require_once('fw/component/core/array.php');
/*
 * TFileSystem
 * Base Class for all file system framework class
 * @author Anthony Mays
 * @category Framework File System
 */
class TFileSystem extends TObject
{
    /*
     * Command Class Object
     * @property object $Command
     */
    protected $Command;

    /*
     * String Class Object
     * @property object $String
     */
    protected $String;

    /*
     * Array Class Object
     * @property object $Array
     */
    protected $Array;

    /*
     * array containing available file modes
     * @property array $file_modes
     */

    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->Command = new TCommand();
        $this->String = new TString();
        $this->Array = new TArray();
        $this->file_modes = array('r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        parent::__destruct();
        $this->Command = null;
        $this->String = null;
        $this->Array = null;
    }

    public function file_mode_exists($file_mode)
    {
        try
        {
            return (in_array($file_mode, $this->file_modes));
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>