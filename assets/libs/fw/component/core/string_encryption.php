<?php
/*
 * load class requires
 */
require_once('string.php');
/*
 * StringEncryption extends String
 * Child class for handling non-native php string encryption and decryption implementations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TStringEncryption extends TString
{
    /*
     * property that holds the encryption method used
     * @property string $encrypt_method
     */
    private $encrypt_method;

    /*
     * property that holds the password used
     * @property string $secret key
     */
    private $secret_key;

    /*
     * property that holds the non-null initialization vector
     * @property string $secret_iv
     */
    private $secret_iv;

    /*
     * property that holds a hash of the password used
     * @property string $key
     */
    private $key;

    /*
     * property that holds the a hash of the initialization vector
     * @property string $iv
     */
    private $iv;

    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->encrypt_method = "AES-256-CBC";
        $this->secret_key = 'bostonredsoxworldchampions2013';
        $this->secret_iv = '3102snoipmahcdlrowxosdernotsob';
        $this->key = hash('sha256', $this->secret_key);
        $this->iv = substr(hash('sha256', $this->secret_iv), 0, 16);
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->encrypt_method = null;
        $this->secret_key = null;
        $this->secret_iv = null;
        $this->key = null;
        $this->iv = null;
        parent::__destruct();
    }

    /*
     * encrypt
     * Encrypts a string
     * @param string $string - the string to encrypt
     * @return string returns an encrypted (scrambled) key
     */
    public function encrypt($string)
    {
        try
        {
            return base64_encode(openssl_encrypt($this->scramble($string), $this->encrypt_method, $this->key, 0, $this->iv));
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * decrypt
     * Decrypts a string
     * @param string $string - the string to decrypt
     * @return string returns an decrypted (descrambled) string
     */
    public function decrypt($string)
    {
        try
        {
            return $this->descramble(openssl_decrypt(base64_decode($string), $this->encrypt_method, $this->key, 0, $this->iv));
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * scramble
     * Scrambles a given string - home grown algorythm
     * @param string $string - the string to scramble
     * @return string
     */
    public function scramble($string)
    {
        try
        {
            $result = '';
            $chars = str_split($string);
            $chars = array_reverse($chars);
            foreach($chars as $key=>$value)
            {
                $result .= '&' . pow(strlen(ord($value)) . ord($value), 2);
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * descrambled
     * Descrambles a given string - home grown algorythm
     * @param string $string - the string to descramble
     * @return string
     */
    public function descramble($string)
    {
        try
        {
            $result = '';
            $chars = explode('&', $string);
            unset($chars[0]);
            $chars = array_reverse($chars);
            foreach($chars as $key=>$value)
            {
                $result .= chr(substr(sqrt($value), 1, strlen(sqrt($value)) - 1));
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