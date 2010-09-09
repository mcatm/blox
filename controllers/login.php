<?php

class Login extends Controller {

	function index() {//ログイン
		if ($this->auth->login($this->input->post('email'), $this->input->post('pwd'))) {
			$redirect = ($this->input->post('redirect')) ? $this->input->post('redirect') : base_url();
			if (preg_match('('.base_url().'(login|logout))', $redirect)) $redirect = base_url();
			header('location:'.$redirect);
		} else {
			$this->load->view('login');
		}
	}
	
	function confirm($key, $token) {
		$this->load->library('user');
		$this->user->confirm($key, $token);
	}
	
	function twitter() {//Twitterでログイン
		#$CI->load->library('user');
		$twitter_user = $this->auth->get_usertype('twitter');
		if (!empty($twitter_user)) {
			$this->load->library(array('user', 'blox/twitter'));
			$this->load->helper(array('date'));
			$now = now();
			if ($auth = $this->auth->oauth(NULL, NULL, base_url().'login/twitter/')) {
				if (is_array($auth)) {//oAuthログイン
					header('location:'.base_url().'login/twitter');
				} else {
					$user = $this->twitter->call('users/show', array('id' => $this->session->userdata['twitter']['user_id']));
					
					$ch = $this->user->get(array(
						'account'	=> $user->screen_name,
						'ext'		=> true,
						'stack'		=> false
					));
					
					if (!isset($ch)) {//ユーザー未登録
						if ($this->user->count(array('account' => $user->screen_name)) == 0) {
							$set = array(
								'user_account'	=> $user->screen_name,
								'user_name'		=> $user->name,
								'user_description'	=> $user->description,
								'user_type'		=> $twitter_user[0]['id'],
								'user_createdate'	=> $now,
								'user_modifydate'	=> $now,
								'user_actiondate'	=> $now
							);
							$this->db->insert(DB_TBL_USER, $set);
							$user_id = $this->db->insert_id();
							
							$this->linx->set('user2extapp', array(
								'a'		=> $user_id,
								'b'		=> $this->session->userdata['twitter']['user_id'],
								'status'	=> 'twitter',
								'param'		=> $this->session->userdata['twitter']
							));
						} else {
							exit('既に同じアカウントの人がいますので、無理');
						}
					} else {
						$user_linx = $this->linx->get('user2extapp', array(
							'b'	=> $this->session->userdata['twitter']['user_id'],
							'status'	=> 'twitter'
						));
						if (!empty($user_linx)) {
							$user_id = $user_linx[0]['a'];
						} else {
							exit('error');
						}
					}
					
					#$this->auth->logout();
					
					$u = $this->user->get(array('id' => $user_id, 'stack' => false));
					
					if (!empty($u)) {
						if ($this->auth->_login($user_id, $u[0]['account'])){
							header('location:'.base_url());
						}
					}
				}
			}
		}
		exit('ログイン失敗');
	}
	
	function Login() {
		parent::Controller();
	}
}