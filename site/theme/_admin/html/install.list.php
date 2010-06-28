<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?$this->load->view('_inc/html.header.php')?>
<body>
	<?$this->load->view('_inc/head.php')?>
	<div id="body">
		<div class="container">
			<div class="list">
				<div class="head round">
					<h2><a href="<?=base_url()?>admin/install/">INSTALL</a></h2>
					<div class="add"></div>
					<div class="tool"></div>
				</div>
				<div class="block">
					<h3>OAuth</h3>
					<div class="box">
						<h4><a href="<?=base_url()?>admin/install/twitter/">Twitter</a></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?$this->load->view('_inc/foot.php')?>
</body>
</html>