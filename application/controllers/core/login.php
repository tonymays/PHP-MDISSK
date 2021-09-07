<?php if (!defined('BASEPATH')) exit("direct access not allowed");
require_once('fw/connection/ldap/ldap.php');
/*
 * Login controller class
 */
class Login extends MY_Controller
{
    private $LDAP;

    /*
     * __construct
     * class constructor that extends CodeIgniter CI_Controller class
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->LDAP = new TLDAP();
    }

    /*
     * index
     * load the main page after login
     * @param None
     * @return html document
     */
    public function index()
    {
        if ($this->login() == false)
        {
            $this->data['version'] = $this->Session->get_version_number();
            $this->data['welcome'] = '';
            $this->data['user_full_name'] = '';
            $this->data['logout_link'] = '';
            $this->data['background'] = $this->parser->parse('core/background.html', $this->data, true);
            echo $this->parser->parse('core/login.html', $this->data, true);
        }
        else
        {
            redirect(SITE_SESSION_NAME . '/core/main');
        }
    }

    /*
     * login
     * logs a user into the site
     * @param None
     * @return None
     */
    public function login()
    {
        if ($this->input->post('username') !== false)
        {
            $ldap_login = $this->LDAP->bind($this->input->post('username'),$this->input->post('password'));
            if ($ldap_login == false)
            {
                $this->data['messages'] = '<p class="login_error_msg">Your LDAP login attempt has failed.  Please see your system administrator if you have questions.</p>';
                return false;
            }
            else
            {
                $username = $this->input->post('username');
                $password = $this->Session->Encrypt->encrypt($this->input->post('password'));
                $sql = "select role_id from users where user_name = ? and password = ? and active = 1 limit 1";
                $pdo = $this->Session->DB->prepare($sql);
                $this->Session->DB->execute($pdo, array($username, $password));
                $set = $this->Session->DB->fetch_all($pdo);
                if (empty($set))
                {
                    $this->data['messages'] = '<p class="login_error_msg">Your login attempt has failed.  Please see your system administrator if you have questions.</p>';
                    return false;
                }
                else
                {
                    $this->Session->end_session();
                    $this->Session->start_session(false);
                    $role_id = $this->get_user_info($username, $password);
                    $this->get_user_modules($role_id);
                    $this->get_user_help_modules($role_id);
                    $this->get_user_permissions($role_id);
                    if (empty($_SESSION[SITE_SESSION_NAME]['permissions']))
                    {
                        $this->data['messages'] = '<p class="login_error_msg">Your login attempt has failed - permission sets not established.  Please see your system administrator if you have questions.</p>';
                        return false;
                    }
                    else
                    {
                        $this->data['messages'] = '';
                        return true;
                    }
                }
            }
        }
        else
        {
            $this->data['messages'] = '';
        }
        return false;
    }

    /*
     * logout
     * kills the session for the current user and load the login page
     * @param None
     * @return html page
     */
    final public function logout()
    {
        $this->Session->end_session();
        $this->index();
    }

    /*
     * get_user_info
     * establish session user information
     * @param string $username
     * @param string $password - encrypted password
     * @return int returns role_id
     */
    private function get_user_info($username, $password)
    {
        $result = '';
        $sql = "select * from users where user_name = ? and password = ? limit 1";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($username, $password));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $_SESSION[SITE_SESSION_NAME]['user']['user_id'] = $set[0]['user_id'];
            $_SESSION[SITE_SESSION_NAME]['user']['user_name'] = $set[0]['user_name'];
            $_SESSION[SITE_SESSION_NAME]['user']['role_id'] = $set[0]['role_id'];
            $_SESSION[SITE_SESSION_NAME]['user']['first_name'] = $set[0]['first_name'];
            $_SESSION[SITE_SESSION_NAME]['user']['last_name'] = $set[0]['last_name'];
            $_SESSION[SITE_SESSION_NAME]['user']['full_name'] = trim($set[0]['first_name']) . ' ' . trim($set[0]['last_name']);
            $_SESSION[SITE_SESSION_NAME]['user']['adr_1'] = $set[0]['adr_1'];
            $_SESSION[SITE_SESSION_NAME]['user']['adr_2'] = $set[0]['adr_2'];
            $_SESSION[SITE_SESSION_NAME]['user']['adr_3'] = $set[0]['adr_3'];
            $_SESSION[SITE_SESSION_NAME]['user']['city'] = $set[0]['city'];
            $_SESSION[SITE_SESSION_NAME]['user']['state'] = $set[0]['state'];
            $_SESSION[SITE_SESSION_NAME]['user']['country'] = $set[0]['country'];
            $_SESSION[SITE_SESSION_NAME]['user']['zip_code'] = $set[0]['zip_code'];
            $_SESSION[SITE_SESSION_NAME]['user']['work_phone'] = $set[0]['work_phone'];
            $_SESSION[SITE_SESSION_NAME]['user']['personal_phone'] = $set[0]['personal_phone'];
            $_SESSION[SITE_SESSION_NAME]['user']['email_address'] = $set[0]['email_address'];
            $_SESSION[SITE_SESSION_NAME]['user']['active'] = $set[0]['active'];
            $_SESSION[SITE_SESSION_NAME]['user']['system_user'] = $set[0]['system_user'];
            $result = $set[0]['role_id'];
        }
        return $result;
    }

    /*
     * get_user_modules
     * establish user modules information
     * @param string $role_id
     * @return None
     */
    private function get_user_modules($role_id)
    {
        $sql = "select a.module, b.submodule_id, b.submodule, b.url, b.help_url, b.css_class, b.single_instance
                from modules a
                    left outer join submodules b on
                        (a.module_id = b.module_id)
                where b.url is not null
                    and b.submodule_id in
                    (
                        select submodule_id
                        from permissions
                        where role_id = ?
                    )
                order by a.module_id, b.submodule";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            foreach($set as $menu=>$item)
            {
                $key = $this->Session->Array->find($_SESSION[SITE_SESSION_NAME]['menu'], 'group', $item['module']);
                if ($key === null)
                {
                    array_push($_SESSION[SITE_SESSION_NAME]['menu'], array('group'=>$item['module'], 'menu_items'=>array()));
                    $key = $this->Session->Array->find($_SESSION[SITE_SESSION_NAME]['menu'], 'group', $item['module']);
                }
                array_push($_SESSION[SITE_SESSION_NAME]['menu'][$key]['menu_items'], array
                (
                    'module'=>$item['module'],
                    'submodule_id'=>$item['submodule_id'],
                    'menu_item'=>$item['submodule'],
                    'url'=>'/' . SITE_SESSION_NAME . '/' . $item['url'],
                    'help_url'=>'/' . SITE_SESSION_NAME . '/' . $item['help_url'],
                    'css_class'=>$item['css_class'],
                    'single_instance'=>$item['single_instance']
                ));
            }
        }
    }

    /*
     * get_user_help_modules
     * establish user help modules information
     * @param string $role_id
     * @return None
     */
    private function get_user_help_modules($role_id)
    {
        $sql = "select a.module, b.submodule_id, b.submodule, b.help_url, b.css_class, b.single_instance
                from modules a
                    left outer join submodules b on
                        (a.module_id = b.module_id)
                where b.help_url is not null
                    and b.submodule_id in
                    (
                        select submodule_id
                        from permissions
                        where role_id = ?
                    )
                order by a.module_id, b.submodule";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            foreach($set as $menu=>$item)
            {
                $key = $this->Session->Array->find($_SESSION[SITE_SESSION_NAME]['help_menu'], 'group', $item['module']);
                if ($key === null)
                {
                    array_push($_SESSION[SITE_SESSION_NAME]['help_menu'], array('group'=>$item['module'], 'menu_items'=>array()));
                    $key = $this->Session->Array->find($_SESSION[SITE_SESSION_NAME]['help_menu'], 'group', $item['module']);
                }
                array_push($_SESSION[SITE_SESSION_NAME]['help_menu'][$key]['menu_items'], array
                (
                    'module'=>$item['module'],
                    'submodule_id'=>$item['submodule_id'],
                    'menu_item'=>$item['submodule'],
                    'url'=>'/' . SITE_SESSION_NAME . '/' . $item['help_url'],
                    'help_url'=>'',
                    'css_class'=>'help_window',
                    'single_instance'=>$item['single_instance']
                ));
            }
        }
    }

    /*
     * get_role_permissions
     * establish permissions based on the user's role id
     * @param string $role_id
     * @return None
     */
    private function get_user_permissions($role_id)
    {
        $sql = "select tag
                from submodules
                where submodule_id in
                (
                    select submodule_id
                    from permissions
                    where role_id = ?
                )";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($role_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            foreach($set as $key=>$value)
            {
                array_push($_SESSION[SITE_SESSION_NAME]['permissions'], $value['tag']);
            }
        }
    }
}
?>