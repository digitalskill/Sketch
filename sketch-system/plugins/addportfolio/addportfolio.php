<?php
class ADDPORTFOLIO extends PLUGIN {
    function ADDPORTFOLIO($args) {
		$settings = array(
			"location" => "",
			"admin" => 1,
			"php" => 1,
			"menuName" => "Add Gallery Image",
			"pluginsection" => "addcontent",
			"adminclass" => "showReEdit:false showPreview:false showPublish:false updateForm:false"
		);
		$this->start($settings, $args);
    }
    function update($old, $new) {
		return $new;
    }
    function doUpdate() {
	global $_POST, $sketch;
	$data = $_POST;
	if(trim($_POST['menu_name'])!= ""){
	    $data['page_updated']   = date("Y-m-d H:i:s");
	    $data['updated_by']	    = @$_SESSION['admin']['user_login'];
	    $data['page_date']	    = date("Y-m-d");
	    $data['page_title']	    = $_POST['menu_name'];
	    $data['sketch_settings_id'] = sketch("siteid");
	    startTransaction();
	    $r = ACTIVERECORD::keeprecord(insertDB("sketch_page", $data));
	    if ($r) {
			$data['page_id'] = lastInsertId();
			$npid = $data['page_id'];
			$r->free();
			$mr = getData("sketch_menu", "*", "WHERE sketch_menu_id=" . intval($_POST['menu_under']));
			$Raw = stripslashes(trim($_POST['menu_name']));
			$RemoveChars = array(
				"([\40])",
				"([^a-zA-Z0-9-])",
				"(-{2,})"
			);
			$ReplaceWith = array(
				"-",
				"",
				"-"
			);
			$guid = strtolower(preg_replace($RemoveChars, $ReplaceWith, $Raw));

			$f = scandir(sketch('sketchPath') . 'controller/');
			$cancreate = true;
			foreach ($f as $key => $value) {
				if (strtolower($guid) == strtolower(str_replace(".php", "", $value))) {
					$cancreate = false;
				}
			}
		if ($cancreate) {
		    if ($mr->advance()) {
			$gui = explode("?", $mr->menu_guid);
			$subpath = ltrim(sketch("main_site_url") . $gui[0], "/");
			$guid = strtolower(trim(rtrim($subpath, "/") . "/" . $guid, "/"));
		    } //$mr->advance()
		    $mr->free();
		    $mo = getData("sketch_menu", "page_id", "WHERE menu_under=" . intval($_POST['menu_under']));
		    $data['menu_order'] = intval($mo->rowCount()) + 2;
		    $data['menu_guid'] = $guid;
		    $mo->free();
		    $r = ACTIVERECORD::keeprecord(insertDB("sketch_menu", $data));
		    if ($r) {
				// Add page content
				global $_POST;
				$data                = $_POST;
				if ( isset( $_POST[ 'page_date' ] ) ) {
				  list( $d, $m, $y ) = explode( "-", @$_POST[ 'page_date' ] );
				} else {
				  list( $d, $m, $y ) = explode( "-", date( "d-m-Y" ) );
				}
				
				if ( isset( $_POST[ 'page_expiry' ] )  && strpos( $_POST[ 'page_expiry' ],"-") !==false) {
					list( $xd, $xm, $xy ) = explode( "-", @$_POST[ 'page_expiry' ] );
					$data[ 'page_expiry' ] = (!is_numeric($xd))? "NULL" : $xy . "-" . $xm . "-" . $xd;
				}else{
					$data[ 'page_expiry' ] = "NULL";	
				}
				
				$data[ 'page_date' ] = $y . "-" . $m . "-" . $d;
				$data[ 'page_id' ]   = $npid;
				if(isset($_POST['password'])){
				  $_POST['password'] = secureit($_POST['password']);
				}
				
				$r = getData("sketch_page","*","page_id='".$npid."'");
				$row = array_keys((array)$r->advance());
				foreach($row as $key => $value){
					if(isset($_POST[$value]) && $value != "edit"){
						unset($_POST[$value]);
					}
				}
				
				foreach ( $_POST as $key => $value ) {
					if(!is_array($value)){
						 $_POST[ $key ] = str_replace(array("?",'"'),array("_##-",';#;'), trim(stripslashes($value)) );
					}
				} //$_POST as $key => $value
				$data[ 'edit' ] = serialize( $_POST );
				$data['content'] = $data['edit'];
				$r              = ACTIVERECORD::keeprecord( updateDB( "sketch_page", $data ) );
							
				
				
				
    ?>
			<script type="text/javascript">
			    var m = new Spinner($(document.body),{id:'loadspin',style:{"z-index":999999,"background-color":"#fff","color":"#778899"},"message":"Loading new page - Please Wait"}).show();
			    function loadNewPage(){
			    window.location = "http://<?php echo $sketch->urlPath($guid); ?>";
			    }
			    loadNewPage.delay(500);
			</script><?php
		    } //$r
		    $r->free();
		    commitTransaction();
		}else {
    ?>		<script type="text/javascript">
			alert("Sorry - that name is reserved for sketch");
		    </script><?php
		}
	    }
	}
    }

    function display() {

    }

    function form() {
		global $sketch;
		@include(loadForm("addportfolioform", false));
    }

}