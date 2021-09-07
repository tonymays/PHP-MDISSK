<?php
/*
 * load class requires
 */
require_once('file.php');
/*
 * TIniFile
 * Child class for ini file like operations
 * ===================================================================================================================
 * WARNING:
 * -------------------------------------------------------------------------------------------------------------------
 * The file provided as a parameter to many of the methods must be an ini file otherwise the outcome of many of the
 * method calls will be unpredictable until a file validation routine can be development to ensure the file specified
 * is actually a valid ini file.
 * ===================================================================================================================
 * @author Anthony Mays
 * @category Framework File System
 */
class TIniFile extends TFile
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
     * add
     * adds a new section to the ini file and returns true om success; otherwise, returns false or throws a specific
     * error
     * @param string $file_name - the file name specified
     * @param string $header - the header of the section to be added
     * @param string $contents - the contents of the section body to be added
     * @return bool|error
     */
    final public function add($file_name, $header, $contents)
    {
        try
        {
            if (!file_exists($file_name))
            {
                throw new Exception ('[ERROR]: File ' . $file_name . ' does not exist');
            }
            else if ($this->header_exists($file_name, $header))
            {
                throw new Exception ('[ERROR]: Header ' . $header . ' already exists in file ' . $file_name);
            }
            else if ($contents == '')
            {
                throw new Exception ('[ERROR]: Contents are empty and are required cannot add header ' . $header . ' to file file ' . $file_name);
            }
            $file_contents = trim(file_get_contents($file_name));
            $file_contents .= "\n" . '[' . trim($header) . "]\n" .  trim($contents) . "\n";
            $handle = $this->open($file_name, 'w+');
            $result = $this->write($handle, $file_contents);
            $this->close($handle);
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * delete
     * deletes an existing section from the ini file and returns true om success; otherwise, returns false or throws a
     * specific error
     * @param string $file_name - the file name specified
     * @param string $header - the header of the section to be deleted
     * @return bool|error
     */
    final public function delete($file_name, $header)
    {
        try
        {
            $file_contents = file_get_contents($file_name);
            $contents = trim($this->get_contents($file_name, $header, TYPE_STRING)) . "\n";
            $file_contents = str_replace($contents, '', $file_contents);
            $handle = $this->open($file_name, 'w+');
            $result = $this->write($handle, $file_contents);
            $this->close($handle);
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * update
     * updates an existing section within the ini file and returns true om success; otherwise, returns false or throws
     * a specific error
     * @param string $file_name - the file name specified
     * @param string $header - the header of the section to be added
     * @param string $contents - the new contents of the section body
     * @return bool|error
     */
    final public function update($file_name, $header, $contents)
    {
        try
        {
            $file_contents = file_get_contents($file_name);
            $old_contents = trim($this->get_contents($file_name, $header, TYPE_STRING)) . "\n";
            $contents = '[' . $header . "]\n" . $contents;
            $file_contents = str_replace ($old_contents, $contents, $file_contents);
            $handle = $this->open($file_name, 'w+');
            $result = $this->write($handle, $file_contents);
            $this->close($handle);
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_headers
     * returns the headers from the specified ini file as an array if found; otherwise, will return an empty array.
     * @param string $file_name - the file name specified
     * @return array
     */
    final public function get_headers($file_name)
    {
        try
        {
            if (!file_exists($file_name))
            {
                throw new Exception ('[ERROR]: File ' . $file_name . ' does not exist');
            }
            $command = "egrep -v '^$' ? | egrep -i '^\[' | cut -d']' -f1 | cut -d'[' -f2";
            $result = $this->Command->prepare_and_execute($command, array($file_name));
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * header_exists
     * returns true if the header exists; otherwise, returns false
     * @param string $file_name - the file name specified
     * @param string $header - the section header sought
     * @return bool
     */
    final public function header_exists($file_name, $header)
    {
        try
        {
            if (!file_exists($file_name))
            {
                throw new Exception ('[ERROR]: File ' . $file_name . ' does not exist');
            }
            $command = "egrep -v '^$' ? | egrep -i '^\[?\]'";
            $result = $this->Command->prepare_and_execute($command, array($file_name, $header));
            return (!empty($result));
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_contents
     * returns the contents of the specified header section based on the return type.
     * @param string $file_name - the file name specified
     * @param string $header - the header sought
     * @param string $return_type = the return type sought options are TYPE_RAW, TYPE_ARRAY and TYPE_STRING. The
     * return type default to TYPE_RAW.  TYPE_RAW is returned if an unidentified return type is specified.
     * @return array|string (return type depended)
     */
    final public function get_contents($file_name, $header, $return_type = TYPE_RAW, $delimiter = "=")
    {
        try
        {
            if (!file_exists($file_name))
            {
                throw new Exception ('[ERROR]: File ' . $file_name . ' does not exist');
            }
            else if (!$this->header_exists($file_name, $header))
            {
                throw new Exception ('[ERROR]: Header ' . $header . ' does not exists in file ' . $file_name);
            }
            $command = "egrep -v '^$' ? | sed -En '/^\[?]/,/^\[|^$/ p' | grep -v '^\['";
            $result = $this->Command->prepare_and_execute($command, array($file_name, $header));
            switch($return_type)
            {
                case TYPE_ARRAY:
                    $array = array();
                    foreach ($result as $key=>$value)
                    {
                        // do not process empty lines
                        if ($value != '')
                        {
                            $row = explode($delimiter, $value);
                            if (array_key_exists($row[0], $array))
                            {
                                $array[$row[0]] = $this->Array->make_array($array[$row[0]]);
                                array_push($array[$row[0]], $row[1]);
                            }
                            else
                            {
                                $array[$row[0]] = $row[1];
                            }
                        }
                    }
                    $result = $array;
                    break;
                case TYPE_STRING:
                    $result = '[' . $header . "]\n" . implode("\n", $result);
                    break;
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_list
     * returns an array of fields contained within the specified file if found; otherwise, returns an empty array
     * @param string $file_name - the specified ini file that contains the fields to gather
     * @param string $field_list - a pipe delimited set of fields
     * @param string $delimiter - the delimiter used to parse the individual rows with. defaults to =
     * @return array
     */
    final public function get_list($file_name, $field_list, $delimiter = '=')
    {
        try
        {
            if (!is_file($file_name))
            {
                throw new Exception ('[ERROR] File ' . $file_name . ' does not exist');
            }
            else if ($field_list == '' || $field_list === null)
            {
                throw new Exception ('[ERROR] Field list is invalid - cannot be blank or equal to null');
            }
            $command = "egrep -v '^$' ? | egrep -i '(\[|?)'";
            $output = $this->Command->prepare_and_execute($command, array($file_name, $field_list));
            $result = array();
            if (!empty($output))
            {
                $tmp = array();
                foreach ($output as $key => $value)
                {
                    if (substr($value, 0, 1) == '[')
                    {
                        if (empty($tmp))
                        {
                            $tmp['raw_header'] = $value;
                            $tmp['header'] = str_replace(array('[', ']'), '', $value);
                        }
                        else
                        {
                            array_push($result, $tmp);
                            $tmp = array();
                            $tmp['raw_header'] = $value;
                            $tmp['header'] = str_replace(array('[', ']'), '', $value);
                        }
                    }
                    else
                    {
                        $row = explode($delimiter, $value);
                        if (array_key_exists($row[0], $tmp))
                        {
                            $tmp[$row[0]] = $this->Array->make_array($tmp[$row[0]]);
                            array_push($tmp[$row[0]], $row[1]);
                        }
                        else
                        {
                            $tmp[$row[0]] = $row[1];
                        }
                    }
                }
                if (!empty($tmp))
                {
                    array_push($result, $tmp);
                }
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>