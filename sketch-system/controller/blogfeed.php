<?php
class BLOGFEED extends CONTROLLER{
  function BLOGFEED($page){
    parent::__construct("newsfeed");
    $r = getData("sketch_page,sketch_menu","*","page_type='blog' AND page_status='published'","order by page_date DESC",10);
    echo '<?xml version="1.0" ?>';
    ?>
    <rss version="2.0">
    <channel>
	<title><?php echo $_SERVER['HTTP_HOST'] ; ?> blog feed</title>
	<link><?php echo urlPath(); ?></link>
	<description>Latest blogs for <?php echo $_SERVER['HTTP_HOST'] ; ?></description>
	<language>en-nz</language>
	<copyright>Copyright <?php echo date("Y"); ?> <?php echo $_SERVER['HTTP_HOST'] ; ?></copyright>
    <?php
    while($r->advance()){
	$c  = unserialize($r->content);
	foreach ($c as $k => $v) {
	    $c[$k] = str_replace(";#;", '"', $v);
	}
	if($r->pageheading !=""){
	?>
	<item>
	<title><?php echo htmlentities(strip_tags(stripslashes(trim($r->page_heading))));?></title>
	<description><?php echo htmlentities(strip_tags(stripslashes(trim($c['edit']))));?></description>
	<link><?php echo urlPath($r->menu_guid); ?></link>
	<author><?php echo $r->updated_by; ?>@<?php echo $_SERVER['HTTP_HOST'] ; ?></author>
	<pubDate><?php list($y,$m,$d) = explode("-",$r->page_date); echo gmdate(DATE_RFC822,mktime(0,0,0,$m,$d,$y)); ?></pubDate>
	</item>
	<?php } ?>
    <?php } ?>
	</channel>
	</rss>
 <?php
  }
}