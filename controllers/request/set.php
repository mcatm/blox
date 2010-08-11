<?php

class Set extends Controller {
	
	function post($id = 0, $param = array('redirect_success' => '', 'redirect_error' => '')) {
		$this->load->library('post');
		
		$msg = $this->post->set($id);
				
		switch ($msg['result']) {
			case 'success':
				print_r($msg);
			break;
			
			case 'error':
			default:
				print_r($msg);
			break;
		}
	}
	
	function comment($id = 0) {
		$this->load->library('post');
		
		$msg = $this->post->set($id);
				
		switch ($msg['result']) {
			case 'success':
				print_r($msg);
				#if ($param['redirect_success'] != "") header('location:'.$param['redirect_success']);
			break;
			
			case 'error':
			default:
				print_r($msg);
				#if ($param['redirect_error'] != "") header('location:'.$param['redirect_error']);
			break;
		}
		
		/*print json_encode($this->msg);*/
	}
	
	function file($type = 'json') {
		if(isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
			$this->load->library('file');
			$file_id = $this->file->set();
			
			if (is_int($file_id) && $file_id > 0) {
				$file = $this->file->get(array('id' => $file_id, 'stack' => false));
				if (isset($file[0])) {
					$this->msg = array(
						'result'	=> 'success',
						'file_id'	=> $file_id,
						'type'		=> $file[0]['type'],
						'msg'		=> 'ファイルをアップロードしました'
					);
					if ($file[0]['type'] == 'image') $this->msg['img_url'] = img_url($file_id, $this->setting->get('img_size_mid'));
				}
			} else {
				$this->msg = array(
					'result'	=> 'error',
					'msg'		=> 'ファイルのアップロードに失敗しました'
				);
			}
		} else {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> '不正なファイルです'
			);
		}
		switch ($type) {
			case 'json':
			print json_encode($this->msg);
			break;
			
			default:
				if ($this->msg['result'] === 'success') {
					print '1';
				} else {
					print '0';
				}
			break;
		}
	}
	
	function linx($type, $a, $b, $status = '', $unique = '') {
		$this->load->library('linx');
		
		$flg_unique = ($unique === 'true') ? true : $unique;
		$flg_unique = ($flg_unique != "") ? $flg_unique : false;
		
		if ($status == "---") $status = "";
		
		$arr = $this->linx->get($type, array(
			'a'			=> $a,
			'b'			=> $b
		));
		
		$set = array(
			'a'			=> $a,
			'b'			=> $b,
			'status'	=> $status,
			'unique'	=> $flg_unique
		);
		
		if (isset($arr)) $set['id'] = $arr[0]['id'];
		
		$this->linx->set($type, $set);
	}
	
	function spam($user_param = array('redirect_success' => '', 'redirect_error' => '')) {
		$this->load->library(array('spam_filter'));
		
		$param = array();
		$param = array_merge($param, $user_param);
		
		$this->spam_filter->set();
	}
	
	function index() {
		header('location:'.base_url());
	}
	
	function Set() {
		parent::Controller();
		
		$url = parse_url(base_url());
		#if (!preg_match('(^(http|https):\/\/'.$url['host'].')', $this->agent->referrer())) exit;//外部からの参照はNG
	}
}