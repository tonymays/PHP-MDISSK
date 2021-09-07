<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * load requires
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_SESSION_NAME . '/assets/libs/config.php');
require_once('api/core/session.php');
/*
 * base controller class
 */
class MY_Controller extends CI_Controller
{
	/*
	 * hold the TSession Object
	 * @protected object $Session
	 */
	protected $Session;
	
	/*
	 * provides a data array for view parsing
	 * @protected array $data
	 */
	protected $data;
	
	/*
	 * holds that the server wishes to communicate to the client
	 * @protected string $message
	 */
	protected $messages;

	/*
	 * __construct
	 * class constructor
	 * @param None
	 * @return None
	*/
	public function __construct()
	{
		parent::__construct();
		$this->Session = new TSessionAPI();
		$this->data = array();
		$this->Session->start_session();
	}

    /*
     * construct_toolbar
     * construct a window toolbar specified by the array parameter
     * @param reference array $array - the array that contains the specified options for the toolbar
     * @return html template
    */
    final protected function construct_toolbar(&$array)
    {
        $data = array('buttons'=>array());
        foreach($array as $key=>$value)
        {
            // if form is blank then the button type will be button ...
            $button = '<button  name="' . $value['name'] . '" type="button" title="' . $value['title'] . '"><img src="/' . SITE_SESSION_NAME . '/' . $value['img'] . '"/>' . $value['text'] . '</button>';
            array_push($data['buttons'], array('button'=>$button));
        }
        return $this->parser->parse('core/toolbar.html', $data, true);
    }

	/*
	 * clear_messages
	 * clears process messages
	 * @param None
	 * @return None
	*/
	final protected function clear_messages()
	{
		try
		{
			$this->messages = array('type'=>'', 'messages'=>array());
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/*
	 * has_messages
	 * returns true if the process log msg array contains data; otherwise,
	 * returns false
	 * @param none
	 * @return bool
	*/
	final protected function has_messages()
	{
		try
		{
			$result = false;
			if (is_array($this->messages))
			{
				if (strlen(trim($this->messages['type'])) > 0)
				{
					$result = true;
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
	 * send_message
	 * establishes a message to be displayed on the page
	 * @param string $message
	 * @param bool $error - invokes the error message css class if true; otherwise, invokes the success message css
	 * class if false.  Defaults to true (error).
	 * @return None
	*/
	final protected function send_message($message, $error = true)
	{
		try
		{
			if (!is_array($this->messages))
			{
				$this->clear_messages();
			}
			if ($error)
			{
				$this->messages['type'] = 'error';
			}
			else
			{
				$this->messages['type'] = 'success';
			}
			array_push($this->messages['messages'], $message);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	
	/*
	 * show_messages
	 * constructs process messages
	 * @param None
	 * @return html div
	*/
	final protected function show_messages()
	{
		try
		{
			$is_error = false;
			if ($this->messages['type'] == 'error')
			{
				$result  = "<div name='message_handler' class='error_msg'>";
				$result .= "<ul><u>The following errors were discovered:</u>";
				$is_error = true;
			}
			else
			{
				$result = "<div name='message_handler' class='success_msg'>";
			}
			
			foreach($this->messages['messages'] as $key=>$value)
			{
				if ($is_error)
				{
					$result .= "<li>" . $value . "</li>";
				}
				else
				{
					$result .= "<ul>" . $value . "</ul>";
				}
			}
				
			if ($is_error)
			{
				$result .= "</ul>";
			}
			
			$result .= "</div>";
			$this->clear_messages();
			return $result;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/*
	 * validate
	 * validates requests
	 * @param string $form - the name of the form to validate
	 * @param bool $store_form_name - if true stores the form name in the session for latter comparison; otherwise,
	 * if false will compare the the session form name to form being passed.  If the two forms do match then validate
	 * will kill your session and send you back to the login page.
	 * @return None
	*/
	protected function validate($form, $store_form_name = true)
	{
		try
		{
			if (!$this->Session->has_user())
			{
				redirect(SITE_SESSION_NAME . '/core/login');
			}
			else if ($store_form_name)
			{
				$_SESSION[SITE_SESSION_NAME]['form'] = $form;
			}
			else if (!isset($_SERVER['HTTP_REFERER']))
			{
				redirect(SITE_SESSION_NAME . 'core/login');
			}
			else
			{
				if ($_SESSION[SITE_SESSION_NAME]['form'] != $form)
				{
					$this->Session->end_session();
					redirect(SITE_SESSION_NAME . '/core/login');
				}
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}