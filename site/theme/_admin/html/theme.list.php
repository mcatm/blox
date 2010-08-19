<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/theme/"><?=$this->lang->line('system_label_theme')?></a></h2>
					<div class="add"><a href="<?=base_url()?>admin/theme/new/">+ ADD</a> | <a id="trigger_tool_filter">+ FILTER</a></div>
					<div class="tool">
						<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['admin/theme']) || count($this->session->userdata['filter']['admin/theme']) == 0){?> hide<?}?>">
							<div class="sort">
								createdate: <a href="<?=base_url()?>admin/theme/filter/sort/createdate/desc/">desc</a> | <a href="<?=base_url()?>admin/theme/filter/sort/createdate/asc/">asc</a> | 
								status: <a href="<?=base_url()?>admin/theme/filter/where/status/0/"><?=$this->setting->get_status(0)?></a> | <a href="<?=base_url()?>admin/theme/filter/where/status/1/"><?=$this->setting->get_status(1)?></a> | <a href="<?=base_url()?>admin/theme/filter/where/status/9/"><?=$this->setting->get_status(9)?></a>
							</div>
							<form action="<?=base_url()?>admin/theme/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/theme']['query'])){?><?=$this->session->userdata['filter']['admin/theme']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/theme/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($tpl)){?><form action="<?=base_url()?>admin/theme/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($tpl as $k => $v){?>
						<?if(isset($v['child'])){?><tr class="theme_id<?=$v['name']?>">
							<td><h3><a href="<?=base_url()?>admin/theme/<?=$page['base_segment']?><?=$v['name']?>/"><?=$v['name']?></a></h3></td>
						</tr>
						<tr><td>
							<?foreach($v['child'] as $ck => $cv){?>
								<?if (isset($cv['child'])) {?><a href="<?=base_url()?>admin/theme/<?=$page['base_segment']?><?=$v['name']?>/<?=$cv['path']?>/"><?=$cv['name']?></a><br /><?} else {?><a href="<?=base_url()?>admin/theme/edit/<?=$page['base_segment']?><?=$v['name']?>/<?=$cv['name']?>"><?=$cv['name']?></a><br /><?}}?>
						</td></tr>
						<?}else{?><tr class="theme_id<?=$v['name']?>">
							<td><h3><a href="<?=base_url()?>admin/theme/edit/<?=$page['base_segment']?><?=$v['name']?>"><?=$v['name']?></a></h3></td>
						</tr><?}?>
						<?}?>
					</table>
					<div id="list_footer" class="clearfix">
						<div class="tool">
							<a id="btn-select-all">select all</a> / 
							<a id="btn-deselect-all">deselect all</a> / 
							<a id="btn-delete">delete</a>
						</div>
						<div class="pager"></div>
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