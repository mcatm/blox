<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/post/edit/" method="post" id="body">
	<?if(isset($post[0]['id'])){?><input type="hidden" name="id" value="<?=$post[0]['id']?>" /><?}?>
	<?if(isset($post[0]['type'])){?><input type="hidden" name="type" value="<?=$post[0]['type']?>" /><?}?>
	<?if(isset($post[0]['parent'])){?><input type="hidden" name="parent" value="<?=$post[0]['parent']?>" /><?}?>
		<div class="container">
			<div class="postmeta">
				<div class="row">
					<label>title : </label>
					<input type="text" name="title" value="<?=set_value('title', (isset($post[0]['title'])) ? $post[0]['title'] : '')?>" class="query input" />
				</div>
				
				<div class="row">
					<label>status : </label><select name="status">
						<option value="0"<?=set_select('status', 0)?><?if(isset($post[0]['status']) && $post[0]['status']==0){?> selected="selected"<?}?>>公開</option>
						<option value="1"<?=set_select('status', 1)?><?if(isset($post[0]['status']) && $post[0]['status']==1){?> selected="selected"<?}?>>指定日投稿</option>
						<option value="9"<?=set_select('status', 9)?><?if(isset($post[0]['status']) && $post[0]['status']==9){?> selected="selected"<?}?>>下書き</option>
					</select>
				</div>
				
				<div class="row">
					<label>tag : </label>
					<input type="text" name="tagstr" value="<?=set_value('tagstr', (isset($post[0]['tagstr'])) ? $post[0]['tagstr'] : '')?>" class="query input" id="tag" autocomplete="off" />
					<div id="suggest"></div>
				</div>
			</div>
			<div class="postbody">
				<p>
					<label>text : </label>
					<textarea name="text" class="input" rows="12"><?=set_value('text', (isset($post[0]['text'])) ? $post[0]['text'] : '')?></textarea>
					<?=form_error('text')?>
				</p>
			</div>
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
			<div class="ext clearfix">
				<div class="postbody">
					<?if(isset($ext) && is_array($ext)) {?><?foreach($ext as $e){?><p>
						<label><?=$e['label']?></label>
						<?if ($e['type'] == 'textarea'){?><textarea name="ext_<?=$e['field']?>" class="input" rows="6"><?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?></textarea>
						<?} else {?><input type="text" name="ext_<?=$e['field']?>" value="<?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?>" class="query input" />
						<?}?>
					</p><?}}?>
				</div>
			</div>
			<div class="advance clearfix">
				<div class="trigger"><a id="trigger_advance">advance</a></div>
				<div id="advance_form">
					<div class="postmeta">
						<p><label>number : </label><input type="text" name="number" value="<?=set_value('number', (isset($post[0]['number'])) ? $post[0]['number'] : '')?>" class="query input" /></p>
						<p><label>createdate : </label>
						<input type="text" name="createdate" value="<?=set_value('createdate', (isset($post[0]['createdate'])) ? $post[0]['createdate'] : 'now')?>" class="query input" /></p>
						<p><label>alias : </label><input type="text" name="alias" value="<?=set_value('alias', (isset($post[0]['alias'])) ? $post[0]['alias'] : '')?>" class="query input" /></p>
						
						<p><label>startedate : </label>
						<input type="text" name="startdate[]" value="<?=set_value('startdate[0]', (isset($post[0]['schedule']['start'][0])) ? $post[0]['schedule']['start'][0] : '')?>" class="query input" /></p>
						
						<p><label>enddate : </label>
						<input type="text" name="enddate[]" value="<?=set_value('enddate[0]', (isset($post[0]['schedule']['end'][0])) ? $post[0]['schedule']['end'][0] : '')?>" class="query input" /></p>
					</div>
					
					<div class="postbody clearfix">
						<div class="postdiv">
							<?if(isset($section)){?><div class="row"><label>section : </label>
							<ul>
							<?foreach($section as $ks => $vs){?><li><input type="checkbox" name="div[]" value="<?=$vs['id']?>"<?if (isset($post[0]['div_id']) && in_array($vs['id'], $post[0]['div_id'])) print ' checked="checked"'?> /> <?=$vs['title']?></li><?}?>
							</ul></div><?}?>
							
							<?if(isset($category)){?><div class="row"><label>category : </label>
							<ul>
							<?foreach($category as $kc => $vc){?><li><input type="checkbox" name="div[]" value="<?=$vc['id']?>"<?if (isset($post[0]['div_id']) && in_array($vc['id'], $post[0]['div_id'])) print ' checked="checked"'?> /> <?=$vc['name']?></li><?}?>
							</ul></div><?}?>
						</div>
						
						<div class="postauthor">
							<?if($this->setting->get('flg_set_author')){?><div class="row"><label>author : </label>
							<ul>
							<?foreach($user as $ku => $vu){?><li><input type="checkbox" name="author[]" value="<?=$vu['id']?>"<?if (isset($post[0]['author_id']) && in_array($vu['id'], $post[0]['author_id'])) print ' checked="checked"'?> /> <?=$vu['name']?></li><?}?>
							</ul></div><?}?>
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
</html>