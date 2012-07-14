<?php
for($i=1;$i<(intval($this->e("listamounts"))+1);$i++){
    if($this->e("listtype".$i)!="" && in_array($sketch->page_id,(array)$this->e("onPages".$i))){	// START IF PANEL TYPE 
	$limit = "";
	$pagelimit = intval($this->e('limitto'.$i));
	if($pagelimit > 0){
	    $limit = "LIMIT 0,".$pagelimit;
	}
	$startfrom = (intval(@$_REQUEST['pl'.$i]) - 1) * $pagelimit;
	$startfrom = ($startfrom < 0)? 0 : $startfrom;
	if($startfrom){
	    $limit = "LIMIT ".$startfrom.",".$pagelimit;
	}
	$where = "";
	if($this->e("getfrom".$i)!='' && !in_array("all",(array)$this->e("getfrom".$i))){
	    $where = " AND (";
	    foreach((array)$this->e("getfrom".$i) as $key => $value){
		$where .= (($where==" AND (")? "" : " OR "). "menu_under='".intval($value)."' ";
	    }
	    $where .=") ";
	}
	$SQL = "SELECT * FROM ".getSettings('prefix')."sketch_page, ".getSettings('prefix')."sketch_menu WHERE ".
		getSettings('prefix')."sketch_page.page_id= ".getSettings('prefix')."sketch_menu.page_id AND ".
		"page_type=".sketch("db")->quote($this->e("listtype".$i))." AND page_status='published' ".
		$where;

	$rowC = "SELECT count(".$sketch->settings['prefix']."sketch_menu.page_id) as recordAmount FROM " .end(explode("FROM",$SQL));
			$max_rows_q = ACTIVERECORD::keeprecord($rowC);
			$max_rows_q->advance();
			$totalRecords = $max_rows_q->recordAmount;
			$max_rows_q->free();
	$SQL .=" ORDER BY ".$this->e('sortby'.$i)." ".$limit;
	$panel_q = ACTIVERECORD::keeprecord($SQL);
	$count = 0;
	if($this->e("getview".$i) != "" && is_file(sketch("abspath").sketch("user_theme_path")."views".sketch("slash").$this->e("getview".$i))){
	    include(sketch("abspath").sketch("user_theme_path")."mobile".sketch("slash")."views".sketch("slash").$this->e("getview".$i));
	}else{
	    if($this->e("listtype".$i) == "gallery"){
		@include(loadView("mobilelistergallery",false,true));
	    }else{
		if($this->e("listtype".$i) == "product"){
		    @include(loadView("mobilelisterproducts",false,true));
		}else{
		    @include(loadView("mobilelisternews",false,true));
		}
	    }
	}
    }		    // END IF PANEL TYPE
}		    // END FOR EACH