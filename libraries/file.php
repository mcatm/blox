<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_File {//ファイル管理クラス
    
	var $dat = array();
	
	function get($user_param = array()) {
		$CI =& get_instance();
		$CI->load->library(array('pagination'));
		
		$param = array(//デフォルトの設定
			'auth'		=> 0,
			'base_url'	=> base_url(),
			'history'	=> false,
			'id'		=> 0,
			'label'		=> 'file',
			'num_links'	=> 4,
			'offset'	=> 0,
			'order'		=> 'desc',
			'pager'			=> true,
			'qty'		=> $CI->setting->get('post_max_qty_per_page'),
			'query'		=> "",
			'sort'		=> 'createdate',
			'stack'		=> true,
			'uri_segment'	=> 2,
			'user'		=> 0,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);
		
		$CI->db->start_cache();
		
		if ($param['where'] != "") $CI->db->where($param['where']);
		if ($param['id'] != 0) {
			if (is_array($param['id'])) {//複数検索
				foreach($param['id'] as $f) {
					$CI->db->or_where('file_id', $f);
				}
			} else {
				$CI->db->where('file_id', $param['id']);
			}
		}
		
		//記事に紐づいているファイルを検索
		if (isset($param['post_id'])) {
			$CI->db->select('*, file_id AS id, file_type AS type');
			$CI->db->join(DB_TBL_LINX, 'file_id = linx_b');
			$CI->db->group_by('file_id');
			$CI->db->where('linx_type', 'post2file');
			$CI->db->where('linx_a', $param['post_id']);
		}
		
		//ユーザーに紐づいているファイルを検索
		if (isset($param['user_id'])) {
			$CI->db->select('*, file_id AS id, file_type AS type');
			$CI->db->join(DB_TBL_LINX, 'file_id = linx_b');
			$CI->db->group_by('file_id');
			$CI->db->where('linx_type', 'user2file');
			$CI->db->where('linx_a', $param['user_id']);
		}
		
		if ($param['query'] != "") $CI->db->like('file_name', $param['query']);//検索キー
		if (isset($param['type'])) $CI->db->where('file_type', $param['type']);
		//if ($param['qty'] == 0) $CI->db
		
		$CI->db->where('file_status <= '.$param['auth']);
		$CI->db->stop_cache();
		$count = $CI->db->count_all_results(DB_TBL_FILE);
		
		if ($count > 0) {//ファイルが存在した場合
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			$CI->db->order_by('file_'.$param['sort'], $param['order']);
			
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_FILE, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_FILE, $param['qty'], $param['offset']), $param);
				$CI->db->flush_cache();
				return $out;
			}
			
			$CI->db->flush_cache();
			
			if ($param['pager']) {
				$CI->data->set_array('page', array(
					'total'			=> $CI->pagination->total_rows,
					'current'		=> $CI->pagination->cur_page,
					'qty'			=> $CI->pagination->per_page,
					'pager'			=> $CI->pagination->create_links()
				));
			}
			
			foreach($CI->data->out[$param['label']] as $k => $v) {//追加データ付与
				$hash = md5($v['size'].$v['mime']);
				$CI->data->out[$param['label']][$k]['hash'] = $hash;
				$CI->data->out[$param['label']][$k]['download_url'] = base_url().'download/'.$v['id'].'/'.$hash.'/';
			}
		}
		$CI->db->flush_cache();
	}
	
	function set($cfg = array()) {//アップロード
		if (isset($_FILES['file'])) {
			$CI =& get_instance();
			$CI->load->helper(array('date', 'file', 'directory'));
			$now = now();
			
			//tmpフォルダがない場合は作成
			$this->_mkdir(FILE_FOLDER_TMP);
			
			//tmpフォルダにアップロード
			$config['upload_path']		= FILE_FOLDER_TMP;
			$config['allowed_types']	= FILE_ALLOWED_TYPE;
			$CI->load->library('upload', $config);
			$CI->upload->do_upload('file');
			
			$fileinfo = $CI->upload->data();
			$ext = strtolower($fileinfo['file_ext']);
			
			if ($this->_check_filetype($ext)) {
			
				//画像をDBに登録する
				$set = array(
					'file_name'				=> $fileinfo['raw_name'],
					'file_ext'				=> $ext,
					'file_width'			=> $fileinfo['image_width'],
					'file_height'			=> $fileinfo['image_height'],
					'file_size'				=> $fileinfo['file_size']*100,
					'file_mime'				=> get_mime_by_extension(strtolower($fileinfo['file_name'])),
					'file_type'				=> $this->_get_filetype_from_ext($ext),
					'file_createdate'		=> $now,
					'file_modifydate'		=> $now
				);
				
				$CI->db->insert(DB_TBL_FILE, $set);
				$file_id = $CI->db->insert_id();
				
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$path = $this->_make_path($file_id, FILE_FOLDER);
					$new_path = $path['full'].$file_id.$ext;
					
					//フォルダがない場合は作成
					$this->_mkdir($path['folder']['base'].'/'.$path['folder']['1']);
					$this->_mkdir($path['folder']['base'].'/'.$path['folder']['1'].'/'.$path['folder']['2']);
					
					$up_flg = move_uploaded_file($_FILES['file']['tmp_name'], $new_path);
					chmod($new_path, 0777);
				} else {
					$up_flg = false;
				}
				
				if (!$up_flg) {//画像がアップロードされなかった場合、DBから一件削除する
					$CI->db->where('file_id', $file_id);
					$CI->db->delete(DB_TBL_FILE, 1);
					$file_id = false;
				}
			} else {
				$file_id = false;
			}
			
			foreach (directory_map($config['upload_path']) as $tmpfile) unlink($config['upload_path'].'/'.$tmpfile);//_tmpからファイルを削除
			
			return $file_id;
		}
	}
	
	/*
	if (!empty($file)) {
		if ($filename != $filename_org) {//サムネイル作成
			$CI->load->library('image_lib');
			$CI->image_lib->make_thumb($cache_dir.$filename_org, $cache_dir.$filename, $w, $trim);
		}
	}
	*/
	
	function set_extfile($url = "") {//アップロード
		if ($url != "") {
			$CI =& get_instance();
			$CI->load->helper(array('date', 'file', 'directory'));
			$now = now();
			
			$file = @file_get_contents($url);
			
			$filepath = pathinfo($url);
			
			//tmpフォルダがない場合は作成
			$this->_mkdir(FILE_FOLDER_TMP);
			$tmppath = FILE_FOLDER_TMP.'/'.$filepath['basename'];
			write_file($tmppath, $file);//オリジナルファイル書き込み
			chmod($tmppath, 0777);
			
			#exit();
			
			$ext = strtolower('.'.$filepath['extension']);
			$fileinfo = @get_file_info($tmppath);
			
			#print_r($fileinfo);exit;
			
			if ($this->_check_filetype($ext)) {
			
				//画像をDBに登録する
				$set = array(
					'file_name'				=> $filepath['basename'],
					'file_ext'				=> $ext,
					'file_size'				=> $fileinfo['size'],
					'file_mime'				=> get_mime_by_extension(strtolower($filepath['basename'])),
					'file_type'				=> $this->_get_filetype_from_ext($ext),
					'file_createdate'		=> $now,
					'file_modifydate'		=> $now
				);
				
				if ($set['file_type'] == 'image') {
					$imgsize = @getimagesize($tmppath);
					$set['file_width']		= $imgsize[0];
					$set['file_height']	= $imgsize[1];
				}
				
				#print_r($set);
				#exit;
				
				$CI->db->insert(DB_TBL_FILE, $set);
				$file_id = $CI->db->insert_id();
				
				if (is_file($tmppath)) {
					$path = $this->_make_path($file_id, FILE_FOLDER);
					$new_path = $path['full'].$file_id.$ext;
					
					//フォルダがない場合は作成
					$this->_mkdir($path['folder']['base'].'/'.$path['folder']['1']);
					$this->_mkdir($path['folder']['base'].'/'.$path['folder']['1'].'/'.$path['folder']['2']);
					
					$up_flg = copy($tmppath, $new_path);
					chmod($new_path, 0777);
				} else {
					$up_flg = false;
				}
				
				if (!$up_flg) {//画像がアップロードされなかった場合、DBから一件削除する
					$CI->db->where('file_id', $file_id);
					$CI->db->delete(DB_TBL_FILE, 1);
					$file_id = false;
				}
			} else {
				$file_id = false;
			}
			
			unlink($tmppath);//_tmpからファイルを削除
			
			return $file_id;
		}
	}
	
	function delete($id = array()) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$file_id = $id;
		} else {
			if ($CI->input->post('id[]')) $file_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $file_id[] = $CI->input->post('id');
		}
		
		if (is_array($file_id)) {
			$CI->load->library('linx');
			$CI->load->helper('directory');
			foreach ($file_id as $id) {
				$CI->linx->unlink('file', $id);//リンクを削除
				
				//ファイルを削除
				$path = $this->_make_path($id, FILE_FOLDER);
				foreach (directory_map($path['full']) as $v) {
					if (preg_match('(^'.$id.'(_|\.).*)', $v)) {
						unlink($path['full'].$v);
					}
				}
				
				//ファイルのデータを削除
				$CI->db->where('file_id', $id);
				$CI->db->delete(DB_TBL_FILE);
			}
		}
	}
	
	function download($id, $hash = "") {
		if ($hash != "") {
			$CI =& get_instance();
			
			$this->get(array(
				'id'	=> $id
			));
			
			if (isset($CI->data->out['file']) && $hash == $CI->data->out['file'][0]['hash']) {
				$CI->load->helper('download');
				
				$this->_download_count($id, $CI->data->out['file'][0]['mime']);
				
				$filename = $CI->data->out['file'][0]['name'].$CI->data->out['file'][0]['ext'];
				$filedat = $this->_load($id, $CI->data->out['file'][0]['ext']);
				
				if ($filedat) {
					force_download($filename, $filedat);
				}
			} else {
				return false;
			}
		}
		return false;
	}
	
	function zip($zipname = 'dat.zip') {
		$CI =& get_instance();
		$CI->load->library('zip');
		
		$file_id = array(120, 80, 61, 64, 45);
		//$file_id = $this->input->post('id[]');
		
		$data = array();
		
		if (!empty($file_id) && is_array($file_id)) {
			foreach($file_id as $id) {
				
				$file = $this->get(array(
					'id'	=> $id,
					'stack'	=> false
				));
				
				if (isset($file) && is_array($file)) {
					$this->_download_count($id, $file[0]['mime']);
					
					$filename = $file[0]['name'].$file[0]['ext'];
					$filedat = $this->_load($id, $file[0]['ext']);
					
					if (array_key_exists($filename, $data)) $filename = $file[0]['name'].$file[0]['id'].$file[0]['ext'];
					
					$data[$filename] = $filedat;
				}
			}
			
			$CI->zip->add_data($data);
			$CI->zip->download($zipname);
		}
	}
	
	function _load($id, $ext) {
		$CI =& get_instance();
		
		$filename = $id.$ext;
		$path = $this->_make_path($id, FILE_FOLDER);
		$filepath = $path['full'].$filename;
		
		if (!is_file($filepath)) return false;
		
		return file_get_contents($filepath);//ファイルの内容を読み取る
	}
	
	function _download_count($file_id, $mime = "") {
		if (isset($_SERVER['HTTP_USER_AGENT']) && !preg_match('/(bot[\\/\\-]|spider|crawl|slurp)/i', $_SERVER['HTTP_USER_AGENT'])) {//ボットでは無い場合
			$CI =& get_instance();
			
			if (!is_array($file_id)) {
				$id[] = $file_id;
			} else {
				$id = $file_id;
			}
			
			foreach($id as $i) {
				$ch = $CI->linx->get('file2download', array(
					'a'		=> $i,
					'type'	=> $mime
				));
				if (empty($ch)) {//カウントを1追加
					$CI->linx->set('file2download', array(
						'a' => $i,
						'b'	=> 0,
						'status'	=> 1,
						'type'	=> $mime
					));
				} else {
					$CI->linx->set('file2download', array(
						'id' => $ch[0]['id'],
						'a' => $i,
						'b' => 0,
						'status'	=> $ch[0]['status'] + 1,
						'type'	=> $mime
					));
				}
			}
		}
	}
	
	function _check_filetype($ext = "") {
		$case = explode('|', FILE_ALLOWED_TYPE);
		$ext = str_replace('.', '', strtolower($ext));
		return (in_array($ext, $case)) ? true : false;
	}
	
	function _get_filetype_from_ext($ext = "") {
		switch (strtolower($ext)) {
			case '.jpeg':
			case '.jpg':
			case '.gif':
			case '.png':
				return "image";
			break;
			
			case '.mp3':
				return "sound";
			break;
			
			case '.csv':
				return "csv";
			break;
			
			case '.xml':
				return "xml";
			break;
			
			case '.xls':
				return "excel";
			break;
			
			case '.pdf':
				return "pdf";
			break;
			
			case '.txt':
			case '.text':
				return "text";
			break;
			
			default:
				return "etc";
			break;
		}
	}
	
	function _make_path($id, $base_path) {//アップロードファイルのパスを設定
		$layer = array(
			'base'	=> $base_path,
			'1'		=> (floor($id/10)%10),
			'2'		=> ($id%10)
		);
		return array(
			'full' => $layer['base'].'/'.$layer['1'].'/'.$layer['2'].'/',
			'folder' => $layer 
		);
	}
	
	function _mkdir($path) {
		if (!is_dir($path)) mkdir($path, 0777);
	}
}

?>