<?php
class PAGES extends CONTROLLER{
	function pages(){
		$record = getData("sketch_menu","menu_name,menu_guid","WHERE menu_under <> 25 AND sketch_menu_id <> 25");
		$count = 0; ?>
		var tinyMCELinkList = new Array( 
		<?php while($record->advance()){ 
					$temp = $record->menu_name;
					if($temp != ""){
						$slash = (strpos($record->menu_guid,"http://")===false)? "/"   : ""  ;
						?>["<?php echo $record->menu_name; ?>","<?php echo $slash.$record->menu_guid; ?>"]<?php if($count < $record->rowCount()-1){ ?>,<?php }
					}
					$count++;
		} ?>);<?php        
	}	 
}