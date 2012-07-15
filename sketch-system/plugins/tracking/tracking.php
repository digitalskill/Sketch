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
		?>
        <div class="accord-body">
        	<div class="accord-container">
        	<iframe src="<?php echo urlPath("trackingchart"); ?>" frameborder="0" allowtransparency="1" height="600" width="100%" ></iframe>
		   </div>
		   </div>
		 <?php
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