<?php
class STYLES extends CONTROLLER {
	function STYLES( $page ) {
		global $_REQUEST,$_SERVER;
		header( 'Vary: Accept-Encoding' );
		header( "Content-type: text/css; charset=utf-8" );
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
		parent::__construct( "styles" );
		$this->expiry  = intval( sketch( "cacheseconds" ) );
		$this->filecss = ( ( sketch( "mobile" ) == false ) ? "styles" : "stylesmobile" ) . @$_REQUEST[ 'v' ] . ( ( adminCheck() ) ? 'admin' : '' );
		helper( "minifycss" );
		helper( "cache" );
		if ( getSettings( "compress" ) ) {
			if (extension_loaded('zlib')) { 
				ob_start(); 
				ob_implicit_flush(0);
			}else{
				ob_start( "ob_gzhandler" );
			}
		} //getSettings( "compress" )
		$this->urlP = urlPath();
		$this->loadStyles();
	}
	function getPluginStyles( $dir, $admindir = false ) {
		$css = "";
		if ( is_dir( $dir ) ) {
			$files = scandir( $dir );
			if ( $files && is_array( $files ) ) {
				foreach ( $files as $key => $value ) {
					if ( stripos( $value, ".css" ) !== false && strpos( $value, "._" ) === false && stripos( $value, "cms.css" ) === false && stripos( $value, "edits.css" ) === false && stripos( $value, "mobile.css" ) === false ) {
						$themepath = $this->urlP . sketch( "themepath" );
						$themepath = str_replace( "index.php/", "", $themepath );
						if ( !$admindir ) {
							$themepath .= "views/styles/images/";
							$css .= ( str_replace( array(
								 "iepngfix/",
								"images/",
								"../" 
							), array(
								 str_replace( array(
									 "/index.php/",
									"/index.php",
									"index.php/" 
								), "/", $this->urlP ) . "index.php/iepngfix/",
								$themepath,
								"" 
							), file_get_contents( rtrim( $dir, "/" ) . "/" . $value ) ) );
						} //!$admindir
						else {
							$themepath = urlPath( "sketch-system/plugins" );
							$themepath = str_replace("index.php/","",$themepath);
							if ( strpos( $dir, "admin" ) !== false ) {
								$themepath .= "/admin";
							} //strpos( $dir, "admin" ) !== false
							else {
								$themepath .= "/general";
							}
							$themepath  = explode( "http://", $themepath );
							$themepath = "http://" . end( $themepath );
							$css .= ( str_replace( array("iepngfix/","images/","../" ), array( str_replace( array("/index.php/","/index.php","index.php/"), "/", $this->urlP ) . "index.php/iepngfix/", $themepath . "/images/","" ), file_get_contents( rtrim( $dir, "/" ) . "/" . $value ) ) );
						}
					} //stripos( $value, ".css" ) !== false && strpos( $value, "._" ) === false && stripos( $value, "cms.css" ) === false && stripos( $value, "edits.css" ) === false && stripos( $value, "mobile.css" ) === false
				} //$files as $key => $value
			} //$files && is_array( $files )
		} //is_dir( $dir )
		return $css;
	}
	function loadStyles( ) {
		$cache = CACHECLASS::cache( $this->filecss, $this->expiry );
		if ( !$cache->start() ) {
			$css = '';
			$css .= $this->getPluginStyles( sketch( "sketchPath" ) . "plugins" . sketch( "slash" ) . "general" . sketch( "slash" ), true );
			$css .= $this->getPluginStyles( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "styles" . sketch( "slash" ) );
			if ( adminCheck() ) {
				$css .= $this->getPluginStyles( sketch( "sketchPath" ) . "plugins" . sketch( "slash" ) . "admin" . sketch( "slash" ), true );
			} //adminCheck()
			if ( getSettings( "version" ) > 2 ) {
				$r = getData( "template", "*", "template_type='css'" );
				//die(urlPath(sketch( "themepath" ) . "views" . sketch( "slash" ) . "styles" . sketch( "slash" )));
				while ( $r->advance() ) {
					$css .= str_replace(array("('images/","(images/",'("images/'),
											array(
												"('".urlPath(sketch( "themepath" ) . "views" . sketch( "slash" ) . "styles" . sketch( "slash" ))."images/",
												"(".urlPath(sketch( "themepath" ) . "views" . sketch( "slash" ) . "styles" . sketch( "slash" ))."images/",
												"(\"".urlPath(sketch( "themepath" ) . "views" . sketch( "slash" ) . "styles" . sketch( "slash" ))."images/",
											)
											,$r->template_content);
				} //$r->advance()
			} //getSettings( "version" ) > 2
			$css = str_replace(array("//",sketch( "themepath" )."views"),array("/",sketch( "themepath" )."/views"),$css);
			echo getSettings( 'cache' ) ? CssMin::compress( $css ) : $css;
			$cache->end();
		} //!$cache->start()
	}
}