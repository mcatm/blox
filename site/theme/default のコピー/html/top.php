<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<?if(isset($post)){?><ul>
		<?foreach($post as $k => $v){?>
		<?if($v['type'] == 0){?><li>
			<h3><a href="<?=$v['url']?>"><?=$v['title']?></a></h3>
			<p><?=$v['text']?></p>
			<p><?=$v['alias']?></p>
		</li><?} elseif ($v['type'] == 1) {?><li>
			<?=$v['author'][0]['name']?> : 
			<?=format_text($v['text'])?>
		</li><?}}?>
	</ul>
	<p class="pager"><?=$page['pager']?></p><?}?>
</body>
</html>