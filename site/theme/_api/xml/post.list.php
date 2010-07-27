<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?='<?xml version="1.0" encoding="UTF-8"?>'?>
<resultSet>
	<resultStatus>
	
	</resultStatus>
	<items>
		<post>
			<?foreach($post as $k => $v){?><item>
				<title><?=$v['title']?></title>
				<text><?=$v['text']?></text>
			</item><?}?>
		</post>
	</items>
</resultSet>