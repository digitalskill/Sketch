<?php
class FEEDXML extends PLUGIN {

    function FEEDXML($args) {
	$settings = array("location" => "feed", "php" => 1, "menuName" => "Feedxml", "adminclass" => "showReEdit:false showPreview:false showPublish:false updateForm:false", "pluginsection" => "sitesettings");
	$settings['content'] = array("url" => "", "frequency" => "daily", "LastChecked" => "", "xml" => array(), "valuestoshow" => "");
	$this->start($settings, $args);
    }

    function update($old, $new) { // [ REQUIRED ]
	return $new;
    }

    function display() {   // [ REQUIRED ]
	@include(loadView("feedxml", false, true));
    }

    function getFeedItems($ar, $key) {
	$feedArray = array();
	if (is_array($ar)) {
	    foreach ($ar as $key => $value) {
		$feedArray = array_merge($feedArray, $this->getFeedItems($value, $key));
	    }
	} else {
	    if (in_array($key, (array) explode(",", $this->e("valuestoshow")))) {
		if ($key == "content") {
		    $contentImages = explode("<img", $ar);
		    list($img, ) = explode(">", $contentImages[1]);
		    if (stripos($img, "src=") !== false) {
			$feedArray[$key] = "<img" . str_replace(array("height=", "width="), "", $img) . "'>";
		    }
		} else {
		    if ($key == "href") {
			if (stripos($ar, "#comments") === false && stripos($ar, "/feed/") === false) {
			    $feedArray[$key] = $ar;
			} else {
			    $ar = "";
			}
		    }
		    if ($ar != "") {
			$feedArray[$key] = str_replace("[...]", "", $ar);
		    }
		}
	    }
	}
	return $feedArray;
    }

    function preview() {
	$this->display();
    }

    function form() {   // [ REQUIRED ]
	@include(loadForm("feedxmlform", false));
    }

    function objectsIntoArray($arrObjData, $arrSkipIndices = array()) {
	$arrData = array();
	// if input is object, convert into array
	if (is_object($arrObjData)) {
	    $arrObjData = get_object_vars($arrObjData);
	}
	if (is_array($arrObjData)) {
	    foreach ($arrObjData as $index => $value) {
		if (is_object($value) || is_array($value)) {
		    $value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call
		}
		$arrData[$index] = $value;
	    }
	}
	return $arrData;
    }
}