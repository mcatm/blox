<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/ex/mailmag/"><?=$this->lang->line('mailmag_label')?></a></h2>
					<div class="add"><a id="trigger_tool_filter">+ FILTER</a></div>
					<div class="tool">
						<div id="target_tool_filter" class="filter <?if(!isset($this->session->userdata['filter']['admin/mail']) || count($this->session->userdata['filter']['admin/mailmag']) == 0){?> hide<?}?>">
							<div class="sort">
								createdate: <a href="<?=base_url()?>admin/mail/filter/sort/createdate/desc/">desc</a> | <a href="<?=base_url()?>admin/mail/filter/sort/createdate/asc/">asc</a>
							</div>
							<form action="<?=base_url()?>admin/mail/filter/q/" method="POST"><input type="text" name="q" value="<?if(isset($this->session->userdata['filter']['admin/mail']['query'])){?><?=$this->session->userdata['filter']['admin/mail']['query']?><?}?>" /></form>
							<a href="<?=base_url()?>admin/mail/filter/clear/">filter clear</a>
						</div>
					</div>
				</div>
				<?if(!empty($maillist)){?><form action="<?=base_url()?>admin/mail/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($maillist as $k => $v){?><tr class="mail_id<?=$v['id']?>">
							<td style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td colspan="5"><?=$v?></td>
						</tr><?}?>
					</table>
					<div id="list_footer" class="clearfix">
						<div class="tool">
							<a id="btn-select-all">select all</a> / 
							<a id="btn-deselect-all">deselect all</a> / 
							<a id="btn-delete">delete</a>
						</div>
						<div class="pager"><?=count($maillist)?></div>
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
				mail_id = $(this).attr('attr');
				flg_del = confirm ('削除してもよろしいですか？');
				if (flg_del) {
					$.ajax({
						url: '<?=base_url()?>request/delete/mail/',
						type: 'post',
						data: {
							id: mail_id
						}
					});
					$('.mail_id' + mail_id).fadeOut('slow');
				}
			});
		});
		
		$('td.tool .spam').each(function() {
			$(this).click(function() {
				mail_id = $(this).attr('attr');
				flg_spam = confirm ('スパムとして報告します。よろしいですか？');
				if (flg_spam) {
					$.ajax({
						url: '<?=base_url()?>request/set/spam/',
						type: 'post',
						data: {
							id: mail_id
						}
					});
					$('.mail_id' + mail_id).fadeOut('slow');
				}
			});
		});
		
		$('#btn-delete').click(function() {
			if($('#form_list :checked').length > 0) {
				flg_del = confirm('記事を削除します。よろしいですか？');
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
<?exit?>