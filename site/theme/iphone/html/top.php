<?$this->load->view('_inc/html.header.php')?>
<body>
<div class="toolbar">
	<h1 id="pageTitle"></h1>
	<a id="backButton" class="button" href="#"></a>
</div>
<ul id="home" title="<?=$this->setting->get('title')?>" selected="true">
	<li><a href="<?=base_url()?>i/post/">POST</a></li>
</ul>
</body>
</html>