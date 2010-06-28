<?='<?xml version="1.0" encoding="UTF-8"?>
'?>
<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/">
	
	<channel>
		<title><?=$setting['title']?></title>
		<link><?=base_url()?></link>
		<description><?=$setting['description']?></description>
		<language>ja</language>
		<copyright>Copyright <?=date('Y', strtotime($post[0]['createdate']))?></copyright>
		<lastBuildDate><?=date("D, d M Y H:i:s +0900", strtotime($post[0]['createdate']))?></lastBuildDate>
		<generator><?=base_url()?></generator>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		
		<?foreach($post as $k => $v){?><item>
			<title><![CDATA[<?if (isset($v['title'])) {?><?=$v['title']?><?} else {?><?=$v['author'][0]['name']?><?}?>]]></title>
			<description><![CDATA[
			<?=format_text($v['text'])?>
			<?if (isset($v['file_main']) && $v['file_main'][0]['type'] == 'image'){?><p><img src="<?=img_url($v['file_main'][0]['id'], $this->setting->get('img_size_mid'))?>" width="<?=$this->setting->get('img_size_mid')?>" border="0" /></p><?}?>
			<?/*if (isset($v['related'])) {?><blockquote><h4>関連記事 - related posts</h4>
			<?foreach($v['related'] as $related){?>- <a href="<?=$related['url']?>" target="_blank"><?=$related['title']?></a><br /><?}?>
			</blockquote><?}*/?>
			]]></description>
			<link><?=$v['url']?></link>
			<guid><?=$v['url']?></guid>
			<?if(isset($v['author'])){?><dc:creator><?=$v['author'][0]['name']?></dc:creator><?}?>
			<pubDate><?=date('D, d M Y H:i:s +0900', strtotime($v['createdate']))?></pubDate>
		</item><?}?>
	</channel>
</rss>