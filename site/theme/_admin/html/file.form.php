<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>

<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div id="fileQueue"></div>
			<input type="file" name="uploadify" id="uploadify" />
			<p><a href="javascript:$('#uploadify').uploadifyClearQueue()">Cancel All Uploads</a></p>
			
			<?/*form action="<?=base_url()?>admin/file/upload/" method="post" enctype="multipart/form-data">
				<p><input type="file" name="file" /></p>
				<p><input type="submit" value="post" /></p>
			</form*/?>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>

<link href="<?=ex_url()?>js/jquery/uploadify/uploadify.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?=ex_url()?>js/jquery/uploadify/swfobject.js"></script>
<script type="text/javascript" src="<?=ex_url()?>js/jquery/uploadify/uploadify.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#uploadify").uploadify({
		'uploader'			: '<?=ex_url()?>js/jquery/uploadify/uploadify.swf',
		'script'			: base_url + 'request/set/file/uploadify/',
		'cancelImg'			: '<?=ex_url()?>js/jquery/uploadify/cancel.png',
		//'folder'			: '/js/jquery/uploadify/uploads',
		'queueID'			: 'fileQueue',
		'auto'				: true,
		'buttonText'		: 'select files',
		'multi'				: true
	});
});
</script>

</html>