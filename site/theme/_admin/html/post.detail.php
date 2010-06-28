<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<?if(isset($post)){?><div id="body">
		<div class="container preview">
			<div class="title">
				<h3><a href="<?=$post[0]['url']?>" target="_blank"><?=$post[0]['title']?></a></h3>
			</div>
			<div class="main">
				<?=format_text($post[0]['text'])?>
			</div>
			<div class="side">
				<?if(isset($post[0]['file_main'])){?><p><?foreach($post[0]['file_main'] as $fm){?><?if($fm['type'] == 'image'){?><img src="<?=img_url($fm['id'], 500)?>" class="filemain" /><?}}?></p><?}?>
				<?if(isset($post[0]['file'])){?><p><?foreach($post[0]['file'] as $f){?><?if($f['type'] == 'image'){?><img src="<?=img_url($f['id'], 100)?>" class="btn_file" attr="<?=$f['id']?>" /><?}}?></p><?}?>
			</div>
			<div class="data clear">
				<?=$post[0]['createdate']?> | <?=$post[0]['author'][0]['name']?> | <?=$post[0]['alias']?> | <a href="<?=$post[0]['edit_url']?>">edit</a>
			</div>
			<?if(isset($post[0]['history'])){?><h2>history</h2>
			<ul>
				<?foreach($post[0]['history'] as $k => $v){?><li>
					<?if(isset($v['value']['post_title'])){?><h3><?=$v['value']['post_title']?></h3><?}?>
					<p>date: <?=$v['update']?></p>
				<?print_r($v)?></li><?}?>
			</ul>
			<hr /><?}?>
			<h4>access</h4>
			<p>count: <?=$access['value']['count']['total']?> (<?=$access['value']['count']['rate']?> per day)</p>
			<p>last access: <?=$access['update']?></p>
		</div>
	</div><?} else {?>このエントリは削除されたか、閲覧出来ない設定になっております<?}?>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript">
	var post_id = <?=$post[0]['id']?>;
	$(function(){
		//メインファイルを設定
		$('img.btn_file').each(function() {
			$(this).click(function() {
				$.ajax({
					type: 'post',
					url: base_url + 'request/set/linx/post2file/' + post_id + '/' + $(this).attr('attr') + '/main/b/'
				});
			});
		});
	});
</script>
</html>