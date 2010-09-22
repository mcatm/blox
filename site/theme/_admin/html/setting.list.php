<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/div/"><?=$this->lang->line('system_label_setting')?></a></h2>
					<div class="navi">
						<a href="<?=base_url()?>admin/div/new/" class="add">+ ADD</a>
						<a id="trigger_tool_filter" class="filter">+ FILTER</a>
					</div>
					<div class="tool">
						<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['admin/setting']) || count($this->session->userdata['filter']['admin/div']) == 0){?> hide<?}?>">
							<div class="sort">
								name: <a href="<?=base_url()?>admin/setting/filter/sort/name/desc/">desc</a> | <a href="<?=base_url()?>admin/setting/filter/sort/name/asc/">asc</a>
							</div>
							<form action="<?=base_url()?>admin/setting/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/setting']['query'])){?><?=$this->session->userdata['filter']['admin/setting']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/setting/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(isset($env)){?><form method="post" id="form_list" name="form_list">
					<table class="setting">
						<?foreach($env as $k => $v){?><tr class="env_id<?=$v['id']?>">
							<td><label><?=$this->lang->line('setting_'.$v['name'], true)?></label>
							<textarea class="query elastic"><?=htmlspecialchars($v['value'])?></textarea></td>
						</tr><?}?>
					</table>
				</form>
				<div id="list_footer" class="foot clearfix">
					<div class="tool">
						<a id="btn-select-all">select all</a><a id="btn-deselect-all">deselect all</a><a id="btn-delete">delete</a>
					</div>
					<div class="pager"></div>
				</div>
				<?}?>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/elastic.js"></script>
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
		
		$('textarea.elastic').elastic();
	});
</script>
</html>