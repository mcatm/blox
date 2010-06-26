<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

function now() {
	return unix_to_human(time(), TRUE, 'eu');//日付取得
}

function convert_day($day = 0) {//0:今日 / n:n日後 / -n:n日前
	$timestamp = time();
	
	if ($day === 0 || $day === 'today' || $day === 'now') {
		$date = $timestamp;
	} else {
		$date = $timestamp + ($day * 24 * 60 * 60);
	}
	
	return unix_to_human($date, TRUE, 'eu');
}

?>
