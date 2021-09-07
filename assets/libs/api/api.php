<?php
/*
 * load class requires
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_SESSION_NAME . '/assets/libs/config.php');
require_once('fw/object.php');
require_once('fw/connection/database/mysql.php');
require_once('fw/component/core/command.php');
require_once('fw/component/core/string.php');
require_once('fw/component/core/array.php');
require_once('fw/component/core/string_encryption.php');
require_once('fw/component/core/xml.php');
require_once('fw/component/core/date.php');
/*
 * TAPI
 * Base class for API based services
 * @author Anthony Mays
 * @category Base API
 */
class TAPI extends TObject
{
    /*
     * MySQL PDO Object
     * @property object DB
     */
    public $DB;

    /*
     * Command Class Object
     * @property object $Command
     */
    public $Command;

    /*
     * String Class Object
     * @property object $String
     */
    public $String;

    /*
     * Array Class Object
     * @property object $Array
     */
    public $Array;

    /*
     * String Encrypt Class Object
     * @property object $Encrypt
     */
    public $Encrypt;

    /*
     * String XML Class Object
     * @property object $XML
     */
    public $XML;

    /*
     * String Date Class Object
     * @property object $Date
     */
    public $Date;

    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->DB = new TMySQL('localhost', '3306', SITE_DB_NAME, SITE_DB_USER_NAME, SITE_DB_PASSWORD, true, true);
        $this->Command = new TCommand();
        $this->String = new TString();
        $this->Array = new TArray();
        $this->Encrypt = new TStringEncryption();
        $this->XML = new TXML();
        $this->Date = new TDate();
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->DB = null;
        $this->Command = null;
        $this->String = null;
        $this->Array = null;
        $this->Encrypt = null;
        $this->XML = null;
        $this->Date = null;
        parent::__destruct();
    }
}
?>