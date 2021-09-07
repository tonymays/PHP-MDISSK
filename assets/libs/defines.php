<?php
/*
 * Establishes site wide defines
 * DO NOT REMOVE ANY DEFINES THAT CAN WITH THE STARTER KIT UNLESS OF COURSE YOU WANT TO BREAK IT!
 * @author Anthony Mays
 * @category Configuration
*/

/*
 * STARTER KIT define types
 */
define('TYPE_ARRAY', 0);
define('TYPE_STRING', 1);
define('TYPE_RAW', 2);

/*
 * STARTER KIT define version number
 */
define('SYS_VERSION_NUMBER', '1.0.0');

/*
 * STARTER KIT define database configuration information
 */
define('SITE_DB_HOST', 'localhost');
define('SITE_DB_PORT', '3306');
define('SITE_DB_NAME', 'mdissk');
define('SITE_DB_USER_NAME', 'mdissk');
define('SITE_DB_PASSWORD', 'mdissk');

/*
 * STARTER KIT define database configuration information
 */
define('SITE_LDAP_PROTOCOL', 'ldap://');
define('SITE_PRIMARY_LDAP_SERVER', '192.168.211.16');
define('SITE_BACKUP_LDAP_SERVER', '192.168.211.17');
define('SITE_LDAP_PORT', 389);
define('SITE_LDAP_USER_DOMAIN', '@edgecommunications.com');

// STARTER KIT YES or NO flag to utilize LDAP to authenticate.  This flag allows me to default our ldap server information
// without actually using it.
define('SITE_USE_LDAP', 'NO');

/*
 * STARTER KIT define error handlers
 */
define('SYS_REDIRECT_LOGIN', 'Redirect Login');
define('SYS_INSUFFICIENT_PRIVILEGES', 'Insufficient Privileges');
define('SYS_INVALID_REQUEST', 'Invalid Request');
define('SYS_FAILURE_TO_PROCESS', 'Failure to Process');
define('SYS_DIRECT_CALL', 'Direct Call Not Allowed');
define('SYS_SUCCESS', 'success');
define('SYS_ERROR', 'error');
?>