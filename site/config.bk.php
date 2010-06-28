<?php

/* -------------------------------------------------------

	blox configration file

------------------------------------------------------- */


define('BASE_URL',			'http://10.144.133.100/artfolio/');//公開URLのルート（/で終わる）
define('BASE_URL_SSL',		'https://10.144.133.100/artfolio/');//公開URLのSSLルート（/で終わる）
define('ENCRYPT_KEY',		'sososososososososososososososososo');//暗号化キー

define('USE_SSL',			false);//SSLを使用するか
define('SYSTEM_FOLDER',		'../blox/core');//システムフォルダの、index.phpから見た相対位置
define('APP_FOLDER',		'../blox');//アプリケーションフォルダの、index.phpから見た相対位置
define('EX_FOLDER',			'./ex');//ユーザー設定フォルダの名前
define('THEME_FOLDER',		'./ex/theme');//テーマフォルダの、index.phpから見た相対位置
define('THEME_URL',			BASE_URL.'ex/theme/');//テーマフォルダのURL（/で終わる）
define('FILE_FOLDER',		'./ex/file');//ファイルフォルダの、index.phpから見た相対位置
define('FILE_URL',			BASE_URL.'ex/file/');//ファイルフォルダのURL（/で終わる）
define('LIB_FOLDER',		'./ex/blox');//外部ライブラリファイルの、index.phpから見た相対位置
define('CACHE_PATH',		'./ex/cache');//キャッシュフォルダの相対位置
define('LANGUAGE_PATH',		'./ex/language');//言語フォルダの相対位置

define('INDEX_PAGE',		'index.php');
define('LANGUAGE_DEFAULT',	'english');
define('CHARSET',			'UTF-8');
define('FLG_USE_CRON',		true);//クロンを利用するか

/* -------------------------------------------------------

	blox configration file > database

------------------------------------------------------- */

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

/* -------------------------------------------------------

	blox configration file > blox

------------------------------------------------------- */

function blox_trigger() {
	return array(
		'af_yahoo_auction'		=> array(
			'c'	=> array(
				array(
					'path'	=> 'frame',
					'method'	=> 'get_frame',
					'param'		=> array(
						'stock_id' => 3,
						'query'		=> 'artfolio',
					)
				)
			)
		),
		'tweetback'			=> array(
			'c'	=> array(
				array(
					'method' => 'get'
				)
			)
		)
	);
}

// --------------------------- don't touch below this line ----------------------------- //

define('FILE_FOLDER_TMP',	FILE_FOLDER.'/_tmp');
define('FILE_ALLOWED_TYPE',	'gif|jpg|png');

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

define('DB_TBL_SETTING',		DB_PREFIX.'setting');			//設定
define('DB_TBL_LOG',			DB_PREFIX.'log');				//設定
define('DB_TBL_SESSION',		DB_PREFIX.'session');			//セッション管理