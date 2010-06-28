<?='<?xml version="1.0" encoding="UTF-8"?>
'?>
<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/">
	
	<channel>
		<title><?=$page['title']?></title>
		<link><?=base_url()?>user/<?=$post[0]['author']['account']?>/</link>
		<description><?=strip_tags($post[0]['author']['description'])?></description>
		<language>ja</language>
		<copyright>Copyright <?=date('Y', strtotime($post[0]['date']['create']))?></copyright>
		<lastBuildDate><?=date("D, d M Y H:i:s +0900", strtotime($post[0]['date']['create']))?></lastBuildDate>
		<generator><?=base_url()?></generator>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		
		<?foreach($post as $k => $v){?><item>
			<title><?if (isset($title)) {?><![CDATA[<?=$v['title']?>]]><?} else {?><?=$v['author']['name']?>の発言<?if($v['ext']['app'] != ""){?> (from <?=$v['ext']['app']?>)<?}?>:<?}?></title>
			<description><![CDATA[
			<?=format_text($v['text'])?>
			<?if (isset($v['img']) && (int)$v['img'] > 0){?><p>img_id : <?=$v['img']?> : <img src="<?=img_url($v['img'], 300)?>" width="300" border="0" /></p><?}?>
			<?if (isset($v['related'])) {?><blockquote><h4>関連記事 - related posts</h4>
			<?foreach($v['related'] as $related){?>- <a href="<?=$related['url']?>" target="_blank"><?=$related['title']?></a><br /><?}?>
			</blockquote><?}?>
			]]></description>
			<link><?=$v['url']?></link>
			<guid><?=$v['url']?></guid>
			<dc:creator><?=$v['author']['name']?></dc:creator>
			<pubDate><?=date('D, d M Y H:i:s +0900', strtotime($v['date']['create']))?></pubDate>
		</item><?}?>
	</channel>
</rss>
