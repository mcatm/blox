<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/tag/"><?=$this->lang->line('system_label_tag')?></a></a></h2>
					<div class="navi">
						<a id="trigger_tool_filter" class="filter">+ FILTER</a>
					</div>
					<div class="tool">
						<div id="target_tool_filter" class="filter<?if(!isset($this->session->userdata['filter']['admin/tag']) || count($this->session->userdata['filter']['admin/tag']) == 0){?> hide<?}?>">
							<div class="sort">createdate: <a href="<?=base_url()?>admin/tag/filter/sort/createdate/desc/">desc</a> | <a href="<?=base_url()?>admin/tag/filter/sort/createdate/asc/">asc</a></div>
							<form action="<?=base_url()?>admin/tag/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/tag']['query'])){?><?=$this->session->userdata['filter']['admin/tag']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/tag/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($tag)){?><form action="<?=base_url()?>admin/tag/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($tag as $k => $v){?><tr>
							<td rowspan="2" style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td colspan="4"><h3><?=$v['name']?></h3></td>
						</tr>
						<tr>
							<td><a href="<?=base_url()?>admin/tag/filter/sort/createdate/desc/"><?=$v['createdate']?></a></td>
							<td><a href="<?=base_url()?>admin/tag/filter/sort/update/desc/"><?=$v['update']?></a></td>
							<td><a href="<?=base_url()?>admin/tag/filter/sort/count/desc/"><?=$v['count']?></a></td>
							<td></td>
						</tr><?}?>
					</table>
					<div id="list_footer" class="foot clearfix">
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
				flg_del = confirm('タグを削除します。よろしいですか？');
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