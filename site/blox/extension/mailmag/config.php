<?

$config = array(
	'flg_use_db'	=> false,
	'path_csv'		=> EX_FOLDER.'/upload/mailmag/maillist.csv',
	'master_email'	=> 'fineartplace@artfolio.co.jp',
	'master_name'	=> 'Fine Art PLACE',
	'charset'		=> 'iso-2022-jp',
	'maillist_test'	=> 'sahamada@sbigroup.co.jp,pelepop@gmail.com,tkcs@pelepop.com,kiwata@sbigroup.co.jp,tmatsuok@sbigroup.co.jp,youksuzu@sbigroup.co.jp,yujogata@sbigroup.co.jp'
);

$admin_menu = array(
	'top'	=> array(
		'alias'	=> 'ex/mailmag'
	),
	'add'	=> array(
		'alias'	=> 'ex/mailmag/add'
	),
	'user'	=> array(
		'alias'	=> 'ex/mailmag/user',
		'sub'	=> array(
			'user_pc'	=> array(
				'alias'	=> 'ex/mailmag/user/pc'
			),
			'user_mobile'	=> array(
				'alias'	=> 'ex/mailmag/user/mobile'
			)
		)
	)
);

?>