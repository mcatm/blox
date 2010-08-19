<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>home/post/"><?=$this->lang->line('system_label_post')?></a></h2>
					<div class="add"><a href="<?=base_url()?>home/post/new/">+ ADD</a> | <a id="trigger_tool_filter">+ FILTER</a></div>
					<div class="tool">
						<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['home/post']) || count($this->session->userdata['filter']['home/post']) == 0){?> hide<?}?>">
							<div class="sort">
								createdate: <a href="<?=base_url()?>home/post/filter/sort/createdate/desc/">desc</a> | <a href="<?=base_url()?>home/post/filter/sort/createdate/asc/">asc</a> | 
								status: <a href="<?=base_url()?>home/post/filter/where/status/0/"><?=$this->setting->get_status(0)?></a> | <a href="<?=base_url()?>home/post/filter/where/status/1/"><?=$this->setting->get_status(1)?></a> | <a href="<?=base_url()?>home/post/filter/where/status/9/"><?=$this->setting->get_status(9)?></a>
							</div>
							<form action="<?=base_url()?>home/post/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['home/post']['query'])){?><?=$this->session->userdata['filter']['home/post']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>home/post/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($post)){?><form action="<?=base_url()?>home/post/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($post as $k => $v){?><tr class="status<?=$v['status']?> post_id<?=$v['id']?>">
							<td rowspan="2" style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td rowspan="2" style="text-align:center;"><?if(isset($v['file_main'])){?><?if($v['file_main'][0]['type'] == 'image'){?><img src="<?=img_url($v['file_main'][0]['id'], 80, true)?>" /><?}?><?} elseif (isset($v['file_sub'])){?><?if($v['file_sub'][0]['type'] == 'image'){?><img src="<?=img_url($v['file_sub'][0]['id'], 80, true)?>" /><?}?><?} elseif (isset($v['file_large'])){?><?if($v['file_large'][0]['type'] == 'image'){?><img src="<?=img_url($v['file_large'][0]['id'], 80, true)?>" /><?}?><?}?></td>
							<td colspan="4"><h3><a href="<?=base_url()?>home/post/edit/<?=$v['id']?>/"><?=$v['title']?></a></h3></td>
						</tr>
						<tr class="status<?=$v['status']?> post_id<?=$v['id']?>">
							<td class="createdate"><a href="<?=base_url()?>home/post/filter/sort/createdate/desc/"><?=$v['createdate']?></a></td>
							<td class="author"><?foreach($v['author'] as $a){?><a href="<?=base_url()?>home/post/filter/where/user/<?=$a['id']?>"><?=$a['name']?></a> <?}?></td>
							<td><?=$this->setting->get_status($v['status'])?></td>
							<td class="tool">
								<a href="<?=base_url()?>home/post/edit/<?=$v['id']?>/" class="btn detail" title="detail"><?=$this->lang->line('system_post_detail')?></a>
								<?#=$this->lang->line('system_post_preview')?>
								<a href="<?=base_url()?>home/post/edit/<?=$v['id']?>/" class="btn edit" title="edit"><?=$this->lang->line('system_post_edit')?></a>
								<a id="btn_delete" attr="<?=$v['id']?>" class="btn delete" title="delete" attr="<?=$v['id']?>"><?=$this->lang->line('system_post_delete')?></a>
								<div class="clear"></div>
							</td>
						</tr><?}?>
					</table>
					<div id="list_footer" class="clearfix">
						<div class="tool">
							<a id="btn-select-all">select all</a> / 
							<a id="btn-deselect-all">deselect all</a> / 
							<a id="btn-delete">delete</a>
						</div>
						<div class="pager"><?=$page['pager']?></div>
					</div>
				</form><?}?>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript">
	$(function() {
		$('td.tool .delete').each(function() {
			$(this).click(function() {
				post_id = $(this).attr('attr');
				flg_del = confirm ('削除してもよろしいですか？');
				if (flg_del) {
					$.ajax({
						url: '<?=base_url()?>request/delete/post/',
						type: 'post',
						data: {
							id: post_id
						}
					});
					$('.post_id' + post_id).fadeOut('slow');
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