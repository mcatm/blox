<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Post {
	
	var $msg;
	
	function get($user_param = array()) {
		$CI =& get_instance();
		$CI->load->library(array('pagination', 'user'));
		$label = explode(',', $CI->setting->get('url_alias_post'));//記事のラベル
		
		$param = array(//デフォルトの設定
			'auth'			=> 0,
			'base_url'		=> base_url(),
			'comment'		=> false,
			'ext'			=> true,
			'file'			=> false,
			'file_main'		=> true,
			'file_main_arr'	=> array(),
			'history'		=> false,
			'id'			=> 0,
			'id_type'		=> 'id',
			'label'			=> 'post',
			'neighbor'		=> false,
			'num_links'		=> 4,
			'offset'		=> 0,
			'order'			=> 'desc',
			'pager'			=> true,
			'get_parent'	=> false,
			'qty'			=> $CI->setting->get('post_max_qty_per_page'),
			'query'			=> "",
			'schedule'		=> false,
			'sort'			=> 'createdate',
			'stack'			=> true,
			'tag'			=> true,
			'uri_segment'	=> 2,
			'user'			=> 0,
			'where'			=> ""
		);
		
		$param = array_merge($param, $user_param);
		$param['id_type'] = 'post_'.$param['id_type'];
		
		$CI->db->start_cache();
		
		if (!isset($param['deleted'])) $CI->db->where('post_deleted', 0);//削除されているものを読み込まない
		
		// ---------------------- 条件定義
		$CI->db->select('*, post_id AS id, post_status AS status, post_createdate AS createdate, post_type AS type');
		$CI->db->join(DB_TBL_LINX, 'post_id = linx_a');
		$CI->db->group_by('post_id');
		
		if (isset($param['div'])) {
			$CI->db->where('linx_type', 'post2div');
		} else {
			$CI->db->where('linx_type', 'post2user');
		}
		
		if ($param['where'] != "") $CI->db->where('('.$param['where'].')');//where文が直接書き込まれている場合
		if ($param['user'] > 0) $CI->db->where('linx_b', $param['user']);//ユーザー指定
		
		if (isset($param['div'])) $CI->db->where('linx_b', $param['div']);//セクション指定
		
		if (isset($param['parent'])) $CI->db->where('post_parent', $param['parent']);//親記事
		if (isset($param['status'])) $CI->db->where('post_status', $param['status']);//状態
		
		if (isset($param['id'])) {
			if (!is_array($param['id']) && !empty($param['id'])) {
				$CI->db->where($param['id_type'], $param['id']);//個別ID
			} elseif (is_array($param['id'])) {
				$where_id = "";
				foreach ($param['id'] as $p_id) $where_id .= ' OR '.$param['id_type'].' = '.$p_id;
				$where_id = "(".substr($where_id, 4).")";
				$CI->db->where($where_id);
			}
		}
		
		if (isset($param['number'])) $CI->db->where('post_number', $param['number']);
		if (isset($param['type'])) $CI->db->where('post_type', $param['type']);//記事タイプ
		if (!empty($param['query'])) {//検索キー
			$CI->db->like('post_meta', $param['query']);
			$CI->data->out['query'] = $param['query'];
		}
		
		if ($param['auth'] !== 'cron') {
			$auth_where = '(post_status <= '.$param['auth'];
			if (!empty($CI->data->out['me']['id']) && defined('ADMIN_MODE') && !isset($param['div'])) $auth_where .= ' OR linx_b = '.$CI->data->out['me']['id'];
			$auth_where .= ')';
			$CI->db->where($auth_where);
		}
		// ---------------------- 条件定義ココまで
		
		$CI->db->stop_cache();
		#exit($CI->db->count_all_results(DB_TBL_POST, false));
		$count = $CI->db->count_all_results(DB_TBL_POST, false);
		
		if ($param['qty'] == 0) $param['qty'] = $count;//qtyが0の場合は、全てを選択
		
		if (isset($param['count'])) {
			$CI->db->flush_cache();
			return $count;//カウントを返すだけ
		}
		#print_r($param);
		#print $count.'<br />';
		
		if ($count > 0) {//if the posts exist
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			if ($param['order'] == 'rand' || $param['order'] == 'random') {
				$CI->db->order_by('rand()');
			} else {
				$CI->db->order_by('post_'.$param['sort'], $param['order']);
			}
			
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_POST, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_POST, $param['qty'], $param['offset']), $param);
				$CI->db->flush_cache();
				return $out;
			}
			
			$CI->db->flush_cache();
			
			//get a status of pages.
			if ($param['pager']) {
				$CI->data->set_array('page', array(
					'total'			=> $CI->pagination->total_rows,
					'current'		=> $CI->pagination->cur_page,
					'qty'			=> $CI->pagination->per_page,
					'offset'		=> $param['offset'],
					'pager'			=> $CI->pagination->create_links()
				));
			}
			
			if (isset($CI->data->out[$param['label']])) {
				foreach($CI->data->out[$param['label']] as $k => $v) {//add extra datas
					
					$linx = array();
					$linx['all'] = $CI->data->set($CI->linx->get_related('post', $v['id']), array('label' => 'linx', 'stack' => false));
					
					if (!empty($linx['all'])) {
						foreach ($linx['all'] as $l) {
							switch ($l['type']) {
								case 'post2user'://authors
									$linx['user'][] = $l;
								break;
								
								case 'post2tag'://tags
									$linx['tag'][] = $l;
								break;
								
								case 'post2file'://files
									$linx['file'][] = $l;
								break;
								
								case 'post2div'://div
									$linx['div'][] = $l;
								break;
								
								case 'post2ext'://ext
									$linx['ext'][] = $l;
								break;
							}
						}
					}
							
					//authors
					$user_where = array();
					if (isset($linx['user']) && is_array($linx['user'])) {
						$user_where = array();
						foreach($linx['user'] as $k2 => $v2) $user_where[] = $v2['b'];
						$CI->user->get(array('id' => $user_where, 'label' => 'tmp_author', 'pager' => false));
						if (isset($CI->data->out['tmp_author'])) {
							$CI->data->out[$param['label']][$k]['author'] = $CI->data->out['tmp_author'];
							unset($CI->data->out['tmp_author']);
						} else {
							$CI->data->out[$param['label']][$k]['author'] = $CI->user->get_anonymous();//if an author wasn't set.
						}
					} else {
						$CI->data->out[$param['label']][$k]['author'] = $CI->user->get_anonymous();//if an author wasn't set.
					}
					$CI->data->out[$param['label']][$k]['author_id'] = $user_where;
					
					//main files
					if ($param['file_main'] && isset($linx['file'])) {
						$CI->load->library('file');
						if (!empty($linx['file']) && is_array($linx['file'])) {
							foreach ($linx['file'] as $fl) {
								$file_label = (!empty($fl['status'])) ? 'file_'.$fl['status'] : 'file';
								$CI->data->out[$param['label']][$k][$file_label] = $CI->file->get(array('id' => $fl['b'], 'stack' => false));
							}
						}
					}
					
					//tags
					if ($param['tag'] && isset($linx['tag'])) {
						$CI->load->library('tag');
						if (is_array($linx['tag'])) {
							$tag_where = array();
							foreach($linx['tag'] as $k2 => $v2) $tag_where[] = $v2['b'];
							$CI->data->out[$param['label']][$k]['tag'] = $CI->tag->get(array(
								'id' => $tag_where,
								'stack' => false,
								'pager' => false
							));
							
							$CI->data->out[$param['label']][$k]['tagstr'] = $CI->tag->_merge_tag($CI->data->out[$param['label']][$k]['tag']);
						} else {
							$CI->data->out[$param['label']][$k]['tag'] = array();
						}
						
						if (isset($param['related']) && !empty($CI->data->out[$param['label']][$k]['tag'])) {//related entries
							$CI->data->out[$param['label']][$k]['related'] = $CI->tag->_get_related($CI->data->out[$param['label']][$k]['tag'], array(
								'label' => 'post',
								'tagstr' => $CI->data->out[$param['label']][$k]['tagstr'],
								'content' => htmlspecialchars($v['title']).'/'.htmlspecialchars($v['text']),
								'qty'	=> $param['related'],
								'id'	=> $v['id']
							));
						}
					}
					
					//sections and categories
					$div_where = array();
					if (isset($linx['div'])) {
						$CI->load->library('div');
						foreach($linx['div'] as $dk => $dv) {
							$div_where[] = $dv['b'];
						}
						$div_value = $CI->div->get(array('id' => $div_where, 'stack' => false));
						if (isset($div_value) && !empty($div_value)) {
							foreach ($div_value as $divv) {
								switch ($divv['type']) {
									case 'section':
									$CI->data->out[$param['label']][$k]['section'][] = $divv;
									break;
									
									case 'category':
									$CI->data->out[$param['label']][$k]['category'][] = $divv;
									break;
									
									default:
									$CI->data->out[$param['label']][$k]['div'][] = $divv;
									break;
								}
							}
						}
					}
					$CI->data->out[$param['label']][$k]['div_id'] = $div_where;
					
					if ($param['ext'] && isset($linx['ext'])) {//extra contents
						$CI->load->helper('array');
						if (!empty($linx['ext']) && is_array($linx['ext'])) {
							foreach ($linx['ext'] as $ex) {
								$ex_value = decompress_array($ex['param']);
								if (!empty($ex_value) && is_array($ex_value)) $CI->data->out[$param['label']][$k][$ex['status']] = $ex_value['value'];
							}
						}
					}
					
					
					
					
					
					
					
					/*if ($param['file']) {//files
						$CI->load->library('file');
						$file_linx = $CI->linx->get('post2file', array('a' => $v['id']));
						if (is_array($file_linx)) {
							$file_where = array();
							foreach($file_linx as $k2 => $v2) $file_where[] = $v2['b'];
							$CI->data->out[$param['label']][$k]['file'] = $CI->file->get(array('id' => $file_where, 'stack' => false));
						}
					}*/
					
					
					
					
					
					
					
					
					
					/*if ($param['schedule']) {//schedule
						$CI->load->helper('date');
						$sc = array('start', 'end');
						$sc_flg = true;
						
						foreach ($sc as $sc_label) {
							$sc_linx = array();
							$sc_linx = $CI->linx->get('post2'.$sc_label.'date', array('a' => $v['id']));
							
							if (isset($sc_linx) && is_array($sc_linx)) {
								foreach ($sc_linx as $sd) {
									$CI->data->out[$param['label']][$k]['schedule'][$sc_label][] = $sd['status'];
									
									if ($sc_label == 'start') {
										if (mysql_to_unix($sd['status']) > time()) $sc_flg = false;
									} elseif ($sc_label == 'end') {
										if (mysql_to_unix($sd['status']) < time()) $sc_flg = false;
									}
								}
							}
						}
						$CI->data->out[$param['label']][$k]['schedule']['flg'] = $sc_flg;
					}*/
					
					if ($param['neighbor']) {
						$neighbor_set = array(
							array(
								'label' => 'next',
								'where' => 'post_createdate > "'.$v['createdate'].'" AND post_status = 0',
								'order' => 'asc'
							), array(
								'label' => 'prev',
								'where' => 'post_createdate < "'.$v['createdate'].'" AND post_status = 0',
								'order' => 'desc'
							)
						);
						foreach ($neighbor_set as $n) {
							$n_type = (isset($param['type'])) ? $param['type'] : 0;
							$this->get(array(
								'where' => $n['where'],
								'qty' => 1,
								'type'	=> $n_type,
								'order' => $n['order'],
								'label' => 'tmp_neighbor'
							));
							if (!empty($CI->data->out['tmp_neighbor'])) {
								$CI->data->out[$param['label']][$k]['neighbor'][$n['label']] = $CI->data->out['tmp_neighbor'][0];
								unset($CI->data->out['tmp_neighbor']);
							}
						}
					}
					
					if ($param['comment']) {//comment
						$this->get(array(
							'type'		=> 1,
							'parent'	=> $v['id'],
							'label'		=> 'tmp_comment'
						));
						if (!empty($CI->data->out['tmp_comment'])) {
							$CI->data->out[$param['label']][$k]['comment'] = $CI->data->out['tmp_comment'];
							unset($CI->data->out['tmp_comment']);
						}
					}
					
					if ($param['get_parent'] && !empty($v['parent'])) {//get a parent
						$this->get(array(
							'id'	=> $v['parent'],
							'pager'	=> false,
							'label'	=> 'tmp_parent'
						));
						if (!empty($CI->data->out['tmp_parent'])) {
							$CI->data->out[$param['label']][$k]['parent'] = $CI->data->out['tmp_parent'][0];
							unset($CI->data->out['tmp_parent']);
						}
					}
					
					$CI->data->out[$param['label']][$k]['url'] = $this->_make_permalink($label[0], $v['id'], $v['alias']);//permalink
					
					//multi urls
					if (count($label) > 1) {
						foreach($label as $su) $CI->data->out[$param['label']][$k]['urls'][$su] = $this->_make_permalink($su, $v['id'], $v['alias']);
					}
					
					if (defined('ADMIN_MODE') && ADMIN_MODE === true) {
						$CI->data->out[$param['label']][$k]['admin_url'] = base_url().'admin/post/'.$v['id'].'/';//url for admin
						$CI->data->out[$param['label']][$k]['edit_url'] = base_url().'admin/post/edit/'.$v['id'].'/';//url for admin to edit
					}
					
					if ($v['title'] == "") $CI->data->out[$param['label']][$k]['title'] = mb_substr(strip_tags($v['text']), 0, 40, 'utf-8');//if it don't have a title.
					if ($param['history']) {//change histories
						$CI->log->get('history/post/'.$v['id'].'/', array('label' => 'history'));
						if (isset($CI->data->out['history'])) $CI->data->out[$param['label']][$k]['history'] = $CI->data->out['history'];
						unset($CI->data->out['history']);
					}
					
					//text
					$text = htmlspecialchars_decode($v['text']);
					$separator = $CI->setting->get_formattag('page');
					$CI->data->out[$param['label']][$k]['text'] = $text;
					$CI->data->out[$param['label']][$k]['paragraph'] = explode($separator, $text);
					
					//page
					if (isset($param['page'])) {
						$p = ($param['page']) ? (int)$param['page'] - 1 : 0;
						$CI->data->out[$param['label']][$k]['page'] = $p;
						
						if (count($CI->data->out[$param['label']][$k]['paragraph']) > 1) {
							$CI->data->out[$param['label']][$k]['pager'] = array(
								'current'	=> $p + 1,
								'base_url'	=> trim(self_url(), '/').'/?p',
								'total'		=> count($CI->data->out[$param['label']][$k]['paragraph'])
							);
							//print_r($CI->data->out[$param['label']][$k]['pager']);exit;
						}
					}
				}
			}
		}
		$CI->db->flush_cache();
	}
	
	function count($user_param = array()) {
		$user_param['count'] = true;
		return $this->get($user_param);
	}
	
	function ondate($user_param = array()) {
		$CI =& get_instance();
		$CI->load->helper('date');
		$id = array();
		
		if (isset($user_param['startdate'])) {
			$where = "(`linx_type` = 'post2startdate' AND linx_status < '".convert_day($user_param['startdate'])."')";
			if ($ondate_start = $CI->linx->get('post2startdate', array('where' => $where))) {
				foreach ($ondate_start as $st) $start_id[] = $st['a'];
			}
		}
		
		if (isset($user_param['enddate'])) {
			$where = "(`linx_type` = 'post2enddate' AND `linx_status` > '".convert_day($user_param['enddate'])."')";
			if ($ondate_end = $CI->linx->get('post2enddate', array('where' => $where))) {
				foreach ($ondate_end as $ed) {
					if (isset($start_id)) {
						if (in_array($ed['a'], $start_id)) $id[] = $ed['a'];
					} else {
						$id[] = $ed['a'];
					}
				}
			}
		}
		
		if (empty($id) && isset($start_id)) $id = $start_id;
		
		if (!empty($id)) {
			$user_param['id'] = $id;
			$this->get($user_param);
		}
	}
	
	var $validation_rule = array(
			array(
				'field'   => 'title',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'text',
				'label'   => 'lang:system_post_label_text',
				'rules'   => 'required|htmlspecialchars'
			),
			array(
				'field'   => 'alias',
				'label'   => 'lang:system_post_label_alias',
				'rules'   => 'xss_clean'
			),
			array(
				'field'   => 'div[]',
				'label'   => 'lang:system_post_label_div',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'author[]',
				'label'   => 'lang:system_post_label_author',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'status',
				'label'   => 'lang:system_post_label_status',
				'rules'   => 'required|xss_clean'
			),
			array(
				'field'   => 'file[]',
				'label'   => 'lang:system_post_label_file',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'tagstr',
				'label'   => 'lang:system_post_label_tag',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'parent',
				'label'   => 'lang:system_post_label_parent',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'type',
				'label'   => 'lang:system_post_label_type',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'createdate',
				'label'   => 'lang:system_post_label_createdate',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'startdate[]',
				'label'   => 'lang:system_post_label_startdate',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'enddate[]',
				'label'   => 'lang:system_post_label_enddate',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'number',
				'label'   => 'lang:system_post_label_number',
				'rules'   => 'trim|xss_clean'
			)
		);
	
	function set($id = 0) {
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation', 'user', 'ext'));
		
		$CI->form_validation->set_error_delimiters($CI->setting->get('output_error_open'), $CI->setting->get('output_error_close'));//エラーメッセージの囲み
		
		$CI->load->helper(array('form', 'date'));
		$now = now();
		$author_id = 0;
		
		if ($id == 0 && $CI->input->post('id')) $id = $CI->input->post('id');//POSTからIDを取得
		
		if ($id > 0) $this->get(array('id' => $id));//エントリを取得
		
		//記事拡張を読込
		$CI->ext->get(array('stack' => true, 'div' => 'post'));
		if (isset($CI->data->out['ext'])) {
			$ext = $CI->data->out['ext'];
			if (!empty($ext) && is_array($ext)) {
				foreach ($ext as $e) {
					$this->validation_rule[] = array(
						'field'		=> 'ext_'.$e['field'],
						'rules'		=> $e['rule'],
						'label'		=> $e['label']
					);
				}
			}
		}
		
		$CI->form_validation->set_rules($this->validation_rule);
		if (isset($CI->data->out['me']['id'])) {
			if ($id == 0 && !$CI->auth->check_auth('post')) {
				$flg_cnt_post = true;//記事を書く権限がない場合
			} elseif (isset($CI->data->out['post'][0]['author']) && $CI->auth->check_auth()) {//他人のエントリを編集する権限がない場合
				foreach($CI->data->out['post'][0]['author'] as $ka => $va) {
					if ($va['id'] == $CI->data->out['me']['id']) $flg_cnt_post = true;
				}
			}
		}
		
		#if ($CI->input->post('postauth')) $flg_cnt_post = true;//コメント等の場合は、権限設定パス
		
		if (!isset($flg_cnt_post)) $msg_stop_post = $CI->lang->line('system_post_error_auth');
		
		if (isset($msg_stop_post) && !$CI->auth->check_auth('post') && $CI->input->post('type') != 1) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $msg_stop_post
			);
		} else {
			if ($CI->form_validation->run() == FALSE) {
				$this->msg = array(
					'result'	=> 'error',
					'msg'		=> $CI->lang->line('system_post_error')
				);
			} else {
				$alias = (set_value('alias') != "") ? set_value('alias') : set_value('title');//記事のエイリアス設定
				if ($id == 0) {//新規投稿
					$createdate = (set_value('createdate') === 'now' || set_value('createdate') == "") ? $now : set_value('createdate');//投稿日時設定
					$arr = array(
						'post_title'			=> set_value('title'),
						'post_text'				=> htmlspecialchars_decode(set_value('text')),
						'post_type'				=> set_value('type'),
						'post_alias'			=> format_alias($alias),
						'post_status'			=> set_value('status'),
						'post_number'			=> set_value('number'),
						'post_parent'			=> set_value('parent'),
						'post_createdate'		=> $createdate,
						'post_modifydate'		=> $now
					);
					$arr['post_meta'] = $this->_get_meta($arr);
					
					$id = $this->_set_post($arr);
					
					//著者登録
					$author = set_value('author[]');
					if (empty($author) && $CI->input->post('author')) $author[] = $CI->input->post('author');
					if (isset($author) && is_array($author) & !empty($author)) {
						foreach($author as $ak => $av) {
							$set = array(
								'a'		=> $id,
								'b'		=> $av
							);
							$CI->linx->set('post2user', $set);
						}
					} else if (isset($CI->data->out['me']['id']) && $CI->data->out['me']['id'] != 0) {
						$CI->linx->set('post2user', array(
							'a'		=> $id,
							'b'		=> $CI->data->out['me']['id'],
							'status'	=> 'main'
						));
						$author_id = $CI->data->out['me']['id'];
					}
					
					//ファイル
					$file = set_value('file[]');
					if (isset($file) && is_array($file) & !empty($file)) {
						$CI->load->library('linx');
						foreach($file as $fk => $fv) {
							$set = array(
								'a'		=> $id,
								'b'		=> $fv
							);
							if ($fk == 0) $set['status'] = 'main';
							$CI->linx->set('post2file', $set);
						}
					}
					
					//セクション登録
					$div = set_value('div[]');
					if (isset($div) && is_array($div) & !empty($div)) {
						$CI->load->library('linx');
						foreach($div as $dk => $dv) {
							$set = array(
								'a'		=> $id,
								'b'		=> $dv
							);
							$CI->linx->set('post2div', $set);
						}
					}
					
					//タグ登録
					$CI->load->library('tag');
					$CI->tag->set($CI->input->post('tagstr'), 'post2tag', $id, $author_id);
					
					if ($CI->setting->get('flg_post_tweet')) {//記事をtweetする
						$set = array(
							'a'		=> $id,
							'status'	=> 'twitter'
						);
						$CI->linx->set('post2extapp', $set);
					}
				} else {//記事編集
					$createdate = (set_value('createdate') === 'now') ? $now : set_value('createdate');//投稿日時設定
					$arr = array(
						'post_title'			=> set_value('title'),
						'post_text'				=> htmlspecialchars_decode(set_value('text')),
						'post_type'				=> set_value('type'),
						'post_number'			=> set_value('number'),
						'post_alias'			=> format_alias($alias),
						'post_parent'			=> set_value('parent'),
						'post_status'			=> set_value('status'),
						'post_createdate'		=> $createdate,
						'post_modifydate'		=> $now
					);
					$arr['post_meta'] = $this->_get_meta($arr);
					
					$id = $this->_set_post($arr, $id);
					
					//著者登録
					$author = set_value('author[]');
					$CI->linx->delete('post2user', array('a' => $id));
					if (isset($author) && is_array($author) & !empty($author)) {
						foreach($author as $ak => $av) {
							$set = array(
								'a'		=> $id,
								'b'		=> $av
							);
							$CI->linx->set('post2user', $set);
						}
					} elseif (isset($CI->data->out['me']['id'])) {
						$set = array(
							'a'		=> $id,
							'b'		=> $CI->data->out['me']['id']
						);
						if (!isset($CI->data->out['post'][0]['author'])) {//著者が設定されていない場合
							$set['status'] = 'main';
							$CI->linx->set('post2user', $set);
						} else {//既に著者が設定されている場合（ここ、処理が汚いので、リファクタリング）
							foreach($CI->data->out['post'][0]['author'] as $k => $v) {
								if ($v['id'] == $CI->data->out['me']['id']) $flg = true;
							}
							if (!isset($flg)) $CI->linx->set('post2user', $set);
						}
						$author_id = $CI->data->out['me']['id'];
					}
					
					//ファイル
					$file = set_value('file[]');
					if (isset($file) && is_array($file)) {
						foreach($file as $fk => $fv) {
							$ch = $CI->linx->count('post2file', array(
								'a'	=> $id,
								'b'	=> $fv
							));
							if ($ch == 0) {
								$set = array(
									'a'		=> $id,
									'b'		=> $fv
								);
								$CI->linx->set('post2file', $set);
							}
						}
					}
					
					//セクション登録
					$div = set_value('div[]');
					$CI->linx->delete('post2div', array('a' => $id));
					if (isset($div) && is_array($div) & !empty($div)) {
						foreach($div as $dk => $dv) {
							$ch = $CI->linx->count('post2div', array(
								'a'	=> $id,
								'b'	=> $dv
							));
							if ($ch == 0) {
								$set = array(
									'a'		=> $id,
									'b'		=> $dv
								);
								$CI->linx->set('post2div', $set);
							}
						}
					}
					
					//タグ登録
					$CI->load->library('tag');
					$CI->tag->set($CI->input->post('tagstr'), 'post2tag', $id, $author_id);
				}
				
				if (!empty($ext) && is_array($ext)) {//記事拡張
					foreach($ext as $e) {
						$ext_value = set_value('ext_'.$e['field']);
						if (isset($ext_value)) {
							$set = array(
								'a'		=> $id,
								'status'	=> $e['field']
							);
							$ch = $CI->linx->get('post2ext', $set);
							
							if (!empty($ch)) $set['id'] = $ch[0]['id'];
							$set['param'] = array('value' => $ext_value);
							
							$CI->linx->set('post2ext', $set);
						}
					}
				}
				
				//スケジューラー：開始日時
				$startdate = set_value('startdate[]');
				if (isset($startdate) && is_array($startdate)) {
					foreach ($startdate as $sd) {
						if (!empty($sd)) {
							$set = array(
								'a'		=> $id
							);
							$ch = $CI->linx->get('post2startdate', $set);
							
							$set['status']		= $sd;
							if (!empty($ch)) $set['id'] = $ch[0]['id'];
							
							$CI->linx->set('post2startdate', $set);
						}
					}
				}
				
				//スケジューラー：終了日時
				$enddate = set_value('enddate[]');
				if (isset($enddate) && is_array($enddate)) {
					foreach ($enddate as $ed) {
						if (!empty($sd)) {
							$set = array(
								'a'		=> $id
							);
							$ch = $CI->linx->get('post2enddate', $set);
							
							$set['status']		= $ed;
							if (!empty($ch)) $set['id'] = $ch[0]['id'];
							
							$CI->linx->set('post2enddate', $set);
						}
					}
				}
				
				if ($author_id) $CI->user->action($author_id);//行動日を更新
				
				$this->msg = array(
					'result'	=> 'success',
					'msg'		=> $CI->lang->line('system_post_success')
				);
				if (isset($id)) $this->msg['id'] = $id;
			}
		}
		return $this->msg;
	}
	
	function delete($id = array(), $flg_delete = false) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$post_id = $id;
		} else {
			if ($CI->input->post('id[]')) $post_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $post_id[] = $CI->input->post('id');
		}
		
		if (is_array($post_id)) {
			foreach ($post_id as $id) {
				if ($flg_delete == false) {
					//記事件数を取得
					$c = $this->count(array(
						'user' => $CI->data->out['me']['id'],
						'id' => $CI->input->post('id')
					));
					
					if ($c > 0) {//自分の記事の場合
						$flg_delete = true;
					} else {
						$flg_delete = ($CI->auth->check_auth()) ? true : false;
					}
				}
				
				$CI->db->flush_cache();
				if ($flg_delete == true) {
					$CI->db->where('post_id', $id);
					$CI->db->update(DB_TBL_POST, array('post_deleted' => 1));
					
					//過去の履歴を削除
					$path = 'history/post/'.$id.'/';
					$history = $CI->log->get($path, array('stack' => false, 'qty' => 0));
					if (!empty($history)) {
						foreach ($history as $h) $del_id[] = $h['id'];
						$CI->log->delete($del_id);
					}
					
					$CI->linx->unlink('post', $id);//リンクを削除
				}
			}
		}
	}
	
	function get_ext($stack = false) {
		$CI =& get_instance();
		
		$CI->db->where('ext_div', 'post');
		$CI->db->order_by('ext_order', 'asc');
		return $CI->data->set($CI->db->get(DB_TBL_EXT), array('stack' => $stack, 'label' => 'ext'));
	}
	
	function _set_post($arr, $id = 0) {
		$CI =& get_instance();
		
		if ($id === 0) {//新規投稿
			$CI->db->insert(DB_TBL_POST, $arr);
			$id = $CI->db->insert_id();
		} else {//編集
			$CI->db->where('post_id', $id);
			$CI->db->update(DB_TBL_POST, $arr);
		}
		$CI->log->set_history('post', $id, $arr);//ヒストリーログを残す
		return $id;
	}
	
	function clear() {//新規作成用
		$CI =& get_instance();
		$CI->load->library(array('form_validation'));
		$CI->form_validation->set_rules($this->validation_rule);
		$CI->form_validation->run();
	}
	
	function _make_permalink($label = "post", $id, $alias = "") {
		$str = base_url().$label.'/'.$id.'/';
		$str .= ($alias != "") ? $alias.'/' : '';
		return $str;
	}
	
	function _get_meta($arr) {
		$str = implode('////', $arr);
		$str = str_replace(array("\n", "\r"), '¥¥¥', $str);
		return $str;
	}
	
	function BLX_Post() {
		
	}
}

?>