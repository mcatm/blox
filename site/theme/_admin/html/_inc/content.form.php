<?if (!isset($ct_key)) {
	$this->load->library(array('div', 'form_validation'));
	
	$ct_key = $this->input->post('key');
	
	#$div[0]['content'] = array();
	
	$this->div->get(array(
		'label'	=> 'div_option',
		'where'	=> '(div_type != "")'
	));
	
	$div_option = $this->data->out['div_option'];
	unset($this->data->out['div']);
}?>
<div class="content_form clearfix">
	<div class="postmeta">
		<p><label>article type : </label>
		<select name="content[<?=$ct_key?>][type]">
			<option value="">---</option>
			<option value="post"<?if (isset($div[0]['content'][$ct_key]['type']) && $div[0]['content'][$ct_key]['type'] == 'post') print ' selected="selected"'?>>post</option>
		</select>
	</div>
	<div class="postbody clearfix">
		<p><label>qty : </label>
		<input type="text" name="content[<?=$ct_key?>][param][qty]" value="<?=set_value('content['.$ct_key.'][param][qty]', (isset($div[0]['content'][0]['param']['qty'])) ? $div[0]['content'][0]['param']['qty'] : '')?>" /></p>
		
		<p><label>division : </label>
		<select name="content[<?=$ct_key?>][param][div]">
			<option value="">----</option>
			<option value="this"<?if(!isset($div)){?> selected="selected"<?}?>>this</option>
			<?foreach($div_option as $opt){?><option value="<?=$opt['id']?>"<?if (isset($div[0]['content'][$ct_key]['param']['div']) && $div[0]['content'][$ct_key]['param']['div'] == $opt['id']) print ' selected="selected"'?>>[<?=$opt['type']?>] <?=$opt['title']?></option><?}?>
		</select></p>
		
		<p><label>label : </label>
		<input type="text" name="content[<?=$ct_key?>][param][label]" value="<?=set_value('content['.$ct_key.'][param][label]', (isset($div[0]['content'][$ct_key]['param']['label'])) ? $div[0]['content'][$ct_key]['param']['label'] : '')?>" /></p>
	</div>
</div>