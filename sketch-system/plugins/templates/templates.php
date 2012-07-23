<?php
class TEMPLATES extends PLUGIN {
	function TEMPLATES($args) {
		$settings = array("location"=>"template","global"=>1,"php"=>1,"adminclass"=>"updateForm:false showReEdit:false showPreview:false showPublish:false","pluginsection"=>"pageedit","menuName"=>"Page Templates");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | class ]
		$settings['content'] = array("favicon"=>"","appleicon"=>'');
		$this->start($settings,$args);
	}
	function update($old,$new){ 				// [ REQUIRED ]
		$r = getData("template","*");
		while($r->advance()){
			@unlink(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php");	
		}
		return $new;
	}
	function doUpdate(){						// [ OVERRIDE ] 
		global $_POST;
		foreach($_POST['template_id'] as $key => $value){
			if(intval($value) > 0){
				if($_POST['template_type'][$key]=="delete"){
					$SQL 	= "DELETE FROM ".$this->prefix."template WHERE template_id='".intval($value)."'";
					ACTIVERECORD::keeprecord($SQL);
				}else{
					$SQL 	= "UPDATE ".$this->prefix."template SET template_content=".sketch("db")->quote(html_entity_decode(htmlentities($_POST['template_content'][$key]))).", template_name=".sketch("db")->quote($_POST['template_name'][$key]).", template_type=".sketch("db")->quote($_POST['template_type'][$key])." WHERE template_id='".intval($value)."'";
					ACTIVERECORD::keeprecord($SQL);
				}
			}else{
				if(trim($_POST['template_content'][$key]) != '' && trim($_POST['template_name'][$key])  != "" ){
					$SQL 	= "INSERT INTO ".$this->prefix."template (template_content,template_name,template_type) VALUES (".sketch("db")->quote(html_entity_decode(htmlentities($_POST['template_content'][$key]))).",".sketch("db")->quote($_POST['template_name'][$key]).",".sketch("db")->quote($_POST['template_type'][$key]).")";
					ACTIVERECORD::keeprecord($SQL);
				}
			}
		}
	}
	function showDisplay( $area=''){
		$this->display();
	}
	function display($args=''){                          // [ REQUIRED ] 		// outputs to the page
		$r = false;
		if(isset($args['data'])){
			$this->settings[ 'content' ] = $args['data'];
		}
		if(isset($args['show'])){
		   if(isset($args['template_type'])){
		  		$r = getData("template","*","template_type=".sketch("db")->quote($args['template_type'])." AND template_name=".sketch("db")->quote($args["template_name"]));
		   }else{
			   $r = getData("template","*","template_name=".sketch("db")->quote($args["template_name"]));
		   }
		  if($r->rowCount()==0){
			 if($args['template_type']=="page"){
         	 	$this->loadView($args["template_name"]);
			 }
			 if($args['template_type']=="form"){
				@include(loadForm($args['template_name'],false));
			 }
			 if($args['template_type']=="output"){
				 @include(loadView($args['template_name'],false,true));
			 }
		  }else{
			 $r->advance();
			 if(!is_file(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php")){
			 	file_put_contents(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php",$r->template_content);
			 }
			 include(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php");
		  }
		}
	}
	function form(){ 							// [ REQUIRED ] 
		@include(loadForm("templateform",false));
	}
}