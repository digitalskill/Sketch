<?php
if(!isset($_REQUEST['replace'])){?>
<ul id="gallery" title="<?php echo $this->e("heading".$i); ?>">
<?php } ?>
    <?php
    while($panel_q->advance()){
	$c = unserialize($panel_q->result['content']);
	foreach ($c as $k => $v) {
	    $c[$k] = str_replace(array("_##-",";#;"),array("?",'"'), $v);
	}
	?><li style="font-size:small">
	    <a href="<?php echo urlPath($panel_q->menu_guid); ?>?m">
		<span style="float:left;width:80px;height:25px;margin-top:-3px;overflow:hidden;display:block">
		    <img src="<?php echo urlPath($c['page_image']); ?>" height="25" alt="" />
		</span>
		<?php echo $panel_q->page_heading; ?>
	    </a></li><?php
    }
    if($totalRecords > ($startfrom + $pagelimit)){
	if(!isset($_REQUEST['pl'.$i])){
	    $_REQUEST['pl'.$i]=1;
	}
    ?><li><a target="_replace" href="<?php echo urlPath(sketch("menu_guid")."?replace&amp;pl".$i."=".(intval(@$_REQUEST['pl'.$i])+1));?>&amp;m">Get Next <?php echo $pagelimit; ?> </a></li>
<?php }
if(!isset($_REQUEST['replace'])){?>
</ul>
<?php } 