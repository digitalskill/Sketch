<?php

class USERS extends PLUGIN {

    function USERS($args) {
	$settings = array("location" => "meta", "admin" => 1, "php" => 1, "menuName" => "Site admins", "pluginsection"=>"sitesettings", "isSuper" => true, "adminclass" => "showReEdit:false showPreview:false showPublish:false"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin ]
	$this->start($settings, $args);
    }

    function update($old, $new) {   // [ REQUIRED ]
	return $new;
    }

    function doUpdate() {	    // [ OVERRIDE ]
	global $_POST;
	foreach ($_POST['user_id'] as $key => $value) {
	    $SQL = "";
	    if ($_POST['delete_user'][$key] == "yes") {
		$SQL = "DELETE FROM " . $this->prefix . "users WHERE user_id='" . intval($value) . "'";
	    } else {
		if (intval($value) > 0) {
		    $SQL = "UPDATE " . $this->prefix . "users SET " .
			    "user_login = " . sketch("db")->quote($_POST['user_login'][$key]) . ", ".
			    "user_email = " . sketch("db")->quote($_POST['user_email'][$key]) . ", ";
		    if (($_POST['user_password'][$key]) != '') {
			$SQL .= "user_password 	= " . sketch("db")->quote(secureit(trim($_POST['user_password'][$key]))) . ", ";
		    }
		    $SQL .= "is_super = " . intval($_POST['is_super'][$key]) . " " .
			    "WHERE user_id  =" . intval($value);
		} else {
		    if (($_POST['user_password'][$key]) != '') {
			$SQL = "INSERT INTO " . $this->prefix . "users (user_login,user_password,user_email,is_super) VALUES (" . sketch("db")->quote($_POST['user_login'][$key]) . "," . sketch("db")->quote(secureit(trim($_POST['user_password'][$key]))) . ",". sketch("db")->quote(trim($_POST['user_email'][$key])) . "," . intval($_POST['is_super'][$key]) . ")";
		    }
		}
	    }
	    if ($SQL != "") {
		$r = ACTIVERECORD::keeprecord($SQL);
		$r->free();
	    }
	}
	$this->showForm();
    }

    function showDisplay() {    // [ OVERRIDE ]
    }

    function display() {     // [ REQUIRED ] 		// outputs to the page
    }

    function preview() {

    }

    function form() {      // [ REQUIRED ]
	$SQL = "SELECT * FROM " . $this->prefix . "users ORDER BY user_login";
	$r = ACTIVERECORD::keeprecord($SQL);
?>
	<ul class="form" style="width:90%">
	    <li><?php $this->getPageDetails(); ?></li>
	</ul>
<?php while ($r->advance()) { ?>
	    <div id="admincontainer">
	        <ul class="form" style="clear:both; float:left;width:99%">
	    	<li style="float:left;width:20%;margin-right:1%;"><label>User Name</label>
	    	    <input type="hidden" name="user_id[]" value="<?php echo $r->user_id; ?>" />
	    	    <input type="text" name="user_login[]" class="required" value="<?php echo $r->user_login; ?>" /></li>
	    	<li style="float:left;width:20%;clear:none; margin-right:1%;">
	                <label>Email</label>
	    	    <input type="text" name="user_email[]" value="<?php echo $r->user_email; ?>" /></li>
	    	<li style="float:left;width:20%;clear:none; margin-right:1%;">
	                <label>Password</label>
	    	    <input type="text" name="user_password[]" value="<?php if (superUser ()) {
		echo secureit($r->user_password, true);
	    } ?>" /></li>
	    	<li style="float:left;width:20%;clear:none;margin-right:1%;">
	                <label>User rights</label>
	                <select name="is_super[]" class="bgClass:'select_bg'">
	                    <option value="1" <?php if ($r->is_super == 1) { ?>selected="selected" <?php } ?>>Super Admin</option>
	                    <option value="0" <?php if ($r->is_super == 0) { ?>selected="selected" <?php } ?>>Editor</option>
	                    <option value="2" <?php if ($r->is_super == 2) { ?>selected="selected" <?php } ?>>Blogger</option>
		    </select>
		</li>
		<li style="float:left;width:15%;clear:none;">
	            <label>Delete User</label>
		    <select name="delete_user[]" class="bgClass:'select_bg'">
	                <option value="no" selected="selected">No</option>
	                <option value="yes">Yes</option>
		    </select>
		</li>
	    </ul><?php } ?>
        <div class="clb"></div>
        <a style="width:107px" class="button" id="addnewadmin"><span class="icons user"></span>Add Admin</a>
        <script type="text/javascript">
    	function setupClones(){
    	    $('addnewadmin').addEvent("click",function(){
    		var newrow = $('admincontainer').getElement("ul").clone();
    		$(newrow).inject($('addnewadmin'),'before');
    		$(newrow).getElements("input").each(function(item,index){
    		    $(item).set("value","");
    		});
    		$(newrow).getElements("select").each(function(item,index){
    		    $(item).addEvent("change",function(){
    			$(this).getParent("div").getElement("span").set("html",$(this).getSelected().get("html"));
    		    });
    		});
    	    });
    	    new Validate($("load-box").getElement("form"));
    	}
    	setupClones.delay(500);
        </script>
    </div>
<?php
    }
}