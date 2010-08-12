<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?if(!empty($dat)){?><ul><?foreach($dat as $f){?>
	<li>
		<?if($f['type'] == 'image'){?><img src="<?=img_url($f['id'], 100, true)?>" class="btn-file" file_id="<?=$f['id']?>" attr="global-<?=$f['id']?>" /><?}else{?><?=$f['type']?><?}?>
		<div class="hide filemenu round" id="filemenu-global-<?=$f['id']?>">
			<p class="img"><?if($f['type'] == 'image'){?><img src="<?=img_url($f['id'], 120, true)?>" /><?} else {?>sound<?}?></p>
			<a class="btn-file-link" file_id="<?=$f['id']?>">link</a>
			<input type="text" value="<?=$this->setting->get('format_tag_open')?>img:<?=$f['id']?>:mid:0::<?=$this->setting->get('format_tag_close')?>" />
		</div>
		<?/*select class="file_segment">
			<option value="">---</option>
			<?foreach($this->data->out['file_segment'] as $seg){?><option value="<?=$seg?>"><?=$seg?></option><?}?>
		</select*/?>
	</li>
<?}?></ul><?}?>