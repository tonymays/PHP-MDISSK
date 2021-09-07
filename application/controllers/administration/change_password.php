<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * Change_Password
 * Base controller class for change password operations
 * @author Anthony Mays
 * @category API Core
 */
class Change_Password extends MY_Controller
{
    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * index
     * controller method that creates the change password page
     * @param None
     * @return html
     */
    public function index()
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSavePasswordChange', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to permanently change password'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_content(0);
        echo $this->parser->parse('administration/change_password_header.html', $this->data, true);
    }

    /*
     * construct_content
     * constructs the change password content template for the index method and related ajax calls
     * @param integer $perform_echo - 0 to return as variable, 1 to echo the page
     * @return html
     */
    public function construct_content($perform_echo = 0)
    {
        $data['users'] = $this->construct_users();
        $data['old_password'] = '<input type="password" name="old_password" value=""/>';
        $data['new_password'] = '<input type="password" name="new_password" value=""/>';
        $data['reenter_password'] = '<input type="password" name="reenter_password" value=""/>';
        if ($this->has_messages())
        {
            $data['messages'] = $this->show_messages();
        }
        else
        {
            $data['messages'] = '';
        }

        if ($perform_echo == 0)
        {
            return $this->parser->parse('administration/change_password_content.html', $data, true);
        }
        else
        {
            echo $this->parser->parse('administration/change_password_content.html', $data, true);
        }
    }

    /*
     * construct_users
     * controller method that constructs the users html drop down
     * @param None
     * @return html
     */
    private function construct_users()
    {
        try
        {
            $username = $this->Session->get_user_name();
            $result = '<select name=users>';
            if ($this->Session->permit('canchangeuserpasswords'))
            {
                $set = $this->Session->DB->query("select user_name from users order by user_name");
            }
            else
            {
                $set = $this->Session->DB->query("select user_name from users where user_id = " . $this->Session->get_user_id() . " order by user_name");
            }
            foreach($set as $key=>$value)
            {
                if ($value['user_name'] == $username)
                {
                    $result .= "<option selected='selected' value='" . $value['user_name'] . "'>" . $value['user_name'] . "</option>";
                }
                else
                {
                    $result .= "<option value='" . $value['user_name'] . "'>" . $value['user_name'] . "</option>";
                }
            }
            $result .= '</select>';
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * process_form
     * controller method that processes form post backs from ajax calls
     * @param None
     * @return html
     */
    public function process_form()
    {
        $users = $this->input->post('users');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $reenter_password = $this->input->post('reenter_password');

        if ($old_password == '')
        {
            $this->send_message('Old Password must be provided');
        }

        if ($new_password == '')
        {
            $this->send_message('Blank passwords are not allowed');
        }

        if ($new_password != $reenter_password)
        {
            $this->send_message('New password and reenter password do not match');
        }

        if (!$this->has_messages())
        {
            $password = $this->Session->Encrypt->encrypt($old_password);
            $sql = "select user_id from users where user_name = '" . $users . "' and password = '" . $password . "'";
            $set = $this->Session->DB->query($sql);
            if (empty($set))
            {
                $this->send_message('Old password is not a valid password for the specified user');
            }
            else
            {
                $password = $this->Session->Encrypt->encrypt($new_password);
                $sql = 'update users set password = ? where user_name = ?';
                $pdo = $this->Session->DB->prepare($sql);
                $this->Session->DB->execute($pdo, array($password, $users));
                $this->send_message('Your changes have been saved successfully.', false);
            }
        }
        echo $this->construct_content(0);
    }
}