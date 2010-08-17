<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	
	<?if($post){?><ul>
		<?foreach($post as $k => $v){?><li>
			<h3><a href="<?=$v['url']?>"><?=$v['title']?></a></h3>
			<p><?=$v['text']?></p>
			<p><?=$v['alias']?></p>
		</li><?}?>
	</ul>
	<p class="pager"><?=$page['pager']?></p><?}?>
</body>
</html>