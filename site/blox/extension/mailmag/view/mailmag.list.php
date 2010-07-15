<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/ex/mailmag/"><?=$this->lang->line('system_label_mail')?></a></h2>
					<div class="add"><a href="<?=base_url()?>admin/ex/mailmag/add/">+ ADD</a> | <a id="trigger_tool_filter">+ FILTER</a></div>
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
				<?if(!empty($mailmag)){?><form action="<?=base_url()?>admin/mail/delete/" method="post" id="form_list" name="form_list">
					<table>
						<?foreach($mailmag as $k => $v){?><tr class="mail_id<?=$v['id']?>">
							<td rowspan="2" style="text-align:center;"><input type="checkbox" name="id[]" value="<?=$v['id']?>" /></td>
							<td colspan="5"><h3><a href="<?=base_url()?>admin/ex/mailmag/edit/<?=$v['id']?>/"><?=$v['status_a']?></a></h3></td>
						</tr>
						<tr class="mail_id<?=$v['id']?>">
							<td class="createdate"><?=$v['createdate']?></td>
							<td></td>
							<td><?=$v['status_b']?></td>
							<td><a href="<?=base_url()?>admin/ex/mailmag/send/<?=$v['id']?>/test/">Test</a></td>
							<td class="tool">
								<a href="<?=base_url()?>admin/mail/detail/<?=$v['id']?>/" class="btn detail" title="detail"><?=$this->lang->line('system_post_detail')?></a>
								<a id="btn_delete" attr="<?=$v['id']?>" class="btn delete" title="delete" attr="<?=$v['id']?>"><?=$this->lang->line('system_post_delete')?></a>
								<a id="btn_spam" attr="<?=$v['id']?>" class="btn spam" title="report a spam" attr="<?=$v['id']?>"><?=$this->lang->line('system_post_spam')?></a>
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