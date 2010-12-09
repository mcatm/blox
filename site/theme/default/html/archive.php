<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	
	<?if($blox){?><ul>
		<?foreach($blox as $k => $v){?><li>
			<h3><a href="<?=$v['url']?>"><?=$v['name']?></a></h3>
			<p><?=$v['body']?></p>
			<p><?=$v['alias']?></p>
		</li><?}?>
	</ul>
	<p class="pager"><?=$page['pager']?></p><?}?>
</body>
</html>