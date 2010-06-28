<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<form action="<?=base_url()?>admin/file/upload/" method="post" enctype="multipart/form-data">
				<p><input type="file" name="file" /></p>
				<p><input type="submit" value="post" /></p>
			</form>
			
			<?if(isset($file)){?><div class="list">
				<table>
					<?foreach($file as $k => $v){?><tr>
						<td rowspan="2" style="text-align:center;"><input type="checkbox" /></td>
						<td rowspan="2" style="text-align:center;"><img src="<?=img_url($v['id'], 100)?>" /></td>
						<td colspan="4"><h3><?=$v['name']?></h3></td>
					</tr>
					<tr>
						<td><?=$v['createdate']?></td>
						<td><?=$v['modifydate']?></td>
						<td><?=$v['type']?></td>
						<td><?=$this->lang->line('system_post_detail')?> <?=$this->lang->line('system_post_preview')?> <?=$this->lang->line('system_post_delete')?></td>
					</tr><?}?>
				</table>
				<div class="pager"><?=$page['pager']?></div>
			</div>
		</div><?}?>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>