<?php

class Pipe extends Controller {

	function _post_detail($param = array()) {//記事詳細
		$this->load->library('post');
		if (isset($param['segment']['id'])) {
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
			if (isset($param['segment']['page'])) $where['page'] = $param['segment']['page'];
			if (isset($param['id_type'])) $where['id_type'] = $param['id_type'];
			
			if (isset($this->data->out['me']['auth']['view_draft'])) $where['auth'] = 10;
			$this->post->get($where);
			
			if (isset($this->data->out['post'])) {
				$access_path = 'access/'.$this->setting->get('url_alias_post').'/'.$post_id.'/';
				$this->log->get_access($access_path);//アクセス解析
				$this->setting->set_title($this->data->out['post'][0]['title']);
				$this->setting->set_description(format_description($this->data->out['post'][0]['text'], 120));
				if (isset($param['theme'])) $this->setting->set('theme', $param['theme']);
				$tpl = (isset($param['tpl'])) ? $param['tpl'] : 'post';
				$tpl .= '.detail.php';
				$this->load->view($tpl);
				return;
			}
		}
		show_404();
	}
	
	function _view($segment = array()) {//リスト
		if (isset($this->data->out['div'])) {
			$content = $this->data->out['div'][0]['content'];
		} else {
			$content = array(
				array(
					'type'			=> 'post',
					'param'			=> array(
						'auth'			=> 0,
						'type'			=> 0,
						'uri_segment'	=> 1
					)
				)
			);
		}
		
		if (!empty($content) && is_array($content)) {
			foreach ($content as $c) {
				$this->load->library($c['type']);
				$c['param']['offset'] = $segment['offset'];
				$param = (isset($c['param']) && is_array($c['param']) && !empty($c['param'])) ? $c['param'] : array();
				$this->$c['type']->get($param);
			}
		}
		
		if (!empty($this->data->out['div'][0]['theme'])) $this->setting->set('theme', $this->data->out['div'][0]['theme']);
		if (isset($this->data->out['div'][0]['name'])) {
			$this->setting->set_title($this->data->out['div'][0]['name'], true);
		} else {
			$this->setting->set_title('post');
		}
		if (isset($this->data->out['div'][0]['description'])) $this->setting->set_description($this->data->out['div'][0]['description']);
		$tpl = (!empty($this->data->out['div'][0]['tpl'])) ? $this->data->out['div'][0]['tpl'].'.php' : 'list.php';
		
		$this->load->view($tpl);
	}
	
	function top($o = 0) {//トップページ
		$this->load->library(array('post'));
		
		$this->post->get(array(
			'qty'			=> $this->setting->get('post_max_qty_per_page'),
			'offset'		=> $o,
			'auth'			=> 0,
			'type'			=> 0,
			'uri_segment'	=> 1
		));
		
		$this->load->view('top');
	}
	
	function _view_category() {
		if ($this->data->out['div'][0]['theme'] != "") $this->setting->set('theme', $this->data->out['div'][0]['theme']);
		$this->setting->set_title($this->data->out['div'][0]['name']);
		$this->load->view($this->data->out['div'][0]['tpl'].'.php');
	}
	
	function _remap($method) {//URLマッピング
		switch ($method) {
			case 'index':
			case 'top':
				$this->load->library('div');
				
				$this->div->get(array('where' => 'div_alias = "'.$this->setting->get_alias().'@top"'));
				#print_r($this->data->out['div']);exit('UUUUU');
				if (isset($this->data->out['div'])) {
					$param = array(
						'segment' => array('offset' => 0),
						'offset'	=> 0
					);
					if (!empty($this->data->out['div'][0]['tpl'])) $param['tpl'] = $this->data->out['div'][0]['tpl'];
					if (!empty($this->data->out['div'][0]['theme'])) $param['theme'] = $this->data->out['div'][0]['theme'];
					$this->_view($param);
				} else {
					$this->top($this->uri->segment(1));
				}
			break;
			
			case 'search':
				$this->load->library('post');
				
				$where = array(
					'query' => $this->uri->segment(2),
				);
				
				$this->post->get($where);
				$this->load->view('list.php');
			break;
			
			/*case $this->setting->get('url_alias_user')://ユーザー表示
				if ($this->uri->segment(2) != "" && (int)$this->uri->segment(2) != 0) {
					$this->_user_detail($this->uri->segment(2));//ユーザー詳細
				} else {
					$this->_user();//ユーザー一覧
				}
			break;*/
			
			case $this->setting->get('url_alias_category')://カテゴリ表示
				if ($this->uri->segment(2) != "" && (int)$this->uri->segment(2) != 0) {
					$this->_category_detail($this->uri->segment(2));
				} else {
					$this->div->get(array('where' => 'div_alias = "'.$this->uri->segment(2).'"'));
					
					$this->load->library('post');
					$where = array(
						'div'	=> $this->data->out['div'][0]['id']
					);
					$this->post->get($where);
					
					$this->_view_category();//カテゴリ一覧
				}
			break;
			
			default:
				$this->load->library('div');
				
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
				
				$url_segment_identifier_id = ($this->setting->get('url_segment_identifier_id')) ? explode('|', $this->setting->get('url_segment_identifier_id')) : array();
				$url_segment_identifier_page = ($this->setting->get('url_segment_identifier_page')) ? explode('|', $this->setting->get('url_segment_identifier_page')) : array();
				$url_segment_identifier_offset = ($this->setting->get('url_segment_identifier_offset')) ? explode('|', $this->setting->get('url_segment_identifier_offset')) : array();
				$url_segment_identifier_stop = ($this->setting->get('url_segment_identifier_stop')) ? explode('|', $this->setting->get('url_segment_identifier_stop')) : array();
				
				for ($i=1;$this->uri->segment($i);$i++) {
					if ($this->uri->segment($i)) {
						if (is_numeric($this->uri->segment($i)) && !isset($segment['id'])) {
							$segment['id'] = $this->uri->segment($i);
							$flg_end_method = true;
						} else {
							if (in_array($this->uri->segment($i), $url_segment_identifier_stop)) $flg_end_method = true;
							if (in_array($this->uri->segment($i), $url_segment_identifier_offset)) $flg_end_method = true;
							
							if (!isset($flg_end_method) && $i!=1) $method .= "/".$this->uri->segment($i);
							
							if (!empty($flg_next_seg)) $segment[$flg_next_seg] = $this->uri->segment($i);
							
							$flg_next_seg = "";
							
							if (in_array($this->uri->segment($i), $url_segment_identifier_id)) {
								$flg_next_seg = 'id';
								$flg_end_method = true;
							}
							
							if (in_array($this->uri->segment($i), $url_segment_identifier_page)) $flg_next_seg = 'page';
							if (in_array($this->uri->segment($i), $url_segment_identifier_offset)) $flg_next_seg = 'offset';
						}
					} else {
						break;
					}
				}
				
				$this->div->get(array('where' => 'div_alias = "'.$this->setting->get_alias($method).'"'));
				
				if (isset($this->data->out['div'])) {
					if ($this->data->out['div'][0]['type'] == 'post') {//分類が記事詳細の場合
						if (isset($segment['id'])) {
							$param = array(
								'segment' => $segment,
								'id_type'	=> $this->data->out['div'][0]['id_type']
							);
							if (!empty($this->data->out['div'][0]['tpl'])) $param['tpl'] = $this->data->out['div'][0]['tpl'];
							if (!empty($this->data->out['div'][0]['theme'])) $param['tpl'] = $this->data->out['div'][0]['theme'];
							$this->_post_detail($param);//記事詳細
							break;
						}
					}
					
					$param = array(
						'segment' => $segment,
						'offset'	=> $segment['offset']
					);
					if (!empty($this->data->out['div'][0]['tpl'])) $param['tpl'] = $this->data->out['div'][0]['tpl'];
					if (!empty($this->data->out['div'][0]['theme'])) $param['theme'] = $this->data->out['div'][0]['theme'];
					$this->_view($param);//記事一覧
					break;
				} elseif ($method == $this->setting->get('url_alias_post')) {
					if (is_numeric($this->uri->segment(2))) {
						$this->_post_detail(array(
							'segment' => $segment
						));//記事詳細
					} else {
						$this->_view($segment);//記事一覧
					}
					break;
				}
				show_404();
			break;
		}
	}
	
	function Pipe() {
		parent::Controller();
	}
}