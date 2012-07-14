<?php
class SITEMAP extends CONTROLLER{
	function SITEMAP($page){
		global $sketch, $_REQUEST;
		if ($sketch->superUser() && isset($_REQUEST['preview'])) {
			$SQL = "SELECT ".getSettings("prefix")."sketch_menu.* FROM ".getSettings("prefix")."sketch_menu,".getSettings("prefix")."sketch_page WHERE ".getSettings("prefix")."sketch_menu.page_id=".getSettings("prefix")."sketch_page.page_id AND sketch_settings_id=".$sketch->siteid." ORDER BY menu_under, menu_order, sketch_menu_id";
			$record = getData("sketch_menu,sketch_page", getSettings("prefix") . "sketch_menu.*", "sketch_settings_id=" . $sketch->siteid, "ORDER BY menu_under, menu_order, sketch_menu_id");
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
			if ($record->menu_under == 0) {
				$a_class .='topLevel';
			}
			if ($record->page_id == $sketch->page_id) {
				$a_class .=' current';
			}
			$a_class .= '"';
			if (strpos($record->menu_guid, "http://") === false) {
				$this->menuData['items'][$record->sketch_menu_id] = 'http://' . $sketch->urlPath($record->menu_guid);
			} else {
				$this->menuData['items'][$record->sketch_menu_id] = 'href="' . $record->menu_guid;
			}
			if (isset($_REQUEST['preview'])) {
				$this->datalookup[$record->sketch_menu_id] = $record->result;
			} else {
				$this->datalookup[$record->sketch_menu_id] = array('menu_class' => $record->menu_class, 'menu_show' => $record->menu_show,'menu_name'=> $record->menu_name,"page_id"=>$record->page_id,"sketch_menu_id"=>$record->sketch_menu_id);
			}
			$this->menuData['parents'][intval($record->menu_under)][] = $record->sketch_menu_id;
			$this->pagetomenu[$record->page_id] = $record->sketch_menu_id;
			$this->findTops[$record->sketch_menu_id] = intval($record->menu_under);
		}
		$record->free();
		if (isset($this->pagetomenu[$sketch->page_id])) {
			$this->findCurrent($this->pagetomenu[$sketch->page_id]);
		}
		header ("Content-Type:text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<urlset
      				xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      				xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
           			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		echo $this->buildXMLMenu(0,1);
		echo "</urlset>";
	}
	function buildXMLMenu($parentid,$pio=1){
		if (isset($this->menuData['parents'][intval($parentid)])) {
			$count = 0;
			foreach (@$this->menuData['parents'][intval($parentid)] as $itemId) {
				$count++;
				echo  '<url>
							<loc>'.$this->menuData['items'][$itemId].'</loc>
							<priority>'.number_format($pio,2,'.','').'</priority>
						  </url>';
				$pio =  $pio < 0.3 ? $pio = 0.5 : $pio;
				$this->buildXMLMenu($itemId,$pio-0.2);
				if($count==1 && $pio==1){
					$pio -= 0.2;	
				}
			}
		}
	}
}