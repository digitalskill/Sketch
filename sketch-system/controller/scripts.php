<?php
class SCRIPTS extends CONTROLLER{
	function SCRIPTS($page){
		global $_REQUEST,$_SERVER;
		header( 'Vary: Accept-Encoding' );
		header( "Content-type: text/javascript; charset=utf-8" );
		header( "Expires: " . gmdate( "D, d M Y H:i:s", ( time() + sketch( 'cacheseconds' ) ) ). " GMT" );
		$proxy = "public,";
		if ( getSettings( 'proxy_css_js' ) == false ) {
			$proxy = "private,";
		} //$site_settings[ 'proxy_css_js' ] == false
		if ( getSettings( 'cache' )  == true ) {
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $_REQUEST[ 'v' ] ) . ' GMT' );
			header( "Cache-Control: max-age=" . sketch( 'cacheseconds' ) . ", " . $proxy . " must-revalidate" );
			if ( isset( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) && ( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] == gmdate( 'D, d M Y H:i:s', $_REQUEST[ 'v' ] ) . ' GMT' ) ) {
				header( 'HTTP/1.1 304 Not Modified' );
				exit( );
			} //isset( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) && ( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] == gmdate( 'D, d M Y H:i:s', $_REQUEST[ 'v' ] ) . ' GMT' )
		} //$site_settings[ 'cache' ] == true
		parent::__construct("scripts");
		$this->expiry 	= intval(sketch("cacheseconds")); 
		$this->filejs	= ((sketch("mobile")==false)? "general" : "scriptssmobile"). @$_REQUEST['v'].((adminCheck())? 'admin':'');
		if(getSettings("compress")){
			ob_start("ob_gzhandler");								// G-zips the page
		}
		helper("minifyjs");
		$this->loadScripts();
	}
	function getPluginScripts($dir) {
		$alljs = "";
		if(is_dir($dir)){
		$files = scandir($dir);
		    if($files && is_array($files)){
			    foreach($files as $key => $value){
				    if(!preg_match('~^\.~', $value) && strpos($value,"ie.js")===false && strpos($value,"._")===false && strpos($value,".js")!==false && strpos($value,"mobile.js")===false){
						if(strpos($value,"jq")==false){
							if(getSettings('cache')==false){
								$alljs .= file_get_contents(rtrim($dir,"/")."/".$value);
							}else{
								$alljs .= JSMin::minify(file_get_contents(rtrim($dir,"/")."/".$value));
							}
						}else{
							if(getSettings('cache')==false){
								$alljs .= str_replace("$","jQuery",file_get_contents(rtrim($dir,"/")."/".$value));
							}else{
								$alljs .= JSMin::minify(str_replace("$","jQuery",file_get_contents(rtrim($dir,"/")."/".$value)));
							}
						}
				    }
			    }
		    }
		}
		return $alljs;
	}
	function loadScripts(){
		helper("cache");
		$cache = CACHECLASS::cache($this->filejs,$this->expiry);
		if(!$cache->start()){
			$js = "";
			if(getSettings('googleapi')==false){
				$js .= file_get_contents(sketch("sketchPath")."core".sketch("slash")."scripts".sketch("slash")."mootools-core-1.4.5-full-nocompat-yc.js");
			}
			if(sketch("mobile")===false){
			    $js .= file_get_contents(sketch("sketchPath")."core".sketch("slash")."scripts".sketch("slash")."mootools-more-1.4.0.1.js");
			    $js .= $this->getPluginScripts(sketch("sketchPath")."plugins".sketch("slash")."general".sketch("slash"));
			    $js .= $this->getPluginScripts(sketch("abspath").sketch("user_theme_path")."views".sketch("slash")."scripts".sketch("slash"));
			    if(adminCheck()){
				    $js .= $this->getPluginScripts(sketch("sketchPath")."plugins".sketch("slash")."admin".sketch("slash"));
			    }
			}else{
			    $js .= $this->getPluginScripts(sketch("abspath").sketch("user_theme_path")."views".sketch("slash")."mobile".sketch("slash")."scripts".sketch("slash"));
			}
			echo $js;
			if(getSettings("version") > 2){
				$r = getData("template","*","template_type='javascript'");
				while($r->advance()){
					echo $r->template_content;
				}
			}
			$cache->end();
		}
	}
}