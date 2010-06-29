<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<?if(isset($post)){?><div>
		<?if(isset($post[0]['file_main'])){?><p><?foreach($post[0]['file_main'] as $fm){?><?if($fm['type'] == 'image'){?><img src="<?=img_url($fm['id'], 200)?>" /><?}}?></p><?}?>
		<h3><a href="<?=$post[0]['url']?>"><?=$post[0]['title']?></a></h3>
		<p><?=format_text($post[0]['paragraph'][$post[0]['page']])?></p>
		<p><?=$post[0]['createdate']?></p>
		<p><?=$post[0]['alias']?></p>
		<p><?=$post[0]['author'][0]['name']?></p>
		<?if(isset($post['tag'])){?><p><?foreach($post[0]['tag'] as $t){?><?=$t['name']?> / <?}?></p><?}?>
		<hr />
		<h4>access</h4>
		<p>count: <?=$access['value']['count']['total']?> (一日あたり<?=$access['value']['count']['rate']?>）</p>
		<p>last access: <?=$access['update']?></p>
		
	</div><?} else {?>このエントリは削除されたか、閲覧出来ない設定になっております<?}?>
</body>
</html>