<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/div/edit/" method="post" id="body">
	<?if(isset($div[0]['id'])){?><input type="hidden" name="id" value="<?=$div[0]['id']?>" /><?}?>
	<?#if(isset($post[0]['parent'])){?><input type="hidden" name="parent" value="<?#=$post[0]['parent']?>" /><?#}?>
		<div class="container">
			<?if(isset($div[0])){?><div class="posthead">
				<div class="div round">
					<a href="<?=$div[0]['url']?>" target="_blank"><?=$div[0]['url']?></a>
				</div>
			</div><?}?>
			<div class="postmeta">
				<p><label>name : </label>
				<input type="text" name="name" value="<?=set_value('name', (isset($div[0]['name'])) ? $div[0]['name'] : '')?>" class="query input" /></p>
				<p><label>alias : </label>
				<input type="text" name="alias" value="<?=set_value('alias', (isset($div[0]['alias'])) ? $div[0]['alias'] : '')?>" class="query input" /></p>
				<p><label>type : </label>
				<select name="type">
					<option value=""<?if(set_value('type') == "" || (isset($div[0]['type']) && $div[0]['type']=="")){?> selected="selected"<?}?>>---</option>
					<option value="section"<?if(set_value('type') == "section" || (isset($div[0]['type']) && $div[0]['type']=="section")){?> selected="selected"<?}?>>section</option>
					<option value="category"<?if(set_value('type') == "category" || (isset($div[0]['type']) && $div[0]['type']=="category")){?> selected="selected"<?}?>>category</option>
					<option value="post"<?if(set_value('type') == "post" || (isset($div[0]['type']) && $div[0]['type']=="post")){?> selected="selected"<?}?>>post</option>
					<option value="mail"<?if(set_value('type') == "mail" || (isset($div[0]['type']) && $div[0]['type']=="mail")){?> selected="selected"<?}?>>mail</option>
					<option value="top"<?if(set_value('type') == "top" || (isset($div[0]['type']) && $div[0]['type']=="top")){?> selected="selected"<?}?>>top</option>
				</select>
				<p><label>theme : </label>
				<select name="theme">
					<option value="">---</option>
					<?foreach($theme as $t){?><option value="<?=$t?>"<?if(set_value('theme') == $t || (isset($div[0]['theme']) && $div[0]['theme']==$t)){?> selected="selected"<?}?>><?=$t?></option><?}?>
				</select></p>
				<p><label>template : </label>
				<input type="text" name="tpl" value="<?=set_value('tpl', (isset($div[0]['tpl'])) ? $div[0]['tpl'] : '')?>" class="query input" /></p>
				<p><label>identify : </label>
				<select name="id_type">
					<option value="id"<?if(set_value('id_type') == "id" || (isset($div[0]['id_type']) && $div[0]['id_type']=="id")){?> selected="selected"<?}?>>id</option>
					<option value="number"<?if(set_value('id_type') == "number" || (isset($div[0]['id_type']) && $div[0]['id_type']=="number")){?> selected="selected"<?}?>>number</option>
					<option value="alias"<?if(set_value('id_type') == "alias" || (isset($div[0]['id_type']) && $div[0]['id_type']=="alias")){?> selected="selected"<?}?>>alias</option>
				</select></p>
			</div>
			<div class="postbody">
				<p><label>description : </label><textarea name="description" class="input" rows="12"><?=set_value('description', (isset($div[0]['description'])) ? $div[0]['description'] : '')?></textarea></p>
			</div>
			<div class="advance clearfix">
				<div class="trigger">content</div>
				<div id="content_form">
					<div class="trigger-add-form"><a id="btn_add_content">+ ADD</a></div>
					<?if(isset($div[0]['content']) && !empty($div[0]['content'])){?><?foreach($div[0]['content'] as $ct_key => $ct){?><?$this->load->view('_inc/content.form.php', array('ct_key' => $ct_key, 'div' => $div))?><?}}?>
				</div>
			</div>
			<div class="console"><p><input type="submit" value="post" class="submit round" /></p></div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/upload.js"></script>
<script type="text/javascript">
	var post = [];
	var cnt_key = <?if(isset($div[0]['content'])){?><?=count($div[0]['content'])?><?} else {?>0<?}?>;
	open_advance = <?if(!empty($post[0]['div_id'])){?>true<?}else{?>false<?}?>;
	
	$(function(){
		//ファイルアップロード
		if ($('#btn_uploadfile').length > 0) {
			new AjaxUpload($('#btn_uploadfile'), {
				action: base_url + 'request/set/file/',
				name: 'file',
				responseType: 'json',
				onComplete: function(file, msg) {
					$('form#body p.msg').text(msg.msg);
					if (msg.result == 'success') {
						$('form#body').append('<input type="hidden" name="file[]" value="' + msg.file_id + '" />');
						if (msg.type == "image") {
							$('#file_list ul').append('<li style="background:url(' + msg.img_url + ') no-repeat 50% 50%"></li>');
						}
					}
				},
				onError: function(file, response){
					$('form#body p.msg').text('通信エラーが発生しました');
				}
			});
		}
		
		if (open_advance === true) {
			$('#advance_form').show();
		}
		
		$('#btn_add_content').click(function() {
			$.ajax({
				url		: '<?=base_url()?>request/get/tpl/',
				type	: 'post',
				data	: {
					tpl		: '_inc/content.form.php',
					theme	: '_admin',
					key	: cnt_key
				},
				success	: function(msg) {
					$('#content_form').append('<div class="content_form clearfix">' + msg + '</div>');
				}
			});
			cnt_key = cnt_key + 1;
			//$('#content_form').append();
		});
		
		$('#btn_existingfile').click(function() {
			$('#existing_file').toggle();
			
			$.ajax({
				url: '<?=base_url()?>request/get/file/',
				type: 'post',
				datatype: 'html',
				data: {
					type: 'html',
					tpl: '_request/file.list',
					theme: '_admin'
				},
				success: function(msg) {
					$('#existing_file').html(msg);
				}
			});
		});
		
		$('#trigger_advance').click(function() {
			$('#advance_form').toggle();
		});
		
		//viewer.naviInit();
		//viewer.get();
	});
</script>
</html>