<?php
class AUTOLIST extends PLUGIN {
    function AUTOLIST($args) {
		$settings = array("location" => "center","noform" => 0, "php" => 1, "menuName" => "Auto Listing", "adminclass" => "updateForm:false showReEdit:false showPreview:false showPublish:false", "pluginsection" => "sitesettings"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | menuName ]
		$settings['content'] = array();
		$this->start($settings, $args);
    }

    function update($old, $new) {	     // [ REQUIRED ]
		return $new;
    }

    function display() {		    // [ REQUIRED ]
		$ptag = " ORDER BY page_date DESC";
		$tables = "sketch_menu,sketch_page";
		
		if(sketch("page_type")=="gallery" || sketch("page_type")=="galleryl"){
			$ptag = " ORDER BY menu_under,menu_order";	
		}
		
		$limit = "";
		$pagelimit = 5;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " LIMIT 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['n']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " LIMIT ".$startfrom.",".$pagelimit;	
		}
		
		if(isset($_GET['tag'])){
			$tables = "sketch_menu,sketch_page,tag";
			$ptag = " AND tag_name=".sketch('db')->quote($_GET['tag'])." ORDER BY page_date DESC ";
		}
	
		if(sketch("page_type")=="listing" && isset($_GET['s'])){
			$tables = "sketch_menu,sketch_page";
			$s 		= trim(sketch("db")->quote(strip_tags(htmlentities($_GET['s']))),"'");
			$ptag 	= "(content LIKE '%".$s."%' OR page_heading LIKE '%".$s."%') AND page_status='published' AND menu_guid NOT LIKE '%search' AND page_type <> 'member' AND page_title <> '' GROUP BY ".getSettings("prefix")."sketch_page.page_id ". $ptag . $limit;	
			$panel_q = getData($tables,"*",$ptag);
			@include(loadView("listernews",false,true));
			
			
			$SQL = end(explode("FROM",$panel_q->query));
				list($SQL,) = explode("limit",strtolower($SQL));
				$rowC = ACTIVERECORD::keeprecord("SELECT count(sketch_menu_id) as recordAmount FROM " .$SQL);
				$rowC->advance();
				?>
                <ul class="page-navi">
				<?php
				$curr = intval(@$_GET['n']) > 1 ? intval($_GET['n']): 1; 
				for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
					 <li><a href="<?php echo urlPath(sketch("menu_guid")); ?>?n=<?php echo $j+1; ?>&amp;s=<?php echo $s; ?>" <?php if($j+1==$curr){?>class="current"<?php } ?>><?php echo $j+1; ?></a></li><?php
      			}
				if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
                	<li><a href="<?php echo urlPath(sketch("menu_guid")); ?>?n=<?php echo $curr+1; ?>&amp;s=<?php echo $s; ?>">&raquo;</a></li>
                <?php } 
				?></ul><?php
			
		}
		
		if(isset($_GET['s'])){
			$tables = "sketch_menu,sketch_page,tag";
			$s 		= trim(sketch("db")->quote(strip_tags(htmlentities($_GET['s']))),"'");
			$ptag = " AND (tag_name LIKE '%".$s."%' OR content LIKE '%".$s."%' OR page_heading LIKE '%".$s."%') GROUP BY ".getSettings("prefix")."sketch_page.page_id ". $ptag;	
		}
		
		$ptag .= $limit;
		
		if(sketch("page_type")=="article" || sketch("page_type")=="newsl"){
			$panel_q = getData($tables,"*","menu_under = ".intval(sketch("sketch_menu_id")).$ptag);
			@include(loadView("listernews",false,true));
			?>
            <div class="clearfix"></div>
			<div id="scroll"> 
            	<a href="#" id="newslist-prev" class="jbutton" disabled="true"></a> 
                <a href="#" id="newslist-next" class="jbutton"></a>
            </div>
            <div class="clearfix"></div>
			<?php
			
		}
		
		if(sketch("page_type")=="productl"){
			$panel_q = getData($tables,"*","page_type='product' AND menu_under = ".intval(sketch("sketch_menu_id")).$ptag);
			@include(loadView("listerproducts",false,true));
			?>
            <div class="clearfix"></div>
			<div id="scroll"> 
            	<a href="#" id="newslist-prev" class="jbutton" disabled="true"></a> 
                <a href="#" id="newslist-next" class="jbutton"></a>
            </div>
            <div class="clearfix"></div>
			<?php
		}
		
		if(sketch("page_type")=="blogl"){
			$panel_q = getData($tables,"*","menu_under = ".intval(sketch("sketch_menu_id"))." AND page_type='blog' ".$ptag,"");
				@include(loadView("listerblog",false,true));
				$SQL = end(explode("FROM",$panel_q->query));
				list($SQL,) = explode("limit",strtolower($SQL));
				$rowC = ACTIVERECORD::keeprecord("SELECT count(sketch_menu_id) as recordAmount FROM " .$SQL);
				$rowC->advance();
				?>
                <ul class="page-navi">
				<?php
				$curr = intval(@$_GET['n']) > 1 ? intval($_GET['n']): 1; 
				for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
					 <li><a href="<?php echo urlPath(sketch("menu_guid")); ?>?n=<?php echo $j+1; ?>" <?php if($j+1==$curr){?>class="current"<?php } ?>><?php echo $j+1; ?></a></li><?php
      			}
				if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
                	<li><a href="<?php echo urlPath(sketch("menu_guid")); ?>?n=<?php echo $curr+1; ?>">&raquo;</a></li>
                <?php } 
				?></ul><?php
		}
		
		if(sketch("page_type")=="galleryl"){
			$master = getData($tables,"*","menu_under = ".intval(sketch("sketch_menu_id"))." AND page_type='galleryl' ".$ptag,"");
			$mcount = 1;
			if($master->rowCount() > 0){
				?> 
				<ul id="gallery" class="grid gallery"><?php
				while($master->advance()){
					$panel_q = getData($tables,"*","menu_under = ".intval($master->sketch_menu_id)." AND page_type='gallery' ".$ptag,"");
					@include(loadView("listergallery",false,true));
				}
				?>
                </ul><?php
			}else{
				$panel_q = getData($tables,"*","menu_under = ".intval(sketch("sketch_menu_id"))." AND page_type='gallery' ".$ptag,"");
				?> 
				<ul id="gallery" class="grid"><?php
					@include(loadView("listergallery",false,true));
				?>
				</ul>
				<?php
			}
		}
    }
    function preview() {
		$this->display();
    }

    function form() {			    // [ REQUIRED ]
	
    }
}