<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?if(!empty($dat)){?><ul class="round"><?foreach($dat as $d){?>
	<li class="clearfix">
		<?if(isset($d['file_main']) && $d['file_main'][0]['type'] == 'image') {?><p class="img"><img src="<?=img_url($d['file_main'][0]['id'], $this->setting->get('img_size_thumbnail'), true)?>"?></p><?}?>
		<div class="data">
			<h3><a href="<?=base_url()?>admin/post/<?=$d['id']?>/"><?=$d['title']?></a></h3>
			<p class="description"><?=format_description($d['text'], 100)?></p>
		</div>
	</li>
<?}?></ul><?}?>