<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/user/edit/" method="post" id="body">
		<div class="container">
			<div class="postmeta">
				<div class="row">
					<label>name : </label>
					<input type="text" name="name" value="<?=set_value('name', (isset($user[0]['name'])) ? $user[0]['name'] : '')?>" class="query" />
					<?=form_error('name')?>
				</div>
				<?if(!isset($user[0]['id'])){?><div class="row">
					<label>account : </label>
					<input type="text" name="account" value="<?=set_value('account', (isset($user[0]['account'])) ? $user[0]['account'] : '')?>" class="query" />
					<?=form_error('account')?>
				</div><?} else {?><div class="row">
					<label>account : </label><?=$user[0]['account']?>
				</div><?}?>
				<div class="row">
					<label>title : </label>
					<input type="text" name="title" value="<?=set_value('title', (isset($user[0]['title'])) ? $user[0]['title'] : '')?>" class="query" />
				</div>
				
				<?if(isset($user[0]['id'])){?><input type="hidden" name="id" value="<?=$user[0]['id']?>" /><?} else {?>
				<div class="row">
					<label>email : </label>
					<input type="text" name="email" value="<?=set_value('email')?>" class="query" />
					<?=form_error('email')?>
				</div>
				
				<div class="row">
					<label>password : </label>
					<input type="password" name="pwd" value="<?=set_value('pwd')?>" class="query" />
					<?=form_error('pwd')?>
				</div>
				
				<div class="row">
					<label>password(confirm) : </label>
					<input type="password" name="pwd_confirm" value="" class="query" />
					<?=form_error('pwd_confirm')?>
				</div>
				<?}?>
				
				<div class="row">
					<select name="type">
						<?foreach($usertype as $type){?><option value="<?=$type['id']?>"<?=set_select('type', $type['id'])?><?if(isset($user[0]['type']) && $type['id'] == $user[0]['type']){?> selected="selected"<?}?>><?=$type['name']?></option><?}?>
					</select>
				</div>
			</div>
			<div class="postbody">
				<div class="row">
					<label>description : </label>
					<textarea name="description" rows="10"><?=set_value('description', (isset($user[0]['description'])) ? $user[0]['description'] : '')?></textarea>
				</div>
			</div>
			<?if(isset($ext) && is_array($ext)) {?><div class="ext clearfix">
				<div class="postbody">
					<?foreach($ext as $e){?><p>
						<label><?=$e['label']?></label>
						<?if ($e['type'] == 'textarea'){?><textarea name="ext_<?=$e['field']?>" class="input" rows="6"><?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?></textarea>
						<?} else {?><input type="text" name="ext_<?=$e['field']?>" value="<?=set_value('ext_'.$e['field'], (isset($user[0][$e['field']])) ? $user[0][$e['field']] : '')?>" class="query input" />
						<?}?>
					</p><?}?>
				</div>
			</div><?}?>
			<div class="file">
				<div id="file_upload" class="file_upload clearfix">
					<a id="tab_uploadfile" class="tab selected" attr="upload">Upload</a>
					<a id="tab_existingfile" class="tab" attr="global">Files</a>
				</div>
				<div class="upload file_list tab-target" class="clearfix" id="tab-target-upload">
					<div class="tool_file_upload clearfix">
						<a id="btn_uploadfile" class="upload round">+ UPLOAD</a>
						<p class="msg"></p>
					</div>
					<ul class="clearfix" id="upload_file"></ul>
				</div>
				<div class="global file_list tab-target" class="clearfix" id="tab-target-global">
					<div class="clearfix" id="existing_file"></div>
				</div>
			</div>
			<div class="console">
				<p><input type="submit" value="post" class="submit round" /></p>
			</div>
		</div>
	</form>
	
	<?if(isset($user[0]['id'])){?><form action="<?=base_url()?>admin/user/password/" method="post" id="body" style="background:none">
		<input type="hidden" name="id" value="<?=$user[0]['id']?>" />
		<div class="container">
			<div class="postmeta">
				<div class="row">
					<label>new password : </label>
					<input type="text" name="pwd" value="<?=set_value('pwd')?>" class="query" />
					<?=form_error('pwd')?>
				</div>
			</div>
			<div class="postbody">
				<div class="row">
					<label>old password : </label>
					<input type="password" name="pwd_old" value="<?=set_value('pwd_old')?>" class="query" />
					<?=form_error('pwd_old')?>
				</div>
				
				<div class="row">
					<label>password(confirm) : </label>
					<input type="password" name="pwd_confirm" value="" class="query" />
					<?=form_error('pwd_confirm')?>
				</div>
			</div>
			<div class="console">
				<p><input type="submit" value="CHANGE PASSWORD" class="submit round" /></p>
			</div>
		</div>
	</form><?}?>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/upload.js"></script>
<script type="text/javascript">
	var post = [];
	
	var post = [];
	<?if(isset($user[0])){?>var user_id = <?=$user[0]['id']?>;<?}?>
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
							$('#upload_file').append('<li style="background:url(' + msg.img_url + ') no-repeat 50% 50%"></li>');
						} else {
							$('#upload_file').append('<li style="">file</li>');
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
		
		$('#trigger_advance').click(function() {
			$('#advance_form').toggle();
		});
		
		$('.tab').each(function() {
			$(this).bind('click', function() {
				$('.tab').each(function() {$(this).removeClass('selected')});
				$('.tab-target').hide();
				$('#tab-target-' + $(this).attr('attr')).show();
				$(this).addClass('selected');
			});
		});
		
		init();
	});
	
	function init() {
		if (typeof(user_id) != "undefined") {
			load_upload();
		}
		load_file();
		set();
		$('.tab-target').hide();
		$('.tab-target:first').show();
	}
	
	function set() {
		if (typeof(user_id) != "undefined") {
			$('.btn-file').each(function() {//ファイルメニューの表示非表示
				$(this).css('cursor', 'pointer');
				$(this).bind('click', function() {
					file_id = $(this).attr('file_id');
					file_key = $(this).attr('attr');
					$('.filemenu').hide();
					$('#filemenu-' + file_key).toggle();
				});
			});
			
			$('select.select-file-segment').each(function() {//ファイルセグメント変更
				$(this).one('change', function() {
					file_id = $(this).attr('file_id');
					file_segment = $(this).val();
					$.ajax({
						type: 'post',
						url: base_url + 'request/set/linx/user2file/' + user_id + '/' + file_id + '/' + file_segment + '/b/',
						success: function() {
							load_upload();
							load_file();
						}
					});
				});
			});
			
			$('a.btn-file-link').each(function() {//ファイルリンク
				$(this).one('click', function() {
					file_id = $(this).attr('file_id');
					$.ajax({
						type: 'post',
						url: base_url + 'request/set/linx/user2file/' + user_id + '/' + file_id + '/---/b/',
						success: function() {
							load_upload();
							load_file();
						}
					});
				});
			});
			
			$('a.btn-file-unlink').each(function() {//ファイルリンク削除
				$(this).one('click', function() {
					file_id = $(this).attr('file_id');
					$.ajax({
						type: 'post',
						url: base_url + 'request/delete/linx/user2file/' + user_id + '/' + file_id,
						success: function() {
							load_upload();
						}
					});
				});
			});
		}
	}
	
	function load_file() {
		$.ajax({
			url: '<?=base_url()?>request/get/file/',
			type: 'post',
			datatype: 'html',
			data: {
				qty	: 16,
				type: 'html',
				tpl: '_request/file.list',
				theme: '_admin'
			},
			success: function(msg) {
				$('#existing_file').html(msg);
				set();
			}
		});
	}
	
	function load_upload() {
		$.ajax({
			url: '<?=base_url()?>request/get/file/',
			type: 'post',
			datatype: 'html',
			data: {
				qty	: 0,
				type: 'html',
				user_id: user_id,
				tpl: '_request/upload.list',
				theme: '_admin'
			},
			success: function(msg) {
				$('#upload_file').html(msg);
				set();
			}
		});
	}
</script>
</html>