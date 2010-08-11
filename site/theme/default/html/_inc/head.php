<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="head" class="clearfix">
	<h1><a href="<?=base_url()?>"><?=$this->setting->get('site_name')?></a></h1>
	<form action="<?=base_url()?>search" method="post" class="search">
		<input type="text" name="q" />
	</form>
</div>