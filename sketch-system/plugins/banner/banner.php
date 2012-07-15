<?php
class BANNER extends PLUGIN {
    function BANNER($args) {
		$settings = array("location" => "banner", "php" => 1, "menuName" => "Banners", "global" => 1, "pluginsection" => "Assets", "adminclass" => "updateForm:true showSave:false showReEdit:false showPreview:false showPublish:false"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
		$settings['content'] = array("banners" => 4, "effect" => "slide", "effectTime" => 1000, "delay" => 8000);
		$this->start($settings, $args);
    }
    function update($old, $new) {    // [ REQUIRED ]
		global $_POST;
		if (isset($_POST['addbanner']) && $_POST['addbanner'] == 'yes') {
			$new = array();
			$SQL = "INSERT INTO " . getSettings("prefix") . "panel (panel_heading,panel_content,panel_image,panel_thumbnail,panel_link,panel_type) VALUES ('New Banner','New Banner','','','',1)";
			$newBanner = ACTIVERECORD::keeprecord($SQL);
			$lid = lastInsertId();
		}
		if(isset( $_POST['panel_id'] ) && !is_array($_POST['panel_id'])){
			$new = array();
			foreach($_POST as $key => $value){
				if(!is_array($value)){
					$_POST[$key] = str_replace("?","_##-",$value);
				}
			}
			$SQL = updateDB("panel", $_POST, "WHERE panel_id=" . intval($_POST['panel_id']));
			$r = ACTIVERECORD::keeprecord($SQL);	
		}
		if(((isset($_POST['panel_id']) && is_array($_POST['panel_id'])) || !isset($_POST['panel_id'])) && !isset($_POST['addbanner'])){
			$delR = getData("panel,panel_to_page","*","panel_type=1 AND page_id=".sketch("page_id"));
			while($delR->advance()){
				// DELETE ALL PANELS ON THIS PAGE
				$SQL = "DELETE FROM ".getSettings("prefix")."panel_to_page WHERE page_id=".sketch("page_id")." AND panel_id=".intval($delR->panel_id);
				ACTIVERECORD::keeprecord($SQL);
			}	
		}
		if (isset($_POST['panel_id']) && is_array($_POST['panel_id'])) {	
			foreach((array)$_POST['panel_id'] as $k => $pid){
				$data = array();
				$data['page_id'] 		= sketch("page_id");
				$data['panel_id']		= $pid;
				$data['panel_order'] 	= intval($k);
				$SQL = insertDB("panel_to_page", $data);
				ACTIVERECORD::keeprecord($SQL);
			}
		}
		if(!isset($_POST['addbanner'])){
			$this->display();
			unset($_SESSION['last_clicked_id']);
			?>
            	<script type="text/javascript">
					function restartBannerAnimate(){
						$$('.animate').each(function(item,index){
							new Animator(item);
							$(item).removeClass("animate");
						 });
						$("loaderArea").morph({"width":0,"left":131});
						$('adminMask').morph({"width":248,"min-width":248});
						$$(".aspin").unspin();
						$$(".aspin").removeClass("aspin");
						if($("loaderArea").getElement("form")){
						$("loaderArea").getElement("form").fireEvent("removeMCE");
						}
						$("loaderArea").empty();
					}
					restartBannerAnimate.delay(500);
				</script>
            <?php
			exit();
		}
		return array_merge((array) $old, (array) $new);
    }	
    function display($args='') {    // [ REQUIRED ]
		if(sketch("page_type")=="blog"){
			$SQL = "SELECT " . getSettings("prefix") . "panel.* " .
				"FROM " . getSettings("prefix") . "panel," . getSettings("prefix") . "panel_to_page " .
				"WHERE " . getSettings("prefix") . "panel_to_page.panel_id=" . getSettings("prefix") . "panel.panel_id " .
				"AND page_id=" . intval(sketch()) . " AND panel_type=1 ORDER BY panel_order";
		}else{
			$SQL = "SELECT " . getSettings("prefix") . "panel.* " .
				"FROM " . getSettings("prefix") . "panel," . getSettings("prefix") . "panel_to_page " .
				"WHERE " . getSettings("prefix") . "panel_to_page.panel_id=" . getSettings("prefix") . "panel.panel_id " .
				"AND page_id=" . intval($this->page_id) . " AND panel_type=1 ORDER BY panel_order";
		}
		$r = ACTIVERECORD::keeprecord($SQL);
		if($r->rowCount() > 0){
			?><div id="cycle-wrapper"><?php
			@include(loadView("banner", false, true));
			?></div><?php
		}
    }

    function displayCheck() {
	$SQL = "SELECT " . getSettings("prefix") . "panel.* " .
		"FROM " . getSettings("prefix") . "panel," . getSettings("prefix") . "panel_to_page " .
		"WHERE " . getSettings("prefix") . "panel_to_page.panel_id=" . getSettings("prefix") . "panel.panel_id " .
		"AND page_id=" . intval($this->page_id) . " AND panel_type=1 ORDER BY panel_order";
	$r = ACTIVERECORD::keeprecord($SQL);
	$rows = $r->rowCount();
	$r->free();
	return $rows > 0 ? true : false;
    }

    function preview() {	// [ REQUIRED ]
		$this->display();
    }

    function showForm() {
	global $sketch, $_POST;
	$this->settings['content'] = $this->settings['edit'];
	if (isset($_POST['getItem'])) {
?>
	<div id="banner_result" style="display:none;"></div>
	<form class="required ajax:true output:'banner'" style="float:left;position:relative;width:99%;" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="banner_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>">
	        <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	        <input type="hidden" name="plugin_id" value="<?php echo $this->settings['plugin_id']; ?>" />
	        <input type="hidden" name="preview" value="edit" />
<?php $this->form(); ?>
	        <div style="clear:both;">&nbsp;</div>
	    </form><?php } else { ?>
	    <div class="form" style="width:200px">
        	<div class="inside accordian" style="width:100%; overflow:hidden;float:left;z-index:1" id="banner_edit_inside">
         		<a class="accord-title button" style="width:92%"><span class="icons downarrow"></span>Banner Settings</a>
	    	    <div style="float:left;width:99%;position:relative;">
                <div class="accord-body">
	    		<form class="required ajax:true output:'load-box'" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="form_banner">
	                        <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	                        <input type="hidden" name="plugin_id" value="<?php echo $this->settings['plugin_id']; ?>" />
	                        <input type="hidden" name="addbanner" value="no" id="addbanner"/>
	    		    <input type="hidden" name="preview" value="edit" />
	    		    <div style="clear:left;">
	    			<label class="pt10" style="clear:both;">Banner Effect</label>
	    			<select name="effect" class="bgClass:'select_bg'" id="bannereffect">
	    			    <option value="">None</option>
	    			    <option value="slide" <?php if ($this->e("effect") == "slide") { ?>selected="selected"<?php } ?>>Horizontal-slide left to Right</option>
	    			    <option value="fadeslide" <?php if ($this->e("effect") == "fadeslide") { ?>selected="selected"<?php } ?>>Horizontal-slide left to Right with fade</option>
	    			    <option value="vertical" <?php if ($this->e("effect") == "vertical") { ?>selected="selected"<?php } ?>>Vertical-slide top to bottom</option>
	    			    <option value="fadevertical"  <?php if ($this->e("effect") == "fadevertical") { ?>selected="selected"<?php } ?>>Vertical-slide top to bottom with Fade</option>
	    			    <option value="fade"  <?php if ($this->e("effect") == "fade") { ?>selected="selected"<?php } ?>>Fade</option>
	    			</select>
	                        </div>
	    		     <div style="clear:left;">
	    			<label style="clear:both;">Show Banner thumbnails</label>
	    			<select name="thumbnails" class="bgClass:'select_bg'" id="bannereffect">
	    			    <option value="yes" <?php if ($this->e("thumbnails") == "yes") { ?>selected="selected"<?php } ?>>Yes</option>
	    			    <option value="no" <?php if ($this->e("thumbnails") == "no") { ?>selected="selected"<?php } ?>>No</option>
	    			</select>
	                        </div>
	    		     <div style="clear:left;">
	    			<label style="clear:both;">Show Banner controls</label>
	    			<select name="controls" class="bgClass:'select_bg'" id="bannereffect">
	    			    <option value="yes" <?php if ($this->e("controls") == "yes") { ?>selected="selected"<?php } ?>>Yes</option>
	    			    <option value="no" <?php if ($this->e("controls") == "no") { ?>selected="selected"<?php } ?>>No</option>
	    			</select>
	                        </div>
	    		     <div style="clear:left;">
	    			<label>Inner class</label>
	    			<input type="text" name="innerclass" value="<?php echo $this->e("innerclass"); ?>" id="innerclass"/>
	                        </div>
	               <div style="clear:left;">
	    			<label>Effect Seconds</label>
	    			<input type="text" name="effectTime" value="<?php echo $this->e("effectTime"); ?>" id="bannereffectTime"/>
	                        </div>
	                <div style="clear:left;">
	    			<label>Delay seconds</label>
	    			<input type="text" name="delay" value="<?php echo $this->e("delay"); ?>" id="bannerdelay"/>
	                 </div>
                     <div style="clear:left;">
                     <button type="submit" class="button">Save</button>
                     </div>
	    		</form>
                </div>
                 <a class="button positive" style="margin-top:5px; display:block" onclick="$('addbanner').set('value','yes'); $('form_banner').fireEvent('submit');"><span class="icons plus"></span>Add banner</a>
	    	    </div>
	    	    <div style="clear:both;height:5px;border-bottom:solid 1px #e2e2e2;margin-bottom:5px;">&nbsp;</div>
<?php $this->form(); ?>
    <div class="loaderArea" id="loaderArea" style="height:auto;padding-top:0px;bottom:0"></div>
    <script type="text/javascript">
	function setupAccords(){
	    $$('.ajaxlink').each(function(item,index){
			new Ajaxlinks(item);
	    });
	    new Validate($("form_banner"));
		new accord($("banner_edit_inside"));
		$("loaderArea").morph({"width":0,"left":235});
		$("load-box").setStyle("padding-bottom",70);
		$("bottom-box").addClass("no-shadow");
		$('adminMask').morph({"width":248,"min-width":248});
		$$(".aspin").unspin();
		$$(".aspin").removeClass("aspin");
		if($("loaderArea").getElement("form")){
			$("loaderArea").getElement("form").fireEvent("removeMCE");
		}
		$("loaderArea").empty();
	}
	setupAccords.delay(500);
    </script>
</div>
<?php
	}
    }
    function form() {       // [ REQUIRED ]
	global $sketch,$_POST;
	@include(loadForm("bannerform", false));
    }

}