<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'] = array(
	'class'    => '',
	'function' => 'init',
	'filename' => 'init.php',
	'filepath' => 'hooks'
);

$hook['post_controller'] = array(
	'class'    => '',
	'function' => 'this',
	'filename' => 'end.php',
	'filepath' => 'hooks'
);

/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */