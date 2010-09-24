<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Cron {
	
	function __construct() {
		//外部プラグイン読込
		$c = directory_map(LIB_FOLDER.'/cron');
		if (!empty($c)) {
			foreach($c as $v) {
				$f = 'cron_'.str_replace('.php', '', $v);
				if (is_file(LIB_FOLDER.'/cron/'.$v)) $f();
			}
		}
	}
}

?>