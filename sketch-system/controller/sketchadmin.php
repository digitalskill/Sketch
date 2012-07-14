<?php
class sketchADMIN extends CONTROLLER{
	function sketchADMIN($page){
		global $sketch,$_REQUEST;
                if(!adminCheck()){
                  if(isset($_REQUEST['ajax']) && !isset($_REQUEST['order'])){?>
                    <script type="text/javascript">
                        alert("Your login has timed out. Please login to continue");
                    </script>
                   <?php } 
                  exit();
                }
                list($page,) = explode(".",end(explode("/",$page)));
		$name = end(explode("_",$page));
		if(strpos($page,"admin") !== false && adminCheck()){ 					// If the plugin is being updated via an admin call
			if(!isset($sketch->plugins[$name])){
				$sketch->registerPlugin(array("name"=>$name));
			}
			$sketch->plugins[$name]->doUpdate();									// Update the plugin
		}else{
			if(strpos($page,"ajax_") !== false && adminCheck()){
				switch(@$_REQUEST['preview']){
					case "edit":
						if(!isset($sketch->plugins[$name])){
							$sketch->registerPlugin(array("name"=>$name));
						}
						$sketch->plugins[$name]->previewEdit();
					break;
					case "preview":
						if(!isset($sketch->plugins[$name])){
							$sketch->registerPlugin(array("name"=>$name));
						}
						$sketch->plugins[$name]->previewCurrent();
					break;
					case "approve":
						if(!isset($sketch->plugins[$name])){
							$sketch->registerPlugin(array("name"=>$name));
						}
						$sketch->plugins[$name]->approve();
					break;
					case "install":
						$sketch->registerPlugin(array("name"=>$name));
						$sketch->plugins[$name]->install();
						break;
					case "updatesettings":
						if(!isset($sketch->plugins[$name])){
							$sketch->registerPlugin(array("name"=>$name));
						}
						$sketch->plugins[$name]->updateSettings();
					break;
					default:
						if(!isset($sketch->plugins[$name])){
							$sketch->registerPlugin(array("name"=>$name));
						}
						$sketch->plugins[$name]->showForm();
				}
			}else{
                          if(!isset($sketch->plugins[$name])){
                            $sketch->registerPlugin(array("name"=>$name));
                          }
			  $sketch->plugins[$name]->filter($_REQUEST);
			}
                }
	}
}