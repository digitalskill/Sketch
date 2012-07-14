<div id="portfolio-single">
      <div class="image"> 
	  <?php echo sketch("content")==""? '<img src="'.urlPath(sketch('page_image')).'" alt="'.sketch('menu_name').'" />' : str_replace(array("<br />","<br>","<p>","</p>"),"",sketch("content")); ?> </div>
      <div class="text" style="margin-top: 0px; " id="pinned">
        <h3><?php echo sketch("page_heading"); ?></h3>
       	<?php echo sketch("page_intro"); ?>
        <div class="divider3"></div>
        <?php 
			// 
			$r = getData("sketch_page,sketch_menu","menu_guid","page_status='published' AND page_type='gallery' order by menu_under, menu_order");
			$lastp = "";
			$nextp = "";
			$oldp = '';
			//echo $r->query;
			while($r->advance()){
				if($nextp==true){
					$nextp = $r->menu_guid;
				}
				if(sketch("menu_guid")==$r->menu_guid){
					$nextp = true;
					$lastp = $oldp;
				}
				$oldp = $r->menu_guid;
			}
		?>
        <a href="<?php echo urlPath($lastp); ?>" class="p-project" <?php if($lastp==""){?>style="visibility:hidden"<?php } ?>>« Previous Project</a> 
        <a href="<?php echo urlPath($nextp); ?>" class="n-project" <?php if($nextp=="" || $nextp===true){?>style="visibility:hidden"<?php } ?>>Next Project »</a> </div>
    </div>
    <script type="text/javascript">
		window.addEvent("domready",function(){
            var offset = $("pinned").getPosition();
            var topPadding = 15;
            window.addEvent("scroll",function() {
                if (window.getScroll().y > offset.y) {
                    $("pinned").morph({
                        'margin-top': window.getScroll().y - offset.y + topPadding
                    });
                } else {
                    $("pinned").morph({
                        'margin-top':0
                    });
                };
            });
        });
	</script>