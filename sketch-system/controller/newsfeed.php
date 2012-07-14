<?php
class NEWSFEED extends CONTROLLER{
  function NEWSFEED($page){
    parent::__construct("newsfeed");
    $r = getData("sketch_page,sketch_menu","*","page_type='news' AND page_status='published'","order by page_date DESC",10);
    echo '<?xml version="1.0" ?>';
    ?>
    <rss version="2.0">
    <channel>
	<title><?php echo $_SERVER['HTTP_HOST'] ; ?> news feed</title>
	<link><?php echo urlPath(); ?></link>
	<description>Latest news for <?php echo $_SERVER['HTTP_HOST'] ; ?></description>
	<language>en-nz</language>
	<copyright>Copyright 2011 <?php echo $_SERVER['HTTP_HOST'] ; ?></copyright>
	<image>
	    <title><?php echo $_SERVER['HTTP_HOST'] ; ?> NEWS FEED</title>
	    <url><?php echo str_replace("index.php","",urlPath("sketch-images/logo.gif")); ?></url>
	    <link><?php echo urlPath("newsfeed"); ?></link>
	</image>
    <?php
    while($r->advance()){
	$c  = unserialize($r->content);
	?>
	<item>
	<title><?php echo htmlentities(strip_tags($r->pageheading));?></title>
	<description><?php echo htmlentities(strip_tags($r->pageheading));?></description>
	<link><?php echo urlPath($r->menu_guid); ?></link>
	<author><?php echo $r->updated_by; ?>@<?php echo $_SERVER['HTTP_HOST'] ; ?></author>
	<pubDate><?php list($y,$m,$d) = explode("-",$r->page_date); echo gmdate(DATE_RFC822,mktime(0,0,0,$m,$d,$y)); ?></pubDate>
	</item>
    <?php } ?>
	</channel>
	</rss>
 <?php
  }
}