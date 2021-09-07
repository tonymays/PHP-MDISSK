<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TMath
 * Child class for handling mathematically based operations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TMath extends TObject
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
     * even
     * returns true if $number is even; otherwise, returns false
     * @param mixed $number - the number to evaluate
     * @return bool
     */
    final public function even($number)
    {
        try
        {
            return ($this->mod($number, 2) === 0);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * odd
     * returns true if $number is odd; otherwise, returns false
     * @param mixed $number - the number to evaluate
     * @return bool
     */
    final public function odd($number)
    {
        try
        {
            return ($this->mod($number, 2) === 1);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * whole_number
     * returns true if $number is a whole number; otherwise, returns false
     * @param mixed $number - the number to evaluate
     * @return bool
     */
    final public function whole_number($number)
    {
        try
        {
            return (floor($number) == $number);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * mod
     * returns the mod of the number and divisor
     * @param mixed $number - the number to evaluate
     * @param mixed $divisor - the divisor to evaluate the number with
     * @return bool
     */
    final public function mod($number, $divisor)
    {
        try
        {
            return $number % $divisor;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>