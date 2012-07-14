<?php
function sessionAdd($name,$value,$replace=true){
	global $_SESSION;
	if(isset($_SESSION[$name]) && $replace==false){
		
	}else{
		$_SESSION[$name] = $value;
	}
}
function sessionSet($name,$value,$replace=true){
    sessionAdd($name,$value,$replace);
}
function sessionGet($name){
	global $_SESSION;
	return isset($_SESSION[$name])? $_SESSION[$name] : false;
}
function sessionRemove($name){
	global $_SESSION;
	if(isset($_SESSION[$name])){
		unset($_SESSION[$name]);	
	}
}
