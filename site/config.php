<?php

/* -------------------------------------------------------

	blox configration file

------------------------------------------------------- */

switch ($_SERVER['SERVER_NAME']) {
	case '10.144.133.100':
		$server_type = "dev";
	break;
	
	default:
		$server_type = "default";
	break;
}

define('SITE_PREFIX',	'');//外部にコアがある場合
define('SITE_CORE_URL', '');//コアへのURL

switch ($server_type) {
	case 'dev':
	define('BASE_URL',			'http://10.144.133.100/artfolio/');//公開URLのルート（/で終わる）
	define('BASE_URL_SSL',		'http://10.144.133.100/artfolio/');//公開URLのSSLルート（/で終わる）
	define('USE_SSL',			false);//SSLを使用するか
	define('SYSTEM_FOLDER',		'../blox/core');//システムフォルダの、index.phpから見た相対位置
	define('APP_FOLDER',		'../blox');//アプリケーションフォルダの、index.phpから見た相対位置
	break;
	
	default:
	define('BASE_URL',			'http://www.artfolio.co.jp/');//公開URLのルート（/で終わる）
	define('BASE_URL_SSL',		'https://www.artfolio.co.jp/');//公開URLのSSLルート（/で終わる）
	define('USE_SSL',			true);//SSLを使用するか
	define('SYSTEM_FOLDER',		'../blox/core');//システムフォルダの、index.phpから見た相対位置
	define('APP_FOLDER',		'../blox');//アプリケーションフォルダの、index.phpから見た相対位置
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

define('INDEX_PAGE',		'pipe.php');
define('LANGUAGE_DEFAULT',	'japanese');
define('URL_ALIAS_HOME',	'jhome');//ホーム画面のエイリアス
define('CHARSET',			'UTF-8');
define('FLG_USE_CRON',		true);//クロンを利用するか

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
	case 'dev':
		define('DB_HOSTNAME',		"localhost");
		define('DB_USERNAME',		"root");
		define('DB_PASSWORD',		"3030");
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
	
	default:
		define('DB_HOSTNAME',		"192.168.2.91");
		define('DB_USERNAME',		"artfolio_web");
		define('DB_PASSWORD',		"iUjx34gTsdpl0w");
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
		'af_yahoo_auction'		=> array(
			'c'	=> array(
				array(
					'path'		=> 'frame',
					'method'	=> 'get_frame',
					'param'	=> array(
						'stock_id'	=> 4,
						'query'	=> ""
					)
				)/*,
				array(
					'path'		=> 'online',
					'method'	=> 'get_item'
				)*/
			)
		),
		'af_online'		=> array(
			'c'	=> array(
				array(
					'path'		=> 'online',
					'method'	=> 'get_popular'
				)
			)
		),
		'af_stock_img'		=> array(
			'c'	=> array(
				array(
					'path'		=> 'frame/convert_img',
					'method'	=> 'convert'
				)
			)
		)/*,
		'test'		=> array(
			'e'	=> array(
				array(
					'path'	=> 'test',
					'method'	=> 'exec',
					'param'	=> array(
						'user'	=> 'drawing4-5'
					)
				)
			)
		)*/
	);
}

// --------------------------- don't touch below this line ----------------------------- //

define('FILE_FOLDER_TMP',	FILE_FOLDER.'/_tmp');
define('FILE_ALLOWED_TYPE',	'gif|jpg|png|mp3');

/* -------------------------------------------------------

	blox configration file > database > table

------------------------------------------------------- */

define('DB_TBL_POST',			DB_PREFIX.'post');				//記事
define('DB_TBL_USER',			DB_PREFIX.'user');				//ユーザー
define('DB_TBL_USERTYPE',		DB_PREFIX.'usertype');			//ユーザー権限タイプ
define('DB_TBL_DIV',			DB_PREFIX.'div');				//サイト区分
define('DB_TBL_TAG',			DB_PREFIX.'tag');				//タグ
define('DB_TBL_FILE',			DB_PREFIX.'file');				//ファイル

define('DB_TBL_LINX',			DB_PREFIX.'linx');				//リンク
define('DB_TBL_EXT',			DB_PREFIX.'ext');				//記事拡張

define('DB_TBL_SETTING',		DB_PREFIX.'setting');			//設定
define('DB_TBL_LOG',			DB_PREFIX.'log');				//設定
define('DB_TBL_SESSION',		DB_PREFIX.'session');			//セッション管理