<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( ! function_exists( 'format_text' ) ) {
	function format_text($str, $limit = 0, $tail = "...", $strip_js_event_handlers = false, $reduce_linebreaks =true) {
		$CI =& get_instance();
		$CI->load->library('typography');
		
		$str = str_replace($CI->setting->get_formattag('page'), '', $str);
		
		$str = str_replace("\"http", "\"{@http}", $str);//引用符に囲まれたURLをエスケープ
		
		$str = strip_tags($str, $CI->setting->get('html_tag_not_escape'));//htmlタグをエスケープ
		
		//タグ中の余計な要素を削除
		preg_match_all('(\<.*?\>)', $str, $htmltag);
		if (!empty($htmltag)) {
			$allowed_attr = explode(',', $CI->setting->get('html_tag_not_escape_attr'));
			foreach($htmltag[0] as $h) {
				$attr = array();
				$tagstr = "";
				$attr = explode(' ', $h);
				if (is_array($attr)) {
					foreach($attr as $a) {
						$aa = explode('=', $a);
						if (count($aa) > 1) {
							if (in_array($aa[0], $allowed_attr)) $tagstr .= ' '.$a;
						} else {
							$tagstr .= $a;
						}
					}
					$str = str_replace($h, str_replace('javascript:', '', trim($tagstr, '>')).'>', $str);
				}
			}
		}
		
		preg_match_all("((http|https)(://[[:alnum:]\S\$\+\?\.=_%,:@!#~*-/&]+))", $str, $url);
		
		$param = array(
			'url'		=> $url[0]
		);
		
		//外部プラグイン読込
		$p = directory_map(LIB_FOLDER.'/plugin');
		if (!empty($p)) {
			foreach($p as $v) {
				if (preg_match('(^format_pi_(.*)\.php)', $v)) {
					$f = str_replace('.php', '', $v);
					if (is_file(LIB_FOLDER.'/plugin/'.$v)) $str = $f($str, $param);
				}
			}
		}
		
		//URLの変換
		foreach ($url[0] as $k=>$v) $str = str_replace($v, "{@LNK:".$k."}", $str);//URLを別の文字列に置換
		foreach ($url[0] as $k=>$v) $str = str_replace("{@LNK:".$k."}", "<a href=\"".$v."\" target=\"_blank\">".shorten_url($v)."</a>", $str);//特殊文字列を、リンクに変換
		
		$str = str_replace("\"{@http}", "\"http", $str);//URL復帰
		
		if ($limit > 0) {
			if (mb_strlen($str, 'UTF-8') > $limit) $str = mb_substr($str, 0, $limit, 'UTF-8').$tail;//文字数
		}
		
		$str = $CI->typography->auto_typography($str, $strip_js_event_handlers, $reduce_linebreaks);
		
		return $str;
	}
}

if ( ! function_exists( 'format_description' ) ) {
	function format_description($str, $limit = 0, $tail = "...") {
		$CI =& get_instance();
		$regex = '/'.$CI->setting->get('format_tag_open').'(.*)'.$CI->setting->get('format_tag_close').'?/';
		
		$str = strip_tags($str);
		
		$str = preg_replace(array("((http|https)(://[[:alnum:]\S\$\+\?\.=_%,:@!#~*-/&]+))", $regex), "", $str);
		if ($limit > 0) {
			if (mb_strlen($str, 'UTF-8') > $limit) $str = mb_substr($str, 0, $limit, 'UTF-8').$tail;//文字数
		}
		return $str;
	}
}

if ( ! function_exists( 'shorten_url' ) ) {
	function shorten_url($str, $limit = 40, $tail = "...") {
		if (strlen($str) > $limit) {
			return mb_substr($str, 0, $limit, CHARSET).$tail;
		} else {
			return $str;
		}
	}
}

if ( ! function_exists( 'tag_pre' ) ) {
	function tag_pre($str, $back = false) {
		/*preg_match_all("(\<pre\>(.*)\<\/pre\>)", $str, $mt);
		
		if(!empty($mt[1])) {
			foreach($mt[1] as $v) {
				#print $v;
				$rt = htmlspecialchars($v);
				$str = str_replace($v, $rt, $str);
			}
		}
		#print_r($mt);*/
		
		return $str;
	}
}

if ( ! function_exists( 'format_date' ) ) {
	function format_date($str, $date_format = "") {
		return date($date_format, strtotime($str));
	}
}

?>
