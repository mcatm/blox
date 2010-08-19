<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=self_url()?>" method="post" id="body">
		<div class="container">
			<div class="postwhole">
				<div class="row">
					<input type="text" name="name" value="<?=$tpl['name']?>" class="query" />
				</div>
				<div class="row">
					<label>template : </label>
					<textarea name="body" class="input elastic" id="edit_text" rows="6"><?=$tpl['body']?></textarea>
				</div>
			</div>
			<div class="postmeta">
			</div>
			<div class="postbody">
			</div>
			<div class="advance clearfix">
				<div class="trigger"><a id="trigger_advance">advance</a></div>
				<div id="advance_form">
					<div class="postmeta">
						
					</div>
					
					<div class="postbody clearfix">
						<div class="postdiv">
							
						</div>
						
						<div class="postauthor">
							
						</div>
					</div>
				</div>
			</div>
			<div class="console"><p><input type="submit" value="post" class="submit round" /></p></div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/upload.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/suggest.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/elastic.js"></script>
<script type="text/javascript">
	$(function(){
		$('textarea.elastic').elastic();
	});
</script>
</html>