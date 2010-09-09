<?php

/* -------------------------------------------------------

	blox configration file

------------------------------------------------------- */

switch ($_SERVER['SERVER_NAME']) {
	default:
		$server_type = "default";
	break;
}

define('SITE_PREFIX',	'');//外部にコアがある場合
define('SITE_CORE_URL', '');//コアへのURL

switch ($server_type) {
	default:
	define('BASE_URL',			'http://localhost/');//公開URLのルート（/で終わる）
	define('BASE_URL_SSL',		'https://localhost/');//公開URLのSSLルート（/で終わる）
	define('USE_SSL',			false);//SSLを使用するか
	define('SYSTEM_FOLDER',		'./blox/core');//システムフォルダの、index.phpから見た相対位置
	define('APP_FOLDER',		'./');//アプリケーションフォルダの、index.phpから見た相対位置
	break;
}

define('ENCRYPT_KEY',		'TyUx$rts8UD31jNgs');//暗号化キー

define('EX_FOLDER',			'./site');//ユーザー設定フォルダの名前
define('EX_URL',			BASE_URL.'site/');
define('THEME_FOLDER',		'./site/theme');//テーマフォルダの、index.phpから見た相対位置
define('THEME_URL',			BASE_URL.'site/theme/');//テーマフォルダのURL（/で終わる）
define('FILE_FOLDER',		'./site/file');//ファイルフォルダの、index.phpから見た相対位置
define('FILE_URL',			BASE_URL.'site/file/');//ファイルフォルダのURL（/で終わる）
define('LIB_FOLDER',		'./site/blox');//外部ライブラリファイルの、index.phpから見た相対位置
define('CACHE_PATH',		'./site/cache');
define('LANGUAGE_PATH',		'./site/language');

define('INDEX_PAGE',		'index.php');
define('LANGUAGE_DEFAULT',	'japanese');
define('URL_ALIAS_HOME',	'home');//ホーム画面のエイリアス
define('CHARSET',			'UTF-8');
define('FLG_USE_CRON',		true);//クロンを利用するか

define('COOKIE_PREFIX', 'blox_');//クッキー用
define('SESSION_EXPIRE', 7200);//セッションの有効期限

/*

.htaccess

php_value upload_max_filesize 20M
php_value post_max_size 20M

RewriteEngine on
RewriteCond $1 !^(index\.php|ex|robots\.txt)
RewriteRule ^(.*)$ /--------/index.php/$1 [L]

*/

/* -------------------------------------------------------

	blox configration file > database

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

	blox configration file > blox

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

define('FILE_FOLDER_TMP',	FILE_FOLDER.'/_tmp');
define('FILE_ALLOWED_TYPE',	'gif|jpg|png');

/* -------------------------------------------------------

	blox configration file > database > table

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

