<?php
class PAGETAGS extends PLUGIN {
    function PAGETAGS($args) {
		$settings = array("location" => "center", "global" => 1, "php" => 1, "adminclass" => "showReEdit:false showPreview:false showPublish:false updateForm:false", "pluginsection" => "pageedit", "menuName" => "Page tags"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | class ]
		$this->start($settings, $args);
    }

    function update($old, $new) {     // [ REQUIRED ]
		return $new;
    }

    function doUpdate() {      // [ OVERRIDE ]
		global $_POST, $_SESSION;
		foreach ($_POST['tag_name'] as $key => $value) {
			$r = getData("tag","*","WHERE page_id=".intval($this->page_id)." AND tag_name=".sketch("db")->quote($value));
			if (trim($value) != "" && $r->rowCount() < 1) {
					$SQL = "INSERT INTO " . $this->prefix . "tag (page_id,tag_name) " .
							"VALUES (".intval($this->page_id) . "," . sketch("db")->quote($value).")";
					$r = ACTIVERECORD::keeprecord($SQL);
			}
		}
		foreach ($_POST['removeit'] as $key => $value) {
			$SQL = "DELETE FROM " . $this->prefix . "tag WHERE tag_id = ".intval($value);
			$r = ACTIVERECORD::keeprecord($SQL);
		}
    }

    function display() {       // [ REQUIRED ] 		// outputs to the page
    }

    function filter($args="") {
		$page = (isset($args['page_id'])) ? intval($args['page_id']) : $this->page_id;
		$name = (isset($args['name'])) ? " AND tag_name LIKE '%" . str_replace("'", "", sketch("db")->quote($args['name'])) . "%' " : "";
		$SQL = "SELECT * FROM " . $this->prefix . "tag WHERE page_id=" . sketch("db")->quote($page) . " " . $name;
		$r = ACTIVERECORD::keeprecord($SQL);
		$r->advance;
		return $r->result;
    }

    function form() {    // [ REQUIRED ]
		global $sketch;
		@include(loadForm("pagetagsform",false));
    }

}