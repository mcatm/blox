<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module {
	
	var $module_path = "";
	var $admin_menu = array();
	
	function controller($mod, $mode = '') {
		$CI =& get_instance();
		$MD =& $CI->mod->$mod;
		$mod_loaded = $CI->setting->get('module_loaded');
		
		define('MOD_CONTROLLER', $mod);
		
		$uri_segment[] = 'top';
		for ($i=1;$CI->uri->segment($i);$i++) {
			if (is_numeric($CI->uri->segment($i))) break;
			if ($i != 1) $uri_segment[] = $CI->uri->segment($i);
		}
		#print_r($uri_segment);
		$ctl = $this->module_path.'controller/';
		foreach ($uri_segment as $i => $u) {
			$classname = $u;
			$ctlflg = false;
			
			if (is_file($ctl.$classname.'.php') && $classname != 'top') {
				$ctl .= $classname.'.php';
				$ctlflg = true;
			}
			
			if (!$ctlflg) {
				$ctl .= ($u != 'top') ? $classname.'/' : '';
				if (is_file($ctl.'top.php')) {
					if ((count($uri_segment) == 1 && $i == 0) || $i > 0) {
						$classname = 'top';
						$ctl .= 'top.php';
						$ctlflg = true;
					}
				}
			}
			$ctlpath = $ctl;
			
			if ($ctlflg) break;
		}
		
		//コントローラーが無くてtop.phpだけ存在する場合、最終的にtop.phpにアクセス
		if (!$ctlflg && is_file($ctl = $this->module_path.'controller/top.php')) {
			$ctlpath = $ctl;
			$classname = 'top';
			$ctlflg = true;
		}
		
		if ($ctlflg) {
			require_once($ctlpath);
			
			$method = (isset($uri_segment[$i+1])) ? $uri_segment[$i+1] : 'index';
			
			switch ($mode) {
				/*case 'admin';
					$MD->controller = new M_Admin_Controller;
					#$method = ($CI->uri->segment(4)) ? $CI->uri->segment(4) : "index";
					if (!empty($this->admin_menu)) $CI->data->out['admin_menu'] = $this->admin_menu;
				break;*/
				
				default:
					$classname = 'Mod_'.$classname;
					#exit($classname);
					$MD->controller = new $classname;
					#$method = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : "index";
				break;
			}
			if (!method_exists($MD->controller, $method)) show_404();//メソッドが存在しない場合、404
			$MD->controller->$method();
			exit;
		}
		
		show_404();
	}
	
	function view($param = array()) {
		$CI =& get_instance();
		
		/*
		
		$param
		type: content type
		
		*/
		
		$div = (isset($CI->data->out['div'][0])) ? $CI->data->out['div'][0] : array();
		$param = array_merge($param, $div);
		print_r($param);
		
		/*
		
		$param['theme']	= (!empty($div['theme'])) ? $div['theme'] : $this->setting->get('theme');//テーマの確定
		if (!isset($param['tpl'])) $param['tpl'] = $this->_get_tpl($div, $param);//テンプレートの確定
		
		if (isset($div['content']) && is_array($div['content'])) {
			foreach ($div['content'] as $c) {
				$this->load->library($c['type']);
				$c['param']['offset'] = (isset($param['segment']['offset'])) ? $param['segment']['offset'] : 0;
				$p = (isset($c['param']) && is_array($c['param']) && !empty($c['param'])) ? $c['param'] : array();
				$this->$c['type']->get($p);
			}
		}
		
		if (isset($param['detail']) && isset($param['segment']['id'])) {//詳細の場合
			$post_id = $param['segment']['id'];
			//記事一件を取得
			$where = array(
				'id'	=> $post_id,
				'related'	=> 10,
				'neighbor'	=> true,
				'schedule'	=> true,
				'access'	=> true,
				'comment'	=> true
			);
			
			if (isset($param['segment']['page']))	$where['page']		= $param['segment']['page'];
			if (isset($param['id_type']))			$where['id_type']	= $param['id_type'];
			
			if ($this->data->out['me']['auth']['type'] == "admin") $where['auth'] = 10;
			$this->post->get($where);
			
			if (isset($this->data->out['post'])) {
				//アクセス解析
				$access_path = 'access/'.$this->setting->get('url_alias_post').'/'.$post_id.'/';
				$this->log->get_access($access_path);
				$this->setting->set_title($this->data->out['post'][0]['title']);//タイトルセット
				$this->setting->set_description(format_description($this->data->out['post'][0]['text'], 120));//要約セット
			}
		} else {
			$flg_title = (isset($param['category']) || !isset($param['title_clear'])) ? false : true;
			#$flg_title = true;
			$site_title = (isset($div['name'])) ? $div['name'] : "";
			$this->setting->set_title($site_title, $flg_title);
			$site_description = (isset($div['description'])) ? $div['description'] : "";
			$this->setting->set_description(format_description($site_description, 300));
			$keyword = (isset($div['keyword'])) ? $div['keyword'] : array();
			$this->setting->set_keyword($keyword, true);
		}
		
		$this->setting->set('theme', $param['theme']);
		$this->load->view($param['tpl']);*/
	}
	
	function init($name, $module_path) {
		$CI =& get_instance();
		$this->module_path = $module_path;
		$config_path = $module_path.'config.php';
		$cfg_prefix = 'mod_'.$name.'_';
		if (is_file($config_path)) {
			require_once($config_path);
			
			if (!empty($config)) {
				foreach ($config as $k => $v) {
					$CI->setting->set($cfg_prefix.$k, $v);
				}
				if (!empty($admin_menu)) $this->admin_menu = $admin_menu;
			}
		}
		if (is_file(EX_FOLDER.'/language/'.$CI->config->item('language').'/'.$name.'_lang.php')) $CI->lang->load($name);//拡張言語ファイル読込
	}
	
	function Module() {
		
	}
}

?>