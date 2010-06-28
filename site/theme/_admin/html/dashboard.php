<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="dashboard clearfix">
				<?if(isset($post)){?><div class="block">
					<div class="title">
						<h2><a href="<?=base_url()?>admin/post/">記事</a></h2>
					</div>
					<ul>
						<?foreach($post as $k => $v){?><li>
							<h3><a href="<?=$v['admin_url']?>"><?=$v['title']?></a></h3>
							<p><?=format_description($v['text'], 140)?></p>
						</li><?}?>
					</ul>
				</div><?}?>
				
				<?if(isset($comment)){?><div class="block">
					<div class="title">
						<h2><a href="<?=base_url()?>admin/comment/">コメント</a></h2>
					</div>
					<ul>
						<?foreach($comment as $k => $v){?><li>
							<h3><?=$v['title']?></h3>
							<p><?=format_description($v['text'], 140)?></p>
						</li><?}?>
					</ul>
				</div><?}?>
				
				<?if(isset($user)){?><div class="block">
					<div class="title">
						<h2><a href="<?=base_url()?>admin/user/">ユーザー</a></h2>
					</div>
					<ul>
						<?foreach($user as $k => $v){?><li>
							<h3><?=$v['name']?></h3>
							<p><?=format_description($v['description'], 100)?></p>
						</li><?}?>
					</ul>
				</div><?}?>
				
				<?if(isset($weekly)){?><div class="block">
					<div class="title">
						<h2><a href="<?=base_url()?>admin/user/">スケジュール</a></h2>
					</div>
					<ul>
						<?foreach($weekly as $k => $v){?><li>
							<h3><?=$v['title']?></h3>
							<p><?=format_description($v['text'], 140)?></p>
						</li><?}?>
					</ul>
				</div><?}?>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>