<?php

/* -------------------------------------------------------

	configration > server

------------------------------------------------------- */

switch ($_SERVER['SERVER_NAME']) {
	default:
		$server_type = "default";
	break;
}

/* -------------------------------------------------------

	configration > path

------------------------------------------------------- */

switch ($server_type) {
	default:
	define('BASE_URL',		'http://localhost/');	//base url (end with "/")
	define('BASE_URL_SSL',	'https://localhost/');	//base url connected by SSL (end with "/")
	define('USE_SSL',		false);					//using SSL connection or not
	define('SYSTEM_FOLDER',	'./blox/core');			//relative path of SYSTEM folder from index.php
	define('APP_FOLDER',	'./');					//relative path of APPLICATION folder from index.php
	break;
}

define('SITE_FOLDER',		'./site');				//relative path of SITE folder.
define('EX_URL',			BASE_URL.'site/');		//external absolute url to SITE folder

define('CACHE_PATH',		'./site/cache');		//relative path of CACHE folder from index.php
define('LANGUAGE_PATH',		'./site/language');		//relative path of LANGUAGE folder from index.php

/* -------------------------------------------------------

	configration > setting

------------------------------------------------------- */

define('INDEX_PAGE',		'index.php');
define('LANGUAGE_DEFAULT',	'japanese');
define('CHARSET',			'UTF-8');
define('FLG_USE_CRON',		true);					//using CRON or not.

define('COOKIE_PREFIX',		'blox_');				//cookie file's prefix
define('SESSION_EXPIRE',	7200);					//session EXPIRE time

define('ENCRYPT_KEY',		'TyUx$rts8UD31jNgs');	//encrypt key for you to have to change your own key.

/* -------------------------------------------------------

	configration > database

------------------------------------------------------- */

switch ($server_type) {
	default:
		define('DB_HOSTNAME',		"localhost");
		define('DB_USERNAME',		"");
		define('DB_PASSWORD',		"");
		define('DB_DATABASE',		"blox");
		define('DB_DRIVER',			"mysql");
		define('DB_PREFIX',			"blx_");
		define('DB_PCONNECT',		TRUE);
		define('DB_DEBUG',			TRUE);
		define('DB_CACHE_ON',		FALSE);
		define('DB_CACHE_DIR',		"");
		define('DB_CHAR_SET',		"utf8");
		define('DB_COLLAT',			"utf8_general_ci");
	break;
}

/* -------------------------------------------------------

	configration > plugin

	c : コントローラー呼び出し後に動作
	e : ビュー呼び出し直前に動作

------------------------------------------------------- */

function blox_trigger() {
	return array(
		/*'test'		=> array(
			'e'	=> array(
				array(
					'path'	=> 'test',
					'method'	=> 'exec',
					'param'	=> array(
						'user'	=> ''
					)
				)
			)
		)*/
	);
}

// --------------------------- don't touch below this line ----------------------------- //

define('THEME_FOLDER',		SITE_FOLDER.'/theme');//テーマフォルダの、index.phpから見た相対位置
define('THEME_URL',			EX_URL.'theme/');//テーマフォルダのURL（/で終わる）
define('FILE_FOLDER',		SITE_FOLDER.'/file');//ファイルフォルダの、index.phpから見た相対位置
define('FILE_URL',			EX_URL.'file');//ファイルフォルダのURL（/で終わる）
define('PLUGIN_FOLDER',		SITE_FOLDER.'/plugin');//プラグインフォルダの、index.phpから見た相対位置

define('FILE_FOLDER_TMP',	FILE_FOLDER.'/_tmp');
define('FILE_ALLOWED_TYPE',	'gif|jpg|png');

define('LIB_FOLDER',		'./site/blox');//外部ライブラリファイルの、index.phpから見た相対位置（廃止予定）

/* -------------------------------------------------------

	configration > database > table

------------------------------------------------------- */

define('DB_TBL_POST',			DB_PREFIX.'post');				//post
define('DB_TBL_USER',			DB_PREFIX.'user');				//user
define('DB_TBL_USERTYPE',		DB_PREFIX.'usertype');			//usertype : auth
define('DB_TBL_DIV',			DB_PREFIX.'div');				//site division
define('DB_TBL_TAG',			DB_PREFIX.'tag');				//tag
define('DB_TBL_FILE',			DB_PREFIX.'file');				//files

define('DB_TBL_LINX',			DB_PREFIX.'linx');				//links between A and B
define('DB_TBL_EXT',			DB_PREFIX.'ext');				//external paramaters

define('DB_TBL_SETTING',		DB_PREFIX.'setting');			//setting
define('DB_TBL_LOG',			DB_PREFIX.'log');				//logs
define('DB_TBL_SESSION',		DB_PREFIX.'session');			//session

