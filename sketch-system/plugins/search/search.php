<?php

class SEARCH extends PLUGIN {
    function SEARCH($args) {
		$settings = array(
			"location" => "search",
			"admin" => 0,
			"php" => 1,
			"menuName" => "Search",
			"pluginsection" => "assets",
			"adminclass" => "showReEdit:false showPreview:false showPublish:false updateForm:false"
		);
		$this->start($settings, $args);
    }
    function update($old, $new) {
		return $new;
    }
  
    function display() { 
		$dat = array();
		$dat['microtime'] = microtime(true);
	?>
		<form action="<?php echo sketch("menu_guid"); ?>" method="get" class="required">
                        	<ul class="forms">
                            	<li style="float:left;width:35%;position:relative;">
                                	<label id='slbl' style="padding:12px;margin-left:-25px">Search</label>
                                    <input type="text" name="s" class="required minValue:3 label:'slbl'" style="width:95%;padding:12px;"/>
                                    <button type="submit" style="margin:0px;position:absolute;right:5px;top:13px;left:auto;width:24px;"><span class="icons magnifier"></span></button>
                                </li>
                                
                            </ul>
                        </form>
                        <div style="clear:both">&nbsp;</div>
					<?php
						
						if(isset($_GET['s']) && strlen($_GET['s']) >= 3){
							$searchString = str_replace("'","",sketch("db")->quote(trim(strip_tags($_GET['s']))));	
							?><h1>Searching for: <?php echo addslashes($searchString); ?></h1><?php
							$dbSearch = sketch("db")->quote("%".$searchString."%");
							$SQL = "SELECT *
									FROM ".getSettings("prefix")."sketch_menu,".getSettings("prefix")."sketch_page
									WHERE ".getSettings("prefix")."sketch_menu.page_id=".getSettings("prefix")."sketch_page.page_id
									AND (content LIKE $dbSearch OR page_heading LIKE $dbSearch) 
									AND page_date <= now() 
									AND sketch_menu_id <> 25 AND menu_under <> 25
									GROUP BY ".getSettings("prefix")."sketch_page.page_id";
							$r = ACTIVERECORD::keeprecord($SQL);
							$tagCloud = array();
							while($r->advance()){
								$tc = explode(" ", strtolower(str_replace(array('&nbsp;', "&", "'", '"', ",", '.', ';', '_', '{', '}', '[', ']', '*', '(', ')', ':', '@', '!', '#', '$', '%', '^', '|'), '', strip_tags(trim(stripslashes($r->content." ".$r->page_heading." ".$r->tag_name))))));
								foreach ($tc as $key => $value) {
									if (strpos($value,$searchString)!==false) {
										if (!isset($tagCloud[$r->id])) {
											$tagCloud[$r->sketch_menu_id] = 0;
										}
										$tagCloud[$r->sketch_menu_id]++;
									}
								}	
							}
							$r->free();
							function cmp($a, $b) {
								return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
							}
							uasort($tagCloud, 'cmp');
							foreach($tagCloud as $key => $value){
								$r = getData("sketch_page,sketch_menu","*","WHERE sketch_menu_id=".intval($key));
								$r->advance();
								$c = contentToArray($r->content);
								$theedit = explode("</p>",$c['edit']);
								$final = "";
								foreach($theedit as $k => $v){
									if(strpos(strtolower($v),strtolower($searchString)) !== false){
										$final .= (($final != "")?  "</br >" : ""  ).strip_tags($v ."</p>");
									}
								}
								if($final==""){
									$final = strip_tags($c['edit']);	
								}
								$final = rtrim($final,"<br />");
								?>
                                <div class="searchblock">
                                	<h5><span style="font-size:12px;float:right; text-transform:none">(Found <?php echo $value; ?> times)</span><a href="http://<?php echo $_SERVER['HTTP_HOST']. $r->main_site_url.$r->menu_guid; ?>"><?php echo $r->menu_name; ?></a></h5>
                                	<p><?php echo str_replace($searchString,"<strong style='font-weight:bold;color:#e2e2e2;'>".$searchString."</strong>",$this->limit_words($final,30)); ?></p>
                                </div>
								<?php	
							}
						}
					?>
                    <div class="searchblock">
                    <h5 style="color:#e2e2e2;">Search took: <?php echo number_format((microtime(true)-$dat['microtime']),3); ?> seconds</h5>
                    </div><?php
    }

    function form() {
		
    }
	
	function limit_words($string, $word_limit){
		$words = explode(" ",$string);
		return implode(" ",array_splice($words,0,$word_limit))."...";
	}

}