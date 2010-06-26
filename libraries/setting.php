<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting {
	
	function get($str) {//設定を取得
		$CI =& get_instance();
		if (!isset($CI->data->out['setting'][$str]) || empty($CI->data->out['setting'][$str])) {
			$this->_get_user_setting($str, true);
		}
		return (isset($CI->data->out['setting'][$str])) ? $CI->data->out['setting'][$str] : false;
	}
	
	function get_status($num, $type = 'post') {
		$CI =& get_instance();
		return $CI->lang->line('system_'.$type.'_status_'.$num);
	}
	
	function get_admin_menu() {
		return $this->admin_menu;
	}
	
	function get_alias($alias = "") {
		if ($this->get('site_prefix')) $alias = '['.$this->get('site_prefix').']'.$alias;
		return $alias;
	}
	
	function get_explode_value($key = "", $delimiter = ",") {
		$str = $this->get($key);
		return ($str) ? explode($delimiter, $str) : array();
	}
	
	function get_theme() {
		$CI =& get_instance();
		$CI->load->helper('directory');
		$theme = array();
		$map = directory_map(THEME_FOLDER, true);
		foreach($map as $t) {
			if (!preg_match('(^_)', $t) && is_dir(THEME_FOLDER.'/'.$t)) $theme[] = $t;
		}
		return $theme;
	}
	
	function set($label, $value) {//設定を書き込む
		$CI =& get_instance();
		$CI->data->out['setting'][$label] = $value;
	}
	
	function store($label, $value) {//設定をDBに保存する
		$CI =& get_instance();
		$CI->db->where('setting_name', $label);
		if ($CI->db->count_all_results(DB_TBL_SETTING) == 0) {
			$CI->db->insert(DB_TBL_SETTING, array('setting_name' => $label, 'setting_value' => $value));
		} else {
			$CI->db->where('setting_name', $label);
			$CI->db->update(DB_TBL_SETTING, array('setting_value' => $value));
		}
	}
	
	function set_title($str = "", $clear = false) {//タイトルを設定
		$CI =& get_instance();
		$str = str_replace('{@sitename}', $this->get('site_name'), $str);//{@site_name} - 置換用タグ
		if ($str != "") {
			$title = $str .$this->get('title_delimiter'). $this->get('site_name');
		} else {
			$title = $this->get('site_name');
		}
		$title = ($clear) ? $str : $title;
		$this->set('title', $title);
	}
	
	function set_description($str = "") {//要約を設定
		$CI =& get_instance();
		$description = ($str != "") ? $str : $this->get('site_description');
		$this->set('description', htmlspecialchars($description));
	}
	
	function set_favicon($str = "") {//faviconを設定
		$CI =& get_instance();
		$favicon = ($str != "") ? $str : $this->get('site_favicon');
		$this->set('favicon', $favicon);
	}
	
	function set_rss($str = "") {//RSSを設定
		$CI =& get_instance();
		$rss = ($str != "") ? $str : $this->get('site_rss');
		$this->set('rss', $rss);
	}
	
	function set_keyword($arr = array()) {//キーワードを設定
		$CI =& get_instance();
		$site_keyword = $this->get('site_keyword').', ';
		$this->set('keyword', $site_keyword.implode(', ', $arr));
	}
	
	function _get_user_setting($p = NULL, $stack = false) {
		$CI =& get_instance();
		if ($p != NULL) {
			$param = (is_array($p)) ? $p : array($p);
			foreach($param as $v) {
				$CI->db->or_where('setting_name', $v);
			}
		}
		$r = $CI->data->set($CI->db->get(DB_TBL_SETTING), array('stack' => false));
		if ($stack === true) {//設定を溜め込む
			if (is_array($r)) $this->_stack_setting($r);
		} else {//設定をそのまま返す
			return (is_array($p)) ? $r : $r[0]['value'];
		}
	}
	
	function _stack_setting($arr) {
		$CI =& get_instance();
		foreach($arr as $k => $v) {
			$CI->data->out['setting'][$v['name']] = $v['value'];
		}
	}
	
	function init() {
		$tmp_home = (defined('URL_ALIAS_HOME')) ? URL_ALIAS_HOME : 'home';
		$default_setting = array(//デフォルトの設定
			'site_name'					=> 'blox',//サイト名
			'site_description'			=> '',//要約
			'site_keyword'				=> '',//キーワード
			'site_rss'					=> base_url().'rss/',//RSS
			'site_favicon'				=> ex_url().'favicon.ico',//favicon
			'theme'						=> 'default',//テーマ
			'url_alias_category'		=> 'category',//カテゴリのエイリアス
			'url_alias_post'			=> 'post',//記事のエイリアス
			'url_alias_tag'				=> 'tag',//タグのエイリアス
			'url_alias_user'			=> 'user',//ユーザーのエイリアス
			'url_alias_home'			=> $tmp_home,//ホーム画面のエイリアス
			'url_alias_bookmarklet'		=> 'bookmarklet',//ブックマークレットのエイリアス
			'url_segment_identifier_id'	=> '',//ID識別子：この次のセグメントがIDとなる
			'url_segment_identifier_page'	=> 'page',//PAGE識別子
			'url_segment_identifier_offset'	=> 'offset',//OFFSET識別子
			'url_segment_identifier_stop'	=> '',//METHODを区別する識別子
			'file_segment'				=> 'main|sub',//ファイルの分類
			'img_notfound'				=> 'default.jpg',
			'author_segment'			=> 'main',//記事に対する著者の役割
			'post_max_qty_per_page'		=> 20,//一ページに表示する記事の数
			'post_max_tag_per_page'		=> 100,//一ページに表示するタグの数
			'img_size_main'				=> 500,//メイン画像のサイズ
			'img_size_mid'				=> 120,//中サイズの画像
			'img_size_thumbnail'		=> 48,//サムネイルのサイズ
			'flg_get_related'			=> 1,//関連記事を取得する
			'title_delimiter'			=> ' / ',//タイトルを分割するデリミタ
			'url_delimiter'				=> '-',//URLを分割するデリミタ
			'tag_delimiter'				=> ',',//タグを分割するデリミタ
			'crypt_salt'				=> 'bx',//暗号化の際に使用する二文字のsalt
			'global_encoding'			=> CHARSET,//マルチバイトエンコード
			'code_google_analytics'		=> '',//google Analytics用
			//'email_admin'				=> '',管理者E-Mailアドレス
			'html_tag_not_escape'		=> '<a><br><blockquote>',//エスケープしないhtmlタグ
			'html_tag_not_escape_attr'	=> 'href,target',//aタグの中でエスケープしない要素
			'doc_help'					=> 'http://blox.pelepop.com/',//ドキュメント
			'doc_forum'					=> 'http://blox.pelepop.com/forum/',//フォーラム
			'output_error_open'			=> '<p class="error">',
			'output_error_close'		=> '</p>',
			'history_log_level'			=> 3,//編集履歴の深さ
			'flg_maintenance'			=> 'false'//メンテナンスモード
		);
		
		$CI =& get_instance();
		foreach ($default_setting as $k => $v) $CI->data->out['setting'][$k] = $v;
		
		$this->_get_user_setting(array_flip($default_setting), true);//ユーザー設定読み込み
		
		$this->set_title();
		$this->set_keyword();
		$this->set_favicon();
		$this->set_description();
		$this->set_rss();
		
		if (defined('SITE_PREFIX') && SITE_PREFIX != "") $this->set('site_prefix', SITE_PREFIX);
		
		$CI->div->get(array('type' => 'section', 'label' => 'section', 'pager' => false));
	}
	
	var $admin_menu = array(
		'post'	=> array(
			'alias'	=> 'post',
			'auth'	=> 'admin',
			'sub'	=> array(
				'post_list'	=> array(
					'alias'	=> 'post'
				),
				'post_new'	=> array(
					'alias'	=> 'post/new',
					'auth'	=> 'post'
				),
				'comment'	=> array(
					'alias' => 'comment',
					'auth'	=> 'post_comment'
				),
				'tag'	=> array(
					'alias'	=> 'tag'
				)
			)
		),
		'user'	=> array(
			'alias'	=> 'user',
			'sub'	=> array(
				'user_list'	=> array(
					'alias'	=> 'user'
				),
				'user_new'	=> array(
					'alias'	=> 'user/new',
					'auth'	=> 'add_user'
				),
				'user_invite'	=> array(
					'alias' => 'user/invite'
				)/*,
				'usertype'	=> array(
					'alias'	=> 'usertype'
				)*/
			)
		),
		'file'	=> array(
			'alias'	=> 'file',
			'sub'	=> array(
				'file_upload'	=> array(
					'alias'	=> 'file/upload'
				),
				'file_list'	=> array(
					'alias'	=> 'file'
				)/*,
				'file_image'	=> array(
					'alias'	=> 'file/img',
					'auth'	=> 1
				),
				'file_sound'	=> array(
					'alias' => 'file/snd',
					'auth'	=> 1
				)*/
			)
		),
		'site'	=> array(
			'alias'	=> 'site',
			'sub'	=> array(
				'theme'	=> array(
					'alias'	=> 'theme',
					'auth'	=> 'add_section'
				),
				'div'	=> array(
					'alias'	=> 'div',
					'auth'	=> 'add_section'
				),
				'setting'	=> array(
					'alias'	=> 'setting',
					'auth'	=> 'add_section'
				),
				'mail'	=> array(
					'alias' => 'mail'
				),
				'install'	=> array(
					'alias' => 'install',
					'auth'	=> 'add_section'
				)
			)
		)/*,
		'analytic'	=> array(
			'alias' => 'analytic',
			'auth'	=> 'add_section'
		)*/
	);
	
}

?>