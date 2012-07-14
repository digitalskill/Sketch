<?php if(!isset($_POST['getItem'])){  
			global $_SESSION;
			if(isset($_SESSION['last_clicked_id'])){
				unset($_SESSION['last_clicked_id']);
			}
			?>
            <div style="width:200px">
            <form method="post" class="required ajax:true output:'panels'" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="banner_add_and_order_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>">
	        <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	        <input type="hidden" name="plugin_id" value="<?php echo $this->settings['plugin_id']; ?>" />
	        <input type="hidden" name="preview" value="approve" />
            
            <ul class="form" id="pagepanels" style="float:left;width:96%;margin:0 0 10px 0; padding:0">
<?php 		$SQL = "SELECT ".getSettings("prefix")."panel.*
					FROM ".getSettings("prefix")."panel
					LEFT JOIN ".getSettings("prefix")."panel_to_page ON (".getSettings("prefix")."panel_to_page.panel_id=".getSettings("prefix")."panel.panel_id AND ".getSettings("prefix")."panel_to_page.page_id=".intval($this->page_id).")
					WHERE panel_type=0
					AND ".getSettings("prefix")."panel_to_page.page_id=".intval($this->page_id)."
					GROUP BY ".getSettings("prefix")."panel.panel_id
					ORDER BY panel_order";
			$r    = ACTIVERECORD::keeprecord($SQL);
			while($r->advance()){?>
					<li style="position:relative;float:left;width:100%">
                    	<input type="hidden" name="panel_id[]" value="<?php echo $r->panel_id; ?>"/>
						<a href="<?php echo urlPath("admin/ajax"); ?>_plugin_<?php echo $this->settings['name']; ?>?preview=&getItem=<?php echo $r->panel_id; ?>&page_id=<?php echo $this->page_id; ?>" rel="<?php echo $r->panel_id; ?>" class="button ajaxlink output:'loaderArea' overlay:false" style="width:97%"><span class="mover icons move"></span><span class="icons trash"></span><?php echo $r->panel_heading != "" ? $r->panel_heading : "Panel"; ?></a>
                    </li>
<?php 		}
?>			</ul>
			</form><?php
			$r->free(); 
			$SQL = "SELECT ".getSettings("prefix")."panel.*
					FROM ".getSettings("prefix")."panel
					LEFT JOIN ".getSettings("prefix")."panel_to_page ON (".getSettings("prefix")."panel_to_page.panel_id=".getSettings("prefix")."panel.panel_id AND ".getSettings("prefix")."panel_to_page.page_id <> ".intval($this->page_id).")
					WHERE panel_type=0
					AND  ".getSettings("prefix")."panel.panel_id NOT IN (SELECT panel_id FROM ".getSettings("prefix")."panel_to_page WHERE page_id=".intval($this->page_id).")
					GROUP BY ".getSettings("prefix")."panel.panel_id
					ORDER BY panel_order";
			$r    = ACTIVERECORD::keeprecord($SQL); ?>
            <ul class="form" id="globalpanels" style="float:left;width:96%;margin:5px 0 0 0; padding:0">
            <?php
			while($r->advance()){?>
					<li style="position:relative;float:left;width:100%">
                        <input type="hidden" name="panel_id[]" value="<?php echo $r->panel_id; ?>"/>
						<a href="<?php echo urlPath("admin/ajax"); ?>_plugin_<?php echo $this->settings['name']; ?>?preview=&getItem=<?php echo $r->panel_id; ?>&page_id=<?php echo $this->page_id; ?>" rel="<?php echo $r->panel_id; ?>" class="button ajaxlink output:'loaderArea' overlay:false" style="width:97%"><span class="mover icons move"></span><span class="icons plus"></span><?php echo $r->panel_heading != "" ? $r->panel_heading : "Panel"; ?></a>
                    </li>
<?php 		}
			$r->free(); ?>
             </ul>
            </div>
<script type="text/javascript">
	function setupPanelPageClicks(){
		$('pagepanels').getElements("a").each(function(item,index){
			$(item).removeEvents("mouseenter");
			if($("pE" + $(item).get("rel"))){
				$("pE" + $(item).get("rel")).addEvent("click",function(event){ try{$(item).fireEvent("click"); new Event(event).stop();}catch(e){}});
			}
			$(item).addEvent("mouseenter",function(){
					var me = "pE" + $(this).get("rel");
					if($(me)){
						var startS = $(me).getStyle("background-color");
						startS = startS != ""? startS : 'transparent';
						$(me).set("tween",{"duration":1000,onComplete:function(){
							if($(me)){
								$(me).setStyle("background-color",startS);	
							}
						}});
						$(me).highlight();
					}
				});
		});
	}
	function setUpClicks(){					  
		$('pagepanels').getElements('.trash').addEvent("mousedown",function(event){
			new Event(event).stop();
			$(this).removeClass("trash");
			$(this).addClass("plus");
			$(this).getParent("a").addClass("ajaxlink");
			var clickAdd = $(this).getParent("li").clone();
			$(clickAdd).inject($('globalpanels'),"top");
			new Ajaxlinks($(this).getParent("a"));
			$(this).getParent("li").destroy();
			$('banner_add_and_order_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>').fireEvent('submit');
			setUpClicks();
		});
		
		$('pagepanels').getElements('.trash').addEvent("click",function(event){
			new Event(event).stop();
		});
		$('pagepanels').getElements(".mover").addEvent("click",function(event){
			new Event(event).stop();
		});
		$('globalpanels').getElements('.plus').addEvent("mousedown",function(event){
			new Event(event).stop();
			$(this).removeClass("plus");
			$(this).addClass("trash");
			$(this).getParent("a").addClass("ajaxlink");
			var clickAdd = $(this).getParent("li").clone();
			$(clickAdd).inject($('pagepanels'),"top");
			$(this).getParent("li").destroy();
			$('banner_add_and_order_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>').fireEvent('submit');
			setUpClicks();	
		});
		$$('.ajaxlink').each(function(item,index){
			$(item).addEvent("click",function(){
				$$(".aspin").removeClass("aspin");
				$$(".aspin").unspin();
				$(this).spin();
				$(this).addClass("aspin");
			});
			new Ajaxlinks(item);
		});
		setupSortPanels();
		setupPanelPageClicks.delay(1000);
	}
	function setupSortPanels(){ 
		new Sortables('pagepanels', {revert:true, duration: 500, transition: 'elastic:out',constrain:true,handle:".mover",onSort:setupBannerSort});
	}
	function sorted(e,a){
		
	}
	function setupBannerSort(){
		$('banner_add_and_order_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>').fireEvent('submit');
		setUpClicks();
	}
	function resizeLoadBox(){
		$("load-box").setStyle("padding-bottom",70);
		$("bottom-box").addClass("no-shadow");
		$('load-box').morph({"width":200,"padding":"5px 12px 5px 28px"});
		$('adminMask').setStyles({"background-image":"none"});
		$('adminMask').morph({"width":248,"min-width":248});
		var loadItem = $('loaderArea').clone();
		$("loaderArea").destroy();
		$(loadItem).inject("adminMask","top");
		$(loadItem).addClass("shadow");
		$(loadItem).set("id","loaderArea");
		$(loadItem).setStyles({"height":$('adminMask').getSize().y - 100,"background-color":"#fff","position":"absolute","padding":"20px 0px 50px 20px","left":131,"bottom":0,"overflow":"auto","top":-5,"width":0});		
		setupSortPanels();
		setUpClicks();
		new Validate($('banner_add_and_order_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>'));
	}
	resizeLoadBox.delay(500);
</script>
        <?php }else{
			global $_SESSION;
			if($_SESSION['last_clicked_id'] != $_POST['getItem']){
				$_SESSION['last_clicked_id'] = $_POST['getItem'];
				$allImages = array();
				$alli = $sketch->getImages();
				foreach($alli as $key => $value){
					foreach($value as $k => $v){
						if(isset($v['details']['width'])){
							$allImages[$k] = $k;
						}
					}
				}
				$r = getData("panel","*","panel_id=".intval($_POST['getItem']));
				$r->advance();
			?>
            <div style="float:left; width:100%; clear:left;">
                <label class="pt10">Panel Heading</label>
      			<input type="text" name="panel_heading" value="<?php echo $r->panel_heading; ?>"/>
      			<input type="hidden" name="panel_id" value="<?php echo $r->panel_id; ?>" />
      <div class="bodys">
      <div style="float:left; width:100%;margin-bottom:5px;overflow:hidden;">
      <textarea name="panel_content" style="height:200px; width:100%" class="input doTiny:true tinySettings:1" id="bannertext"><?php echo $r->panel_content; ?></textarea>
      </div>
      <div class="" style="clear:both;float:right;width:45%;height:50px">
      	<label>&nbsp;</label>
        <img src="<?php echo str_replace("index.php","",urlPath()). $r->panel_image; ?>" id="bannerfeatureimg" height="50" alt="No panel image selected"/>
       </div>
       <div style="float:left; width:48%;overflow:hidden;">
       <label>Image</label>
        <select name="panel_image" class="bgClass:'select_bg'" onchange="$('bannerfeatureimg').set('src','<?php echo str_replace("index.php","",urlPath()); ?>'+this.value);">
          <option value="">None</option>
          <?php foreach($allImages as $key => $value){?>
          <option value="<?php echo $key; ?>" <?php if($key==$r->panel_image){?>selected="selected"<?php } ?>><?php echo end(explode("/",$key)); ?></option>
          <?php } ?>
        </select>
       </div>
       <div style="left:right;clear:both;width:48%;margin-right:5px;margin-bottom:5px;">
        <label class="pt10" style="clear:both;">URL Link (enter in full path, http://www...)</label>
    	<input type="text" name="panel_link" value="<?php echo $r->panel_link; ?>" />
      </div>
      <?php $r->free(); ?>
	    </div>
             <script type="text/javascript">
				function setuppanelForms(){
					$('adminMask').setStyles({"width":760});
					$('loaderArea').morph({"width":500,"left":231});
					$$(".aspin").unspin();
					if($("banner_form_<?php echo $this->settings['name'].$_POST['getItem']; ?>")){
						new Validate($("banner_form_<?php echo $this->settings['name'].$_POST['getItem']; ?>"));
					}
				}
				setuppanelForms.delay(500);
		   </script>
        <?php }else{
				unset($_SESSION['last_clicked_id']);
			?>
            	<script type="text/javascript">
					$("loaderArea").morph({"width":0,"left":131});
					$('adminMask').morph({"width":248});
					$$(".aspin").unspin();
					$$(".aspin").removeClass("aspin");
					$("loaderArea").empty();
				</script>
            <?php	
		}
		}