<?php
require_once('api/api.php');
require_once('fw/connection/session/http_session.php');

/*
 * TSession
 * Session API class
 * @author Anthony Mays
 * @category API
 */
class TSessionAPI extends TAPI
{
    /*
     * holds the TSession Object
     * @public object $session
     */
    public $HTTPSession;

    /*
     * class constructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
        $this->HTTPSession = new THTTPSession();
    }

    /*
     * class destructor
     * @param None
     * @return None
     */
    public function __destruct()
    {
        $this->HTTPSession = null;
        parent::__destruct();
    }

    /*
     * end_session
     * destroys the https session
     * @param None
     * @return None
     */
    final public function end_session()
    {
        try
        {
            if ($this->session_exists())
            {
                unset($_SESSION[SITE_SESSION_NAME]);
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_menu
     * returns the menu built in the session
     * @param None
     * @return None
     */
    public function get_menu()
    {
        return $_SESSION[SITE_SESSION_NAME]['menu'];
    }

    /*
     * get_help_menu
     * returns the help menu built in the session
     * @param None
     * @return None
     */
    public function get_help_menu()
    {
        return $_SESSION[SITE_SESSION_NAME]['help_menu'];
    }

    /*
     * get_session_contents
     * returns the contents of the session
     * @param None
     * @return array
     */
    final public function get_session_contents()
    {
        try
        {
            if ($this->session_exists())
            {
                return $_SESSION[SITE_SESSION_NAME];
            }
            return array();
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_user_id
     * returns the user id from the current session if a user is logged in; otherwise,
     * returns -1
     * @param None
     * @return integer
    */
    final public function get_user_id()
    {
        try
        {
            if ($this->session_exists())
            {
                return $_SESSION[SITE_SESSION_NAME]['user']['user_id'];
            }
            return -1;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_user_name
     * returns the user name from the current session if a user is logged in; otherwise,
     * returns an empty string
     * @param None
     * @return string
    */
    final public function get_user_name()
    {
        try
        {
            if ($this->session_exists())
            {
                return $_SESSION[SITE_SESSION_NAME]['user']['user_name'];
            }
            return -1;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_user_role_id
     * returns the current users role id if session exists; otherwise, returns -1
     * @param None
     * @return integer
    */
    final public function get_user_role_id()
    {
        try
        {
            if ($this->session_exists())
            {
                return $_SESSION[SITE_SESSION_NAME]['user']['role_id'];
            }
            return -1;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_version_number
     * returns the version number of the current site
     * @param None
     * @return string
    */
    public function get_version_number()
    {
        try
        {
            return SYS_VERSION_NUMBER;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_user_full_name
     * returns the current user's full name if a user is logged in; otherwise, returns an empty string
     * @param None
     * @return string
    */
    public function get_user_full_name()
    {
        try
        {
            $result = '';
            if ($this->has_user())
            {
                $result = $_SESSION[SITE_SESSION_NAME]['user']['full_name'];
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * has_user
     * returns true if the site session contains a valid user; otherwise, returns false
     * @param None
     * @return bool
    */
    public function has_user()
    {
        try
        {
            $result = false;
            if ($this->session_exists())
            {
                $result = ($_SESSION[SITE_SESSION_NAME]['user']['user_id'] != '');
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * has_permissions
     * returns true if the current session has permissions associated with it
     * @param None
     * @return bool
     */
    final public function has_permissions()
    {
        try
        {
            return (count($_SESSION[SITE_SESSION_NAME]['permissions']) > 0);
        }
        catch (Exception $e)
        {
            throw $e;

        }
    }

    /*
     * permit
     * returns true if the specified tag is permit-able within the site
     * @param string $tag - the permission sought
     * @return bool
     */
    final public function permit($tag)
    {
        try
        {
            return $this->Array->contains($_SESSION[SITE_SESSION_NAME]['permissions'], $tag);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * start_session
     * starts an http session
     * @param None
     * @return None
     */
    final public function start_session($start_php_session = true)
    {
        try
        {
            if ($start_php_session)
            {
                $this->HTTPSession->start();
            }

            if (!$this->session_exists())
            {
                $_SESSION[SITE_SESSION_NAME] = array
                (
                    'user'=>array
                    (
                        'user_id'=>'',
                        'user_name'=>'',
                        'role_id'=>'',
                        'first_name'=>'',
                        'last_name'=>'',
                        'full_name'=>'',
                        'adr_1'=>'',
                        'adr_2'=>'',
                        'adr_3'=>'',
                        'city'=>'',
                        'state'=>'',
                        'country'=>'',
                        'zip_code'=>'',
                        'work_phone'=>'',
                        'personal_phone'=>'',
                        'email_address'=>'',
                        'submodule_id'=>'',
                        'active'=>'',
                        'system_user'=>''
                    ),
                    'menu'=>array
                    (
                    ),
                    'help_menu'=>array
                    (
                    ),
                    'permissions'=>array
                    (
                    ),
                    'ldap_active'=>'No',
                    'form'=>''
                );
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * session_exists
     * returns true if the session has been started; otherwise, returns false
     * @param None
     * @return bool
     */
    final public function session_exists()
    {
        try
        {
            return isset($_SESSION[SITE_SESSION_NAME]);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * get_favorites
     * constructs an returns the user favorites as an html template
     * @param None
     * @return html unordered list
     */
    final public function get_favorites()
    {
        $html = '';
        $user_id = $this->get_user_id();
        $sql = "select favorite_id, url, title, help_url, css_class, single_instance
                from favorites
                where user_id = ?
                order by title";
        $pdo = $this->DB->prepare($sql);
        $this->DB->execute($pdo, array($user_id));
        $set = $this->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $html .= '<ul>';
            foreach($set as $menu=>$item)
            {
                $html .= '<li class="module_li">';
                $html .= '<a href="javascript:Core.launch(\'' . $item['url'] . '\', \''  . $item['title'] . '\',  \''  . $item['css_class'] . '\', \''  . $item['help_url'] . '\', \''  . $item['single_instance'] . '\', \'-1\', \'-1\', \'-1\', \'-1\', \'No\', \'-1\', \'-1\', \'-1\', \'-1\', \'-1\')">';
                $html .= "<span>" . $item['title'] . "</span>";
                $html .= '<a title="Click to delete favorite" href="javascript:Core.delete_favorite(\'' . $item['favorite_id'] . '\')">';
                $html .= '<img class="side_bar_section_img" src="/' . SITE_SESSION_NAME . '/assets/images/circle_remove.png">';
                $html .= "</a>";
                $html .= "</li>";
            }
            $html .= '</ul>';
        }
        return $html;
    }
}
?>