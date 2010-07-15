<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/ex/mailmag/edit/" method="post" id="body">
	<?if(isset($mailmag[0]['id'])){?><input type="hidden" name="id" value="<?=$mailmag[0]['id']?>" /><?}?>
		<div class="container">
			<div class="postmeta">
				<div class="row">
					<label>title : </label>
					<input type="text" name="title" value="<?=set_value('title', (isset($mailmag[0]['value']['title'])) ? $mailmag[0]['value']['title'] : '')?>" class="query input" />
				</div>
				
				<div class="row">
					<label>device : </label><select name="device">
						<option value="pc"<?=set_select('device', 'pc')?><?if(isset($mailmag[0]['value']['device']) && $mailmag[0]['value']['device']=='pc'){?> selected="selected"<?}?>>PC</option>
						<option value="mobile"<?=set_select('device', 'mobile')?><?if(isset($mailmag[0]['value']['device']) && $mailmag[0]['value']['device']=='mobile'){?> selected="selected"<?}?>>携帯</option>
						<option value="all"<?=set_select('device', 'all')?><?if(isset($mailmag[0]['value']['device']) && $mailmag[0]['value']['device']=='all'){?> selected="selected"<?}?>>すべて</option>
					</select>
				</div>
				
				<div class="row">
					<label>createdate : </label>
					<input type="text" name="createdate" value="<?=set_value('createdate', (isset($mailmag[0]['createdate'])) ? $mailmag[0]['createdate'] : 'now')?>" class="query input" />
				</div>
			</div>
			<div class="postbody">
				<div class="row">
					<label>text : </label>
					<textarea name="text" class="input elastic editor" id="edit_text" rows="18"><?=set_value('text', (isset($mailmag[0]['value']['text'])) ? $mailmag[0]['value']['text'] : '')?></textarea>
					<?=form_error('text')?>
				</div>
			</div>
			<?if(isset($ext) && is_array($ext)) {?><div class="ext clearfix">
				<div class="postbody">
					<?foreach($ext as $e){?><p>
						<label><?=$e['label']?></label>
						<?if ($e['type'] == 'textarea'){?><textarea name="ext_<?=$e['field']?>" class="input elastic" rows="6"><?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?></textarea>
						<?} else {?><input type="text" name="ext_<?=$e['field']?>" value="<?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?>" class="query input" />
						<?}?>
					</p><?}?>
				</div>
			</div><?}?>
			<div class="console"><p><input type="submit" value="post" class="submit round" /></p></div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/upload.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/suggest.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/elastic.js"></script>
<script type="text/javascript">
	var post = [];
	<?if(isset($post[0])){?>var post_id = <?=$post[0]['id']?>;<?}?>
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
		
		$.getJSON('<?=base_url()?>request/get/tag/', function(json) {
			new Suggest.LocalMulti(
				'tag',		//入力のエレメントID
				'suggest',	//補完候補を表示するエリアのID
				json,		//補完候補の検索対象となる配列
				{dispMax: 10, interval: 1000, delim:',', prefix:true}// オプション
			);
		});
		
		$('textarea.elastic').elastic();
	});
	
	
	
	function init() {
		if (typeof(post_id) != "undefined") {
			load_upload();
		}
		load_file();
		set();
		//get_tag();
		$('.tab-target').hide();
		$('.tab-target:first').show();
	}
	
	function set() {
		if (typeof(post_id) != "undefined") {
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
						url: base_url + 'request/set/linx/post2file/' + post_id + '/' + file_id + '/' + file_segment + '/b/',
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
						url: base_url + 'request/set/linx/post2file/' + post_id + '/' + file_id + '/---/b/',
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
						url: base_url + 'request/delete/linx/post2file/' + post_id + '/' + file_id,
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
				post_id: post_id,
				tpl: '_request/upload.list',
				theme: '_admin'
			},
			success: function(msg) {
				$('#upload_file').html(msg);
				set();
			}
		});
	}
	
	function get_tag() {
		$.ajax({
			url: '<?=base_url()?>request/get/tag/',
			type: 'post',
			datatype: 'ajax',
			success: function(msg) {
				alert('UUU');
			}
		});
	}
</script>
</html><?exit?>