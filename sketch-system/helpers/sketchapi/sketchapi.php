<?php
/*
 * Database
 * Created By Kevin Dibble, (c) FLAXsketch 2010
 * Part of the sketch framework (c) FLAXsketch
 * 
 * Purpose - To allow the quick access to sketch functions
 */
if(class_exists("sketch")){
	function sketch($item,$value=""){
		global $sketch;
		if($value != ""){
			$sketch->$item = $value;
		}
		if(is_string($sketch->$item)){
			return $sketch->$item;
		}
		return $sketch->$item;
	}
	function checkPlugin($plugin){
		global $sketch;
		$plugin = trim(strtolower($plugin));
		$sketch->registerPlugin(array("name"=>$plugin));
		if(isset($sketch->plugins[$plugin]) && method_exists($sketch->plugins[$plugin],"displayCheck")){
			if(!$sketch->plugins[$plugin]->displayCheck()){
				return false;
			}
		}
		return true;
	}
	function contentToArray($content){
		$content = stripslashes(trim(str_replace(array(";#;","_##-"),array('"',"?"),$content)));
		$c = $content;
		if(strpos($content,"}")!==false){
			 $c = @unserialize(preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $content));
			foreach ((array) $c as $k => $v) {
				if(is_array($v)){
					$c[$k] = $v;
				}else{
					$c[$k] =  $v;
				}
			}
		}
		return $c;
	}
	function doCache($plugin){
		helper("cache");
		$cache = CACHECLASS::cache($plugin.sketch("page_id"));
		if(!$cache->start()){
			plugin($plugin);
			$cache->end();
		}
	}
	function doCaches($plugin){
		helper("cache");
		$cache = CACHECLASS::cache($plugin.sketch("page_id"));
		if(!$cache->start()){
			plugins($plugin);
			$cache->end();
		}
	}
	function pageElements($setting){
		global $sketch;	
		return (isset($sketch->pageElements[$setting]))? $sketch->pageElements[$setting] : false ;
	}
	function loadForm($form,$return=true){
		global $sketch;
		return $sketch->loadForm($form,$return);
	}
	function includeForm($form){
		global $sketch;
		include_once($sketch->loadForm($form,false));	
	}
	function outputHeaders(){
		global $sketch;
		$sketch->outputHeaders();
	}
	/*
	 * Function URLPATH
	 * allows the calling of anypage to be url safe
	 * 
	 */
	function urlPath($path="",$http=true){
		global $sketch;
		$http = ($http)? "http://" : "";
		return $http.$sketch->urlPath($path);
	}
	function adminCheck(){
		global $sketch;
		return $sketch->adminCheck();
	}
	function getDirectory($dir){
		global $sketch;
		return $sketch->getDirectory($dir);
	}
	function getSettings($setting){
		global $sketch;
		return $sketch->checkSettings($setting);	
	}
	function updateSettings($setting,$value){
		global $sketch;
		$sketch->updateSetting($setting,$value);
	}
	function superUser(){
		global $sketch;
		return $sketch->superUser();
	}
	function getPageName(){
		global $sketch;
		return $sketch->getPageName();
	}
	function getSiteSettings($item){
		global $sketch;
		return $sketch->getSiteSettings($item);	
	}
	function getImages(){
		global $sketch;
		return $sketch->getImages();	
	}
	function getFiles(){
		global $sketch;
		return $sketch->getFiles();	
	}
	function plugin($item){
		callPlugin($item);
	}
	function callPlugin($item){
		global $sketch;
		$sketch->callPlugin($item);	
	}
	function adminFilter($name,$args=""){
		global $sketch;
		$sketch->callAdminFilter($name,$args);	
	}
	function filter($name,$args=""){
		callPluginFilter($name,$args);
	}
	function callPluginFilter($name,$args=""){
		global $sketch;
		$sketch->callPluginFilter($name,$args);	
	}
	function plugins($area){
		getPlugins($area);	
	}
	function getPlugins($area){
		global $sketch;
		$sketch->getPlugins($area);	
	}
	function load($helper){
		return loadHelper($helper);	
	}
	function helper($helper){
		return loadHelper($helper);	
	}
	function loadHelper($helper){
		global $sketch;
		return $sketch->loadHelper($helper);	
	}
	function setQueryData($started,$sql=""){
		global $sketch;
		$sketch->setQueryData($started,$sql);
	}
	function styles(){
		getStylePath();
	}
	function getStylePath(){
		global $sketch;
		$sketch->getStylePath();
	}
	function scripts(){
		getScriptPath();
	}
	function getScriptPath(){
		global $sketch;
		$sketch->getScriptPath();
	}
	function benchmark($start,$description=""){
		global $sketch;
		$sketch->setQueryData($start,$description);	
	}
	function loadInclude($view){
		global $sketch;
		@include($sketch->loadView($view,false,true));
	}
	function loadView($view="index",$return=false,$path=false){
		global $sketch;
		return $sketch->loadView($view,$return,$path);
	}
	function loadScript($view="index",$return=true){
		global $sketch;
		return $sketch->loadScript($view,$return);
	}
	function loadModel($model="index"){
		global $sketch;
		$sketch->loadModel($model);
	}
	function model($name){
		global $sketch;
		return $sketch->getModel( $name );
	}
	function loadController($controller="index"){
		global $sketch;
		$sketch->loadController($controller);
	}
	function loadError($type,$message=""){
		global $sketch;
		sketch("errorMessage",$message);
		$sketch->loadError($type);
		exit();
	}
	function template($name){
		filter("templates",array("show"=>true,"template_name"=>$name));	
	}
	function sidenav($args=''){
		if($args != '' && is_array($args)){
			filter("menu",$args);
		}else{
			filter("menu",array('id'=>"tab-menu",'doTop'=>false,"page_id"=>sketch("page_id")));
		}
	}
	if(!function_exists("secureit")){
	    function secureit($text,$decode=false,$newSalt=""){
		    $salt = ($newSalt=="")? SALT : $newSalt;
		    if(function_exists("mcrypt_cbc") && function_exists("mcrypt_create_iv") && trim($text)!= ''){
			    if($decode){
				    $text =  trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
			    }else{
				    $text =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
			    }
		    }
		    if(strlen($text) > 25 && $decode==true){
			return "";
		    }
		    return $text;
	    }
	}
	if(!function_exists("recall")){
	    function recall($values){
		$arrayBack = array();
		foreach($values as $key => $value){
		    if(is_array($value)){
		       $arrayBack = array_merge($arrayBack,recall($value));
		    }else{
			$arrayBack[$key] = $key;
		    }
		}
		return $arrayBack;
	    }
	}
} // End if sketch Class Exists