<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

$db['default']['hostname'] = DB_HOSTNAME;
$db['default']['username'] = DB_USERNAME;
$db['default']['password'] = DB_PASSWORD;
$db['default']['database'] = DB_DATABASE;
$db['default']['dbdriver'] = DB_DRIVER;
$db['default']['dbprefix'] = DB_PREFIX;
$db['default']['pconnect'] = DB_PCONNECT;
$db['default']['db_debug'] = DB_DEBUG;
$db['default']['cache_on'] = DB_CACHE_ON;
$db['default']['cachedir'] = DB_CACHE_DIR;
$db['default']['char_set'] = DB_CHAR_SET;
$db['default']['dbcollat'] = DB_COLLAT;

/* -------------------------------------------------------

	blox configration file > database > table

------------------------------------------------------- */

define('DB_TBL_BLOX',			DB_PREFIX.'blox');				//ブロックス

define('DB_TBL_POST',			DB_PREFIX.'post');				//記事
define('DB_TBL_USER',			DB_PREFIX.'user');				//ユーザー
define('DB_TBL_USERTYPE',		DB_PREFIX.'usertype');			//usertype : auth
define('DB_TBL_DIV',			DB_PREFIX.'div');				//サイト区分
define('DB_TBL_TAG',			DB_PREFIX.'tag');				//タグ
define('DB_TBL_FILE',			DB_PREFIX.'file');				//ファイル

define('DB_TBL_LINX',			DB_PREFIX.'linx');				//リンク
define('DB_TBL_EXT',			DB_PREFIX.'ext');				//記事拡張

define('DB_TBL_SETTING',		DB_PREFIX.'setting');			//設定
define('DB_TBL_LOG',			DB_PREFIX.'log');				//ログ
define('DB_TBL_SESSION',		DB_PREFIX.'session');			//セッション管理


/* End of file database.php */
/* Location: ./system/application/config/database.php */