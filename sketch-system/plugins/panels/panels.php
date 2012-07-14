<?php
class PANELS extends PLUGIN {
    function PANELS($args) {
	$settings = array("location" => "sidebar", "php" => 1, "menuName" => "Panels", "global" => 1, "pluginsection" => "Assets", "adminclass" => "updateForm:false showSave:false showReEdit:false showPreview:false showPublish:false"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
	$settings['content'] = array("heading1" => "Panel Heading", "content1" => "Panel Content", "limitto1" => "", "getfrom1" => array(), "onPages1" => array(), "paneltype" => "", "panels" => 3);
	$this->start($settings, $args);
    }
    function update($old, $new) {    // [ REQUIRED ]
		global $_POST;
		if (isset($_POST['addbanner']) && $_POST['addbanner'] == 'yes') {
			$new = array();
			$SQL = "INSERT INTO " . getSettings("prefix") . "panel (panel_heading,panel_content,panel_image,panel_thumbnail,panel_link,panel_type) VALUES ('New Panel','New Panel','','','',0)";
			$newBanner = ACTIVERECORD::keeprecord($SQL);
			$lid = lastInsertId();
			$this->showForm();
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
			$delR = getData("panel,panel_to_page","*","panel_type=0 AND page_id=".sketch("page_id"));
			while($delR->advance()){
				// DELETE ALL PANELS ON THIS PAGE
				$SQL = "DELETE FROM ".getSettings("prefix")."panel_to_page WHERE page_id=".intval(sketch("page_id"))." AND panel_id=".intval($delR->panel_id);
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
		$this->approve();
		if(!isset($_POST['addbanner'])){
			$this->display();
			unset($_SESSION['last_clicked_id']);
			?>
            	<script type="text/javascript">
					if($("loaderArea")){
						$("loaderArea").morph({"width":0,"left":131});
						$('adminMask').morph({"width":248,"min-width":248});
						$("load-box").setStyle("padding-bottom",70);
						$("bottom-box").addClass("no-shadow");
						$$(".aspin").unspin();
						$$(".aspin").removeClass("aspin");
						if($("loaderArea").getElement("form")){
							$("loaderArea").getElement("form").fireEvent("removeMCE");
						}
						$("loaderArea").empty();
					}
				</script>
            <?php
		}
		return $new;
    }
    function display($args='') {    // [ REQUIRED ]
		if(sketch("page_type")=="blog" || sketch("page_type")=="news" || sketch("page_type")=="product"){
			$SQL = "SELECT " . getSettings("prefix") . "panel.* " .
				"FROM " . getSettings("prefix") . "panel," . getSettings("prefix") . "panel_to_page " .
				"WHERE " . getSettings("prefix") . "panel_to_page.panel_id=" . getSettings("prefix") . "panel.panel_id " .
				"AND page_id IN (select page_id FROM ".getSettings("prefix")."sketch_menu WHERE sketch_menu_id=".intval(sketch("menu_under")) . ") AND panel_type=0 ORDER BY panel_order";
		}else{
			$SQL = "SELECT " . getSettings("prefix") . "panel.* " .
				"FROM " . getSettings("prefix") . "panel," . getSettings("prefix") . "panel_to_page " .
				"WHERE " . getSettings("prefix") . "panel_to_page.panel_id=" . getSettings("prefix") . "panel.panel_id " .
				"AND page_id=" . intval($this->page_id) . " AND panel_type=0 ORDER BY panel_order";
		}
		$r = ACTIVERECORD::keeprecord($SQL);
		$count = $r->rowCount();
		$current = 0;
		while ($r->advance()) {
			$current++;
			@include(loadView("panels", false, true));
		}
		$r->free();
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
	<form class="required ajax:true output:'panels'" style="position:relative;width:99%;" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="banner_form_<?php echo $this->settings['name'] . $_POST['getItem']; ?>">
	        <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	        <input type="hidden" name="plugin_id" value="<?php echo $this->settings['plugin_id']; ?>" />
	        <input type="hidden" name="preview" value="edit" />
<?php $this->form(); ?>
			 <button class="button positive" type="submit">Save</button>
	        <div style="clear:both;">&nbsp;</div>
	    </form><?php } else { ?>
	    <div class="form" style="width:200px">
	    	<div class="inside" style="width:100%; overflow:hidden;float:left;z-index:1" id="banner_edit_inside">
	    	    <div style="float:left;width:96%;position:relative;">
	    		<form class="required ajax:true output:'load-box'" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="form_banner">
	                        <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	                        <input type="hidden" name="plugin_id" value="<?php echo $this->settings['plugin_id']; ?>" />
	                        <input type="hidden" name="addbanner" value="no" id="addbanner"/>
				<input type="hidden" name="preview" value="edit" />
				<a onclick="$('addbanner').set('value','yes'); $('form_banner').fireEvent('submit');" style="margin-top:5px; display:block" class="button positive"><span class="icons plus"></span>Add panel</a>
	    		</form>
	    	    </div>
	    	    <div style="clear:both;height:5px;border-bottom:solid 1px #e2e2e2;margin-bottom:5px;">&nbsp;</div>
<?php $this->form(); ?>
	</div>
    <div class="loaderArea" id="loaderArea" style="height:auto;padding-top:0px;"></div>
</div>
<script type="text/javascript">
    function setupAccords(){
	new Validate($("form_banner"));
	$$('.ajaxlink').each(function(item,index){
	    new Ajaxlinks(item);
	});
    }
    setupAccords.delay(500);
</script>
<?php
	}
    }

    function form() {       // [ REQUIRED ]
		global $sketch,$_POST;
		@include(loadForm("panels", false));
    }

}