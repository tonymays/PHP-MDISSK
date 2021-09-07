<?php
/*
 * load class requires
 */
require_once('fw/object.php');
/*
 * TCommand
 * Child class for handling linux command based operations
 * @author Anthony Mays
 * @category Framework Core Component
 */
class TCommand extends TObject
{
    /*
     * private property that stores the command being prepared and executed
     * @property string $command
     */
    private $command;

    /*
     * private property that stores the output from an executed command
     * @property array $output
     */
    private $output;

    /*
     * private property that stores the execution result of a prepared command
     * @property integer $result
     */
    private $result;

    /*
     * Class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->initialize();
        parent::__destruct();
    }

    /*
     * prepare
     * prepares a command to be executed
     * @param string $command - the command to be executed
     * @param array $params - a reference to the command parameters
     * @return bool or throws an error
     */
    public function prepare($command, &$params = array())
    {
        try
        {
            $param_count = count($params);
            if (substr_count($command, '?') != $param_count)
            {
                throw new Exception('[ERROR]: Command preparation failed due to parameter count mismatch');
            }
            if ($param_count == 0)
            {
                $this->command = $command;
            }
            else
            {
                foreach($params as $key=>$value)
                {
                    $params[$key] = str_replace(array('"'), '', $value);
                }
                $this->command  = vsprintf(str_replace('?', '%s', $command), $params);
            }
            return true;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * execute
     * executes a command and returns the execution result
     * @param None
     * @return bool or throws an error
     */
    public function execute()
    {
        try
        {
            if ($this->command === null)
            {
                throw new Exception('[ERROR]: No command has been established for execution');
            }
            $this->output = array();
            $this->result = null;
            return exec($this->command, $this->output, $this->result);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * prepare_and_execute
     * prepares and executes a command and returns the execution output
     * @param string $command - the command to be executed
     * @param array $params - a reference to the command parameters
     * @return bool or throws an error
     */
    public function prepare_and_execute($command, $params = array())
    {
        try
        {
            $this->prepare($command, $params);
            $this->execute();
            return $this->output;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_command
     * command getter for the command private property
     * @param None
     * @return string
     */
    public function get_command()
    {
        return $this->command;
    }

    /*
     * get_output
     * output getter for the output private property
     * @param None
     * @return array
     */
    public function get_output()
    {
        return $this->output;
    }

    /*
     * get_result
     * result getter for the result private property
     * @param None
     * @return integer
     */
    public function get_result()
    {
        return $this->result;
    }

    /*
     * initialize
     * initializes private properties to their default state
     * @param None
     * @return None
     */
    private function initialize()
    {
        $this->command = null;
        $this->output = null;
        $this->result = null;
    }
}
?>