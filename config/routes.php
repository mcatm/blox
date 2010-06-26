<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "pipe";

$route['(:num)'] = "pipe/top/$1";

$route['request/(:any)']	= "request/$1";
$route['request']			= "request";

$home = (defined('URL_ALIAS_HOME')) ? URL_ALIAS_HOME : 'home';

$route[$home.'/(:any)']		= "home/$1";
$route[$home]				= "home/top";

$route['admin/(:any)']		= "admin/$1";
$route['admin']				= "admin/top";

#$route['search/(:any)']	= "search/$1";
#$route['search']			= "search";

$route['feed/(:any)']		= "feed/$1";
$route['feed']				= "feed";
$route['rss/(:any)']		= "feed/$1";
$route['rss']				= "feed";

$route['login/(:any)']		= "login/$1";
$route['login']				= "login";

$route['download/(:any)']	= "download/$1";

$route['logout/(:any)']		= "logout/$1";
$route['logout']			= "logout";

$route['cron/(:any)']		= "cron/$1";
$route['cron']				= "cron";

$route['(:any)']	= 'pipe/$1';

$route['scaffolding_trigger'] = "";

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */