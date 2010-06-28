<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?if(isset($dat)){?><ul><?foreach($dat as $f){?>
	<li<?if($f['status'] != ""){?><?if($f['status'] == "main"){?> class="main"<?} else {?> class="segment"<?}}?>>
		<?if($f['type'] == 'image'){?><img src="<?=img_url($f['id'], 100, true)?>" class="btn-file" file_id="<?=$f['id']?>" attr="upload-<?=$f['id']?>" /><?}else{?><?=$f['type']?><?}?>
		<div class="hide filemenu round" id="filemenu-upload-<?=$f['id']?>">
			<p class="img"><img src="<?=img_url($f['id'], 120, true)?>" /></p>
			<label>setting</label>
			<select class="select-file-segment" file_id="<?=$f['id']?>">
				<option file_id="<?=$f['id']?>" file_segment="---">---</option>
				<?foreach($this->data->out['file_segment'] as $seg){?><option value="<?=$seg?>"<?if($f['status'] == $seg){?> selected="selected"<?}?>><?=$seg?></option><?}?>
			</select>
			<a class="btn-file-unlink" file_id="<?=$f['id']?>">unlink</a>
			<input type="text" value="{@img:<?=$f['id']?>:100}" />
		</div>
	</li>
<?}?></ul><?}?>