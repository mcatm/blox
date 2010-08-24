<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<?if(isset($div)){?><?=$div[0]['description']?><?}?>
	<form action="<?=base_url()?>login/" method="post">
		<input type="hidden" name="redirect" value="<?=self_url()?>" />
		email.<input type="text" name="email" /><br />
		pwd.<input type="password" name="pwd" /><br />
		<input type="submit" value="login" />
	</form>
</body>
</html>

<?print_r($me)?>