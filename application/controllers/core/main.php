<?php if (!defined('BASEPATH')) exit("direct access not allowed");
require_once('fw/connection/ldap/ldap.php');
class Main extends MY_Controller
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
        $this->data = array();
        $this->data['version'] = $this->Session->get_version_number();
        $this->data['welcome'] = 'Welcome';
        $this->data['user_full_name'] = $this->Session->get_user_full_name();
        $this->data['logout_link'] = '<a href="/' . SITE_SESSION_NAME . '/core/login/logout">Logout</a>';
        $this->data['background'] = $this->parser->parse('core/background.html', $this->data, true);
        $data = array('menu'=>$this->Session->get_menu());
        $this->data['site_menu'] = $this->parser->parse('core/menu.html', $data, true);
        $data = array('menu'=>$this->Session->get_help_menu());
        $this->data['site_help_menu'] = $this->parser->parse('core/help_menu.html', $data, true);
        $this->data['site_favorites'] = $this->Session->get_favorites();
        echo $this->parser->parse('core/main.html', $this->data, true);
    }

    /*
     * show_favorites
     * loads favorites into the sidebar
     * @param None
     * @return html template
     */
    final public function show_favorites()
    {
        $result = $this->Session->get_favorites();
        echo $result;
    }

    /*
     * add_favorite
     * adds a submodule as a favorites
     * @param string $url - the url of the submodule
     * @param string $title - the title to display when the window is loaded
     * @param string $css_class - the css class that will be the basis for rendering the window
     * @param string $help_url - the help url associated with the window
     * @param integer $single_instance - is the window single (1) or multiple (0) instance
     * @return string
     */
    final public function add_favorite($url, $title, $css_class, $help_url, $single_instance)
    {
        $user_id = $this->Session->get_user_id();
        $url = str_replace('FORWARDSLASH' , '/', $url);

        if (!$this->favorite_exists($user_id, $url))
        {
            $title = str_replace('OPENBRACKET' , '[', str_replace('CLOSEBRACKET', ']', str_replace('SPACE', ' ', $title)));
            $help_url = str_replace('FORWARDSLASH' , '/', $help_url);
            $sql = "insert into favorites select null, ?, ?, ?, ?, ?, ?";
            $pdo = $this->Session->DB->prepare($sql);
            $this->Session->DB->execute($pdo, array($user_id, $url, $title, $help_url, $css_class, $single_instance));
            $result = $this->Session->get_favorites();
            echo $result;
        }
        else
        {
            echo 'DUPLICATE';
        }
    }

    /*
     * delete_favorite
     * deletes the favorite specified by favorite_id
     * @param integer $favorite_id - the id associated with the favorite
     * @return html template
     */
    final public function delete_favorite($favorite_id)
    {
        $user_id = $this->Session->get_user_id();
        $sql = "delete from favorites where user_id = ? and favorite_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id, $favorite_id));
        $result = $this->Session->get_favorites();
        echo $result;
    }

    /*
     * delete_favorites
     * deletes all favorites associated with the user that has logged in
     * @param None
     * @return html template
     */
    final public function delete_favorites()
    {
        $user_id = $this->Session->get_user_id();
        $sql = "delete from favorites where user_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $result = $this->Session->get_favorites();
        echo $result;
    }

    /*
     * favorite_exists
     * returns true if the specified user profile contains the specified url as a favorite
     * @param integer $user_id - the user id associated with the login
     * @param string $url - the url associated with the specified url
     * @return bool
     */
    private function favorite_exists($user_id, $url)
    {
        $sql = "select favorite_id
                from favorites
                where user_id = ?
                  and url= ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id, $url));
        $set = $this->Session->DB->fetch_all($pdo);
        return (!empty($set));
    }

    /*
     * clear_desktop
     * removes a save desktop associated with the user that is currently logged in
     * @param None
     * @return None
     */
    final public function clear_desktop()
    {
        $user_id = $this->Session->get_user_id();
        $sql = "delete from desktops where user_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
    }

    /*
     * has_desktop
     * returns DESKTOP if the logged in user profile has a desktop; otherwise, returns NODESKTOP
     * @param None
     * @return string
     */
    final public function has_desktop()
    {
        $user_id = $this->Session->get_user_id();
        $sql = 'select desktop_id from desktops where user_id = ?';
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (empty($set))
        {
            echo 'NODESKTOP';
        }
        else
        {
            echo 'DESKTOP';
        }
    }

    /*
     * load_desktop
     * loads a saved desktop if the logged in user possess one
     * @param None
     * @return html template
     */
    final public function load_desktop()
    {
        $user_id = $this->Session->get_user_id();
        $sql = 'select * from desktops where user_id = ?';
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (empty($set))
        {
            echo 'NODESKTOP';
        }
        else
        {
            $html = '<div name="desktop-request" class="hide">';
            foreach($set as $key=>$value)
            {
                $html .= '<div>';
                $html .= '<p name="desktop-id">' . $value['desktop_id'] . '</p>';
                $html .= '<p name="user-id">' . $value['user_id'] . '</p>';
                $html .= '<p name="url">' . $value['url'] . '</p>';
                $html .= '<p name="title">' . $value['title'] . '</p>';
                $html .= '<p name="help-url">' . $value['help_url'] . '</p>';
                $html .= '<p name="css-class">' . $value['css_class'] . '</p>';
                $html .= '<p name="single-instance">' . $value['single_instance'] . '</p>';
                $html .= '<p name="original-top">' . $value['original_top'] . '</p>';
                $html .= '<p name="original-left">' . $value['original_left'] . '</p>';
                $html .= '<p name="original-width">' . $value['original_width'] . '</p>';
                $html .= '<p name="original-height">' . $value['original_height'] . '</p>';
                $html .= '<p name="maximized">' . $value['maximized'] . '</p>';
                $html .= '<p name="actual-top">' . $value['actual_top'] . '</p>';
                $html .= '<p name="actual-left">' . $value['actual_left'] . '</p>';
                $html .= '<p name="actual-width">' . $value['actual_width'] . '</p>';
                $html .= '<p name="actual-height">' . $value['actual_height'] . '</p>';
                $html .= '<p name="z-index">' . $value['z_index'] . '</p>';
                $html .= '<p name="minimized">' . $value['minimized'] . '</p>';
                $html .= '</div>';
            }
            $html .= '</div>';
            echo $html;
        }
    }

    /*
     * save_desktop
     * saves the current desktop window structure for the logged in user based upon an XML document passed by the
     * client session
     * @param None
     * @return None
     */
    final public function save_desktop()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
        {
            $xml_doc = str_replace("&", "and", (string)$GLOBALS['HTTP_RAW_POST_DATA']);
            $array = $this->Session->XML->to_array($xml_doc);
            $this->clear_desktop();
            if (!empty($array))
            {
                $array = $this->Session->Array->make_multi_dimensional($array['window']);
                $user_id = $this->Session->get_user_id();
                foreach($array as $key=>$value)
                {
                    if ($value['maximized'] == 'Yes')
                    {
                        $value['maximized'] = 1;
                    }
                    else
                    {
                        $value['maximized'] = 0;
                    }
                    $sql = "insert into desktops select null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
                    $pdo = $this->Session->DB->prepare($sql);
                    $this->Session->DB->execute($pdo, array($user_id, $value['url'], $value['title'],
                        $value['help_url'], $value['css_class'], $value['single_instance'], $value['original_top'],
                        $value['original_left'], $value['original_width'], $value['original_height'],
                        $value['maximized'], $value['actual_top'], $value['actual_left'], $value['actual_width'],
                        $value['actual_height'], $value['z_index'], $value['minimized']));
                }
            }
        }
    }
}