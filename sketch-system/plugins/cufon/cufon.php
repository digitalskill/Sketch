<?php
class CUFON extends PLUGIN {
    function CUFON($args) {
	$settings = array("location" => "script", "global" => 1, "js" => 1, "php" => 1, "menuName" => "Cufon", "pluginsection" => "Assets", "adminclass" => "updateForm:false showReEdit:false showPreview:false showPublish:false", "isSuper" => true); // [ js | css | php | global | location | admin | menuName | topnav ]
	$settings['content'] = array("amount" => 3, "class1" => "", "family1" => "", "class2" => "", "family2" => "", "class3" => "", "family3" => "");
	$this->start($settings, $args);
    }

    function update($old, $new) {    // [ REQUIRED ]
	return $new;
    }

    function display($args='') {    // [ REQUIRED ] 
	@include(loadView("cufon",false,true));
    }

    function preview() {      // [ REQUIRED ]
	$this->display();
    }

    function form() {       // [ REQUIRED ]
	@include(loadForm("cufonform", false));
    }
}