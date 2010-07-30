<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<form action="<?=base_url()?>admin/file/edit/<?=$file[0]['id']?>" method="post" id="body">
		
		<div class="container">
			<div class="postmeta">
				<div class="row"><img src="<?=base_url()?>request/img/<?=$file[0]['id']?>/300/" /></div>
			</div>
			<div class="postbody">
				<div class="row">
					<label>name : </label>
					<input type="text" name="name" value="<?=set_value('name', (isset($file[0]['name'])) ? $file[0]['name'] : '')?>" class="query input" />
				</div>
				<div class="row">
					<label>copyright : </label>
					<input type="text" name="copyright" value="<?=set_value('copyright', (isset($file[0]['copyright'])) ? $file[0]['copyright'] : '')?>" class="query input" />
				</div>
				<div class="row">
					<label>comment : </label>
					<textarea name="comment" class="input elastic editor" id="edit_comment" rows="6"><?=set_value('comment', (isset($file[0]['comment'])) ? $file[0]['comment'] : '')?></textarea>
				</div>
			</div>
			<?/*
			<?if(isset($ext) && is_array($ext)) {?><div class="ext clearfix">
				<div class="postbody">
					<?foreach($ext as $e){?><p>
						<label><?=$e['label']?></label>
						<?if ($e['type'] == 'textarea'){?><textarea name="ext_<?=$e['field']?>" id="edit_text_<?=$e['field']?>" class="input elastic editor" rows="6"><?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?></textarea>
						<?} else {?><input type="text" name="ext_<?=$e['field']?>" value="<?=set_value('ext_'.$e['field'], (isset($post[0][$e['field']])) ? $post[0][$e['field']] : '')?>" class="query input" />
						<?}?>
					</p><?}?>
				</div>
			</div><?}?>
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
							<?$contributor = array();
							foreach($user as $ku => $vu){?><li><input type="checkbox" name="author[]" value="<?=$vu['id']?>"<?if (isset($post[0]['author_id']) && in_array($vu['id'], $post[0]['author_id'])) print ' checked="checked"'?> /> <?=$vu['name']?></li>
							<?$contributor[]=$vu['id'];}?>
							</ul></div>
							
							<?if(isset($post[0]['author'])){?><ul>
							<?foreach($post[0]['author'] as $ku=>$vu){
								if(!in_array($vu['id'], $contributor)) {?>
							<li><input type="checkbox" name="author[]" value="<?=$vu['id']?>" checked="checked"' /> <?=$vu['name']?></li>
							<?}}?>
							</ul><?}?>
							
							<?}?>
						</div>
					</div>
				</div>
			</div>*/?>
			<div class="console"><p><input type="submit" value="post" class="submit round" /></p></div>
		</div>
	</form>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript">
	$(function() {
		
	});
</script>
</html>