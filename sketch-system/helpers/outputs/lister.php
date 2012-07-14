<?php
for($i=1;$i<(intval($this->e("listamounts"))+1);$i++){
	 $ia = $i."-".sketch("siteid");
    if($this->e("listtype".$ia)!="" && in_array($sketch->sketch_menu_id,(array)$this->e("onPages".$ia))){	// START IF PANEL TYPE ?>
	    <div class="lister-<?php echo $this->e("listtype".$ia); ?>">
	    <?php
			$limit = "";
			$pagelimit = intval($this->e('limitto'.$ia));
			if($pagelimit > 0){
				$limit = "LIMIT 0,".$pagelimit;
			}
			$startfrom = (intval(@$_REQUEST['pl'.$ia]) - 1) * $pagelimit;
			$startfrom = ($startfrom < 0)? 0 : $startfrom;
			if($startfrom){
				$limit = "LIMIT ".$startfrom.",".$pagelimit;
			}
	$where = "";
	if($this->e("getfrom".$ia)!='' && !in_array("all",(array)$this->e("getfrom".$ia))){
	    $where = " AND (";
	    foreach((array)$this->e("getfrom".$ia) as $key => $value){
		$where .= (($where==" AND (")? "" : " OR "). "menu_under='".intval($value)."' ";
	    }
	    $where .=") ";
	}
	
	// Select news Articles By Year
	if(defined(YEARS) && YEARS){
		$where = " AND page_date BETWEEN '".intval(YEAR) ."-1-1' AND '".(intval(YEAR)+1) ."-1-1' ";
	}
	$SQL = "SELECT * FROM ".getSettings('prefix')."sketch_page, ".getSettings('prefix')."sketch_menu WHERE ".
			getSettings('prefix')."sketch_page.page_id= ".getSettings('prefix')."sketch_menu.page_id AND ".
			"page_type=".sketch("db")->quote($this->e("listtype".$ia))." AND page_status='published' ".
			$where;
	$rowC = "SELECT count(".$sketch->settings['prefix']."sketch_menu.page_id) as recordAmount FROM " .end(explode("FROM",$SQL));
			$max_rows_q = ACTIVERECORD::keeprecord($rowC);
			$max_rows_q->advance();
			$totalRecords = $max_rows_q->recordAmount;
			$max_rows_q->free();
	$SQL .=" ORDER BY ".$this->e('sortby'.$ia)." ".$limit;
	$panel_q = ACTIVERECORD::keeprecord($SQL);
	if($panel_q->rowCount() > 0){
		?><h2><?php echo $this->e("heading".$ia); ?></h2><?php
	}
			$count = 0;
			if($this->e("getview".$ia) != "" && is_file(sketch("abspath").sketch("user_theme_path")."views".sketch("slash").$this->e("getview".$ia))){
				include(sketch("abspath").sketch("user_theme_path")."views".sketch("slash").$this->e("getview".$ia));
			}else{
			    if($this->e("listtype".$ia) == "gallery"){
				@include(loadView("listergallery",false,true));
			    }else{
				if($this->e("listtype".$ia) == "product"){
				    @include(loadView("listerproducts",false,true));
				}else{
				    @include(loadView("listernews",false,true));
				}
			    }
			$panel_q->free();
			$nextpURL = urlPath(sketch("menu_guid"));
			$nextpURL .= "?";
			foreach($_GET as $k => $val){
				if($k!="pl".$ia && $k != "approve" && $k != "preview"){
					$nextpURL .= $k."=".$val ."&";
				} // END IF
			} // END FOR EACH
			if($totalRecords > $pagelimit && $pagelimit != 0){ ?>
				<div class="lister-pages clear">
				<?php if((intval(@$_REQUEST['pl'.$ia])) > 1){?>
					<a class="lister-link lister-back button" href="<?php echo $nextpURL."pl".$ia."=".(intval(@$_REQUEST['pl'.$ia])-1);?>"><span class="icons leftarrow"></span>Back</a>
				<?php } 
				for($j=0;$j<($totalRecords/$pagelimit);$j++){ ?>
					<a class="lister-link button <?php if($j+1==intval(@$_REQUEST['pl'.$ia])){?>current<?php } ?>" href="<?php echo $nextpURL."pl".$ia."=".($j+1);?>"><span><?php echo ($j+1);?></span></a>
				<?php }
			   if($totalRecords > ($startfrom + $pagelimit)){
					if(!isset($_REQUEST['pl'.$ia])){
						$_REQUEST['pl'.$ia]=1;
					}
					?><a class="lister-link lister-back button" href="<?php echo $nextpURL."pl".$ia."=".(intval(@$_REQUEST['pl'.$ia])+1);?>"><span class="icons rightarrow"></span>Next</a>
		 <?php } ?>
			   </div>
	<?php 		}	// END Total Record if
		}
	?></div><?php
	}		// END IF PANEL TYPE
} 			// END FOR EACH