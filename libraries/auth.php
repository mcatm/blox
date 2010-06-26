<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {
	
	function login($email, $password) {
		$CI =& get_instance();
		
		$CI->db->where('user_email', $email);
		$q = $CI->db->get(DB_TBL_USER);
		
		if ($q->num_rows() == 0) return false;
		
		$r = $q->result();
		if ($CI->setting->get('crypt_salt').$r[0]->user_password == crypt($password, $CI->setting->get('crypt_salt'))) {
			return $this->_login($r[0]->user_id, $r[0]->user_account);
		} else {
			return false;
		}
	}
	
	function _login($user_id, $user_account) {
		$CI =& get_instance();
		$CI->load->library('session');
		
		$keytime = time();
		$hash = sha1($user_id.$user_account.$keytime);//ログイン用のハッシュ作成
		
		$this->_destroy_session();
		$CI->session->set_userdata(array(
			'id'		=> $user_id,
			'time'		=> $keytime
		));//セッションにハッシュ書き込み
		$CI->db->where('user_id', $user_id);//DBにハッシュ書き込み
		$CI->db->update(DB_TBL_USER, array('user_hash' => $hash));
		return true;
	}
	
	function logout() {
		$this->_destroy_session();
	}
	
	function init($authtype = array()/*, $twitter = false, $access_token = NULL, $access_token_secret = NULL*/) {//コンストラクタ（ログイン状態をチェック）
		$CI =& get_instance();
		$CI->load->library(array('session', 'user'));
		$CI->session->unset_userdata('login');//ログイン情報を記録したセッションを削除
		$login = false;
		$set = array();
		
		if ($CI->session->userdata('id') && $CI->session->userdata('time')) {//ログインチェック
			$q = $CI->user->get(array('qty' => 1, 'id' => $CI->session->userdata('id'), 'label' => 'tmp', 'pager' => false));
			if (count($q) > 0) {
				$r = $q[0];
				$hash = sha1($r['id'].$r['account'].$CI->session->userdata('time'));//ハッシュ生成
				$login = ($r['hash'] = $hash) ? true : false;//DBと比較
				$set = array(
					'id'		=> $r['id'],
					'account'	=> $r['account'],
					'name'		=> $r['name']
				);
				if (isset($r['file_main'])) $set['file_main'] = $r['file_main'];
				$set['auth'] = $this->_set_usertype($r['type']);//権限付与
			}
		}
		
		if (!isset($set['auth'])) $set['auth'] = $this->_set_usertype();//ログインしていない場合も権限付与
		
		$CI->data->set_array('me', $set);//出力用データに変換（パスワードやログイン用ハッシュが外に出ないよう）
		
		if ($login === true) {//ログイン状態
			$CI->session->set_userdata(array('login' => true));
		} else {//未ログイン
			$this->_destroy_session();
		}
		
		/*$user_id = 0;
		#print_r($CI->session->userdata['twitter']);exit;
		
		$this->callback_url_login = base_url().TWITTER_LOGIN_URL;
		$this->callback_url_sync = base_url().TWITTER_SYNC_URL;
		
		if (isset($CI->session->userdata['twitter']['access_token'])) $access_token = $CI->session->userdata['twitter']['access_token'];
		if (isset($CI->session->userdata['twitter']['access_token_secret'])) $access_token_secret = $CI->session->userdata['twitter']['access_token_secret'];
		
		if ($CI->session->userdata('id') == true) {
			$user_id = $CI->session->userdata('id');
		} elseif ($twitter == true) {//twitterアカウントでログイン
			$login = $this->oauth($access_token, $access_token_secret);
			if ($login == true) {
				$twitter_id = $CI->session->userdata['twitter']['user_id'];
				$CI->db->where('user_twitter_id', $twitter_id);
				$user = $CI->user->get($CI->db->get(DB_TBL_USER), array(), 'tmp', true);
				#exit($user_id);
				if (count($user) == 0) {
					$CI->load->helper('date');
					$now = unix_to_human(now(), TRUE, 'eu');//日付取得
					$screen_name = $CI->session->userdata['twitter']['screen_name'];
					$twitter_dat = $CI->twitter->call('users/show', array('id' => $screen_name));
					foreach ($twitter_dat as $k => $v) {
						$post = array(
							'user_type'					=> 0,
							'user_twitter_id'			=> $v->user->id,
							'user_twitter_screen_name'	=> $v->user->screen_name,
							'user_twitter_name'			=> $v->user->name,
							'user_twitter_img'			=> $v->user->profile_image_url,
							'user_description'			=> $v->user->description,
							'user_ext_url'				=> $v->user->url,
							'user_createdate'			=> $now,
							'user_modifydate'			=> $now,
							'user_actiondate'			=> $now
						);
						$CI->db->insert(DB_TBL_USER, $post);
						$user_id = $this->db->insert_id();
					}
				}
			}
		}*/
	}
	
	function oauth($access_token = NULL, $access_token_secret = NULL, $callback_url = "") {
		$CI =& get_instance();
		$CI->load->library('session');
		
		if ($CI->setting->get('twitter_consumer_key') && $CI->setting->get('twitter_consumer_secret')) {
			
			if ($access_token == "") $access_token = NULL;
			if ($access_token_secret == "") $access_token_secret = NULL;
			
			$tokens['access_token'] = $access_token;
			$tokens['access_token_secret'] = $access_token_secret;

			$oauth_tokens = $CI->session->userdata('twitter');
			if ($oauth_tokens !== FALSE) $tokens = $oauth_tokens;
			
			$CI->load->library('blox/twitter');
			
			if ($callback_url == "") $callback_url = $CI->setting->get('oauth_callback_url');
			
			if ($callback_url != "") {
				$auth = $CI->twitter->oauth($CI->setting->get('twitter_consumer_key'), $CI->setting->get('twitter_consumer_secret'), $tokens['access_token'], $tokens['access_token_secret'], $callback_url);
				
				if (isset($auth['access_token']) && isset($auth['access_token_secret'])){
					$CI->session->unset_userdata('twitter');
					$CI->session->set_userdata('twitter', $auth);
				} elseif (isset($_GET['oauth_token']) ){
					$uri = $_SERVER['REQUEST_URI'];
					$parts = explode('?', $uri);
					header('Location: '.$parts[0]);
					return;
				}
				return $auth;
			} else {
				return false;
			}
		}
	}
	
	function get_usertype($type = "") {
		$CI =& get_instance();
		if ($type == 'anonymous') {
			$CI->db->where('usertype_anonymous', 1);
		} elseif ($type == 'administor') {
			$CI->db->where('usertype_administor', 1);
		} elseif ($type != "") {
			$CI->db->where('usertype_type', $type);
		}
		$q = $CI->data->get($CI->db->get(DB_TBL_USERTYPE));
		if (count($q) > 0) return $q;
		return false;
	}
	
	function _set_usertype($type = 0) {
		$CI =& get_instance();
		$auth = array();
		if ($type > 0) {
			$CI->db->where('usertype_id', $type);
		} else {//基本権限
			$CI->db->where('usertype_anonymous', 1);
		}
		$q = $CI->data->get($CI->db->get(DB_TBL_USERTYPE));
		if (count($q) > 0) {
			$r = $q[0];
			$auth = array(
				'id'	=> $r['id'],
				'name' => $r['name'],
				'alias'	=> $r['alias']
			);
			//権限付与
			if ($r['auth_view_draft'] > 0) $auth['view_draft'] = true;//下書きを読む権限
			if ($r['auth_post'] > 0) $auth['post'] = true;//記事を投稿する権限
			if ($r['auth_post_comment'] > 0) $auth['post_comment'] = true;//コメントを投稿する権限
			if ($r['auth_admin'] > 0) $auth['admin'] = true;//管理画面に入れる権限
			if ($r['auth_delete_others_post'] > 0) $auth['delete_others_post'] = true;//
			if ($r['auth_add_user'] > 0) $auth['add_user'] = true;//ユーザーを追加する権限
			if ($r['auth_delete_user'] > 0) $auth['delete_user'] = true;//ユーザーを削除する権限
			if ($r['auth_invite_user'] > 0) $auth['invite_user'] = true;//ユーザーを招待する権限
			if ($r['auth_add_category'] > 0) $auth['add_category'] = true;//カテゴリを追加する権限
			if ($r['auth_add_section'] > 0) $auth['add_section'] = true;//セクションを追加する権限
		}
		return $auth;
	}
	
	function _destroy_session() {//セッションを破棄
		$CI =& get_instance();
		$CI->load->library('session');
		
		$CI->session->unset_userdata('id');
		$CI->session->unset_userdata('time');
	}
}

?>