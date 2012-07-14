<?php if(isset($_REQUEST['m'])){
	plugin("lister");
	plugin("member");
	plugin("products");
	plugin("forum");?>
	<div id="p<?php echo sketch("page_id"); ?>" class="panel" title="<?php sketch("page_heading"); ?>"><?php
	    plugin("pageedit");
	?></div><?php 
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<title><?php echo strip_tags(sketch("page_title")); ?></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	<meta name="apple-touch-fullscreen" content="YES" />
	<?php plugin("seo"); ?>
	<?php getStylePath(); ?>
	<?php getScriptPath();?>
    </head>
    <body>
	<div class="toolbar">
	    <h1 id="pageTitle"></h1>
	    <a id="backButton" class="button" href="#"></a>
	    <a class="button" href="#searchForm">Search</a>
	</div>
	<?php echo plugin("menu"); ?>
    </body>
</html>