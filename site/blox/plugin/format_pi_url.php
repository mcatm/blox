<?

function format_pi_url($str, $param = array()) {
	
	$CI =& get_instance();
	$CI->load->library('xml');
	
	if (!empty($param['url'])) {
		foreach ($param['url'] as $k => $v) {
			$dat = array();
			/*if (preg_match("(http://(.*).auctions.yahoo.co.jp/jp/auction/(.*)?\?(.*))", $v, $mt)) {//youtube
				$CI->load->library('ext/yahoo_auction');
				$CI->load->helper('array');
				$stock_id = $mt[2];
				$path = 'yahoo_auction/auctionItem/'.$stock_id.'/';
				
				$data = decompress_array($CI->output->get_cache($path));
				if (!$data) {
					$data = $CI->yahoo_auction->auctionItem(array(
						'auctionID' => $stock_id,
						'type' => 'php'
					));
					$CI->output->set_cache($path, compress_array($data));
				}
				
				
				
				print $data['ResultSet']['Result']['Title'].'<br />';
				print '<img src="'.$data['ResultSet']['Result']['Img']['Image1'].'" width="100" /><br />';
				print $data['ResultSet']['Result']['EndTime'].'<br />';
			} else */if (preg_match("(http://(.*)youtube.com/watch\?(.*)v=(.*))", $v, $mt)) {//youtube
				$info = parse_url($mt[0]);
				if ($info['query'] != "") {
					preg_match("(v=([\w\W]*))", $info['query'], $video_id);
					$youtube_url = 'http://'.$mt[1].'youtube.com/v/'.$video_id[1];
					$str = str_replace($v, '<p class="youtube"><object width="425" height="350"><param name="movie" value="'.$youtube_url.'"></param><param name="wmode" value="transparent"></param><embed src="'.$youtube_url.'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object></p>', $str);
				}
			} else if (preg_match("(http://www\.amazon\.(.*?)/(.*?)(dp\/ASIN|ASIN|dp)/(.*[/|\r|\n]?))", $v, $mt)) {
				$CI->load->library(array('ext/amazon'));
				$asin = trim(substr($mt[4], 0, 10));//ASINは10文字ってことでいいのかなあ？
				$dat['out'] = '';
				
				if ($CI->setting->get('amazon_dev_id') && $CI->setting->get('amazon_dev_secret')) {
					
					$result = $CI->amazon->get(array(
						'Operation'		=> 'ItemLookup',
						'ResponseGroup'	=> 'Small,Images',
						'ItemId'		=> $asin
					));
					
					if (isset($result['ItemLookupResponse']['Items']['Item'])) {
						$arr = $result['ItemLookupResponse']['Items']['Item'];
						
						$dat['title']			= $arr['ItemAttributes']['Title'];
						if (isset($arr['ItemAttributes']['Creator'])) $dat['creator'] = $arr['ItemAttributes']['Creator'];
						if (isset($arr['ItemAttributes']['Artist'])) $dat['artist'] = $arr['ItemAttributes']['Artist'];
						if (isset($arr['ItemAttributes']['Creator']['Role'])) $dat['creator_attr'] = $arr['ItemAttributes']['Creator']['Role'];
						$dat['manufacturer']	= $arr['ItemAttributes']['Manufacturer'];
						$dat['media']			= $arr['ItemAttributes']['ProductGroup'];
						$dat['url']				= $arr['DetailPageURL'];
						$amazon_img = $arr['MediumImage']['URL'];
						
						//出力結果生成
						$dat['out'] .= '<div class="amazon">';
						$dat['out'] .= '<div class="amazon-data">';
						$dat['out'] .= '<p class="amazon-data-media">'.$dat['media'].'</p>';
						if (isset($dat['artist'])) $dat['out'] .= '<p class="amazon-data-artist">'.$dat['artist'].'</p>';
						$dat['out'] .= '<h4><a href="'.$dat['url'].'" target="_blank">'.$dat['title'].'</a></h4>';
						if (isset($creator)) {
							$dat['out'] .= '<p class="amazon-data-creater">';
							if (count($dat['creator'])<=1) $dat['out'] .= $dat['creator'].' ';
							$dat['out'] .= '</p>';
						}
						$dat['out'] .= '<p class="amazon-data-manufacturer">'.$dat['manufacturer'].'</p>';
						$dat['out'] .= '</div>';
						
						$dat['out'] .= '<div class="amazon-img"><a href="'.$dat['url'].'" target="_blank"><img src="'.$amazon_img.'" /></a></a>';
						$dat['out'] .= '</div>';
						$dat['out'] .= '<div class="amazon-foot"></div>';
						$dat['out'] .= '</div>';
					}
				} else {
					$amazon_img = "http://images.amazon.com/images/P/".$asin.".01._SCMZZZZZZZ.jpg";
					$amazon_url = trim("http://www.amazon.".$mt[1]."/o/ASIN/".$asin."/".$CI->setting->get('amazon_associate_id'), '/').'/ref=nosim';
					$dat['out'] = '<a href="'.$amazon_url.'" target="_blank">'.$amazon_url.'</a>';
					#$str = str_replace($v, /*'<a href="'.$amazon_url.'" target="_blank">{@img:'.$amazon_img.'}</a>'."\n".*/'<a href="'.$amazon_url.'" target="_blank">'.$amazon_url.'</a>', $str);
				}
				$str = str_replace($v, $dat['out'], $str);
			}
		}
	}
	return $str;
}

function urlencode_rfc3986($str) {//RFC3986 形式でURLエンコードする関数(via. p4life)
	return str_replace('%7E', '~', rawurlencode($str));
}

?>