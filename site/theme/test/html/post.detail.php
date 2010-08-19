<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<div class="wrapper">
		<?$this->load->view('_inc/head.php')?>
		<div id="main"><?if(isset($post)){?>
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
			
			<?if(isset($comment)){?>
			<h4>comment</h4>
			
			<?foreach($post[0]['comment'] as $c){?><div><?=$c['author'][0]['name']?> : <?=$c['text']?></div><?}?>
			<?}?>
			
			<?if ($this->auth->check_auth('comment')) {?>
			<form action="<?=base_url()?>request/set/comment/" method="post" />
				<input type="hidden" name="parent" value="<?=$post[0]['id']?>" />
				<input type="hidden" name="type" value="1" />
				<input type="hidden" name="status" value="0" />
				<textarea name="text"></textarea>
				<input type="submit" value="add a comment" />
			</form><?}?>
		</div>
		<div id="side">
			<?if(isset($post[0]['author'])){?><ul>
				<?foreach($post[0]['author'] as $a){?><li><a href="<?=base_url()?>user/<?=$a['account']?>/"><?=$a['name']?></a></li><?}?>
			</ul><?}?>
			
			<?if(isset($post[0]['tag'])){?><ul>
				<?foreach($post[0]['tag'] as $t){?><li><a href="<?=base_url()?>tag/<?=$t['name']?>/"><?=$t['name']?></a></li><?}?>
			</ul><?}?>
		</div>
		<?} else {?>このエントリは削除されたか、閲覧出来ない設定になっております<?}?>
	</div>
</body>
</html>