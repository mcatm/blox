<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$lang_prefix = (defined('EXTENSION_CONTROLLER')) ? EXTENSION_CONTROLLER : 'system';?>
<a name="top" id="top"></a>
<div id="head">
	<div class="container clearfix">
		<h1><a href="<?=base_url()?>admin/"><?=$this->setting->get('title')?></a></h1>
		<ul class="tool clearfix">
			<li class="website"><a href="<?=base_url()?>" target="_blank">WEBSITE</a></li>
			<li class="search" id="btn-tool-search"><a>SEARCH</a></li>
			<li class="doc"><a href="<?=$this->setting->get('doc_help')?>" target="_blank">DOCS</a></li>
			<li class="forum"><a href="<?=$this->setting->get('doc_forum')?>" target="_blank">FORUM</a></li>
		</ul>
		<div class="status clearfix">
			<div class="name" id="trigger-tool-profile"><?=$me['name']?> [<?=$me['auth']['name']?>]</div>
			<div class="logout"><a href="<?=base_url()?>admin/logout/">logout</a></div>
		</div>
	</div>
	<div class="window clearfix">
		<div class="search clearfix" id="tool-search">
			<div class="result" id="tool-search-result"></div>
			<div class="navi round" id="tool-search-navi">
				<input type="text" class="round" id="tool-search-query" />
			</div>
		</div>
		<div class="profile" id="tool-profile">
			<div class="container clearfix">
				<h2><a href="<?=base_url()?>admin/user/edit/<?=$me['id']?>/"><?=$me['name']?></a></h2>
				<?if(isset($me['file_main'])){?><div class="avater"><a href="<?=base_url()?>admin/user/edit/<?=$me['id']?>/"><img src="<?=img_url($me['file_main'][0]['id'], 48, true)?>" /></a></div><?}?>
				<div class="data">
					<p class="auth"><?=$me['auth']['name']?></p>
					<p class="account"><?=$me['account']?></p>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="navi">
	<div class="container clearfix">
		<ul class="menu clearfix">
			<?foreach($admin_menu as $menu_sec => $menu_val){?><?if(!isset($menu_val['auth']) || $me['auth']['type'] == 'admin' || isset($menu_val['auth']) && isset($me['auth'][$menu_val['auth']])){?><li class="section<?if($menu_val['alias'] == 'site' || $menu_val['alias'] == 'file'){?> setting <?=$menu_val['alias']?><?}?>">
				<h2><a href="<?=base_url()?>admin/<?=$menu_val['alias']?>/"><?if ($this->lang->line($lang_prefix.'_label_'.$menu_sec)) {?><?=$this->lang->line($lang_prefix.'_label_'.$menu_sec)?><?} else {?><?=$menu_sec?><?}?></a></h2>
				<?if(isset($menu_val['sub'])){?><div class="sub clearfix">
					<ul>
						<?foreach($menu_val['sub'] as $menu_sub_key => $menu_sub_val){?><?if(!isset($menu_sub_val['auth']) || $me['auth']['type'] == 'admin' || (isset($menu_sub_val['auth']) && isset($me['auth'][$menu_sub_val['auth']]))){?><li><a href="<?=base_url()?>admin/<?=$menu_sub_val['alias']?>/"><?if ($this->lang->line($lang_prefix.'_label_'.$menu_sec)) {?><?=$this->lang->line($lang_prefix.'_label_'.$menu_sub_key)?><?} else {?><?=$menu_sub_key?><?}?></a></li><?}}?>
					</ul>
				</div><?} elseif ($menu_sec == 'extension' && $this->setting->get('extension_loaded')) {?><div class="sub clearfix">
					<ul>
						<?foreach($this->setting->get('extension_loaded') as $menu_ex_val) {?><li><a href="<?=base_url()?>admin/ex/<?=$menu_ex_val?>/"><?=$menu_ex_val?></a></li><?}?>
					</ul>
				</div><?}?>
			</li><?}}?>
		</ul>
	</div>
</div>
