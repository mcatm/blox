<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="head round">
				<h2><a href="<?=base_url()?>admin/file/"><?=$this->lang->line('system_label_file')?></a></h2>
				<div class="add"><a href="<?=base_url()?>admin/file/add/">+ ADD</a> | <a id="trigger_tool_filter">+ FILTER</a></div>
				<div class="tool">
					<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['admin/file']) || count($this->session->userdata['filter']['admin/file']) == 0){?> hide<?}?>">
						<div class="sort">createdate: <a href="<?=base_url()?>admin/file/filter/sort/createdate/desc/">desc</a> | <a href="<?=base_url()?>admin/file/filter/sort/createdate/asc/">asc</a></div>
						<form action="<?=base_url()?>admin/file/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/file']['query'])){?><?=$this->session->userdata['filter']['admin/file']['query']?><?}?>" /></form>
						<a href="<?=base_url()?>admin/file/filter/clear/">filter clear</a>
					</div>
				</div>
			</div>
			<?if(isset($file)){?><form action="<?=base_url()?>admin/file/delete/" method="post" id="form_list" name="form_list">
				<div class="list">
					<table>
						<?foreach($file as $k => $v){?><tr>
							<td rowspan="2" style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td rowspan="2" style="text-align:center;"><a href="<?=base_url()?>admin/file/edit/<?=$v['id']?>/"><img src="<?=img_url($v['id'], 100)?>" /></a></td>
							<td colspan="4"><h3><a href="<?=base_url()?>admin/file/edit/<?=$v['id']?>/"><?=$v['name']?></a></h3></td>
						</tr>
						<tr>
							<td class="createdate"><?=$v['createdate']?></td>
							<td><?=ceil($v['size']/100)?>KB</td>
							<td><?=$v['type']?></td>
							<td>
								<a href="<?=$v['download_url']?>">download</a>
								<?=$this->lang->line('system_file_detail')?>
								<?=$this->lang->line('system_file_preview')?>
								<?=$this->lang->line('system_file_delete')?></td>
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
				</div>
			</form><?}?>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript">
	$(function() {
		/*$('td.tool .delete').each(function() {
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
		});*/
		
		$('#btn-delete').click(function() {
			if($('#form_list :checked').length > 0) {
				flg_del = confirm ('ファイルを削除します。よろしいですか？');
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