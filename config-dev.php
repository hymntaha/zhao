<?php 

include_once 'cachebust.php';
if (!defined('CACHEBUST')) define('CACHEBUST', '1');

include_once 'cfg/contributor-photos.php';
if (!defined('CONTRIBUTOR_PHOTOS')) define('CONTRIBUTOR_PHOTOS', '');

define('G_PATH', '/app/www.bravoyourcity.com/current/');
define('LIB_PATHS', '/app/www.bravoyourcity.com/current/klib/,'.G_PATH.'mdl/,'.G_PATH.'ctl/,'.G_PATH.'lib/');
define('G_URL', 'http://www.bravoyourcity.com/');

/* search */
define('SEARCH_HOST', 'localhost');
define('SEARCH_PORT', 9312);

/* kdebug */
define('KDEBUG', false);
define('KDEBUG_HANDLER', false);

/* editors picks */
define('EDITORS_PICKS_USER_ID', "50d20e326747df2238000000");

/* image scaler */
define('IMAGE_SECRET', 'YQp!0^#t6294G');
define('IMAGE_ROOT', 'http://origin.img.brvo.me:9001/images');
define('IMAGE_COMPRESSION_QUALITY', 80);
define('IMAGE_VERSION', 1);

/* google analytics */
define('GA_ACCOUNT', 'UA-35556879-1');
define('GA_DOMAINNAME', 'brvo.me');

/* mongo config */
define('MONGO_HOST','mongodb://localhost:27017/');
define('MONGO_DB','bravo');
//define('MONGO_REPLICA_SET', 'replica3');
define('MONGO_DEBUG', true);

/* facebook */
define('FB_APPID', '325643424183091');
define('FB_SECRET', '061ac24f8d4ce43aed0c1f2f1a2d92be');
define('FB_NAMESPACE', 'bravoyourcity');

/* email */
define('EMAIL_FROM', 'info@bravoyourcity.com');
define('EMAIL_FROM_FULL_NAME', 'Bravo Your City!');
define('EMAIL_MODE', 'filter'); // filter, send or log
define('EMAIL_FILTER', 'andrew@bravoyourcity.com,misoon@bravoyourcity.com,misoonyang@gmail.com,every1elsesdoinit@gmail.com,bittmanmax@gmail.com');

/* mailchimp */
define('MAILCHIMP_APIKEY', '45ae0bad512a4b38dae36624bb60dd43-us6');
define('MAILCHIMP_LISTID', '91f96b9ae4');
define('MAILCHIMP_OPTIN', false);

/* summon cookie secret */
define('SUMMON_SECRET', 'changeme');

/* data */
require_once G_PATH.'dat/forms.php';

$_OTHER['data'] = $_D;

/* ignore past this line */

set_include_path(get_include_path().PATH_SEPARATOR.G_PATH);

spl_autoload_register(function($class) { 

	foreach (explode(',', LIB_PATHS) as $libdir) {
  	foreach (array('.class.php','.interface.php') as $file) {
			if (is_file($libdir.$class.$file)) {
				return require_once $libdir.$class.$file;
			}
		}
	}

	return false;

});

if (defined('KDEBUG') && KDEBUG == true && php_sapi_name() != 'cli') {
	if (!defined('KDEBUG_JSON') || KDEBUG_JSON == false) {
		register_shutdown_function(array('kdebug', 'init'));
		if (defined('KDEBUG_HANDLER') && KDEBUG_HANDLER == true) {
			set_error_handler(array('kdebug', 'handler'), E_ALL);
		}
	}
}

function hpr() { return call_user_func_array(array('k','hpr'), func_get_args()); }
function cpr() { return call_user_func_array(array('k','cpr'), func_get_args()); }
function highlight() { return call_user_func_array(array('k','highlight'), func_get_args()); }
function xmlindent() { return call_user_func_array(array('k','xmlindent'), func_get_args()); }

require_once('hooks.php');
