<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=$this->setting->get('title')?></title>

<meta name="Description" content="<?=$this->setting->get('description')?>" />
<meta name="Keywords" content="<?=$this->setting->get('keyword')?>" />

<link rel="stylesheet" type="text/css" href="<?=theme_url()?>css/import.css" />
<?if (isset($setting['favicon'])){?><link rel="shortcut icon" href="<?=$setting['favicon']?>" /><?}?>

<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?=base_url()?>rss/" /> 

<script type="text/javascript" src="<?=ex_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/editor.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/smoothscroll.js"></script>
<script type="text/javascript" src="<?=theme_url()?>js/navi.js"></script>
<script type="text/javascript">
	<!--//
	var base_url = "<?=base_url()?>";
	
	$(function() {
		$('textarea.editor').Editor();
	});
	
	//-->
</script>
</head>
