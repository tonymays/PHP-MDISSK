<?php
/*
 * load class requires
 */
require_once('fw/object.php');

/*
 * TXML
 * Child class for handling non-native php xml implementations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TXML extends TObject
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
     * to_array
     * Converts an XML document to an array
     * @param string $xml_document - xml document to be converted
     * @return array
     */
    public function to_array($xml_document)
    {
        $array = json_decode(json_encode((array) simplexml_load_string($xml_document)), true);
        // it is possible for an array element to exist that contains only \n and must be removed ... something in
        // encode and decode process
        if (isset($array[0]) && $array[0] == "\n")
        {
            unset($array[0]);
        }
        return $array;
    }
}
?>