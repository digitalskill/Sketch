<?php
while($panel_q->advance()){
    $c = contentToArray($panel_q->result['content']);
?>
	<div class="post">
     <h2 class="title"><a href="<?php echo urlPath($panel_q->menu_guid); ?>" title=""><?php echo $panel_q->page_heading; ?></a></h2>
     <div class="meta">
          <div class="top-border"></div>
          Posted on
          <div class="date">
<?php
	    	list($y,$m,$d) = explode("-",$panel_q->page_date);
	    	echo @date("d F, Y",mktime(0,0,0,$m,$d,$y)); 
			// Get posting subject
			$r = getData("sketch_page,sketch_menu","*","sketch_menu_id=".$panel_q->menu_under);
			$r->advance();
			// GET Comment Count
			$comments = getData("sketch_menu,sketch_page","count(sketch_menu_id) as commentcount","page_status='published' AND menu_under=".intval($panel_q->sketch_menu_id));
			$comments->advance();	
?>		  </div>
   			by <a href="#" title=""><?php echo $panel_q->updated_by; ?></a> under 
            	<a href="<?php echo urlPath($r->menu_guid); ?>" title=""><?php echo $r->page_title; ?></a> | 
            	<a href="<?php echo urlPath($r->menu_guid); ?>" title=""><?php echo intval($comments->commentcount); ?> Comments</a> </div>
         		<a href="<?php echo urlPath($panel_q->menu_guid); ?>"><?php if(isset($c['page_image']) && trim($c['page_image'])!=""){?><img src="<?php echo str_replace("index.php","",urlPath($c["page_image"])); ?>" alt=""/><?php } ?></a>
   			<?php echo stripslashes(trim($c['page_intro'])); ?>
    		<div class="tags">
          	<div class="top-border"></div>
          	<?php
            // Get posting Tags
			$r->free();
			$r = getData("tag","*","page_id=".$panel_q->page_id,"");
			if($r->rowCount() > 0){?>Tags:
            <?php
			$counter = 0;
			while($r->advance()){
          		echo $counter > 0? ", " : ""; ?><a href="<?php echo urlPath(sketch("menu_guid"));?>?tag=<?php echo urlencode($r->tag_name); ?>" title=""><?php echo $r->tag_name; ?></a><?php 
				$counter++;
				}
			}
			$r->free();?>
            </div>
      </div>
<?php
	$count++;
} // End WHILE