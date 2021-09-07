<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * Roles
 * Base controller class for role based operations
 * @author Anthony Mays
 * @category API Core
 */
class Roles extends MY_Controller
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
     * controller method that creates the roles view page
     * @param None
     * @return html
     */
    public function index()
    {
        $toolbar = array();
        if ($this->Session->permit('canaddrole'))
        {
            array_push($toolbar, array('name'=>'btnAddRole', 'text'=>'Add', 'img'=>'assets/images/circle_plus.png', 'title'=>'Click to add role'));
        }
        array_push($toolbar, array('name'=>'btnSaveRoles', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save role changes'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_roles_content(0);
        echo $this->parser->parse('administration/view_roles_header.html', $this->data, true);
    }

    /*
     * construct_roles_content
     * constructs the roles content template for the index method and related ajax calls
     * @param integer $perform_echo - 0 to return as variable, 1 to echo the page
     * @return html
     */
    public function construct_roles_content($perform_echo = 0)
    {
        $result = array('rows'=>array(), 'messages'=>'');
        $sql = "select role_id, role, description,
                    case when system_role = 1
                        then 'Yes'
                        else 'No'
                    end as system_role
                from roles
                order by role";
        $rows = $this->Session->DB->query($sql);
        if (empty($rows))
        {
            $rows = array();
        }
        else
        {
            foreach($rows as $key=>$value)
            {
                if ($this->Session->permit('caneditrole') == false)
                {
                    $rows[$key]['role'] = $value['role'];
                }
                else
                {
                    $rows[$key]['role'] = '<a href="javascript:RolesObj.edit(' . $value['role_id'] . ')">' . $value['role'] . '</a>';
                }
                $rows[$key]['actions'] = $this->get_actions($value['role_id']);
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
            return $this->parser->parse('administration/view_roles_content.html', $result, true);
        }
        else
        {
            echo $this->parser->parse('administration/view_roles_content.html', $result, true);
        }
    }

    /*
     * add_role
     * controller method that creates the add role page
     * @param None
     * @return html
     */
    public function add_role()
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSave', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save role'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_add_role_content();
        echo $this->parser->parse('administration/add_role_header.html', $this->data, true);
    }

    /*
     * construct_add_role_content
     * controller method that creates the add role content section and is reused during form processing and post backs
     * @param None
     * @return html
     */
    private function construct_add_role_content()
    {
        if ($this->input->post('role') !== false)
        {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
        }
        else
        {
            $role = '';
            $description = '';
        }
        $this->data['role'] = '<input type="text" name="role" value="' . $role . '"/>';
        $this->data['description'] = '<input type="text" name="description" value="' . $description . '"/>';
        if ($this->has_messages())
        {
            $this->data['messages'] = $this->show_messages();
        }
        else
        {
            $this->data['messages'] = '';
        }
        return $this->parser->parse('administration/add_role_content.html', $this->data, true);
    }

    /*
     * edit_role
     * controller method that creates the edit role page
     * @param integer $role_id - the role id the page is base upon
     * @return html
     */
    public function edit_role($role_id)
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSave', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save role'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['content'] = $this->construct_edit_role_content($role_id);
        echo $this->parser->parse('administration/edit_role_header.html', $this->data, true);
    }

    /*
     * construct_edit_role_content
     * controller method that creates the edit role content section and is reused during form processing and post backs
     * @param integer $role_id - the role id the page is base upon
     * @return html
     */
    private function construct_edit_role_content($role_id)
    {
        if ($this->input->post('role') !== false)
        {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
        }
        else
        {
            $sql = "select role, description from roles where role_id = ?";
            $pdo = $this->Session->DB->prepare($sql);
            $this->Session->DB->execute($pdo, array($role_id));
            $set = $this->Session->DB->fetch_all($pdo);
            if (empty($set))
            {
                $role = '';
                $description = '';
            }
            else
            {
                $role = $set[0]['role'];
                $description = $set[0]['description'];
            }
        }
        $this->data['role_id'] = '<input type="text" readonly name="role_id" value="' . $role_id . '"/>';
        $this->data['role'] = '<input type="text" name="role" value="' . $role . '"/>';
        $this->data['description'] = '<input type="text" name="description" value="' . $description . '"/>';
        if ($this->has_messages())
        {
            $this->data['messages'] = $this->show_messages();
        }
        else
        {
            $this->data['messages'] = '';
        }
        return $this->parser->parse('administration/edit_role_content.html', $this->data, true);
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
            case 'roles_form':
                $post = &$this->input->post('actions');
                if (!empty($post))
                {
                    foreach($post as $key=>$value)
                    {
                        $actions = explode(':', $value);
                        $role_id = $this->Session->String->slice_before($actions[0], '-');
                        if ($actions[1] == 'Delete' && !$this->is_system_role($role_id))
                        {
                            if ($this->has_users($role_id))
                            {
                                $role = $this->get_role($role_id);
                                $this->send_message('Role ' + $role + ' cannot be deleted because it has users currently assigned');
                            }
                            else
                            {
                                $sql = "delete from roles where role_id = ?";
                                $pdo = $this->Session->DB->prepare($sql);
                                $this->Session->DB->execute($pdo, array($role_id));
                            }
                        }
                    }
                }
                $this->send_message('Your changes have been saved successfully.', false);
                echo $this->construct_roles_content();
                break;
            case 'add_role_form':
                $role = $this->input->post('role');
                $description = $this->input->post('description');
                if ($role == '')
                {
                    $this->send_message('Role is a required field', true);
                }
                if ($description == '')
                {
                    $this->send_message('Description is a required field', true);
                }
                if ($this->role_exists($role))
                {
                    $this->send_message('Role ' . $role . ' already exist', true);
                }
                if (!$this->has_messages())
                {
                    $sql = "insert into roles select null, ?, ?, 0";
                    $pdo = $this->Session->DB->prepare($sql);
                    $this->Session->DB->execute($pdo, array($role, $description));
                    $this->send_message('Your changes have been saved successfully', false);
                }
                echo $this->construct_add_role_content();
                break;
            case 'edit_role_form':
                $role_id = $this->input->post('role_id');
                $role = $this->input->post('role');
                $description = $this->input->post('description');
                if ($role == '')
                {
                    $this->send_message('Role is a required field', true);
                }
                if ($description == '')
                {
                    $this->send_message('Description is a required field', true);
                }
                if (!$this->role_id_exists($role_id))
                {
                    $this->send_message('Role ID ' . $role_id . ' does not currently exist', true);
                }
                if ($this->role_exists($role) && $this->get_role_id($role) != $role_id)
                {
                    $this->send_message('Role ' . $role . ' already exists', true);
                }
                if (!$this->has_messages())
                {
                    $sql = "update roles set role=?, description=? where role_id = ?";
                    $pdo = $this->Session->DB->prepare($sql);
                    $this->Session->DB->execute($pdo, array($role, $description, $role_id));
                    $this->send_message('Your changes have been saved successfully', false);
                }
                echo $this->construct_edit_role_content($role_id);
                break;
        }
    }

    /*
     * role_id_exists
     * returns true if the role id specified exists; otherwise, returns false
     * @param integer $role_id - the role id sought
     * @return bool
     */
    private function role_id_exists($role_id)
    {
        $sql = "select role from roles where role_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        return (!empty($set));
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
     * is_system_role
     * returns true if the role id specified is a system roles; otherwise, returns false
     * @param integer $role_id - the role id sought
     * @return bool
     */
    private function is_system_role($role_id)
    {
        $result = false;
        $sql = "select system_role from roles where role_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = ($set[0]['system_role'] == 1);
        }
        return $result;
    }

    /*
     * get_actions
     * returns an html drop down of available actions for the role specified
     * @param integer $role_id - the role id sought
     * @return html drop down
     */
    private function get_actions($role_id)
    {
        $html  = '<select class="option_actions" name="actions[]">';
        $role = $this->get_role($role_id);
        $html .= '<option value="' . $role_id . '-' . $role . ':None">None</option>';
        if ($this->Session->permit('candeleterole') && $this->has_users($role_id) == false)
        {
            $html .= '<option value="' . $role_id . '-' . $role . ':Delete">Delete</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /*
     * has_users
     * returns true if the role id specified has users assigned; otherwise, returns false
     * @param integer $role_id - the role id sought
     * @return bool
     */
    private function has_users($role_id)
    {
        $set = $this->Session->DB->query("select * from users where role_id = $role_id limit 1");
        return (!empty($set));
    }

    private function get_role($role_id)
    {
        $result = '';
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
}