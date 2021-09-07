<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * Help controller class
 */
class Help extends MY_Controller
{
    /*
     * __construct
     * class constructor that extends CodeIgniter CI_Controller class
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * show_help
     * controller method that displays the help page request
     * @param string $directory - the name of the directory that the page resides in
     * @param string $page_name - the name of the page to display
     * @return html page
     */
    public function show_help($directory, $page_name)
    {
        echo $this->parser->parse('help/' . $directory . '/' . $page_name . '.html', array(), true);
    }
}
?>