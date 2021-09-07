<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * Users
 * Base controller class for role based operations
 * @author Anthony Mays
 * @category API Core
 */
class Users extends MY_Controller
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
     * controller method that creates the users view page
     * @param None
     * @return html
     */
    public function index()
    {
        $toolbar = array();
        if ($this->Session->permit('canadduser'))
        {
            array_push($toolbar, array('name'=>'btnAddUser', 'text'=>'Add', 'img'=>'assets/images/circle_plus.png', 'title'=>'Click to add user'));
        }
        array_push($toolbar, array('name'=>'btnSaveUsers', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save user changes'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_users_content(0);
        echo $this->parser->parse('administration/view_users_header.html', $this->data, true);
    }

    /*
     * construct_users_content
     * constructs the users content template for the index method and related ajax calls
     * @param integer $perform_echo - 0 to return as variable, 1 to echo the page
     * @return html
     */
    public function construct_users_content($perform_echo = 0)
    {
        $result = array('rows'=>array(), 'messages'=>'');
        $sql = "select a.user_id, a.user_name, a.password, a.role_id,
					b.role, a.first_name, a.last_name, a.adr_1, a.adr_2,
					a.adr_3, a.city, a.state, a.country, a.zip_code,
					a.work_phone, a.personal_phone, a.email_address,
					concat(a.first_name, ' ', a.last_name) as full_name,
					case when active = 1
						then 'Yes'
						else 'No'
					end as active,
					case when system_user = 1
						then 'Yes'
						else 'No'
					end as system_user
				from users a
					left outer join roles b on (a.role_id = b.role_id)
					order by a.last_name, a.first_name";
        $rows = $this->Session->DB->query($sql);
        if (empty($rows))
        {
            $rows = array();
        }
        else
        {
            foreach($rows as $key=>$value)
            {
                if ($this->Session->permit('canedituser') == false)
                {
                    $rows[$key]['full_name'] = $value['full_name'];
                }
                else
                {
                    $rows[$key]['full_name'] = '<a href="javascript:UsersObj.edit(' . $value['user_id'] . ')">' . $value['full_name'] . '</a>';
                }
                $rows[$key]['actions'] = $this->get_actions($value['user_id'], $value['system_user'], $value['active']);
            }
        }

        $result['rows'] = $rows;
        if ($this->has_messages())
        {
            $result['messages'] = $this->show_messages();
        }
        else
        {
            $result['messages'] = '';
        }

        if ($perform_echo == 0)
        {
            return $this->parser->parse('administration/view_users_content.html', $result, true);
        }
        else
        {
            echo $this->parser->parse('administration/view_users_content.html', $result, true);
        }
    }

    /*
     * add_user
     * controller method that creates the add user page
     * @param None
     * @return html
     */
    public function add_user()
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSave', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save user'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_add_user_content();
        echo $this->parser->parse('administration/add_user_header.html', $this->data, true);
    }

    /*
     * construct_add_user_content
     * controller method that creates the add user content section and is reused during form processing and post backs
     * @param None
     * @return html
     */
    private function construct_add_user_content()
    {
        if ($this->input->post('user_name') !== false)
        {
            $user_name = $this->input->post('user_name');
            $password = $this->input->post('password');
            $reenter_password = $this->input->post('reenter_password');
            $role = $this->construct_roles($this->input->post('roles'));
            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            $adr_1 = $this->input->post('adr_1');
            $adr_2 = $this->input->post('adr_2');
            $adr_3 = $this->input->post('adr_3');
            $city = $this->input->post('city');
            $state = $this->input->post('state');
            $country = $this->input->post('country');
            $zip_code = $this->input->post('zip_code');
            $work_phone = $this->input->post('work_phone');
            $personal_phone = $this->input->post('personal_phone');
            $email_address = $this->input->post('email_address');
        }
        else
        {
            $user_name = '';
            $password = '';
            $reenter_password = '';
            $role = $this->construct_roles('');
            $first_name = '';
            $last_name = '';
            $adr_1 = '';
            $adr_2 = '';
            $adr_3 = '';
            $city = '';
            $state = '';
            $country = '';
            $zip_code = '';
            $work_phone = '';
            $personal_phone = '';
            $email_address = '';
        }
        $this->data['user_name'] = '<input type="text" name="user_name" value="' . $user_name . '"/>';
        $this->data['password'] = '<input type="password" name="password" value="' . $password . '"/>';
        $this->data['reenter_password'] = '<input type="password" name="reenter_password" value="' . $reenter_password . '"/>';
        $this->data['role'] = $role;
        $this->data['first_name'] = '<input type="text" name="first_name" value="' . $first_name . '"/>';
        $this->data['last_name'] = '<input type="text" name="last_name" value="' . $last_name . '"/>';
        $this->data['adr_1'] = '<input type="text" name="adr_1" class="large_width" value="' . $adr_1 . '"/>';
        $this->data['adr_2'] = '<input type="text" name="adr_2" class="large_width" value="' . $adr_2 . '"/>';
        $this->data['adr_3'] = '<input type="text" name="adr_3" class="large_width" value="' . $adr_3 . '"/>';
        $this->data['city'] = '<input type="text" name="city" class="large_width" value="' . $city . '"/>';
        $this->data['state'] = '<input type="text" name="state" value="' . $state . '"/>';
        $this->data['country'] = '<input type="text" name="country" class="large_width" value="' . $country . '"/>';
        $this->data['zip_code'] = '<input type="text" name="zip_code" value="' . $zip_code . '"/>';
        $this->data['work_phone'] = '<input type="text" name="work_phone" value="' . $work_phone . '"/>';
        $this->data['personal_phone'] = '<input type="text" name="personal_phone" value="' . $personal_phone . '"/>';
        $this->data['email_address'] = '<input type="text" name="email_address" class="large_width" value="' . $email_address . '"/>';
        if ($this->has_messages())
        {
            $this->data['messages'] = $this->show_messages();
        }
        else
        {
            $this->data['messages'] = '';
        }
        return $this->parser->parse('administration/add_user_content.html', $this->data, true);
    }

    /*
     * construct_roles
     * controller method that constructs the roles drop drop
     * @param string $selected_value - the name of the option value to select
     * @return html
     */
    private function construct_roles($selected_value)
    {
        try
        {
            $result = '<select name=roles>';
            $set = $this->Session->DB->query("select role from roles order by role");
            foreach($set as $key=>$value)
            {
                if ($selected_value == $value['role'])
                {
                    $result .= "<option selected='selected' class='option_actions' value='" . $value['role'] . "'>" . $value['role'] . "</option>";
                }
                else
                {
                    $result .= "<option class='option_actions' value='" . $value['role'] . "'>" . $value['role'] . "</option>";
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
     * edit_user
     * controller method that creates the edit user page
     * @param integer $user_id - the user id the page is base upon
     * @return html
     */
    public function edit_user($user_id)
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSave', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save user'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_edit_user_content($user_id);
        echo $this->parser->parse('administration/edit_user_header.html', $this->data, true);
    }

    /*
     * construct_edit_user_content
     * controller method that creates the edit user content section and is reused during form processing and post backs
     * @param integer $user_id - the user id the page is base upon
     * @return html
     */
    private function construct_edit_user_content($user_id)
    {
        if ($this->input->post('user_name') !== false)
        {
            $user_name = $this->input->post('user_name');
            $role = $this->construct_roles($this->input->post('roles'));
            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            $adr_1 = $this->input->post('adr_1');
            $adr_2 = $this->input->post('adr_2');
            $adr_3 = $this->input->post('adr_3');
            $city = $this->input->post('city');
            $state = $this->input->post('state');
            $country = $this->input->post('country');
            $zip_code = $this->input->post('zip_code');
            $work_phone = $this->input->post('work_phone');
            $personal_phone = $this->input->post('personal_phone');
            $email_address = $this->input->post('email_address');
        }
        else
        {
            $sql = "select * from users where user_id = ?";
            $pdo = $this->Session->DB->prepare($sql);
            $this->Session->DB->execute($pdo, array($user_id));
            $set = $this->Session->DB->fetch_all($pdo);
            $user_name = $set[0]['user_name'];
            $role = $this->construct_roles($this->get_role($set[0]['role_id']));
            $first_name = $set[0]['first_name'];;
            $last_name = $set[0]['last_name'];
            $adr_1 = $set[0]['adr_1'];
            $adr_2 = $set[0]['adr_2'];
            $adr_3 = $set[0]['adr_3'];
            $city = $set[0]['city'];
            $state = $set[0]['state'];
            $country = $set[0]['country'];
            $zip_code = $set[0]['zip_code'];
            $work_phone = $set[0]['work_phone'];
            $personal_phone = $set[0]['personal_phone'];
            $email_address = $set[0]['email_address'];
        }
        $this->data['user_id'] = '<input type="text" name="user_id" readonly value="' . $user_id . '"/>';
        $this->data['user_name'] = '<input type="text" name="user_name" value="' . $user_name . '"/>';
        $this->data['role'] = $role;
        $this->data['first_name'] = '<input type="text" name="first_name" value="' . $first_name . '"/>';
        $this->data['last_name'] = '<input type="text" name="last_name" value="' . $last_name . '"/>';
        $this->data['adr_1'] = '<input type="text" name="adr_1" class="large_width" value="' . $adr_1 . '"/>';
        $this->data['adr_2'] = '<input type="text" name="adr_2" class="large_width" value="' . $adr_2 . '"/>';
        $this->data['adr_3'] = '<input type="text" name="adr_3" class="large_width" value="' . $adr_3 . '"/>';
        $this->data['city'] = '<input type="text" name="city" class="large_width" value="' . $city . '"/>';
        $this->data['state'] = '<input type="text" name="state" value="' . $state . '"/>';
        $this->data['country'] = '<input type="text" name="country" class="large_width" value="' . $country . '"/>';
        $this->data['zip_code'] = '<input type="text" name="zip_code" value="' . $zip_code . '"/>';
        $this->data['work_phone'] = '<input type="text" name="work_phone" value="' . $work_phone . '"/>';
        $this->data['personal_phone'] = '<input type="text" name="personal_phone" value="' . $personal_phone . '"/>';
        $this->data['email_address'] = '<input type="text" name="email_address" class="large_width" value="' . $email_address . '"/>';
        if ($this->has_messages())
        {
            $this->data['messages'] = $this->show_messages();
        }
        else
        {
            $this->data['messages'] = '';
        }
        return $this->parser->parse('administration/edit_user_content.html', $this->data, true);
    }

    /*
     * process_form
     * controller method that processes form post backs from ajax calls
     * @param integer $form_name - the name of the form to process
     * @return html
     */
    public function process_form($form_name)
    {
        switch ($form_name)
        {
            case 'users_form':
                $post = &$this->input->post('actions');
                if (!empty($post))
                {
                    foreach($post as $key=>$value)
                    {
                        $actions = explode(':', $value);
                        $user_id = $this->Session->String->slice_before($actions[0], '-');
                        if ($actions[1] == 'Delete' && !$this->is_system_user($user_id))
                        {
                            $sql = "delete from users where user_id = ?";
                            $pdo = $this->Session->DB->prepare($sql);
                            $this->Session->DB->execute($pdo, array($user_id));
                        }
                        else if ($actions[1] == 'Deactivate')
                        {
                            $sql = "update users set active = 0 where user_id = ?";
                            $pdo = $this->Session->DB->prepare($sql);
                            $this->Session->DB->execute($pdo, array($user_id));
                        }
                        else if ($actions[1] == 'Activate')
                        {
                            $sql = "update users set active = 1 where user_id = ?";
                            $pdo = $this->Session->DB->prepare($sql);
                            $this->Session->DB->execute($pdo, array($user_id));
                        }
                    }
                }
                $this->send_message('Your changes have been saved successfully.', false);
                echo $this->construct_users_content(0);
                break;
            case 'add_user_form':
                $user_name = $this->input->post('user_name');
                $password = $this->input->post('password');
                $reenter_password = $this->input->post('reenter_password');
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $adr_1 = $this->input->post('adr_1');
                $adr_2 = $this->input->post('adr_2');
                $adr_3 = $this->input->post('adr_3');
                $city = $this->input->post('city');
                $state = $this->input->post('state');
                $country = $this->input->post('country');
                $zip_code = $this->input->post('zip_code');
                $work_phone = $this->input->post('work_phone');
                $personal_phone = $this->input->post('personal_phone');
                $email_address = $this->input->post('email_address');
                if ($user_name == '')
                {
                    $this->send_message('User Name is a required field', true);
                }
                else if ($this->user_name_exists($user_name))
                {
                    $this->send_message('User name ' . $user_name . ' already exists');
                }

                if ($password == '')
                {
                    $this->send_message('Password is a required field', true);
                }

                if ($reenter_password == '')
                {
                    $this->send_message('Reenter Password is a required field', true);
                }

                if ($password != $reenter_password)
                {
                    $this->send_message('Password and Reenter Password must be the same', true);
                }

                if ($first_name == '')
                {
                    $this->send_message('First Name is a required field', true);
                }

                if ($last_name == '')
                {
                    $this->send_message('Last Name is a required field', true);
                }

                if ($email_address == '')
                {
                    $this->send_message('Email Address is a required field', true);
                }

                if ($this->role_exists($this->input->post('roles')))
                {
                    $role_id = $this->get_role_id($this->input->post('roles'));
                }
                else
                {
                    $this->send_message('Role ' . $this->input->post('roles') . ' does not currently exist', true);
                }

                if (!$this->has_messages())
                {
                    $password = $this->Session->Encrypt->encrypt($password);
                    $sql = "insert into users select null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0";
                    $pdo = $this->Session->DB->prepare($sql);
                    $this->Session->DB->execute($pdo, array($user_name, $password, $role_id, $first_name, $last_name,
                        $adr_1, $adr_2, $adr_3, $city, $state, $country, $zip_code, $work_phone, $personal_phone,
                        $email_address));
                    $this->send_message('Your changes have been saved successfully', false);
                }
                echo $this->construct_add_user_content();
                break;
            case 'edit_user_form':
                $user_id = $this->input->post('user_id');
                $user_name = $this->input->post('user_name');
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $adr_1 = $this->input->post('adr_1');
                $adr_2 = $this->input->post('adr_2');
                $adr_3 = $this->input->post('adr_3');
                $city = $this->input->post('city');
                $state = $this->input->post('state');
                $country = $this->input->post('country');
                $zip_code = $this->input->post('zip_code');
                $work_phone = $this->input->post('work_phone');
                $personal_phone = $this->input->post('personal_phone');
                $email_address = $this->input->post('email_address');
                if (!$this->user_id_exists($user_id))
                {
                    $this->send_message('User ID ' . $user_id . ' does not currently exist');
                }

                if ($user_name == '')
                {
                    $this->send_message('User Name is a required field', true);
                }
                else if ($this->user_name_exists($user_name) && $this->get_user_id($user_name) != $user_id)
                {
                    $this->send_message('User name ' . $user_name . ' already exist');
                }

                if ($first_name == '')
                {
                    $this->send_message('First Name is a required field', true);
                }

                if ($last_name == '')
                {
                    $this->send_message('Last Name is a required field', true);
                }

                if ($email_address == '')
                {
                    $this->send_message('Email Address is a required field', true);
                }

                if ($this->role_exists($this->input->post('roles')))
                {
                    $role_id = $this->get_role_id($this->input->post('roles'));
                }
                else
                {
                    $role_id = -1;
                    $this->send_message('Role ' . $this->input->post('roles') . ' does not currently exist', true);
                }
                if (!$this->has_messages())
                {
                    $sql = "update users set user_name = ?,	role_id = ?, first_name = ?, last_name = ?, adr_1 = ?,
								adr_2 = ?, adr_3 = ?, city = ?,	state = ?, country = ?,	zip_code = ?,
							    work_phone = ?,	personal_phone = ?, email_address = ?
                            where user_id = ?";
                    $pdo = $this->Session->DB->prepare($sql);
                    $this->Session->DB->execute($pdo, array($user_name, $role_id, $first_name, $last_name, $adr_1,
                        $adr_2, $adr_3, $city, $state, $country, $zip_code, $work_phone, $personal_phone,
                        $email_address, $user_id));
                    $this->send_message('Your changes have been saved successfully', false);
                }
                echo $this->construct_edit_user_content($user_id);
                break;
        }
    }

    /*
     * user_id_exists
     * returns true if the user id specified exists; otherwise, returns false
     * @param integer $user_id - the user id sought
     * @return bool
     */
    private function user_id_exists($user_id)
    {
        $sql = "select user_name from users where user_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $set = $this->Session->DB->fetch_all($pdo);
        return (!empty($set));
    }

    /*
     * user_name_exists
     * returns true if the user name specified exists; otherwise, returns false
     * @param integer $user_name - the user name sought
     * @return bool
     */
    private function user_name_exists($user_name)
    {
        $sql = "select user_id from users where user_name = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_name));
        $set = $this->Session->DB->fetch_all($pdo);
        return (!empty($set));
    }

    /*
     * get_user_id
     * returns the user id for the specified user name; otherwise, returns an ugly large number
     * @param string $user_name - the user name sought
     * @return bool
     */
    private function get_user_id($user_name)
    {
        $result = '-1000000000';
        $sql = "select user_id from users where user_name = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_name));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = $set[0]['user_id'];
        }
        return $result;
    }

    /*
     * get_user
     * returns the user name for the specified user id; otherwise, returns an empty string
     * @param string $user_id - the user id sought
     * @return bool
     */
    private function get_user($user_id)
    {
        $result = '-1000000000';
        $sql = "select user_name from users where user_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = $set[0]['user_name'];
        }
        return $result;
    }

    /*
     * role_exists
     * returns true if the role specified exists; otherwise, returns false
     * @param integer $role - the role sought
     * @return bool
     */
    private function role_exists($role)
    {
        $sql = "select role_id from roles where role = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role));
        $set = $this->Session->DB->fetch_all($pdo);
        return (!empty($set));
    }

    /*
     * get_role_id
     * returns the role id for the specified role, otherwise, returns a big negative number if not found
     * @param integer $role - the role sought
     * @return integer
     */
    private function get_role_id($role)
    {
        $result = '-1000000000';
        $sql = "select role_id from roles where role = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = $set[0]['role_id'];
        }
        return $result;
    }

    /*
     * get_role
     * returns the role for the specified role id, otherwise, returns a big negative number if not found
     * @param integer $role_id - the role_id sought
     * @return integer
     */
    private function get_role($role_id)
    {
        $result = '-1000000000';
        $sql = "select role from roles where role_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = $set[0]['role'];
        }
        return $result;
    }

    /*
     * is_system_user
     * returns true if the user id specified is a system roles; otherwise, returns false
     * @param integer $user_id - the user id sought
     * @return bool
     */
    private function is_system_user($user_id)
    {
        $result = false;
        $sql = "select system_user from users where user_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = ($set[0]['system_user'] == 1);
        }
        return $result;
    }

    /*
     * get_actions
     * returns an html drop down of available actions for the user id specified
     * @param integer $user_id - the user id sought
     * @param integer $system_user - a flag indicating if the user is a system user
     * @param integer $active - a flag indicating if the user account is active
     * @return html drop down
     */
    private function get_actions($user_id, $system_user, $active)
    {
        try
        {
            $html  = '<select class="option_actions" name="actions[]">';
            $user_name = $this->get_user($user_id);
            $html .= '<option value="' . $user_id . '-' . $user_name . ':None">None</option>';
            if ($this->Session->permit('candeleteuser') && $system_user == 'No')
            {
                $html .= '<option value="' . $user_id . '-' . $user_name . ':Delete">Delete</option>';
            }
            if ($this->Session->permit('canedituser') && $system_user == 'No')
            {
                if ($active == 'Yes')
                {
                    $html .= '<option value="' . $user_id . '-' . $user_name . ':Deactivate">Deactivate</option>';
                }
                else
                {
                    $html .= '<option value="' . $user_id . '-' . $user_name . ':Activate">Activate</option>';
                }
            }

            $html .= '</select>';
            return $html;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}