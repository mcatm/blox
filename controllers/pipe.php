<?php

class Pipe extends Controller {
	
	/*
	
	$param:
		segment - id / page / offset
		content - type
	
	*/
	
	function _remap($method) {//URLマッピング
		$this->load->library('div');
		
		switch($method) {
			case 'index':
			case 'top':
				$this->div->get(array('where' => 'div_alias = "'.$this->setting->get_alias().'@top"'));
				if (empty($this->data->out['div'])) {
					$offset = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
					
					$this->data->set_array('div', array(
						array(
							'type'	=> 'top'
						)
					));
					
					$this->load->library('post');
					$this->post->get(array(
						'offset'	=> $offset,
						'uri_segment'	=> 1,
						'type'		=> 0,
						'pager'		=> true
					));
				}
				
				$this->_view();
			break;
			
			case $this->setting->get('url_alias_post')://記事
				$this->load->library('post');
				if (is_numeric($this->uri->segment(2))) {//記事詳細
					$post_id = (int)$this->uri->segment(2);
					$page = 0;//get page number
					for ($i = 3; $this->uri->segment($i); $i++) {
						if (preg_match('/^\?/', $this->uri->segment($i))) $page = trim(substr($this->uri->segment($i), 2));
					}
					$this->post->get(array(
						'id'		=> $post_id,
						'related'	=> true,
						'neighbor'	=> true,
						'pager'		=> false,
						'page'		=> $page,
						'qty'		=> 1
					));
					
					$this->log->get_access('access/'.$this->setting->get('url_alias_post').'/'.$this->uri->segment(2));
					
					if ($this->data->out['post']) {
						$this->data->set_array('div', array(
							array(
								'type'	=> 'post',
								'name'	=> $this->data->out['post'][0]['title'],
								'description'	=> $this->data->out['post'][0]['text'],
								'keyword'		=> $this->data->out['post'][0]['tag'],
								'title_clear'	=> false
							)
						));
						$this->_view(array(
							'detail'	=> true
						));
					} else {
						show_404();
					}
				} else {//記事一覧
					$offset = ($this->uri->segment(3)) ? (int)$this->uri->segment(3) : 0;
					
					$this->post->get(array(
						'base_url'	=> base_url().$this->setting->get('url_alias_post').'/offset/',
						'offset'	=> $offset
					));
										
					$this->log->get_access();
					
					if ($this->data->out['post']) {
						$this->data->set_array('div', array(
							array(
								'type'	=> 'post',
								'name'	=> $this->setting->get('url_alias_post'),
								'description'	=> "",
								'keyword'		=> "",
								'tpl'	=> 'list',
								'title_clear'	=> false
							)
						));
						$this->_view(array());//記事詳細
					} else {
						show_404();
					}
				}
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
						
						#if (isset($this->data->out['post'])) {
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
						$this->_view(array());
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
			
			case 'search'://検索
				$this->load->library('post');
				
				$query = $this->input->post('query');
				
				if (!$this->uri->segment(2) && $query) {
					header('location:'.base_url().'search/'.urlencode($query).'/');
				} else {
					$query = urldecode($this->uri->segment(2));
				}
				
				$offset = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				
				$this->post->get(array(
					'query'		=> $query,
					'base_url'	=> base_url().'search/'.$this->uri->segment(2).'/',
					'offset'	=> $offset,
					'uri_segment'	=> 3,
					'pager'		=> true
				));
				
				$this->setting->set_title('Search Results: '.$query);
				$this->setting->set_keyword(array($query));
				
				$this->load->view('list.php');
			break;
			
			case $this->setting->get('url_alias_user')://ユーザー表示
				$this->load->library(array('user', 'post'));
				if ($this->uri->segment(2) && $this->uri->segment(2) != 'offset') {//ユーザー詳細
					$offset = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
					$this->user->get(array(
						'account'	=> $this->uri->segment(2)
					));
					
					if (isset($this->data->out['user'])) {
						$this->post->get(array(
							'user'	=> $this->data->out['user'][0]['id'],
							'pager'		=> true,
							#'qty'		=> 1,
							'base_url'	=> base_url().'/'.$this->setting->get('url_alias_user').'/'.$this->uri->segment(2).'/',
							'offset'	=> $offset
						));
						print_r($this->data->out['post']);
					} else {
						show_404();
					}
				} else {//ユーザー一覧
					$offset = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
					$this->user->get(array(
						'offset'	=> $offset,
						'base_url'	=> base_url().'/'.$this->setting->get('url_alias_user').'/',
						'sort'		=> 'actiondate',
						'order'		=> 'desc',
						'reject_tmp_user'	=> true,
						'pager'		=> true
					));
					print_r($this->data->out['user']);
				}
			break;
			
			default:
				/*
				デフォルトでは一番最初に出てきた数字がID、
				/page/の次に出てきた数字がPAGE。
				ただし、$this->setting->get('url_segment_identifier_id')の次のsegmentがID
				$this->setting->get('url_segment_identifier_page')の次のsegmentがPAGE
				とすることも可能
				*/
				
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
						if (is_numeric($this->uri->segment($i)) && !isset($segment['id'])) {
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
	}
	
	function _view($param = array()) {
		$div = (isset($this->data->out['div'][0])) ? $this->data->out['div'][0] : array();
		
		$param['theme']	= (!empty($div['theme'])) ? $div['theme'] : $this->setting->get('theme');//テーマの確定
		if (!isset($param['tpl'])) $param['tpl']	= $this->_get_tpl($div, $param);//テンプレートの確定
		
		if (isset($div['content']) && is_array($div['content'])) {
			foreach ($div['content'] as $c) {
				$this->load->library($c['type']);
				$c['param']['offset'] = (isset($param['offset'])) ? $param['offset'] : 0;
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
			$site_title = (isset($div['name'])) ? $div['name'] : "";
			$site_description = (isset($div['description'])) ? $div['description'] : "";
			$this->setting->set_title($site_title, $flg_title);
			$this->setting->set_description(format_description($site_description, 300));
			$keyword = (isset($div['keyword'])) ? $div['keyword'] : array();
			$this->setting->set_keyword($keyword, true);
		}
		
		$this->setting->set('theme', $param['theme']);
		$this->load->view($param['tpl']);
	}
	
	
	function Pipe() {
		parent::Controller();
	}
	
	function _get_tpl($div = array(), $param = array()) {
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
	}
}