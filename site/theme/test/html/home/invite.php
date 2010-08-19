<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/user/invite/" method="post" id="body">
	<input type="hidden" name="user_type" value="2" />
		<div class="container">
			<div class="postbody">
				<div class="row">
					<label>email : </label>
					<input type="text" name="email" value="<?=$this->input->post('email')?>" class="query input" />
				</div>
				<div class="row">
					<label>message : </label>
					<textarea name="message" rows="10"><?=$this->input->post('message')?></textarea>
				</div>
			</div>
			<div class="console">
				<p><input type="submit" value="招待する" class="submit round" /></p>
			</div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>