<?php

class ADDPAGE extends PLUGIN {
    function ADDPAGE($args) {
	$settings = array(
	    "location" => "",
	    "admin" => 1,
	    "php" => 1,
	    "menuName" => "Add Page",
		"pluginsection" => "addcontent",
	    "topnav" => 1,
	    "adminclass" => "showReEdit:false showPreview:false showPublish:false updateForm:false"
	);
	$this->start($settings, $args);
    }
    function update($old, $new) {
	return $new;
    }
    function createMultiplePages(){
	global $_POST, $sketch;
	if(is_array($_POST['menu_name_multi']) && count($_POST['menu_name_multi']) > 1){
	    foreach($_POST['menu_name_multi'] as $key => $value){
		if(trim($value) != ""){
		$data['page_updated']	= date("Y-m-d H:i:s");
		$data['updated_by']	= @$_SESSION['admin']['user_login'];
		$data['page_date']	= date("Y-m-d");
		$data['page_title']	= $value;
		$data['page_type']	= $_POST['page_type_multi'][$key];
		$data['page_status']	= $_POST['page_status_multi'][$key];
		$data['sketch_settings_id'] = sketch("siteid");
		startTransaction();
		$r = ACTIVERECORD::keeprecord(insertDB("sketch_page", $data));
		if ($r) {
		    $data['page_id'] = lastInsertId();
		    $r->free();
		    if(is_numeric($_POST['menu_under_multi'][$key])){
			$mr = getData("sketch_menu", "*", "WHERE sketch_menu_id=" . intval($_POST['menu_under_multi'][$key]));
		    }else{
			$mr = getData("sketch_menu,sketch_page", "*", "WHERE sketch_settings_id=".sketch("siteid")." AND page_title=".sketch("db")->quote($_POST['menu_under_multi'][$key]));
		    }
		    $Raw = stripslashes(trim($value));
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
		    foreach ($f as $k => $v) {
			if (strtolower($guid) == strtolower(str_replace(".php", "", $v))) {
			    $guid .= "_page";
			}
		    }
		    if ($cancreate) {
			if ($mr->advance()) {
			    $gui = explode("?", $mr->menu_guid);
			    $subpath = ltrim(sketch("main_site_url") . $gui[0], "/");
			    $guid = strtolower(trim(rtrim($subpath, "/") . "/" . $guid, "/"));
			} //$mr->advance()
			$mr->free();
			if(is_numeric($_POST['menu_under_multi'][$key])){
			    $mo = getData("sketch_menu", "*", "WHERE sketch_menu_id=" . intval($_POST['menu_under_multi'][$key]));
			}else{
			    $mo = getData("sketch_menu,sketch_page", "*", "WHERE sketch_settings_id=".sketch("siteid")." AND page_title=".sketch("db")->quote($_POST['menu_under_multi'][$key]));
			}
			$mo->advance();
			$data['menu_order'] = intval($mo->rowCount()) + 2;
			$data['menu_guid']  = $guid;
			$data['menu_show']  = 1;
			$data['menu_mobile']= 1;
			$data['menu_under'] = $mo->sketch_menu_id;
			$data['menu_name']  = stripslashes(trim($value));
			$mo->free();
			$r = ACTIVERECORD::keeprecord(insertDB("sketch_menu", $data));
			$r->free();
			commitTransaction();
		    }
		}
	    }
	}
	?>
	    <script type="text/javascript">
		var m = new Spinner($(document.body),{id:'loadspin',style:{"z-index":999999,"background-color":"#fff","color":"#778899"},"message":"Loading new pages - Please Wait"}).show();
		function loadNewPage(){
		window.location = window.location;
		}
		loadNewPage.delay(500);
	    </script>
<?php	    exit();
	}
    }
    function doUpdate() {
	global $_POST, $sketch;
	$data = $_POST;
	$this->createMultiplePages();
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
	@include(loadForm("addpageform", false));
    }

}