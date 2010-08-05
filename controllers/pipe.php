<?php

class Pipe extends Controller {
	
	function _remap($method) {//switch as url segment
		
		if ($method == 'index') $method = 'top';
		
		if ($mod_loaded = $this->setting->get('module_loaded')) {
			$this->load->library('module');
			if (array_key_exists($method, $mod_loaded)) {
				require_once($mod_loaded[$method]['path'].'core.php');
				$this->mod->$mod_loaded[$method]['name'] = new $mod_loaded[$method]['name'];
				$this->mod->$mod_loaded[$method]['name']->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
				$this->mod->$mod_loaded[$method]['name']->controller($mod_loaded[$method]['name']);
				exit;
			}
		}
		
		show_404();
	}
	
	
	
	/*function _remap($method) {//URLマッピング
		$this->load->library('div');
		
		switch($method) {
			case 'index':
			case 'top':
				$this->div->get(array('where' => 'div_alias = "'.$this->setting->get_alias().'@top"'));
				$offset = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
				#exit($offset);
				
				if (empty($this->data->out['div'])) {
					$this->data->set_array('div', array(
						array(
							'type'	=> 'top'
						)
					));
					
					$this->load->library('post');
					$this->post->get(array(
						'offset'		=> $offset,
						'uri_segment'	=> 1,
						'type'			=> 0,
						'pager'			=> true
					));
					
					$param = array();
				} else {
					#print_r($this->data->out['div']);
					$this->data->out['div'][0]['content'][0]['param']['uri_segment'] = 1;
					$param = array(
						'title_clear'		=> true
					);
				}
				
				$param['segment']['offset'] = $offset;
				$this->_view($param);
			break;
			
			case $this->setting->get('url_alias_category')://カテゴリ表示
				$this->load->library('post');
				if (!$this->uri->segment(2) || is_numeric($this->uri->segment(2))) {//カテゴリ一覧
					exit('categories');
				} else {
					$this->div->get(array('where' => 'div_alias = "'.$this->uri->segment(2).'"'));//カテゴリ詳細
					
					if (!empty($this->data->out['div'])) {
						$offset = ($this->uri->segment(4)) ? (int)$this->uri->segment(4) : 0;
						
						$this->post->get(array(
							'base_url'	=> base_url().$this->setting->get('url_alias_category').'/'.$this->uri->segment(2).'/offset/',
							'div'		=> $this->data->out['div'][0]['id'],
							'pager'		=> true,
							'offset'	=> $offset
						));
						
						$this->log->get_access();
						
						$theme = (!empty($this->data->out['div'][0]['theme'])) ? $this->data->out['div'][0]['theme'] : '';
						$tpl = (!empty($this->data->out['div'][0]['tpl'])) ? $this->data->out['div'][0]['tpl'] : 'list';
						$description = (!empty($this->data->out['div'][0]['description'])) ? $this->data->out['div'][0]['description'] : '';
						$keyword = (!empty($this->data->out['div'][0]['tag'])) ? $this->data->out['div'][0]['tag'] : '';
						
						#if (isset($this->data->out['post'])) {//test
							$this->data->set_array('div', array(
								array(
									'type'			=> 'post',
									'name'			=> $this->data->out['div'][0]['name'],
									'description'	=> $description,
									'keyword'		=> $keyword,
									'theme'			=> $theme,
									'tpl'			=> $tpl,
									'title_clear'	=> false
								)
							));
						#}
						$this->_view(array(
							'title_clear'		=> true
						));
					} else {
						show_404();
					}
				}
			break;
			
			case $this->setting->get('url_alias_bookmarklet')://ブックマークレット表示
				$this->setting->set('theme', 'home');
				$this->load->library(array('post', 'user', 'ext'));
				$this->div->get(array('type' => 'section', 'label' => 'section'));
				$this->div->get(array('type' => 'category', 'label' => 'category'));
				$this->user->get(array('qty' => 0, 'auth' => 'usertype_contributor'));
				$this->ext->get(array('stack' => true, 'div' => 'post'));
				
				$param = preg_split('/=|&|\?/', $this->uri->segment(3));
				
				if (is_array($param) && !empty($param)) {
					$flg = false;
					foreach($param as $v) {
						if (!empty($v) || $flg) {
							if ($flg) {
								$value[$flg] = urldecode($v);
								$flg = false;
							} else {
								$flg = $v;
							}
						}
					}
					$this->data->set_array('bookmarklet', $value);
				}
				#print_r($this->data->out['bookmarklet']);exit;
				$this->post->clear();
				$this->setting->set_title($this->lang->line('system_post_add').' '.$this->lang->line('system_label_post'));
				$this->load->view('bookmarklet.php');
			break;
			
			
			
			case $this->setting->get('url_alias_user')://ユーザー表示
				
			break;
			
			default:
				
				#
				# extensionから確認
				#
				
				#print '2: get extensions<br />';
				#print_r($this->setting->get('extension_loaded'));
				if (in_array($method, $this->setting->get('extension_loaded'))) {
					$this->extension->$method->controller($method);
					exit;
				}
				
				
				# デフォルトでは一番最初に出てきた数字がID、
				# /page/の次に出てきた数字がPAGE。
				# ただし、$this->setting->get('url_segment_identifier_id')の次のsegmentがID
				# $this->setting->get('url_segment_identifier_page')の次のsegmentがPAGE
				# とすることも可能
				
				//URLからmethodを呼び出し
				$flg_next_seg = "";
				$segment['offset'] = 0;
				
				//URL解釈用キー
				$url_segment_identifier_id		= $this->setting->get_explode_value('url_segment_identifier_id', '|');
				$url_segment_identifier_page	= $this->setting->get_explode_value('url_segment_identifier_page', '|');
				$url_segment_identifier_offset	= $this->setting->get_explode_value('url_segment_identifier_offset', '|');
				$url_segment_identifier_stop	= $this->setting->get_explode_value('url_segment_identifier_stop', '|');
				
				for ($i=1; $this->uri->segment($i); $i++) {
					if ($this->uri->segment($i)) {
						#print $this->uri->segment($i).'/';
						if (is_numeric($this->uri->segment($i)) && !isset($segment['id']) && empty($flg_next_seg)) {
							$segment['id']		= $this->uri->segment($i);
							$flg_end_method		= true;
						} else {
							if (in_array($this->uri->segment($i), $url_segment_identifier_stop))	$flg_end_method = true;
							if (in_array($this->uri->segment($i), $url_segment_identifier_offset))	$flg_end_method = true;
							
							if (!isset($flg_end_method) && $i!=1) $method .= "/".$this->uri->segment($i);
							if (!empty($flg_next_seg)) $segment[$flg_next_seg] = $this->uri->segment($i);
							
							$flg_next_seg = "";
							
							if (in_array($this->uri->segment($i), $url_segment_identifier_id)) {
								$flg_next_seg	= 'id';
								$flg_end_method	= true;
							}
							
							if (in_array($this->uri->segment($i), $url_segment_identifier_page))	$flg_next_seg = 'page';
							if (in_array($this->uri->segment($i), $url_segment_identifier_offset))	$flg_next_seg = 'offset';
						}
					} else {
						break;
					}
				}
				
				$this->div->get(array('where' => 'div_alias = "'.$this->setting->get_alias($method).'"'));
				#print_r($segment);#exit;
				
				if (isset($this->data->out['div'])) {
					if ($this->data->out['div'][0]['type'] == 'post') {//分類が記事詳細の場合
						$param = array(
							'segment'	=> $segment,
							'id_type'	=> $this->data->out['div'][0]['id_type']
						);
						if (isset($segment['id'])) $param['detail'] = true;
					} elseif ($this->data->out['div'][0]['type'] == 'mail') {//分類がメールの場合
						$this->load->library('mail');
						$theme = $this->setting->get('theme');
						
						$msg = $this->mail->set();
						
						$param = array(
							'segment' => $segment
						);
						
						$this->setting->set('theme', $theme);
						if (!isset($msg['result']) || $msg['result'] == 'error') {
							$param['tpl'] = $this->data->out['div'][0]['tpl'];
						} else {
							if ($this->input->post('confirm') == 'true') {
								$param['tpl'] = $this->data->out['div'][0]['tpl'].'.confirm.php';
							} else {
								$param['tpl'] = $this->data->out['div'][0]['tpl'].'.finish.php';
							}
						}
					} else {
						$param = array(
							'segment' => $segment,
							'offset'	=> $segment['offset']
						);
					}
					$this->_view($param);
				} else {
					show_404();
				}
			break;
		}
	}*/
	
	
	function Pipe() {
		parent::Controller();
	}
	
	/*function _get_tpl($div = array(), $param = array()) {
		if (empty($div['tpl'])) {//テンプレートの確定
			switch ($div['type']) {
				case 'top':
					$tpl = 'top.php';
				break;
				
				case 'category':
				case 'section':
					$tpl = (isset($param['detail'])) ? 'div.detail.php' : 'list.php';//詳細の場合、テンプレートに.detailを付ける
				break;
				
				case 'post':
					$tpl = $this->data->out['div'][0]['type'];
					$tpl .= (isset($param['detail'])) ? '.detail.php' : '.php';//詳細の場合、テンプレートに.detailを付ける
				break;
				
				default:
					$tpl	= 'page.php';
				break;
			}
		} else {
			$tpl = $div['tpl'];
			$tpl .= (isset($param['detail'])) ? '.detail.php' : '.php';//詳細の場合、テンプレートに.detailを付ける
		}
		
		return $tpl;
	}*/
}