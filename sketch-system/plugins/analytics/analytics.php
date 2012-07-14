<?php
class ANALYTICS extends PLUGIN {

    function ANALYTICS($args) {
	$settings = array("location" => "script", "php" => 1, "menuName" => "Analytics", "adminclass" => "updateForm:false showReEdit:false showPreview:false showPublish:false", "pluginsection" => "sitesettings"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | menuName ]
	$settings['content'] = array("tracker" => "");
	$this->start($settings, $args);
    }

    function update($old, $new) {	     // [ REQUIRED ]
	return $new;
    }

    function display() {		    // [ REQUIRED ]
	if ($this->e('tracker') != '') {
	    @include(loadView("analytics",false,true));
	}
    }

    function preview() {
	$this->display();
    }

    function form() {			    // [ REQUIRED ]
	@include(loadForm("analyticsform",false));
    }
}