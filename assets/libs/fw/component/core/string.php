<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TString
 * Child class for handling string based operations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TString extends TObject
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
     * returns true if the needle is contained within haystack after the
     * offset; otherwise, returns false
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @param integer $offset - the string offset where the search is to begin
     * @return bool
     */
    public function contains($haystack, $needle, $offset=0)
    {
        return (strpos($haystack, $needle, $offset) !== false);
    }

    /*
     * count
     * returns that the number of occurences of $needle within $haystack
     * beginning at offset.  Please note: the needle is case sensitive and
     * overlapped substrings are not counted
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @param integer $offset - the string offset where the search is to begin
     * @return integer
     */
    public function count($haystack, $needle, $offset=0)
    {
        return substr_count($haystack, $needle, $offset);
    }

    /*
     * ends_with
     * returns true if $haystack ends with $needle; otherwise, returns false
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @return bool
     */
    final public function ends_with($haystack, $needle)
    {
        try
        {
            $length = strlen($needle);
            if ($length == 0)
            {
                return false;
            }
            return (substr($haystack, -$length) === $needle);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * slice_after
     * returns the portion of $haystack found after $needle.  Will also return
     * the needle as part of the output if $include_needle is true.  If the
     * needle is not found then $haystack is returned.
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @param bool $include_needle - attach needle to output if needle found
     * in haystack
     * @return string
     */
    final public function slice_after($haystack, $needle, $include_needle = false)
    {
        try
        {
            $result = strstr($haystack, $needle, false);
            if ($result === false)
            {
                return $haystack;
            }
            // remove need if include needle is false ... strstr function with
            // with the before_needle parameter set to false will always return]
            // the needle as part of the output.  Thus it has to be removed if
            // unwanted.
            else if (!$include_needle)
            {
                $result = str_replace($needle, '', $result);
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * slice_before
     * returns the portion of $haystack found before $needle.  Will also return
     * the needle as part of the output if $include_needle is true.  If the
     * needle is not found then $haystack is returned.
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @param bool $include_needle - attach needle to output if needle found
     * in haystack
     * @return string
     */
    final public function slice_before($haystack, $needle, $include_needle = false)
    {
        try
        {
            $result = strstr($haystack, $needle, true);
            if ($result === false)
            {
                return $haystack;
            }
            else if ($include_needle)
            {
                $result .= $needle;
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * slice_before
     * returns the portion of $haystack found between the before and after
     * needles.  If the either needle (before and after) are not found within
     * haystack then the haystack is returned.
     * @param string $haystack - the string to be search
     * @param string $before_needle the string where the search begins
      * @param string $after_needle the string where the search ends in haystack
     * @return string
     */
    final public function slice_between($haystack, $before_needle, $after_needle)
    {
        try
        {
            $before_pos = strpos($haystack, $before_needle);
            $after_pos = strpos($haystack, $after_needle, $before_pos + 1);
            if ($before_pos === false || $after_pos === false)
            {
                return $haystack;
            }
            else
            {
                return substr($haystack, $before_pos + strlen($before_needle), $after_pos - $before_pos - strlen($before_needle));
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * starts_with
     * return true if $haystack starts with $needle; otherwise, returns false
     * @param string $haystack - the string to be search
     * @param string $needle - the string sought
     * @return bool
     */
    final public function starts_with($haystack, $needle)
    {
        try
        {
            return (substr($haystack, 0, strlen($needle)) === $needle);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * highlight_diff
     * returns an array of highlight differences between old and new versions of a string
     * @param string $old - old version of the string
     * @param string $new - new version of the string
     * @return array
     */
    final public function highlight_diff($old, $new)
    {
        $from_start = strspn($old ^ $new, "\0");
        $from_end = strspn(strrev($old) ^ strrev($new), "\0");
        $old_end = strlen($old) - $from_end;
        $new_end = strlen($new) - $from_end;
        $start = substr($new, 0, $from_start);
        $end = substr($new, $new_end);
        $new_diff = substr($new, $from_start, $new_end - $from_start);
        $old_diff = substr($old, $from_start, $old_end - $from_start);
        $new = "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
        $old = "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";
        return array("old"=>$old, "new"=>$new);
    }

    /*
     * decode_html_chars
     * decodes html codes to ascii within the specified string and returns the decoded string
     * @param string $string - the string to decode
     * @return string
     */
    final public function decode_html_chars($string)
    {
        return html_entity_decode($string);
    }

    /*
     * decode_url_chars
     * decodes url codes to ascii within the specified string and returns the decoded string
     * @param string $string - the string to decode
     * @return string
     */
    final public function decode_url_chars($string)
    {
        return urldecode($string);
    }
}
?>