<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TArray
 * Child class for handling array based operations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TArray extends TObject
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
     * contains
     * Returns true if the haystack contains needle; otherwise, return false
     * @param string|array $haystack contains the string or array to search
     * @param string $needle the value sought
     * @return bool returns true if the needle is found in the haystack;
     * otherwise, returns false
     */
    final public function contains($haystack, $needle)
    {
        try
        {
            $result = false;
            if (!is_array($haystack))
            {
                $haystack = $this->make_array($haystack);
            }

            foreach($haystack as $key=>$value)
            {
                if ($value == $needle)
                {
                    $result = true;
                    break;
                }
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * find
     * finds and returns an array key based on the specified column_name
     * equalling the specified column_value.  -1 is returned if no match is
     * found.
     * @param array &$array - reference to the array to be search
     * @param string $column_name - the name of the column within each row to examine
     * @param mixed $column_value - the value within the column to match on
     * @return integer
     */
    public function find(&$array, $column_name, $column_value)
    {
        $result = null;
        foreach($array as $key=>$value)
        {
            if ($value[$column_name] == $column_value)
            {
                $result = $key;
                break;
            }
        }
        return $result;
    }

    /*
     * join
     * joins two arrays together by comparing the values within the join column
     * parameter.  Please note that this acts like an outer join within a
     * database query.  $array1 is always operated on and returned.
     * @param array &$array1 - reference to the source array to operator on
     * @param array &$array1 - reference to the array to be searched
     * @param string $join_column - the column that the join will take place on
     * @return array
     */
    public function join(&$array1, &$array2, $join_column)
    {
        $result = $array1;
        foreach($array1 as $key=>$value)
        {
            $fkey = $this->find($array2, $join_column, $value[$join_column]);
            if ($fkey != -1)
            {
                $result[$key] = array_merge($result[$key], $array2[$fkey]);
            }
        }
        return $result;
    }

    /*
     * filter
     * walks the array filtering it based on the specified column value
     * contained within the specified column name.  This acts like a single
     * column where clause in a database query
     * @param array &$array - reference to the array to be search
     * @param string $column_name - the name of the column within each row to examine
     * @param mixed $column_value - the value within the column to match on
     * @return array
     */
    public function filter(&$array, $column_name, $column_value)
    {
        $result = array();
        foreach($array as $key=>$value)
        {
            if (isset($value[$column_name]) && $value[$column_name] == $column_value)
            {
                array_push($result, $array[$key]);
            }
        }
        return $result;
    }

    /*
     * make_array
     * Push a simple string into an array
     * @param mixed $mixed - contains the string to convert
     * @return array
     */
    final public function make_array($mixed)
    {
        try
        {
            $result = array();
            array_push($result, $mixed);
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * make_multi_dimensional
     * makes a single dimensional array multi-dimensional
     * @param array $array - the array to convert
     * @return array
     */
    final public function make_multi_dimensional($array)
    {
        try
        {
            $result = array();
            if (count($array) == count($array, COUNT_RECURSIVE))
            {
                array_push($result, $array);
            }
            else
            {
                $result = $array;
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
