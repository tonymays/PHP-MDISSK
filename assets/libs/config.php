<?php
/*
 * Establishes the initial path of the framework
 * @author Anthony Mays
 * @category Configuration
*/
// establish path to site libraries (framework and apis)
set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/' . SITE_SESSION_NAME . '/assets/libs/');

// load eos3 defines
require_once('defines.php');
?>