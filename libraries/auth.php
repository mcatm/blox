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
	
	function init($authtype = array()) {//コンストラクタ（ログイン状態をチェック）
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
			
			$CI->load->library('ext/twitter');
			
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
	
	function check_auth($action = '') {//権限を確認する（行動を指定しない場合、管理者であればtrueを返す）
		$CI =& get_instance();
		$auth = false;
		if (!empty($action)) {
			if (isset($CI->data->out['me']['auth'][$action]) || $CI->data->out['me']['auth']['type'] == 'admin') $auth = true;
		} else {
			if ($CI->data->out['me']['auth']['type'] == 'admin') $auth = true;
		}
		return $auth;
	}
	
	function get_usertype($type = "") {
		$CI =& get_instance();
		if ($type != "") $CI->db->where('usertype_type', $type);
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
			$CI->db->where('usertype_type', 'anonymous');
		}
		$q = $CI->data->get($CI->db->get(DB_TBL_USERTYPE));
		if (count($q) > 0) {
			$r = $q[0];
			$auth = array(
				'id'	=> $r['id'],
				'name' => $r['name'],
				'alias'	=> $r['alias']
			);
			
			$auth['type'] = $r['type'];//権限タイプ（admin|contributor|anonymous）
			
			if (!empty($r['auth'])) {//権限付与
				foreach(explode(',', $r['auth']) as $a) $auth[$a] = true;
			}
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