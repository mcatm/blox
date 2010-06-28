<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/user/invite/" method="post" id="body">
		<div class="container">
			<div class="postmeta">
				<div class="row">
					<label>usertype : </label>
					<select name="type">
						<?foreach($usertype as $type){?><?if (isset($me['auth']['admin']) || $type['administor'] != 1){?><option value="<?=$type['id']?>"<?=set_select('type', $type['id'])?><?if(isset($user[0]['type']) && $type['id'] == $user[0]['type']){?> selected="selected"<?}?>><?=$type['name']?></option><?}?><?}?>
					</select>
				</div>
			</div>
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
				<p><input type="submit" value="invite" class="submit round" /></p>
			</div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>