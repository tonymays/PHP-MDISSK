<?php
/*
 * load class requires
 */
require_once('file_system.php');
/*
 * TFile
 * Child class for file based operations
 * @author Anthony Mays
 * @category Framework File System
 */
class TFile extends TFileSystem
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
     * open
     * opens a file given the specified mode and the returns the file handle.  Will throw an error if the file does
     * not exist or if the mode if invalid.
     * @param string $file_name - the name of the file to open
     * @param string $mode - the mode inwhich the file will be opened - defaults to w+
     * @return resource
     */
    final public function open($file_name, $mode = 'w+')
    {
        try
        {
            if (!file_exists($file_name))
            {
                throw new Exception('[ERROR]: File ' . $file_name . ' does not exists');
            }
            else if (!$this->file_mode_exists($mode))
            {
                throw new Exception('[ERROR]: File mode ' . $mode . ' does not exists');
            }
            return fopen($file_name, $mode);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * close
     * closes the file associated with the handle specified and returns true on success; otherwise, returns false or
     * throws an error if the handle is not valid
     * @param resource $handle - the handle associated with the file name
     * @return bool
     */
    final public function close($handle)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: Close operation failed the File handle provided is not a valid file handle');
            }
            return fclose($handle);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * read
     * reads the contents of the file associated with the specified handle base on the specified chunk size and will
     * return true on success.  Will throw an error if the handle is not valid.
     * @param resource $handle - the handle associated with the file name
     * @param string $chunk_size - the size of each iterative loop
     * @return bool
     */
    final public function read($handle, $chunk_size = 4096)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: Read operation failed - received invalid file handle');
            }
            $result = '';
            while(!feof($handle))
            {
                $result .= fgets($handle, $chunk_size);
            }
            return trim($result);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * write
     * writes contents to the specified handle and returns true on success; otherwise, returns false on failure or
     * throws an error if the resource is not valid.  The contents are written based on the mode of the handle given
     * during the open operation.
     * @param resource $handle - the handle associated with the file name
     * @param string $contents - the contents to write
     * @return bool
     */
    final public function write($handle, $contents)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: Write operation failed the File handle provided is not a valid file handle');
            }
            return fwrite($handle, $contents);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * truncate
     * removes all file contents effectively emptying the file and returns true on success.  Will throw an error if
     * the handle is not valid.
     * @param resource $handle - the handle associated with the file name
     * @return bool
     */
    final public function truncate($handle)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: Truncate operation failed - received invalid file handle');
            }
            ftruncate($handle, 0);
            return rewind($handle);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * lock
     * locks the file associated with the specified handle based upon the locking type given and returns true on
     * success.  Currently supports LOCK_SH (shared locks) and LOCK_EX (exclusive locks).  Will throw an error if the
     * handle is not valid or if the lock type is not LOCK_SH or LOCK_EX.
     * @param resource $handle - the handle associated with the file name
     * @param define $lock_type - the type of lock sought.  Defaults to LOCK_EX.
     * @return bool
     */
    final public function lock($handle, $lock_type = LOCK_EX)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: File lock operation failed - received invalid file handle');
            }
            switch ($lock_type)
            {
                case LOCK_SH:
                case LOCK_EX:
                    break;
                default:
                    throw new Exception ('[ERROR]: File lock operation faile - received invalid lock type');
            }
            return flock($handle, $lock_type);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * unlock
     * removes all locks on the file associated with the specified handle and returns true on success.  Will throw an
     * error if the handle specified is invalid.
     * @param resource $handle - the handle associated with the file name
     * @return bool
     */
    final public function unlock($handle)
    {
        try
        {
            if (!is_resource($handle))
            {
                throw new Exception ('[ERROR]: File lock operation failed - received invalid file handle');
            }
            fflush($handle);
            return flock($handle, LOCK_UN);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>