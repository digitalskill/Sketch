<?php
helper("member");
helper("shoppingcart");?>
<div id="news">
<div id="newslist">
<div class="scroller backid:'newslist-prev' nextid:'newslist-next'" id="scrollzone" style="overflow: hidden; position: relative; height:525px">
        <ul>
<?php
while($panel_q->advance()){
    $c = contentToArray($panel_q->result['content']);
?>
  <li> 
  	<div class="one-fourth">
  	<a href="<?php echo urlPath($panel_q->menu_guid); ?>"><?php if(isset($c['page_image']) && trim($c['page_image'])!=""){?><img src="<?php echo urlPath($c["page_image"]); ?>" alt="" style="width:100%" class="left" /><?php } ?></a>
    </div>
    <div class="one-half">
    <h4 class="title"><a href="<?php echo urlPath($panel_q->menu_guid); ?>"><?php echo $panel_q->page_heading; ?></a></h4>
    <p><?php echo strip_tags(stripslashes(trim($c['page_intro']))); ?></p>
    </div>
    <div class="one-fourth last">
    	<?php
			// Get all ratings
			$r = getData("sketch_page,sketch_menu","avg(menu_class) as rating","WHERE menu_under=".intval($panel_q->sketch_menu_id));
			$r->advance();
			if(intval($r->rating) > 0){ ?>
    		 <div class="infor"><span class="date">Average rating: <?php echo number_format($r->rating,0); ?></span></div>
        <?php } ?>
    	<a href="<?php echo urlPath($panel_q->menu_guid); ?>" class="button" style="display:block;width:80%"><span class="icons magnifier"></span>View Product</a>
        <a class="button" style="display:block;width:80%" href="<?php echo urlPath($panel_q->menu_guid); ?>?product=<?php echo $panel_q->page_id; ?>&quantity=<?php echo getItemAmount($panel_q->page_id) > 0? getItemAmount($panel_q->page_id) : 1; ?>"><span class="icons check"></span>Buy Now</a>
    </div>
  </li>
<?php
	$count++;
}
?>
</ul>
</div>
</div>
</div>