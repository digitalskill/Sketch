<?php 
	$type 	= sketch("page_type");
	$type 	= rtrim($type,"l");
	$type 	= $type=="article" ? "news" : $type;
	$parent = $type=="news" ? "article" : "blogl";
	$parent = $type=="product"? "productl" : $parent;
?>
<div class="<?php echo $args['page_style']; ?> <?php if ($current == $count) { ?>last<?php } ?>" id="pE<?php echo $r->panel_id; ?>">
    <?php if ($r->panel_image != ""){?>
	    <?php if (trim($r->panel_link) != "") { ?>
        	<a href="<?php echo $r->panel_link; ?>">
        <?php } ?>
        	<img src="<?php echo str_replace("index.php","",urlPath()). $r->panel_image; ?>" class="panel-image" alt="<?php echo strip_tags(trim($r->panel_heading)); ?>"/>
        <?php if (trim($r->panel_link) != "") { ?>
        </a>
        <?php } ?>
	<?php } ?>
		<?php if(trim($r->panel_heading)!=""){ ?><h3><?php echo $r->panel_heading; ?></h3><?php } ?>
        <?php 
			if(stripos($r->panel_content,"#twitter#")!==false){
            		plugin("twitter");
            }else{ 
			if(stripos($r->panel_content,"#popularproducts#")!==false){?>
            	<ul class="post-list">
            <?php
				$SQL = "SELECT sum(viewcount),
						".getSettings("prefix")."sketch_menu.menu_guid,
						".getSettings("prefix")."sketch_menu.sketch_menu_id,
						".getSettings("prefix")."sketch_menu.menu_under,
						".getSettings("prefix")."sketch_page.page_heading,
						".getSettings("prefix")."sketch_page.content,
						".getSettings("prefix")."sketch_page.page_date 
						FROM `".getSettings("prefix")."sketch_page`,`".getSettings("prefix")."sketch_menu`,`".getSettings("prefix")."sketch_views` 
						WHERE `".getSettings("prefix")."sketch_page`.`page_id`=`".getSettings("prefix")."sketch_menu`.`page_id` 
						AND page_type='product' AND page_status='published'
						AND ".getSettings("prefix")."sketch_views.page_id=".getSettings("prefix")."sketch_page.page_id 
						GROUP BY `".getSettings("prefix")."sketch_views`.`page_id`
						ORDER BY `".getSettings("prefix")."sketch_views`.`viewcount` DESC 
						LIMIT 4";
				$popPosts = ACTIVERECORD::keeprecord($SQL);
				while($popPosts->advance()){
					// GET Comment Count
					$c = contentToArray($popPosts->content);
					$comments = getData("sketch_menu,sketch_page","count(sketch_menu_id) as commentcount",
										"WHERE page_status='published' AND menu_under=".intval($popPosts->sketch_menu_id));
					$comments->advance();
					?>
            		<li><a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><img src="<?php echo $c['page_image']==""?  urlPath("sketch-images/art/blog-th1.jpg") :  urlPath($c['page_image']); ?>" alt=""></a>
            		<h4><a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><?php echo $popPosts->page_heading; ?></a></h4>
            		<span class="infor"><?php echo @date("d F, Y",strtotime($popPosts->page_date)); ?> | <a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><?php echo intval($comments->commentcount); ?> Comments</a></span></li>
            <?php } ?>
            	</ul>
         	<?php 
			}else{
			if(stripos($r->panel_content,"#popularposts#")!==false){?>
            	<ul class="post-list">
            <?php
				$SQL = "SELECT sum(viewcount),
						".getSettings("prefix")."sketch_menu.menu_guid,
						".getSettings("prefix")."sketch_menu.sketch_menu_id,
						".getSettings("prefix")."sketch_menu.menu_under,
						".getSettings("prefix")."sketch_page.page_heading,
						".getSettings("prefix")."sketch_page.content,
						".getSettings("prefix")."sketch_page.page_date 
						FROM `".getSettings("prefix")."sketch_page`,`".getSettings("prefix")."sketch_menu`,`".getSettings("prefix")."sketch_views` 
						WHERE `".getSettings("prefix")."sketch_page`.`page_id`=`".getSettings("prefix")."sketch_menu`.`page_id` 
						AND page_type=".sketch("db")->quote($type)." AND page_status='published'
						AND ".getSettings("prefix")."sketch_views.page_id=".getSettings("prefix")."sketch_page.page_id 
						GROUP BY `".getSettings("prefix")."sketch_views`.`page_id`
						ORDER BY `".getSettings("prefix")."sketch_views`.`viewcount` DESC 
						LIMIT 4";
				$popPosts = ACTIVERECORD::keeprecord($SQL);
				while($popPosts->advance()){
					// GET Comment Count
					$c = contentToArray($popPosts->content);
					$comments = getData("sketch_menu,sketch_page","count(sketch_menu_id) as commentcount",
										"WHERE page_status='published' AND menu_under=".intval($popPosts->sketch_menu_id));
					$comments->advance();
					?>
            		<li><a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><img src="<?php echo $c['page_image']==""?  urlPath("sketch-images/art/blog-th1.jpg") :  urlPath($c['page_image']); ?>" alt=""></a>
            		<h4><a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><?php echo $popPosts->page_heading; ?></a></h4>
            		<span class="infor"><?php echo @date("d F, Y",strtotime($popPosts->page_date)); ?> | <a href="<?php echo urlPath($popPosts->menu_guid); ?>" title=""><?php echo intval($comments->commentcount); ?> Comments</a></span></li>
            <?php } ?>
            	</ul>
         	<?php 
			}else{
				if(stripos($r->panel_content,"#tags#")!==false){ ?>
					<ul class="tags">
                    <?php
					$tagData = getData("sketch_page,sketch_menu,tag,sketch_views","*","page_type=".sketch("db")->quote($type),"GROUP BY tag_name ORDER BY viewcount DESC",10);
					
					$blogpage = getData("sketch_page,sketch_menu","menu_guid","page_type=".sketch("db")->quote($parent),1);
					$blogpage->advance();
					while($tagData->advance()){?>
                    	<li><a href="<?php echo urlPath($blogpage->menu_guid); ?>?tag=<?php echo urlencode($tagData->tag_name); ?>" title=""><?php echo $tagData->tag_name; ?></a></li>
                    <?php }
					?>
                    </ul>
				<?php	
				}else{
					if(stripos($r->panel_content,"#search#")!==false){
						?>
                        	<form id="searchform" class="required" action="<?php echo urlPath("search"); ?>" method="get"><label id="searchlbl" style="padding-top:5px;margin-left:3px">Type and hit enter</label> <input id="s" class="required label:'searchlbl'" type="text" name="s" /></form>
                        <?php
					}else{	
            			echo $r->panel_content;
					}
				}
			}
          } 
			}?>
</div>