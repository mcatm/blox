<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<?if(isset($mail)){?><div id="body">
		<div class="container preview">
			<div class="title">
				<h3><?if(isset($mail[0]['value']['email'])){?><a href="mailto:<?=$mail[0]['value']['email']?>"><?=$mail[0]['status_a']?></a><?} else {?><?=$mail[0]['status_a']?><?}?></h3>
			</div>
			<div class="main">
				<?if(isset($mail[0]['value']['content'])){?><?foreach($mail[0]['value']['content'] as $k => $v){?><div class="row">
					<label><?if($this->lang->line('mail_content_'.$k)){?><?=$this->lang->line('mail_content_'.$k)?><?} else {?><?=$k?><?}?></label>
					<?=format_text($v)?>
				</div><?}}?>
			</div>
			<div class="side">
				<?if(isset($mail[0]['value']['require'])){?><?foreach($mail[0]['value']['require'] as $k => $v){?><div class="row">
					<label><?if($this->lang->line('mail_content_'.$k)){?><?=$this->lang->line('mail_content_'.$k)?><?} else {?><?=$k?><?}?></label>
					<?=format_text($v)?>
				</div><?}}?>
			</div>
			<div class="data clear">
				<label>送信日時</label>
				<?=$mail[0]['createdate']?>
			</div>
		</div>
	</div><?} else {?>このメールは削除されたか、閲覧出来ない設定になっております<?}?>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>