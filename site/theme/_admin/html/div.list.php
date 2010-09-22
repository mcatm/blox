<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/div/"><?=$this->lang->line('system_label_div')?></a></h2>
					<div class="navi">
						<a href="<?=base_url()?>admin/div/new/" class="add">+ ADD</a>
						<a id="trigger_tool_filter" class="filter">+ FILTER</a>
					</div>
					<div class="tool">
						<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['admin/div']) || count($this->session->userdata['filter']['admin/div']) == 0){?> hide<?}?>">
							<div class="sort">
								theme: <a href="<?=base_url()?>admin/div/filter/sort/theme/desc/">desc</a> | <a href="<?=base_url()?>admin/div/filter/sort/theme/asc/">asc</a> 
								type: <a href="<?=base_url()?>admin/div/filter/sort/type/desc/">desc</a> | <a href="<?=base_url()?>admin/div/filter/sort/type/asc/">asc</a>
								url: <a href="<?=base_url()?>admin/div/filter/sort/alias/desc/">desc</a> | <a href="<?=base_url()?>admin/div/filter/sort/alias/asc/">asc</a>
							</div>
							<form action="<?=base_url()?>admin/div/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/div']['query'])){?><?=$this->session->userdata['filter']['admin/div']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/div/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($div)){?><form action="<?=base_url()?>admin/div/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($div as $k => $v){?><tr class="div_id<?=$v['id']?>">
							<td rowspan="2" style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td rowspan="2" style="text-align:center;"><a href="<?=base_url()?>admin/div/filter/where/type/<?=$v['type']?>/"><?if($v['type'] != ""){?><?=$v['type']?><?}else{?>page<?}?></a></td>
							<td colspan="4"><h3><a href="<?=base_url()?>admin/div/edit/<?=$v['id']?>/"><?=$v['title']?></a></h3></td>
						</tr>
						<tr class="div_id<?=$v['id']?>">
							<td><a href="<?=$v['url']?>" target="_blank"><?=$v['url']?></a></td>
							<td><?=$v['theme']?>/html/<?=$v['tpl']?>.php</td>
							<td class="tool">
								<a href="<?=base_url()?>admin/div/edit/<?=$v['id']?>/" class="btn edit" title="edit"><?=$this->lang->line('system_post_edit')?></a>
								<a id="btn_delete" attr="<?=$v['id']?>" class="btn delete" title="delete" attr="<?=$v['id']?>"><?=$this->lang->line('system_post_delete')?></a>
								<div class="clear"></div>
							</td>
						</tr><?}?>
					</table>
				</form>
				<div id="list_footer" class="foot clearfix">
					<div class="tool">
						<a id="btn-select-all">select all</a><a id="btn-deselect-all">deselect all</a><a id="btn-delete">delete</a>
					</div>
					<div class="pager"><?=$page['pager']?></div>
				</div>
				<?}?>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript">
	$(function() {
		$('td.tool .delete').each(function() {
			$(this).click(function() {
				div_id = $(this).attr('attr');
				flg_del = confirm ('削除してもよろしいですか？');
				if (flg_del) {
					$.ajax({
						url: '<?=base_url()?>request/delete/div/',
						type: 'post',
						data: {
							id: div_id
						}
					});
					$('.div_id' + div_id).fadeOut('slow');
				}
			});
		});
		
		$('#btn-delete').click(function() {
			if($('#form_list :checked').length > 0) {
				flg_del = confirm ('記事を削除します。よろしいですか？');
				if (flg_del) {
					document.form_list.submit();
				}
			}
		});
		
		$('#btn-select-all').click(function() {
			$(':checkbox').attr('checked', 'checked');
		});
		
		$('#btn-deselect-all').click(function() {
			$(':checkbox').removeAttr('checked');
		});
	});
</script>
</html>