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
				<?if(isset($user)){?><table>
					<?foreach($user as $k => $v){?><tr>
						<td rowspan="2" style="text-align:center;"><input type="checkbox" /></td>
						<td rowspan="2" style="text-align:center;"><?if(isset($v['file_main'])){?><img src="<?=img_url($v['file_main'][0]['id'], 80, true)?>" /><?}?></td>
						<td colspan="3" class="author"><h3><a href="<?=base_url()?>admin/user/detail/<?=$v['id']?>/"><?=$v['name']?></a></h3></td>
					</tr>
					<tr>
						<td class="createdate"><a href="<?=base_url()?>admin/user/filter/sort/createdate/desc/"><?=$v['createdate']?></a></td>
						<td class="usertype"><?if(!empty($v['usertype'])){?><a href="<?=base_url()?>admin/user/filter/where/type/<?=$v['type']?>"><?=$v['usertype']['name']?></a><?}?></td>
						<td class="tool">
							<a href="<?=base_url()?>admin/user/detail/<?=$v['id']?>/" class="detail btn"><?=$this->lang->line('system_post_detail')?></a>
							<a href="<?=base_url()?>admin/user/edit/<?=$v['id']?>/" class="edit btn"><?=$this->lang->line('system_post_edit')?></a>
						</td>
					</tr><?}?>
				</table>
				<div class="pager"><?=$page['pager']?></div>
				<?}?>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>