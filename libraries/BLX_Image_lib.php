<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Image_lib extends CI_Image_lib {
	function trim() {
		$protocol = 'image_process_gd';//ごめん、gdしか対応していない、現状は。
		return $this->$protocol('trim');
	}
	
	/**
	 * Image Process Using GD/GD2
	 *
	 * This function will resize or crop
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function image_process_gd($action = 'resize') {	
				
		$v2_override = FALSE;
				
		//ターゲットの横／縦幅が、ソースと一致する、もしくは新しいファイル名が古いファイル名に一致した場合、
		//単純にオリジナルのものを新しい名前に書き換えるだけで、動的なレンダリングは行いません。
		if ($this->dynamic_output === FALSE) {
			if ($this->orig_width == $this->width AND $this->orig_height == $this->height) {
				if ($this->source_image != $this->new_image) {
					if (@copy($this->full_src_path, $this->full_dst_path)) {
						@chmod($this->full_dst_path, DIR_WRITE_MODE);
					}
				}
				return TRUE;
			}
		}

		switch($action) {//アクションに合わせて、要素を定義していきます。
			case 'crop'://切り抜きの場合、ソースの縦／横幅を指定し直します
				$this->orig_width  = $this->width;
				$this->orig_height = $this->height;
				
				// GD 2.0 has a cropping bug so we'll test for it
				if ($this->gd_version() !== FALSE) {
					$gd_version = str_replace('0', '', $this->gd_version());			
					$v2_override = ($gd_version == 2) ? TRUE : FALSE;
				}
			break;
			
			case 'trim':
				if ($this->width > $this->height) {//横幅が縦幅よりも大きい場合、横をトリムする
					$size = $this->width;
					//if ($this->x_axis == 0) $this->x_axis = $size / 2;
				} else {//縦をトリムする
					$size = $this->height;
					if ($this->y_axis == 0) $this->y_axis = ceil(($this->height - $size) / 2);
					//$this->y_axis = 20;
					//print "size:".$size." - 元の縦幅".$this->height." - y:".$this->y_axis;exit;
					//print_r($this);exit;
				}
			break;
			
			case 'resize':
			default:			
				$this->x_axis = 0;
				$this->y_axis = 0;
			break;
		}

		//ハンドルを生成します
		if (!($src_img = $this->image_create_gd())) {		
			return FALSE;
		}

		//  Create The Image
		//
		//  old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		//  it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		//  below should that ever prove inaccurate.
		//
		//  if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor') AND $v2_override == FALSE)
		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor')) {
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		} else {
			$create	= 'imagecreate';	
			$copy	= 'imagecopyresized';
		}

		if($action == "trim") {//トリミングの場合
			$tmp_img = $create($this->width, $this->height);
			$copy($tmp_img, $src_img, 0, 0, 0, 0, $this->width, $this->height, $this->orig_width, $this->orig_height);
			$dst_img = $create($size, $size);
			$copy($dst_img, $tmp_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->width, $this->height);
		} else {
			$dst_img = $create($this->width, $this->height);
			$copy($dst_img, $src_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->orig_width, $this->orig_height);
		}
		
		//画像を表示します	
		if ($this->dynamic_output == TRUE) {
			$this->image_display_gd($dst_img);
		} else {//もしくは保存します
			if ( ! $this->image_save_gd($dst_img)) {
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		// Set the file to 777
		@chmod($this->full_dst_path, DIR_WRITE_MODE);

		return TRUE;
	}
	
	function make_thumb($org_path, $path, $w = 0, $trim = "", $h = 0) {
		
		$config['image_library']	= 'gd2';
		$config['source_image']		= $org_path;
		$config['new_image']		= $path;
		$config['create_thumb']		= TRUE;
		$config['thumb_marker']		= "";
		$config['quality']			= 100;
		
		if ($trim != '') {//トリミングする場合
			$info_org = @getimagesize($org_path);//オリジナル画像のサイズを取得
			$org_w	= $info_org[0];
			$org_h	= $info_org[1];
			
			if (!isset($w) || $w == 0) $w = $org_w;
			
			if ($trim == 'auto') {//トリミングせず、長辺を合わせる場合
				if ($h > 0) {//高さまで指定
					$tar_w = $w;
					$tar_h = ceil($org_h * ($w / $org_w));
					
					$config['maintain_ratio']	= true;
					if ($tar_h > $h) {
						$config['width']		= $w * (ceil($h / $tar_h));
						$config['height']		= $h;
						$config['master_dim']	= 'height';
					} else {
						$config['width']		= $w;
						$config['height']		= $h;
						$config['master_dim']	= 'width';
					}
					
					$this->initialize($config);
					$this->resize();
					
					@chmod($path, DIR_WRITE_MODE);
				} else {//高さ指定無し（tumblr風）
					if ($w > $org_w) $w = $org_w;
					
					$config['maintain_ratio']	= true;
					$config['width']		= $w;
					$config['height']		= $w;
					$config['master_dim']	= 'width';
					
					$this->initialize($config);
					$this->resize();
					
					@chmod($path, DIR_WRITE_MODE);
				}
			} else {//正方形にトリミングする場合
				//一旦大きめの画像を作成
				$config['maintain_ratio']	= TRUE;
				$config['width']		= $w;
				$config['height']		= $w;
				if ($org_w < $org_h) {
					$config['master_dim']		= 'width';
				} else {
					$config['master_dim']		= 'height';
				}
				
				$this->initialize($config);
				$this->resize();
				
				//指定のサイズに切り抜く
				unset($config);
				$config['image_library']	= 'gd2';
				$config['source_image']		= $path;
				$config['quality']			= 100;
				$config['width']			= $w;
				$config['height']			= $w;
				$config['maintain_ratio']	= FALSE;
				
				$v2 = @getimagesize($path);
				
				if($org_w < $org_h) {
					$config['x_axis'] = '0';
					$config['y_axis'] = ceil(($v2[1] - $w) / 2);
				} else {
					$config['x_axis'] = ceil(($v2[0] - $w) / 2);
					$config['y_axis'] = '0';
				}
				
				$this->initialize($config);
				$this->crop();
			}
		} else {//正方形にトリミングしない場合
			$config['maintain_ratio']	= TRUE;
			if ($w > 0) {
				$config['width']		= $w;
				$config['height']		= $w;
			}
			$config['master_dim']		= 'width';
			
			$this->initialize($config);
			$this->resize();
		}
	}
	
	function view($org_path, $w = 0, $trim = "", $h = 0) {
		
		$config['image_library']	= 'gd2';
		$config['source_image']		= $org_path;
		#$config['new_image']		= $path;
		$config['create_thumb']		= FALSE;
		$config['dynamic_output']	= TRUE;
		$config['quality']			= 80;
		
		#exit($org_path);
		
		if ($trim != '') {//トリミングする場合
			$info_org = @getimagesize($org_path);//オリジナル画像のサイズを取得
			$org_w	= $info_org[0];
			$org_h	= $info_org[1];
			
			if (!isset($w) || $w == 0) $w = $org_w;
			
			if ($trim == 'auto') {//トリミングせず、長辺を合わせる場合
				if ($h > 0) {//高さまで指定
					$tar_w = $w;
					$tar_h = ceil($org_h * ($w / $org_w));
					
					$config['maintain_ratio']	= true;
					if ($tar_h > $h) {
						$config['width']		= $w * (ceil($h / $tar_h));
						$config['height']		= $h;
						$config['master_dim']	= 'height';
					} else {
						$config['width']		= $w;
						$config['height']		= $h;
						$config['master_dim']	= 'width';
					}
					
					$this->initialize($config);
					$this->resize();
					
					@chmod($path, DIR_WRITE_MODE);
				} else {//高さ指定無し（tumblr風）
					if ($w > $org_w) $w = $org_w;
					
					$config['maintain_ratio']	= true;
					$config['width']		= $w;
					$config['height']		= $w;
					$config['master_dim']	= 'width';
					
					$this->initialize($config);
					$this->resize();
					
					@chmod($path, DIR_WRITE_MODE);
				}
			} else {//正方形にトリミングする場合
				//一旦大きめの画像を作成
				$config['maintain_ratio']	= TRUE;
				$config['width']		= $w;
				$config['height']		= $w;
				
				if ($org_w < $org_h) {
					$config['master_dim']		= 'width';
				} else {
					$config['master_dim']		= 'height';
				}
				
				$this->initialize($config);
				$this->resize();
				
				//指定のサイズに切り抜く
				unset($config);
				$config['image_library']	= 'gd2';
				$config['source_image']		= $path;
				$config['quality']			= 100;
				$config['width']			= $w;
				$config['height']			= $w;
				$config['maintain_ratio']	= FALSE;
				
				$v2 = @getimagesize($path);
				
				if($org_w < $org_h) {
					$config['x_axis'] = '0';
					$config['y_axis'] = ceil(($v2[1] - $w) / 2);
				} else {
					$config['x_axis'] = ceil(($v2[0] - $w) / 2);
					$config['y_axis'] = '0';
				}
				
				$this->initialize($config);
				$this->crop();
			}
		} else {//正方形にトリミングしない場合
			$config['maintain_ratio']	= TRUE;
			if ($w > 0) {
				$config['width']		= $w;
				$config['height']		= $w;
			}
			$config['master_dim']		= 'width';
			
			$this->initialize($config);
			$this->resize();
		}
	}
	
	function image_reproportion() {
		if ( ! is_numeric($this->width) OR ! is_numeric($this->height) OR $this->width == 0 OR $this->height == 0) return;//数字じゃなかったり、0が設定されていた場合には、空を返す
		
		if ( ! is_numeric($this->orig_width) OR ! is_numeric($this->orig_height) OR $this->orig_width == 0 OR $this->orig_height == 0) return;//オリジナルのものに関しても同様
		
		$new_width	= ceil($this->orig_width*$this->height/$this->orig_height);		
		$new_height	= ceil($this->width*$this->orig_height/$this->orig_width);
		
		$ratio = (($this->orig_height/$this->orig_width) - ($this->height/$this->width));
	
		if ($this->master_dim != 'width' AND $this->master_dim != 'height' AND $this->master_dim != 'square') {
			$this->master_dim = ($ratio < 0) ? 'width' : 'height';
		}
		
		if (($this->width != $new_width) AND ($this->height != $new_height)) {
			if ($this->master_dim == 'height') {
				$this->width = $new_width;
			} else if ($this->master_dim == 'square') {
				if ($ratio<0) {
					$this->width = $new_width;
				} else {
					$this->height = $new_height;
				}
			} else {
				$this->height = $new_height;
			}
		}
	}
	
	function BLX_Image_lib($props = array()) {
		parent::CI_Image_lib($props);
	}
}
?>