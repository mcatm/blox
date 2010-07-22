<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/user/"><?=$this->lang->line('system_label_user')?></a></h2>
					<div class="add"><a href="<?=base_url()?>admin/user/new/">+ ADD</a> | <a id="trigger_tool_filter">+ FILTER</a></div>
					<div class="tool">
						<div id="target_tool_filter" class="filter<?if(!isset($this->session->userdata['filter']['admin/user']) || count($this->session->userdata['filter']['admin/user']) == 0){?> hide<?}?>">
							<div class="sort">
								createdate: <a href="<?=base_url()?>admin/user/filter/sort/createdate/desc/" class="desc">desc</a> | <a href="<?=base_url()?>admin/user/filter/sort/createdate/asc/" class="asc">asc</a>
							</div>
							<form action="<?=base_url()?>admin/user/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/user']['query'])){?><?=$this->session->userdata['filter']['admin/user']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/user/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($user)){?><form action="<?=base_url()?>admin/user/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($user as $k => $v){?><tr class="user_id<?=$v['id']?>">
							<td rowspan="2" style="text-align:center;"><?if($this->data->out['me']['id'] != $v['id']){?><input type="checkbox" name="id[]" value="<?=$v['id']?>" /><?}?></td>
							<td rowspan="2" style="text-align:center;"><?if(isset($v['file_main'])){?><img src="<?=img_url($v['file_main'][0]['id'], 80, true)?>" /><?}?></td>
							<td colspan="3" class="author"><h3><a href="<?=base_url()?>admin/user/detail/<?=$v['id']?>/"><?=$v['name']?></a></h3></td>
						</tr>
						<tr class="user_id<?=$v['id']?>">
							<td class="createdate"><a href="<?=base_url()?>admin/user/filter/sort/createdate/desc/"><?=$v['createdate']?></a></td>
							<td class="usertype"><?if(!empty($v['usertype'])){?><a href="<?=base_url()?>admin/user/filter/where/type/<?=$v['type']?>"><?=$v['usertype']['name']?></a><?}?></td>
							<td class="tool">
								<a href="<?=base_url()?>admin/user/detail/<?=$v['id']?>/" class="detail btn"><?=$this->lang->line('system_post_detail')?></a>
								<a href="<?=base_url()?>admin/user/edit/<?=$v['id']?>/" class="edit btn"><?=$this->lang->line('system_post_edit')?></a>
								<?if($this->data->out['me']['id'] != $v['id']){?><a id="btn_delete" attr="<?=$v['id']?>" class="btn delete" title="delete" attr="<?=$v['id']?>"><?=$this->lang->line('system_post_delete')?></a><?}?>
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
				user_id = $(this).attr('attr');
				flg_del = confirm ('削除してもよろしいですか？');
				if (flg_del) {
					$.ajax({
						url: '<?=base_url()?>request/delete/user/',
						type: 'post',
						data: {
							id: user_id
						}
					});
					$('.user_id' + user_id).fadeOut('slow');
				}
			});
		});
		
		$('#btn-delete').click(function() {
			if($('#form_list :checked').length > 0) {
				flg_del = confirm ('ユーザーを削除します。よろしいですか？');
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