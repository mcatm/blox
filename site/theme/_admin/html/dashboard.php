<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<?if(isset($post)){?>
			<h2><a href="<?=base_url()?>admin/post/">記事</a></h2>
			<ul>
				<?foreach($post as $k => $v){?><li>
					<h3><a href="<?=$v['admin_url']?>"><?=$v['title']?></a></h3>
					<p><?=$v['text']?></p>
					<p><?=$v['alias']?></p>
				</li><?}?>
			</ul>
			<hr /><?}?>
			
			<?if(isset($comment)){?>
			<h2><a href="<?=base_url()?>admin/comment/">コメント</a></h2>
			<ul>
				<?foreach($comment as $k => $v){?><li>
					<h3><a href="<?=$v['admin_url']?>"><?=$v['title']?></a></h3>
					<p><?=$v['text']?></p>
					<p><?=$v['alias']?></p>
				</li><?}?>
			</ul>
			<hr /><?}?>
			
			<?if(isset($user)){?>
			<h2><a href="<?=base_url()?>admin/user/">ユーザー</a></h2>
			<ul>
				<?foreach($user as $k => $v){?><li>
					<h3><?=$v['name']?></h3>
					<p><?=$v['description']?></p>
				</li><?}?>
			</ul>
			<hr /><?}?>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>