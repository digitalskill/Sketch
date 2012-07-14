<?php
class LISTER extends PLUGIN {
	function LISTER($args) {
		$settings = array("location"=>"center","php"=>1,"menuName"=>"Lister","global"=>1,"pluginsection"=>"Assets","adminclass"=>"updateForm:false");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
		$settings['content'] = array("heading1"=>"List Heading","limitto1"=>"","getfrom1"=>array(),"sortby1"=>"page_date DESC","onPages1"=>array(),"listtype"=>"news","listamounts"=>3);
		$this->start($settings,$args);
	}
	function update($old,$new){				// [ REQUIRED ]
		return array_merge($old,$new);
	}
	function display($args=''){				// [ REQUIRED ]
            global $sketch,$_GET,$_POST,$_SERVER;
	    if(sketch("mobile")){
		@include(loadView("mobilelister",false,true));
	    }else{
		@include(loadView("lister",false,true));
	    }
	}
	function preview(){					// [ REQUIRED ]
		$this->display();
	}
	function filter($args=""){
	    global $sketch;
	    for($i=1;$i<(intval($this->e("listamounts"))+1);$i++){
            if($args['heading']==$this->e("heading".$i)){	// START IF PANEL TYPE
				$limit = "";
                $pagelimit = intval($this->e('limitto'.$i));
				if($pagelimit > 0){
					$limit = "LIMIT	0,".$pagelimit;
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
				
				$type = "AND page_type=".sketch("db")->quote($this->e("listtype".$i));
				if($this->e("listtype".$i)=="any"){
					$type = "";
				}
				
                $SQL = "SELECT * FROM ".$sketch->settings['prefix']."sketch_page, ".$sketch->settings['prefix']."sketch_menu WHERE ".
                        $sketch->settings['prefix']."sketch_page.page_id= ".$sketch->settings['prefix']."sketch_menu.page_id ".
						$type .
                        " AND page_status='published' ".
                        $where;
				$rowC = "SELECT count(".$sketch->settings['prefix']."sketch_menu.page_id) as recordAmount FROM " .end(explode("FROM",$SQL));
				$max_rows_q = ACTIVERECORD::keeprecord($rowC);
				$max_rows_q->advance();
				$totalRecords = $max_rows_q->recordAmount;
                $SQL .=" ORDER BY ".$this->e('sortby'.$i)." ".$limit;
                $panel_q = ACTIVERECORD::keeprecord($SQL);
				$count = 0;
				if($this->e("getview".$i) != "" && is_file(sketch("abspath").sketch("user_theme_path")."views".sketch("slash").$this->e("getview".$i))){
					include(sketch("abspath").sketch("user_theme_path")."views".sketch("slash").$this->e("getview".$i));
				}else{
					if($this->e("listtype".$i) == "product"){
						@include(loadView("listerproducts",false,true));
					}else{
						@include(loadView("listernews",false,true));
					}
				$panel_q->free();
				$nextpURL = urlPath(sketch("menu_guid"));
				$nextpURL .= "?";
				foreach($_GET as $k => $val){
					if($k!="pl".$i){
						$nextpURL .= $k."=".$val ."&";
					} // END IF
				} // END FOR EACH
				if($totalRecords > $pagelimit && $pagelimit != 0){ ?>
					<div class="lister-pages">
					<?php if($startfrom > 1){?>
						<a class="lister-link lister-back" href="http://<?php echo $nextpURL."pl".$i."=".($startfrom-1);?>"><span>Back</span></a>
					<?php } ?>
					<div class="lister-mid"><?php 
					for($j=0;$j<($totalRecords/$pagelimit);$j++){ ?>
						<a class="lister-link <?php if($j==intval(@$_REQUEST['pl'.$i])){?>current<?php } ?>" href="http://<?php echo $nextpURL."pl".$i."=".($j+1);?>"><span><?php echo ($j+1);?></span></a>
					<?php }?>
					</div>
				   <?php 
				   if($totalRecords > ($startfrom + $pagelimit)){
						if(!isset($_REQUEST['pl'.$i])){
							$_REQUEST['pl'.$i]=1;
						}	
						?><a class="lister-link lister-back" href="http://<?php echo $nextpURL."pl".$i."=".(intval(@$_REQUEST['pl'.$i])+1);?>"><span>Next</span></a>
			 <?php } ?>
				   </div>
		<?php 		}	// END Total Record if
				}
			}		// END IF PANEL TYPE
		} 			// END FOR EACH
	}
	function form(){ 						// [ REQUIRED ] 
		global $sketch;
		@include(loadForm("listerform",false));
	}
}