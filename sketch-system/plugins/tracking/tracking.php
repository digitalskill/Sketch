<?php
class TRACKING extends PLUGIN {
	function TRACKING($args) {
		$settings = array("location"=>"start","global"=>1,"php"=>1,"adminclass"=>"showReEdit:false showPreview:false showPublish:false","pluginsection"=>"Assets","menuName"=>"Tracking");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | class ]
		$settings['content'] = array("favicon"=>"","appleicon"=>'');
		$this->start($settings,$args);
	}
	function update($old,$new){ 		// [ REQUIRED ]
		return $new;
	}
	function doUpdate(){						// [ OVERRIDE ] 
		global 	$_POST,$_SESSION;
		
	}
	function display(){                             // [ REQUIRED ] 		// outputs to the page
		$r = getData("sketch_views","*","WHERE dateviewed='".date("Y-m-d")."' AND page_id=".intval(sketch("page_id")));
		if($r->rowCount() > 0){
			$r->advance();
			$views = intval($r->viewcount);
			$views++;
			$r = setData("sketch_views",array("viewcount"=>intval($views)),"WHERE view_id=".$r->view_id); 
		}else{
			$r = addData("sketch_views",array("dateviewed"=>date("Y-m-d"),"viewcount"=>intval(1),"page_id"=>intval(sketch("page_id")))); 
		}
	}
	function form(){ 			// [ REQUIRED ] 
		$r = getData("sketch_views,sketch_menu","menu_name, sum(viewcount) as viewcount, min(dateviewed) as dateviewed, max(dateviewed) as lastdateviewed","sketch_views.page_id=".sketch("page_id")." GROUP BY menu_name","dateviewed DESC");
		if($r->rowCount() > 0){
		?>
        
        <ul class="form accordian" style="float:left;width:90%">
        	<li>
            
            </li>
        	<?php while($r->advance()){?>
        		<li><strong><?php echo $r->menu_name; ?></strong> page has been viewed <?php echo intval($r->viewcount); ?> times between <?php echo $r->dateviewed; ?> - <?php echo $r->lastdateviewed; ?></li>
        	<?php } ?>
        </ul>
        <?php
		}else{
			echo "<p>No Pages have been tracked yet</p>";	
		}
	}
	function install( ) {
		$global_location             = ( @$this->settings[ 'global' ] == 1 ) ? @$this->settings[ 'location' ] : '';
		$this->settings[ 'content' ] = str_replace( '"', ";#;", @$this->settings[ 'content' ] );
		$values                      = array_merge( (array) @$this->settings[ 'content' ] );
		$SQL                         = "INSERT INTO " . $this->prefix . "plugin (php,activated,`name`,is_admin,global_location,menu_name,content,edit) " . "values(" . intval( @$this->settings[ 'php' ] ) . ",1" . "," . sketch( "db" )->quote( $this->settings[ 'name' ] ) . " " . "," . intval( @$this->settings[ 'admin' ] ) . "," . sketch( "db" )->quote( $global_location ) . " " . "," . sketch( "db" )->quote( $this->e( 'menuName', $this->settings[ 'name' ] ) ) . " " . "," . sketch( "db" )->quote( serialize( $values ) ) . " " . "," . sketch( "db" )->quote( serialize( $values ) ) . " " . ")";
		$r                           = ACTIVERECORD::keeprecord( $SQL );
		$SQL = "CREATE TABLE `" . $this->prefix ."sketch_views` (
				`view_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`dateviewed` date DEFAULT NULL,
				`viewcount` int(11) DEFAULT '0',
				`page_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`view_id`),
				KEY `viewcount_fk` (`page_id`),
				CONSTRAINT `viewcount_fk` FOREIGN KEY (`page_id`) REFERENCES `" . $this->prefix ."sketch_page` (`page_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$r  = ACTIVERECORD::keeprecord( $SQL );
	}
}