<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<div class="wrapper">
		<?$this->load->view('_inc/head.php')?>
		<?if(isset($blox)){?><div id="main">
			<ul>
				<?foreach($blox as $k => $v){?>
				<?if($v['type'] == 'page'){?><li>
					<h3><a href="<?=$v['url']?>"><?=$v['name']?></a></h3>
					<p class="data"><?=$v['createdate']?></p>
					<p><?=$v['body']?></p>
					<p><?=$v['alias']?></p>
				</li><?} elseif ($v['type'] == 1) {?><li>
					<?=$v['author'][0]['name']?> : 
					<?=$v['text']?>aaa
				</li><?}}?>
			</ul>
			<p class="pager"><?=$page['pager']?></p>
		</div><?}?>
		<div id="side">
			ままままま
		</div>
		<div id="foot"></div>
	</div>
</body>
</html>