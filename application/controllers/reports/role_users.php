<?php if (!defined('BASEPATH')) exit("direct access not allowed");

/*
 * RoleUsers
 * this controller is responsible for reporting on which user is assigned to which role
 */
class Role_Users extends MY_Controller
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
        echo $this->construct_role_users($set[0]['role']);
    }

    /*
     * construct_permissions
     * constructs the permission page for the specified role
     * @param string $role
     * @return html page
     */
    public function construct_role_users($role)
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnRefresh', 'text'=>'Refresh', 'img'=>'assets/images/refresh.png', 'title'=>'Click to refresh report'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['roles'] = $this->construct_roles($role);
        $this->data['content'] = $this->construct_role_users_content($role, 0);
        return $this->parser->parse('reports/view_role_users_header.html', $this->data, true);
    }

    public function construct_role_users_content($role, $perform_echo = 0)
    {
        $role = str_replace('%20', ' ', $role);
        $data = array('rows'=>array());
        $data['role'] = $role;
        $data['rows'] = $this->get_role_users($role);
        if ($perform_echo == 0)
        {
            return $this->parser->parse('reports/view_role_users_content.html', $data, true);
        }
        else
        {
            echo $this->parser->parse('reports/view_role_users_content.html', $data, true);
        }
    }

    /*
     * construct_roles
     * construct html drop down of roles
     * @param $selected_value
     * @return html select
     */
    private function construct_roles($selected_value)
    {
        $result = '<select name="roles" class="role_users_roles_drop_down">';
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
     * get_role_users
     * returns role users as an array
     * @param string $role
     * @return array
     */
    private function get_role_users($role)
    {
        $result = array();
        $tmp = array('users'=>array());
        $role_id = $this->get_role_id($role);
        $sql = "select user_id, user_name, first_name, last_name, email_address
                from users
                where role_id = " . $role_id .
                " order by user_name";
        $set = $this->Session->DB->query($sql);
        foreach($set as $key=>$value)
        {
            if ($this->Session->permit('canedituser') == false)
            {
                $user_name = $value['user_name'];
            }
            else
            {
                $user_name = '<a href="javascript:UsersObj.edit(' . $value['user_id'] . ')">' . $value['user_name'] . '</a>';
            }
            array_push($tmp['users'],
                array
                (
                    'user_name'=>$user_name,
                    'first_name'=>$value['first_name'],
                    'last_name'=>$value['last_name'],
                    'email_address'=>$value['email_address']
                ));
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
