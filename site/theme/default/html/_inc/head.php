<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<h2>Mail</h2>
<?php echo validation_errors(); ?>
<form action="<?=self_url()?>" method="post">
	<input type="hidden" name="label" value="contact" />
	<input type="hidden" name="confirm" value="true" />
	<label>name</label>
	<input type="text" name="name" value="<?=set_value('name', 'name')?>" /><br />
	<label>email</label>
	<input type="text" name="email" value="<?=set_value('email', 'e-mail@email.com')?>" /><br />
	<label>tel</label>
	<input type="text" name="require[tel]" value="<?=set_require_value('tel', '88844449999')?>" /><br />
	<textarea name="content[content1]" style="width:400px;height:200px;"><?=set_content_value('content1')?></textarea>
	<input type="submit" value="send" />
</form>