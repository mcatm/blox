<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<div id="login">
		<?if ($this->session->userdata('login') !== true) {?><form method="post" action="<?=base_url()?>login/" class="round">
			<?/*input type="hidden" name="redirect" value="<?if(isset($_SERVER['HTTP_REFERER'])){?><?=$_SERVER['HTTP_REFERER']?><?}else{?><?=base_url()?>admin/<?}?>" /*/?>
			<input type="hidden" name="redirect" value="<?=base_url()?>admin/" />
			<p><label>email : </label><input type="text" name="email" class="query" /></p>
			<p><label>password : </label><input type="password" name="pwd" class="query" /></p>
			<p><input type="submit" value="login" /></p>
		</form><?} else {?>管理画面に入室する権限がありません。詳しくは、管理者にお問い合わせいただくか、<a href="<?=base_url()?>admin/logout/">ログアウト</a>して別のアカウントをお試し下さい。<?}?>
	</div>
</body>
</html>
<?#print_r($this->data->out)?>