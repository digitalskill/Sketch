<div id="news">
<div id="newslist">
<div class="scroller backid:'newslist-prev' nextid:'newslist-next'" id="scrollzone" style="overflow: hidden; position: relative;">
        <ul>
<?php
while($panel_q->advance()){
    $c = contentToArray($panel_q->result['content']);
?>
  <li> <a href="<?php echo urlPath($panel_q->menu_guid); ?>"><?php if(isset($c['page_image']) && trim($c['page_image'])!=""){?><img src="<?php echo urlPath($c["page_image"]); ?>" alt="" style="width:60px" class="left" /><?php } ?></a>
    <h4 class="title"><a href="<?php echo urlPath($panel_q->menu_guid); ?>"><?php echo $panel_q->page_heading; ?></a><span>
    <?php
	    list($y,$m,$d) = explode("-",$panel_q->page_date);
	    echo @date("d F, Y",mktime(0,0,0,$m,$d,$y)); ?>
    </span></h4>
    <p><?php echo strip_tags(stripslashes(trim($c['page_intro']))); ?> <a href="<?php echo urlPath($panel_q->menu_guid); ?>" class="more">Continue Reading &raquo;</a> </p>
  </li>
<?php
	$count++;
}
?>
</ul>
</div>
</div>
</div>