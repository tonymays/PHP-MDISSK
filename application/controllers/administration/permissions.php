<?php if (!defined('BASEPATH')) exit("direct access not allowed");

/*
 * Permissions
 * this controller is responsible for managing site role permissions
 */
class Permissions extends MY_Controller
{
    /*
     * __construct
     * page constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * index
     * constructs and loads the roles form
     * @param None
     * @return html page
     */
    public function index()
    {
        $set = $this->Session->DB->query("select role from roles order by 1");
        echo $this->construct_permissions($set[0]['role']);
    }

    /*
     * construct_permissions
     * constructs the permission page for the specified role
     * @param string $role
     * @return html page
     */
    public function construct_permissions($role)
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSave', 'text'=>'Save', 'img'=>'assets/images/disk_save.png', 'title'=>'Click to save role permissions'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['roles'] = $this->construct_roles($role);
        $this->data['content'] = $this->construct_permissions_content($role, 0);
        return $this->parser->parse('administration/view_permissions_header.html', $this->data, true);
    }

    public function construct_permissions_content($role, $perform_echo = 0)
    {
        $role = str_replace('%20', ' ', $role);
        $data = array('rows'=>array());
        $data['role'] = $role;
        $data['rows'] = $this->get_permissions($role);
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
            return $this->parser->parse('administration/view_permissions_content.html', $data, true);
        }
        else
        {
            echo $this->parser->parse('administration/view_permissions_content.html', $data, true);
        }
    }

    /*
     * process_form
     * processes all role forms on a post back
     * @param None
     * @return html page
     */
    public function process_form()
    {
        $role_id = $this->get_role_id($this->input->post('roles'));
        $sql = "delete from permissions where role_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $permits = $this->input->post('permit');
        foreach($permits as $key=>$value)
        {
            $permission_id = $this->Session->String->slice_before($value, '-');
            $sql = "insert into permissions select ?, ?";
            $pdo = $this->Session->DB->prepare($sql);
            $this->Session->DB->execute($pdo, array($role_id, $permission_id));
        }
        $this->send_message("Your changes have been successfully saved", false);
        echo $this->construct_permissions_content($this->input->post('roles'), 1);
    }

    /*
     * construct_roles
     * construct html drop down of roles
     * @param $selected_value
     * @return html select
     */
    private function construct_roles($selected_value)
    {
        $result = '<select name="roles" class="permissions_roles_drop_down">';
        $set = $this->Session->DB->query("select role from roles order by role");
        foreach($set as $key=>$value)
        {
            if ($selected_value == $value['role'])
            {
                $result .= "<option selected='selected' value='" . $value['role'] . "'>" . $value['role'] . "</option>";
            }
            else
            {
                $result .= "<option value='" . $value['role'] . "'>" . $value['role'] . "</option>";
            }
        }
        $result .= '</select>';
        return $result;
    }

    /*
     * get_permissions
     * returns role permissions as an array
     * @param string $role
     * @return array
     */
    private function get_permissions($role)
    {
        $result = array();
        $tmp = array();
        $module = '';
        $role_id = $this->get_role_id($role);

        $sql = "select a.submodule_id, a.module_id, c.module, a.submodule,
                    a.description, b.role_id, d.role,
                    case when b.submodule_id is null then 'No' else 'Yes' end as permit,
                    case when a.url is null then 'Page Permission' else 'Feature Permission' end as permission_type
                from submodules a
                    left outer join permissions b on (b.role_id = " . $role_id . " and a.submodule_id = b.submodule_id)
                    left outer join modules c on (a.module_id = c.module_id)
                    left outer join roles d on (b.role_id = d.role_id)
                order by c.module, a.submodule";
        $set = $this->Session->DB->query($sql);
        foreach($set as $key=>$value)
        {
            if ($module != $value['module'])
            {
                if (!empty($tmp))
                {
                    array_push($result, $tmp);
                }
                $module = $value['module'];
                $tmp = array('module'=>$value['module'], 'submodules'=>array());
                array_push($tmp['submodules'],
                    array
                    (
                        'submodule_id'=>$value['submodule_id'],
                        'module_id'=>$value['module_id'],
                        'module'=>$value['module'],
                        'submodule'=>$value['submodule'],
                        'description'=>$value['description'],
                        'role_id'=>$value['role_id'],
                        'role'=>$value['role'],
                        'permission_type'=>$value['permission_type'],
                        'permit'=>(($value['permit'] == 'Yes')
                            ? '<input type="checkbox" name="permit[]" value="' . $value['submodule_id'] . '-' . $value['submodule'] . '" checked />'
                            : '<input type="checkbox" name="permit[]" value="' . $value['submodule_id'] . '-' . $value['submodule'] . '" />')
                    ));
            }
            else
            {
                array_push($tmp['submodules'],
                    array
                    (
                        'submodule_id'=>$value['submodule_id'],
                        'module_id'=>$value['module_id'],
                        'module'=>$value['module'],
                        'submodule'=>$value['submodule'],
                        'description'=>$value['description'],
                        'role_id'=>$value['role_id'],
                        'role'=>$value['role'],
                        'permission_type'=>$value['permission_type'],
                        'permit'=>(($value['permit'] == 'Yes')
                            ? '<input type="checkbox" name="permit[]" value="' . $value['submodule_id'] . '-' . $value['submodule'] . '" checked />'
                            : '<input type="checkbox" name="permit[]" value="' . $value['submodule_id'] . '-' . $value['submodule'] . '" />')
                    ));
            }
        }
        array_push($result, $tmp);
        return $result;
    }

    /*
     * get_role_id
     * returns the role_id for the specified role
     * @param string $role
     * @return integer
     */
    private function get_role_id($role)
    {
        $sql = "select role_id from roles where role = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role));
        $set = $this->Session->DB->fetch_all($pdo);
        return $set[0]['role_id'];
    }
}
?>
