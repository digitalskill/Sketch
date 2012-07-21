<?php
class MENU extends PLUGIN {
    function MENU($args) {
		$settings = array("location" => "menu", "global" => 1, "topnav" => 1, "pluginsection" => "menu", "adminclass" => "showReEdit:false showPreview:false showPublish:false");
		$this->loadMenu();
		$this->start($settings, $args);
    }
    function loadMenu() {
		global $sketch, $_REQUEST;
		if ($sketch->superUser() && isset($_REQUEST['preview'])) {
			$SQL = "SELECT ".getSettings("prefix")."sketch_menu.* FROM ".getSettings("prefix")."sketch_menu,".getSettings("prefix")."sketch_page WHERE ".getSettings("prefix")."sketch_menu.page_id=".getSettings("prefix")."sketch_page.page_id AND sketch_settings_id=".$sketch->siteid." ORDER BY menu_under, menu_order, sketch_menu_id";
			//$record = getData("sketch_menu,sketch_page", getSettings("prefix") . "sketch_menu.*", "sketch_settings_id=" . $sketch->siteid, "ORDER BY menu_under, menu_order, sketch_menu_id");
		} else {
			if (adminCheck() && isset($_REQUEST['preview'])) {
				$SQL = "SELECT * FROM ".getSettings("prefix")."sketch_menu WHERE sketch_settings_id=" . $sketch->siteid . " AND menu_under <> 25 AND sketch_menu_id <> 25 ORDER BY menu_under, menu_order, sketch_menu_id";
			} else {
				helper("member");
				$sqlExtra = " AND page_status <> 'hidden' AND page_status <> 'member' AND (page_expiry >= NOW() || page_expiry IS NULL || page_expiry < 1)  ";
				if (memberid ()) {
				 $sqlExtra = " AND page_status <> 'hidden' ";
				}
				if (adminCheck ()) {
				 $sqlExtra = "";
				}
				$SQL = "SELECT ".getSettings("prefix")."sketch_menu.* FROM ".getSettings("prefix")."sketch_menu,".getSettings("prefix")."sketch_page WHERE ".getSettings("prefix")."sketch_menu.page_id=".getSettings("prefix")."sketch_page.page_id AND sketch_settings_id=".$sketch->siteid." AND menu_under <> 25 AND sketch_menu_id <> 25 " . $sqlExtra. " ORDER BY menu_under, menu_order, sketch_menu_id";
			}
		}
		$record = ACTIVERECORD::keeprecord($SQL);
		$this->menuData = array('items' => array(), 'parents' => array());
		$this->pagetomenu = array(); // Page id to menu id lookup array
		$this->findTops = array(); // Hold all the menu id that point to the menu its under
		$this->currents = array(); // Hold all the current items for the menu
		$this->datalookup = array(); // Holds extra settings for the menu item
		while ($record->advance()) {
			$a_class = 'class="';
			if ($record->page_id == $sketch->page_id) {
				$a_class .=' active';
			}
			$a_class .= '"';
			if (strpos($record->menu_guid, "http://") === false) {
				$this->menuData['items'][$record->sketch_menu_id] = '<a ' . $a_class . ' href="http://' . $sketch->urlPath($record->menu_guid) . '">' . stripslashes(trim($record->menu_name)) . '</a>';
			} else {
				$this->menuData['items'][$record->sketch_menu_id] = '<a ' . $a_class . ' href="' . $record->menu_guid . '" target="_blank">' . stripslashes(trim($record->menu_name)) . '</a>';
			}
			if (isset($_REQUEST['preview'])) {
				$this->datalookup[$record->sketch_menu_id] = $record->result;
			} else {
				$this->datalookup[$record->sketch_menu_id] = array('menu_class' => $record->menu_class, 'menu_show' => $record->menu_show,'menu_name'=> $record->menu_name,"page_id"=>$record->page_id,"sketch_menu_id"=>$record->sketch_menu_id,"menu_guid"=>$record->menu_guid);
			}
			$this->menuData['parents'][intval($record->menu_under)][] = $record->sketch_menu_id;
			$this->pagetomenu[$record->page_id] = $record->sketch_menu_id;
			$this->findTops[$record->sketch_menu_id] = intval($record->menu_under);
		}
		$record->free();
		if (isset($this->pagetomenu[$sketch->page_id])) {
			$this->findCurrent($this->pagetomenu[$sketch->page_id]);
		}
		$this->searchMenus = array();
    }
    function doUpdate() {       // [ OVERRIDE ]
	global $_POST,$sketch;
	if (isset($_POST['order'])) {
	    $updates = explode(";", $_POST['order']);
	    foreach ($updates as $key => $value) {
			@list($id, $ord) = @explode(":", $value);
			if (is_numeric($id) && is_numeric($ord)) {
				$SQL = "UPDATE " . $sketch->settings['prefix'] . "sketch_menu " .
					"SET menu_order =" . intval($ord) . " " .
					"WHERE sketch_menu_id =" . intval($id);
				$r = ACTIVERECORD::keeprecord($SQL, array(":order" => $ord, ":id" => $id));
				$r->free();
			}
	    }
	} else {
	    if (isset($_POST['sketch_menu_id'])) {
			foreach ($_POST['sketch_menu_id'] as $key => $value) {
				$SQL = "UPDATE " . $sketch->settings['prefix'] . "sketch_menu " .
					"SET menu_name =" . sketch("db")->quote($_POST['menu_name'][$key]) . " " .
					", menu_under=" . intval($_POST['menu_under'][$key]) . " " .
					", menu_guid=" . sketch("db")->quote($_POST['menu_guid'][$key]) . " " .
					", menu_show=" . intval($_POST['menu_show'][$key]) . " " .
					", menu_class=" . sketch("db")->quote($_POST['menu_class'][$key]) . " " .
					", menu_mobile=" . sketch("db")->quote($_POST['menu_mobile'][$key]) . " " .
					"WHERE sketch_menu_id=" . intval($value);
				$r = ACTIVERECORD::keeprecord($SQL);
				if(isset($_POST['removemenu'][$key])){
					if($_POST['removemenu'][$key]==1){
						// delete the page from the menu
						$SQL = "SELECT * FROM ".getSettings("prefix")."sketch_menu WHERE menu_guid IN (SELECT menu_guid FROM ".getSettings("prefix")."sketch_menu WHERE sketch_menu_id=".intval($value).")";
						$delRecordcheck = ACTIVERECORD::keeprecord( $SQL );
						while($delRecordcheck->advance()){
								$data = array();
								$data["sketch_menu_id"]	=	$delRecordcheck->sketch_menu_id;
								$data['page_id']		=	$delRecordcheck->page_id;
								removeData("sketch_menu",$data);
								removeData("sketch_page",$data);
						
						}
					}
				}
			}
	    }
	    $this->loadMenu();
	    $this->form();
	}
    }

    function update($old, $new) {    // [ REQUIRED ]
		return $new;
    }

    function display($args) {     // [ REQUIRED ]
    }

    function showDisplay($area="") {      // [ OVERRIDE ]
		echo $this->buildMenu(0);
    }

    function findTop($menuid) {
	if (isset($this->findTops[$menuid]) && intval($this->findTops[$menuid]) > 0) {
	    $menuid = $this->findTop($this->findTops[$menuid]);
	}
	$this->currents[$menuid] = $menuid; // Save the current items in the menu
	return $menuid;
    }

    function findCurrent($menuid) {
	if (isset($this->findTops[$menuid]) && intval($this->findTops[$menuid]) > 0) {
	    $this->currents[$menuid] = $this->findCurrent($this->findTops[$menuid]);
	}
	$this->currents[$menuid] = $menuid; // Save the current items in the menu
    }

    function preview() {      // [ REQUIRED ]
    }

    function filter($args="") {
		if(isset($args['output'])){
			@include(loadView($args['output'], false, true));
		}else{
			if (isset($args['breadcrumb'])) {
				$count = 0;
				foreach ($this->currents as $key => $value) {
					echo "<span class='bc" . $count . "'>" . $this->menuData['items'][$value] . "</span>";
					$count++;
				}
			} else {
				if(isset($args['select'])){
					if(!isset($args['id'])){
						$args['id'] = sketch("sketch_menu_id");	
					}
					$type = isset($args['type'])? $args['type'] : "sketch_menu_id";
					if(!isset($this->searchMenus[$args['id'].$type])){
						$this->searchMenus[$args['id'].$type] = $this->createSelect(0,$args['id'],$type);
					}
					echo $this->searchMenus[$args['id'].$type];
				}else{
					$topMenu = (isset($args['page_id']) && isset($this->pagetomenu[$args['page_id']])) ? $this->findTop($this->pagetomenu[$args['page_id']]) : intval($args);
					if (is_array($args) && isset($this->pagetomenu[$args['page_id']])) {
						$currentsTmp = $this->currents;
						if (isset($args['currentTree']) && $args['currentTree'] == false) {
							$this->currents = array($this->pagetomenu[$args['page_id']] => $this->pagetomenu[$args['page_id']]);
						}
						if (isset($args['doTop']) && $args['doTop'] == true) {
							$c = (in_array($topMenu, $this->currents)) ? 'selected' : '';
							$arrow = ($c == '') ? '' : '&gt;';
							echo "<ul id=".@$args['id']." class='".@$args['class']."'><li class='" . $c . " " . $this->datalookup[$topMenu]['menu_class'] . "'>";
							echo str_replace("#", $arrow, $this->menuData['items'][$topMenu]);
							echo $this->buildMenu($topMenu);
							echo "</li></ul>";
						} else {
							echo $this->createSubMenu($topMenu,$args);
						}
					} else {
						if (is_numeric($args)) {
							echo $this->buildMenu(intval($this->pagetomenu[$args]));
						}
					}
					if (isset($currentsTmp)) {
						$this->currents = $currentsTmp;
					}
				}
			}
		}
    }
	
	function createSubMenu($parentid,$args=''){ 
		if(isset($this->menuData['parents'][intval($parentid)])){
			$html = '<ul id="'.@$args['id'].'" class="'.@$args['class'].'">';
			foreach ($this->menuData['parents'][intval($parentid)] as $itemId) {
				$c = (in_array($itemId, $this->currents)) ? 'selected' : '';
				if($itemId==end($this->menuData['parents'][intval($parentid)])){
					$c .= ' last';	
				}
				if($this->datalookup[$itemId]['menu_show']==1){
					$html .= '<li class="' . $c . ' ' . $this->datalookup[$itemId]['menu_class'] . ' ' .htmlentities(str_replace(" ","-",$this->datalookup[$itemId]['menu_name'])).'"><a href="'.urlPath($this->datalookup[$itemId]['menu_guid']).'" class="menu-btn smoothAnchors">'.$this->datalookup[$itemId]['menu_name'].'</a></li>';
				}
			}
			$html .= '</ul>';
			if($html=="<ul></ul>"){
				$html = "";	
			}
		return $html;
		}
	}
	
	function createSelect($parentid,$sel,$type="sketch_menu_id",$child=""){
		$html = '';
		$curr = $child;
		$r = getData("sketch_menu","*","menu_under=".$parentid ." and page_id <> 25 and sketch_settings_id=".sketch("siteid"));
		if($r->rowCount() > 0){
			while($r->advance()){
				$dummyid = ($type=="sketch_menu_id")? $r->sketch_menu_id : $r->page_id;
				$selected = ($sel == $dummyid)? "selected" : "";
				$dummyid = ($type=="sketch_menu_id")? $r->sketch_menu_id : (($type=="url")? urlPath($r->menu_guid) : $r->page_id);
				if(trim($r->menu_name) != ""){
					$html .= '<option value="'.$dummyid.'" '.$selected.'>' . $curr ." ". $r->menu_name.'</option>';
				}
				$html .= $this->createSelect($r->sketch_menu_id,$sel,$type,$curr."&raquo;");
			}
		}
		return $html;
	}
	
	function createSelectAll($parentid,$sel,$type="sketch_menu_id",$child=""){
		$html = '';
		$curr = $child;
		$r = getData("sketch_menu,sketch_settings","*","menu_under=".$parentid,"ORDER BY sketch_settings_id");
		if($r->rowCount() > 0){
			while($r->advance()){
				$dummyid = ($type=="sketch_menu_id")? $r->sketch_menu_id : $r->page_id;
				$selected = ($sel == $dummyid)? "selected" : "";
				$dummyid = ($type=="sketch_menu_id")? $r->sketch_menu_id : (($type=="url")? urlPath($r->menu_guid) : $r->page_id);
				if(trim($r->menu_name) != ""){
					$html .= '<option value="'.$dummyid.'" '.$selected.'>'.$r->site_name ." " . $curr ." ". $r->menu_name.'</option>';
				}
				$html .= $this->createSelectAll($r->sketch_menu_id,$sel,$type,$curr."&raquo;");
			}
		}
		return $html;
	}

    function buildMenu($parentid) {
		$html = '';
		if(sketch("mobile")){
			@include(loadView("mobilemenu", false, true));
		}else{
			if (isset($this->menuData['parents'][intval($parentid)])) {
				@include(loadView("menu", false, true));
			}
		}
		return $html;
    }
	
    function getMenuSettings($itemId) {
		global $sketch;
		$record = getData("sketch_page", "*", "page_id=" . intval($this->datalookup[$itemId]['page_id']), "", "1");
		$record->advance();
		@include(loadForm("menusettingsform",false));
    }
	
    function buildAdminMenu($parentid) {
		$html = '';
		if (isset($this->menuData['parents'][intval($parentid)])) {
			$html = '<ul class="menu-lister form url:\'' . urlPath("admin") . '/admin_plugin_' . $this->settings['name'] . '\'" style="height:100%;">';
			$count = 0;
			foreach ($this->menuData['parents'][intval($parentid)] as $itemId) {
			$count++;
			if ($count == 1) {
				$par = (isset($this->datalookup[$this->datalookup[$itemId]['menu_under']]['menu_name']) && trim(stripslashes($this->datalookup[$this->datalookup[$itemId]['menu_under']]['menu_name'])) != "") ? trim(stripslashes($this->datalookup[$this->datalookup[$itemId]['menu_under']]['menu_name'])) : stripslashes($this->datalookup[$itemId]['menu_name']);
				$html .= '<li><div class="content-column">' .
					'<div class="big-font">' . $par . '</div></div><div class="clb"></div>' .
					'<div style="float:left; width:40px;"><div class="col1"><label>Order</label></div></div><div style="float:left; width:130px;"><div class="col1"><label>Menu name</label></div></div></li>';
			}
			$html .= '<li rel="' . intval($this->datalookup[$itemId]['sketch_menu_id']) . '">';
			$html .= '<a href="' . urlPath("admin") . '/ajax_plugin_' . $this->settings['name'] . '?preview=&getItem=' . intval($this->datalookup[$itemId]['sketch_menu_id']) . '&page_id=' . $this->page_id . '" class="button positive ajaxlink output:\'menuajaxzone\'" rel="settings' . $itemId . '">';
			if (isset($this->menuData['parents'][intval($itemId)])) {
				$html .= '<span class="icons rightarrow expander"></span>';
			}
			$html .= '<span class="icons move mover"></span>';
			$html .= ( trim(stripslashes($this->datalookup[$itemId]['menu_name'])) != "") ? trim(stripslashes($this->datalookup[$itemId]['menu_name'])) : trim(stripslashes($this->datalookup[$itemId]['menu_guid']));
			$html .= '</a>';
			$html .= '<div class="menusettings">';
			$html .= $this->buildAdminMenu($itemId);
			$html .= '</div>';
			$html .= '</li>';
			}
			$html .= '</ul>';
		}
		return $html;
    }

    public function showForm() {       // [ OVERRIDE ]
		$this->form();
    }

    function form() {     // [ REQUIRED ]
		@include(loadForm("menuform", false, true));
    }
}