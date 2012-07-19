<?php
/* sketch
Created By: Kevin Dibble
Date First Created: 2009
Date Updated: 21 Nov 2011

Sketch is the core class to load to manage the site
*/
class sketch {
	private $themepath = 'theme';
	private $user_theme_path = '';
	private $db = false;
	public  $plugins = array( );		// An Array To store the Plugins Installed
	private $database = '';
	public  $page_id = 0;
	private $abspath = '';
	private $sketchPath = '';
	private $page = '';
	public  $pageElements = array( );
	public  $nativePage = true;
	private $settings = array( );
	private $siteid = 1;
	private $isAdminLoggedIn = false;
	private $siteurl = '';
	public  $pageAreas = array( );
	public  $pageHasLoaded = array( );
	public  $slash = "/";
	private $queryAmounts = array( );
	private $pluginArgs = array( );
	private $helpers = array( );
	private $updateTime = "";
	private $startTime = 0;
	private $controller = array( );
	private $models = array( );
	private $errorMessage = "";
	public  $cacheseconds = 31536000;
	public  $mobile = false;
	public  $iis = false;
	public  $imagePage = false;
	public  $savedurlPath = "";
	function __construct( ) {
		$start = microtime( true );
		global $site_settings, $_SERVER, $_REQUEST;
		if ( !isset( $_SERVER[ 'REQUEST_URI' ] ) || $site_settings[ 'htaccess' ] == false ) {
			if ( !isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
				$_SERVER[ 'REQUEST_URI' ] = @$_SERVER[ 'ORIG_PATH_INFO' ];
			} //!isset( $_SERVER[ 'REQUEST_URI' ] )
			if ( trim( ini_get( 'date.timezone' ) ) == "" ) {
				date_default_timezone_set( 'Pacific/Auckland' );
			} //trim( ini_get( 'date.timezone' ) ) == ""
			$this->iis = true;
		} //!isset( $_SERVER[ 'REQUEST_URI' ] ) || $site_settings[ 'htaccess' ] == false
		$this->settings = $site_settings;
		if ( strpos( dirname( __FILE__ ), "\\" ) !== false ) {
			$this->slash = "\\";
		} //strpos( dirname( __FILE__ ), "\\" ) !== false
		$this->abspath    = SITEROOT . $this->slash;
		$this->sketchPath = ( isset( $site_settings[ 'PathTosketch' ] ) && $site_settings[ 'PathTosketch' ] != '' ) ? rtrim( $site_settings[ 'PathTosketch' ], $this->slash ) . $this->slash . 'sketch-system' . $this->slash : SITEROOT . $this->slash . 'sketch-system' . $this->slash;
		if ( is_file( $this->abspath . "sketch-admin" . $this->slash . "install.php" ) ) {
			$this->loadError( "notinstalled" );
			exit( );
		} //is_file( $this->abspath . "sketch-admin" . $this->slash . "install.php" )
		else {
			list( $thisRequest,  ) = explode( "?", $_SERVER[ 'REQUEST_URI' ] );
			if ( strpos( $thisRequest, ".jpg" ) !== false || strpos( $thisRequest, ".gif" ) !== false || strpos( $thisRequest, ".png" ) !== false || strpos( $thisRequest, ".htc" ) !== false ) {
				$this->imagePage = true;
			} //strpos( $thisRequest, ".jpg" ) !== false || strpos( $thisRequest, ".gif" ) !== false || strpos( $thisRequest, ".png" ) !== false || strpos( $thisRequest, ".htc" ) !== false
			else {
				$this->startTime = microtime( true );
				$this->loadHelper( "database" );
				$this->updateTime = @date( "U" );
				$start            = microtime( true );
				$this->db         = dbconnect( $this->settings[ 'hostname' ], $this->settings[ 'database' ], $this->settings[ 'username' ], $this->settings[ 'password' ], $this->settings[ 'dbtype' ] );
				$this->database   = $this->settings[ 'database' ];
				$this->setQueryData( $start, "Database Connection time" );
			}
		}
	}
	function updateSetting( $item, $value ) {
		$this->settings[ $item ] = $value;
	}
	function checkSettings( $item ) {
		return isset( $this->settings[ $item ] ) ? $this->settings[ $item ] : false;
	}
	public function start( ) {
		global $_REQUEST, $_SERVER;
		$this->loadHelper( "sketchapi" );
		if ( isset( $_REQUEST[ 'logout' ] ) ) {
			session_destroy();
			header( "Location: " . urlPath() );
			exit( );
		} //isset( $_REQUEST[ 'logout' ] )
		if ( sketch( "imagePage" ) ) {
			list( $page,  ) = explode( "?", end( explode( "/", $_SERVER[ 'REQUEST_URI' ] ) ) );
			$fn = sketch( "sketchPath" ) . "plugins" . sketch( "slash" ) . "general" . sketch( "slash" ) . "images" . sketch( "slash" ) . $page;
			if ( !is_file( $fn ) ) {
				$fn = sketch( "sketchPath" ) . "plugins" . sketch( "slash" ) . "admin" . sketch( "slash" ) . "images" . sketch( "slash" ) . $page;
			} //!is_file( $fn )
			if ( !is_file( $fn ) ) {
				header( "HTTP/1.0 404 Not Found" );
				exit( );
			} //!is_file( $fn )
			if ( stripos( $page, ".htc" ) !== false ) {
				if ( strpos( $_SERVER[ 'HTTP_USER_AGENT' ], "MSIE 9" ) !== false ) {
					header( 'Content-type: text/x-component' );
					echo "";
					exit( );
				} //strpos( $_SERVER[ 'HTTP_USER_AGENT' ], "MSIE 9" ) !== false
				else {
					header( 'Content-type: text/x-component' );
					$this->outputImageHeaders( $fn );
				}
			} //stripos( $page, ".htc" ) !== false
			if ( stripos( $page, ".png" ) !== false ) {
				header( 'Content-Type: image/png' );
				$this->outputImageHeaders( $fn );
			} //stripos( $page, ".png" ) !== false
			if ( stripos( $page, ".gif" ) !== false ) {
				header( 'Content-Type: image/gif' );
				$this->outputImageHeaders( $fn );
			} //stripos( $page, ".gif" ) !== false
			if ( stripos( $page, ".jpg" ) !== false ) {
				header( 'Content-Type: image/jpeg' );
				$this->outputImageHeaders( $fn );
			} //stripos( $page, ".jpg" ) !== false
		} //sketch( "imagePage" )
		else {
			$this->adminCheck();
			$this->getSettings();
			$this->loadHelper( "mobile" );
			$this->mobile = mobileCheck();
			if ( isset( $_REQUEST[ 'v' ] ) && is_numeric( $_REQUEST[ 'v' ] ) ) {
				$this->loadController( $_SERVER[ 'REQUEST_URI' ] );
				if ( isset( $_REQUEST[ 'benchmark' ] ) ) {
					helper( "benchmark" );
					showBenchmark();
				} //isset( $_REQUEST[ 'benchmark' ] )
			} //isset( $_REQUEST[ 'v' ] ) && is_numeric( $_REQUEST[ 'v' ] )
			else {
				$this->getPageID( $_SERVER[ 'REQUEST_URI' ] );
				$this->outputHeaders();
				$this->loadController( "plugin" );
				if ( getSettings( "compress" ) ) {
					ob_start( "ob_gzhandler" );
				} //getSettings( "compress" )
				if ( $this->page_cache == 1 && !adminCheck() && !isset( $_REQUEST[ 'admin' ] ) && !isset( $_REQUEST[ 'adminlogin' ] ) && !$this->mobile ) {
					helper( "cache" );
					$cache = CACHECLASS::cache( $this->pagefile . $this->page_id . $this->page_updated );
					if ( !$cache->start() ) {
						$this->getPage();
						$cache->end();
					} //!$cache->start()
				} //$this->page_cache == 1 && !adminCheck() && !isset( $_REQUEST[ 'admin' ] ) && !isset( $_REQUEST[ 'adminlogin' ] ) && !$this->mobile
				else {
					$this->getPage();
				}
				if ( isset( $_REQUEST[ 'benchmark' ] ) ) {
					helper( "benchmark" );
					showBenchmark();
				} //isset( $_REQUEST[ 'benchmark' ] )
				if ( getSettings( "compress" ) ) {
					ob_end_flush();
				} //getSettings( "compress" )
			}
		}
	}
	function outputImageHeaders( $fn ) {
		global $_REQUEST;
		header( 'Vary: Accept-Encoding' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $fn ) ) . ' GMT' );
		header( "Expires: " . gmdate( "D, d M Y H:i:s", ( time() + $this->cacheseconds ) ) . " GMT" );
		header( "Cache-Control: max-age=" . $this->cacheseconds . ", must-revalidate" );
		if ( isset( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) && ( strtotime( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) == filemtime( $fn ) ) ) {
			header( 'HTTP/1.1 304 Not Modified' );
			exit( );
		} //isset( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) && ( strtotime( $_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ] ) == filemtime( $fn ) )
	}
	function setQueryData( $started, $sql = "" ) {
		if ( $sql != "" ) {
			$this->queryAmounts[ ] = array(
				 $sql,
				$started,
				microtime( true ) 
			);
		} //$sql != ""
	}
	function adminCheck( ) {
		global $_SESSION;
		$this->isAdminLoggedIn = false;
		if ( isset( $_SESSION[ 'admin' ] ) && isset( $_SESSION[ 'lock' ] ) && $_SESSION[ 'lock' ] == md5( md5( $_SERVER[ 'HTTP_USER_AGENT' ] ) . md5( $_SERVER[ 'REMOTE_ADDR' ] ) ) ) {
			$this->isAdminLoggedIn = true;
		} //isset( $_SESSION[ 'admin' ] ) && isset( $_SESSION[ 'lock' ] ) && $_SESSION[ 'lock' ] == md5( md5( $_SERVER[ 'HTTP_USER_AGENT' ] ) . md5( $_SERVER[ 'REMOTE_ADDR' ] ) )
		return $this->isAdminLoggedIn;
	}
	function __set( $item, $value ) {
		$this->$item = $value;
	}
	function __get( $item ) {
		if ( isset( $this->pageElements[ $item ] ) ) {
			if ( is_numeric( $this->pageElements[ $item ] ) ) {
				return $this->pageElements[ $item ];
			} //is_numeric( $this->pageElements[ $item ] )
			else {
				if ( is_array( $this->pageElements[ $item ] ) ) {
					return $this->pageElements[ $item ];
				} //is_array( $this->pageElements[ $item ] )
				else {
					return stripslashes( trim( $this->pageElements[ $item ] ) );
				}
			}
		} //isset( $this->pageElements[ $item ] )
		else {
			if ( isset( $this->$item ) ) {
				return $this->$item;
			}
		}
	}
	function urlPath( $url = "" ) {
		global $_SERVER;
		if ( $this->saveURLPath == "" ) {
			$ww = "";
			if ( strpos( $_SERVER[ 'HTTP_HOST' ], "www" ) === false && $this->settings[ 'www' ] == true ) {
				$ww = "www.";
			} //strpos( $_SERVER[ 'HTTP_HOST' ], "www" ) === false && $this->settings[ 'www' ] == true
			$tmp               = str_replace( "//", "/", str_replace( "\\", "/", $ww . ltrim( rtrim( $_SERVER[ 'HTTP_HOST' ] . "/" . str_replace( array(
				 $_SERVER[ 'HTTP_HOST' ],
				"http://",
				"www." 
			), "", $this->isLocal() ) . $this->isIIS(), "/" ), "/" ) ) );
			$this->saveURLPath = $tmp . "/";
		} //$this->saveURLPath == ""
		$url = $this->saveURLPath . $url;
		if ( strpos( $url, "sketch-images" ) !== false || strpos( $url, "sketch-files" ) !== false ) {
			$url = str_replace( "/index.php", "", $url );
		} //strpos( $url, "sketch-images" ) !== false || strpos( $url, "sketch-files" ) !== false
		return $url;
	}
	function isIIS( ) {
		return ( $this->iis ) ? "index.php/" : "";
	}
	function isLocal( ) {
		if ( stripos( $_SERVER[ 'HTTP_HOST' ], "localhost" ) !== false ) {
			return ( isset( $this->settings[ 'ignore' ] ) ) ? trim( $this->settings[ 'ignore' ] ) . "/" : '';
		} //stripos( $_SERVER[ 'HTTP_HOST' ], "localhost" ) !== false
		else {
			return str_replace( $this->settings[ 'remove_from_path' ], "", $this->settings[ 'dbsettings' ][ 'main_site_url' ] );
		}
	}
	function getDirectory( $dir ) {
		$path  = scandir( $dir );
		$files = array( );
		if ( $path ) {
			natcasesort( $path );
			foreach ( $path as $key => $value ) {
				if ( $value != ".." && $value != "." ) {
					$files[ $key ] = $value;
				} //$value != ".." && $value != "."
			} //$path as $key => $value
		} //$path
		return $files;
	}
	function getSettings( ) {
		$r       = false;
		$isfound = false;
		if ( $this->db ) {
			if ( $this->settings[ 'directory' ] == false ) {
				$host = ( $this->settings[ 'directory' ] == false ) ? $_SERVER[ 'HTTP_HOST' ] : '';
				list( $uri,  ) = explode( "?", $_SERVER[ 'REQUEST_URI' ] );
				list( $site, $dir,  ) = @explode( "/", @end( @explode( "//", $host . $uri ) ) );
				$site = str_replace( "www.", "", ( ( $this->settings[ 'directory' ] ) ? $site . "/" . $dir : $site ) );
				$site = str_ireplace( $this->settings[ 'ignore' ], "", $site );
			} //$this->settings[ 'directory' ] == false
			else {
				list( $uri,  ) = explode( "?",  str_ireplace( $this->settings[ 'ignore' ], "",$_SERVER[ 'REQUEST_URI' ] ) );
				$site = @explode( "/", ltrim($uri,"/") );
				$site = $site[0];
			}
			$SQL = "SELECT * FROM " . $this->settings[ 'prefix' ] . "sketch_settings WHERE (main_site_url LIKE :site) LIMIT 1";
			$r   = ACTIVERECORD::keeprecord( $SQL, array(
				 ":site" => "%" . $site ."/" 
			) );
			if ( $r->advance() ) {
				$isfound = true;
				$this->settings[ 'ignore' ] =  $this->settings[ 'directory' ] == true ? $r->main_site_url : $this->settings[ 'ignore' ];
			} //$r->advance()
			else {
				$SQL = "SELECT * FROM " . $this->settings[ 'prefix' ] . "sketch_settings WHERE sketch_settings_id=1 LIMIT 1";
				$r   = ACTIVERECORD::keeprecord( $SQL, array(
					 ":site" => "%" . $site 
				) );
				if ( $r->advance() ) {
					$isfound = true;
				} //$r->advance()
			}
			if ( $isfound ) {
				if ( $this->settings[ 'directory' ] == true && $this->settings[ 'ignore' ] == "" ) {
					$this->settings[ 'ignore' ] = end( explode( "/", trim( $r->result[ 'main_site_url' ], "/" ) ) );
				} //$this->settings[ 'directory' ] == true && $this->settings[ 'ignore' ] == ""
				$settings_row                   = $r->result;
				$this->settings[ 'dbsettings' ] = $settings_row;
				$this->cacheseconds             = $settings_row[ 'cache_seconds' ];
				$this->siteid                   = $settings_row[ 'sketch_settings_id' ];
				$this->siteurl                  = "http://" . $_SERVER[ 'HTTP_HOST' ] . $site;
				$this->user_theme_path          = ( trim( stripslashes( $settings_row[ 'theme_path' ] ) ) !== '' ) ? trim( stripslashes( $settings_row[ 'theme_path' ] ) ) . $this->slash : $this->themepath . $this->slash;
				$this->themepath                = $this->user_theme_path;
				$r->free();
				$r = true;
			} //$isfound
			else {
				$r->free();
				$r = false;
			}
		} //$this->db
		if ( !$r ) {
			$this->db              = false;
			$this->user_theme_path = ( ( isset( $this->settings[ 'themePath' ] ) && $this->settings[ 'themePath' ] != '' ) ? $this->settings[ 'themePath' ] : $this->themepath ) . $this->slash;
			$this->themepath       = ( ( isset( $this->settings[ 'themePath' ] ) && $this->settings[ 'themePath' ] != '' ) ? $this->settings[ 'themePath' ] : $this->themepath ) . $this->slash;
		} //!$r
	}
	function outputHeaders( ) {
		global $_REQUEST, $_SERVER;
		if ( stripos( $_SERVER[ 'REQUEST_URI' ], "%3Cscript" ) !== false ) {
			header( "HTTP/1.1 301 Moved Permanently" );
			header( "Location: http://" . $_SERVER[ 'HTTP_HOST' ] );
			exit( );
		} //stripos( $_SERVER[ 'REQUEST_URI' ], "%3Cscript" ) !== false
		header( 'Vary: Accept-Encoding' );
		if ( @$this->pageElements[ 'page_updated' ] != '' ) {
			list( $y, $m, $d ) = explode( "-", $this->pageElements[ 'page_updated' ] );
			$d    = explode( " ", $d );
			$rest = ( !isset( $d[ 1 ] ) ) ? date( "H:i:s" ) : $d[ 1 ];
			$d    = $d[ 0 ];
			list( $h, $i, $s ) = explode( ":", $rest );
			header( 'Content-Type: text/html; charset=utf-8' );
			header( 'Last-Modified: ' . @gmdate( 'D, d M Y H:i:s', mktime( $h, $i, $s, $m, $d, $y ) ) . ' GMT' );
			header( 'Expires: ' . @gmdate( 'D, d M Y H:i:s', mktime( $h - 1, $i, $s, $m, $d, $y ) ) . ' GMT' );
			header( "Cache-Control: max-age=0, no-store, no-cache, private, must-revalidate" );
		} //@$this->pageElements[ 'page_updated' ] != ''
		else {
			header( "Expires: " . gmdate( "D, d M Y H:i:s", ( time() - 3600 ) ) . " GMT" );
			header( "Cache-Control: max-age=" . $this->cacheseconds . ", private, must-revalidate" );
		}
	}
	function superUser( ) {
		if ( isset( $_SESSION[ 'admin' ][ 'is_super' ] ) && $_SESSION[ 'admin' ][ 'is_super' ] == 1 ) {
			return true;
		} //isset( $_SESSION[ 'admin' ][ 'is_super' ] ) && $_SESSION[ 'admin' ][ 'is_super' ] == 1
	}
	function getPageID( $path ) {
		global $_REQUEST;
		if ( isset( $_REQUEST[ 'page_id' ] ) ) {
			$this->page_id = intval( $_REQUEST[ 'page_id' ] );
		} //isset( $_REQUEST[ 'page_id' ] )
		list( $path,  ) = explode( "?", trim( stripslashes( $path ) ) );
		$path       = str_replace( array(
			 ".php",
			".asp",
			".apsx",
			".cfm",
			".html",
			"index" 
		), array(
			 "",
			"",
			"",
			"",
			"",
			"" 
		), $path );
		$path       = trim( end( explode( $_SERVER[ 'HTTP_HOST' ], $path ) ) );
		$path       = str_ireplace( $this->settings[ 'ignore' ], "", $path );
		$path       = ltrim( rtrim( $path, "/" ), "/" );
		$this->page = trim( $path ) . ".php";
		if ( $this->db ) {
			if ( $this->page_id < 1 ) {
				$SQL = "SELECT page_id FROM " . $this->settings[ 'prefix' ] . "sketch_menu WHERE menu_guid LIKE :path AND sketch_settings_id='" . intval( $this->settings[ 'dbsettings' ][ 'sketch_settings_id' ] ) . "' AND menu_under <> 25";
				$r   = ACTIVERECORD::keeprecord( $SQL, array(
					 ":path" => "%" . $path 
				) );
				if ( $r->rowCount() > 0 ) {
					$r->advance();
					$this->page_id = ( intval( $r->page_id ) > 0 ) ? intval( $r->page_id ) : $this->page_id;
					$r->free();
				} //$r->rowCount() > 0
				else {
					$this->nativePage = false;
				}
			} //$this->page_id < 1
			$SQL = "SELECT * FROM " . $this->settings[ 'prefix' ] . "sketch_page," . $this->settings[ 'prefix' ] . "sketch_menu WHERE " . $this->settings[ 'prefix' ] . "sketch_page.page_id=" . $this->settings[ 'prefix' ] . "sketch_menu.page_id AND " . $this->settings[ 'prefix' ] . "sketch_page.page_id =:page_id LIMIT 1";
			$r   = ACTIVERECORD::keeprecord( $SQL, array(
				 ":page_id" => $this->page_id 
			) );
			$r->advance();
			$this->pageElements = $r->result;
			$r->free();
			if ( isset( $_REQUEST[ 'checking' ] ) || isset( $_REQUEST[ 'preview' ] ) ) {
				$this->pageElements[ 'content' ] = $this->pageElements[ 'edit' ];
				unset( $this->pageElements[ 'edit' ] );
			} //isset( $_REQUEST[ 'checking' ] ) || isset( $_REQUEST[ 'preview' ] )
			$tempContent = contentToArray( $this->pageElements[ 'content' ] );
			if ( is_array( $tempContent ) ) {
				unset( $this->pageElements[ 'content' ] );
				if ( isset( $tempContent[ 'edit' ] ) ) {
					$tempContent[ 'content' ] = $tempContent[ 'edit' ];
				} //isset( $tempContent[ 'edit' ] )
				$this->pageElements = array_merge( $this->pageElements, $tempContent );
				unset( $tempContent );
			} //is_array( $tempContent )
			if ( !isset( $_REQUEST[ 'preview' ] ) ) {
				$adminq = ( $this->isAdminLoggedIn ) ? " OR (is_admin=1 AND activated=1)" : " AND is_admin<>1 ";
				$SQL    = "SELECT global_location,`name`,plugin_id,php,admin_order " . "FROM " . $this->settings[ 'prefix' ] . "plugin WHERE " . $this->settings[ 'prefix' ] . "plugin.activated=1 " . $adminq . "ORDER BY site_order";
				$r      = ACTIVERECORD::keeprecord( $SQL );
				while ( $r->advance() ) {
					if ( $r->php == 1 && is_file( $this->sketchPath . "plugins/" . $r->name . $this->slash . $r->name . ".php" ) ) {
						$this->pluginArgs[ strtolower( $r->name ) ] = $r->result;
					} //$r->php == 1 && is_file( $this->sketchPath . "plugins/" . $r->name . $this->slash . $r->name . ".php" )
					else {
						if ( $r->php == 1 && is_file( $this->abspath . $this->themepath . "plugins" . $this->slash . $r->name . "/" . $r->name . ".php" ) ) {
							$this->pluginArgs[ strtolower( $r->name ) ] = $r->result;
						} //$r->php == 1 && is_file( $this->abspath . $this->themepath . "plugins" . $this->slash . $r->name . "/" . $r->name . ".php" )
					}
				} //$r->advance()
				$r->free();
				list( $y, $m, $d ) = explode( "-", $this->settings[ 'dbsettings' ][ 'global_update' ] );
				$d                = explode( " ", $d );
				$rest             = ( !isset( $d[ 1 ] ) ) ? date( "H:i:s" ) : $d[ 1 ];
				$d                = $d[ 0 ];
				$this->updateTime = date( "U", mktime( 0, 0, 0, $m, $d, $y ) );
			} //!isset( $_REQUEST[ 'preview' ] )
		} //$this->db
	}
	function getModel( $name ) {
		if ( isset( $this->models[ trim( strtoupper( $name ) ) ] ) ) {
			return $this->models[ trim( strtoupper( $name ) ) ];
		} //isset( $this->models[ trim( strtoupper( $name ) ) ] )
		else {
			$this->loadModel( $name );
			return $this->models[ trim( strtoupper( $name ) ) ];
		}
	}
	function getPage( ) {
		if ( strpos( $_SERVER[ 'REQUEST_URI' ], "/admin/" ) !== false ) {
			$this->loadController( "sketchadmin" );
		} //strpos( $_SERVER[ 'REQUEST_URI' ], "/admin/" ) !== false
		else {
			$this->page = end( explode( "/", $this->page ) );
			if ( $this->nativePage ) {
				@list( $y, $m, $d ) = explode( "-", $this->page_date );
				@list( $ey, $em, $ed ) = explode( "-", $this->page_expiry );
				$expiry = false;
				if ( !$this->adminCheck() ) {
					if ( intval( $ey ) > 0 ) {
						$expiry = date( "U", mktime( 0, 0, 0, $em, $ed, $ey ) ) < date( "U" ) ? true : false;
					} //intval( $ey ) > 0
					if ( $this->page_status == "hidden" || @date( "U", mktime( 0, 0, 0, $m, $d, $y ) ) > date( "U" ) || $this->page_status == "member" || $expiry ) {
						if ( $this->adminCheck() == false && $this->page_status == "hidden" || date( "U", mktime( 0, 0, 0, $m, $d, $y ) ) > date( "U" ) || $expiry ) {
							header( "location: " . urlPath() );
							die( );
						} //$this->adminCheck() == false && $this->page_status == "hidden" || date( "U", mktime( 0, 0, 0, $m, $d, $y ) ) > date( "U" ) || $expiry
						else {
							helper( "member" );
							if ( !memberid() && !adminCheck() ) {
								header( "location: " . urlPath() );
								die( );
							} //!memberid() && !adminCheck()
						}
					} //$this->page_status == "hidden" || @date( "U", mktime( 0, 0, 0, $m, $d, $y ) ) > date( "U" ) || $this->page_status == "member" || $expiry
				} //!$this->adminCheck()
			} //$this->nativePage
			if ( isset( $this->pageElements[ 'pagefile' ] ) && trim( $this->pageElements[ 'pagefile' ] ) != "" && $this->nativePage ) {
				$this->page = stripslashes( $this->pageElements[ 'pagefile' ] );
			} //isset( $this->pageElements[ 'pagefile' ] ) && trim( $this->pageElements[ 'pagefile' ] ) != "" && $this->nativePage
			$this->loadController( $this->page );
		}
	}
	function loadView( $view = "index", $return = false, $path = false ) {
		list( $view,  ) = explode( "?", $view );
		$view = str_replace( ".php", "", $view );
		$view = ( $view == "" ) ? "index" : $view;
		$r = getData("template","*","template_type <> 'form' AND template_type <> 'css' AND template_type <> 'javascript' AND template_name=".sketch("db")->quote($view));
		if($r->rowCount()==0){
			$file = $this->abspath . $this->themepath . "views" . $this->slash . $view . ".php";
			if ( !is_file( $file ) ) {
				$file = $this->abspath . $this->themepath . "override" . $this->slash . $view . ".php";
			} //!is_file( $file )
			if ( !is_file( $file ) ) {
				$file = $this->sketchPath . "helpers" . $this->slash . "outputs" . $this->slash . $view . ".php";
			} //!is_file( $file )
			if ( is_file( $file ) ) {
				if ( $return == true ) {
					return file_get_contents( $file );
				} //$return == true
				else{
					if ( $path == true ) {
						return $file;
					} //$return == true
					else {
						include_once( $file );
					}
				}
			} //is_file( $file )
			else {
				$this->loadError( "404" );
			}
		}else{
			$r->advance();
			if(!is_file(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php")){
				file_put_contents(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php", str_replace(array("endphp","phpstart"),array(' ?>',' <?php '),$r->template_content));
			}
			$file = sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php";
			if ( $return == true ) {
				return file_get_contents( $file );
			}else{
				if ( $path == true ) {
					return $file;
				} //$return == true
				else {
					include_once( $file );
				}
			}
		}	
	}
	function loadScript( $script = "", $return = false ) {
		$script = trim( str_replace( ".js", "", $script ) );
		$script = ( $script == "" ) ? "index" : $script;
		$file   = $this->abspath . $this->user_theme_path . "views" . $this->slash . "scripts" . $this->slash . $script . ".js";
		if ( is_file( $file ) ) {
			if ( $return == true ) {
				return file_get_contents( $file );
			} //$return == true
			else {
				include_once( $file );
			}
		} //is_file( $file )
	}
	function loadForm( $view = "index", $return = true ) {
		list( $view,  ) = explode( "?", $view );
		$view = str_replace( ".php", "", $view );
		$view = ( $view == "" ) ? "index" : $view;
		$r = getData("template","*","template_type = 'form' AND template_type <> 'css' AND template_type <> 'javascript' AND template_name=".sketch("db")->quote($view));
		if($r->rowCount()==0){
			$file = $this->abspath . $this->themepath . "views" . $this->slash . "forms" . $this->slash . $view . ".php";
			if ( !is_file( $file ) ) {
				$file = $this->sketchPath . "helpers" . $this->slash . "forms" . $this->slash . $view . ".php";
			} //!is_file( $file )
			if ( is_file( $file ) ) {
				if ( $return == true ) {
					return file_get_contents( $file );
				} //$return == true
				else {
					return $file;
				}
			} //is_file( $file )
		}else{
			$r->advance();
			if(!is_file(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php")){
				file_put_contents(sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php", str_replace(array("endphp","phpstart"),array(' ?>',' <?php '),$r->template_content));
			}
			$file = sketch( "abspath" ) . sketch( "themepath" ) . "cache" . sketch( "slash" ) .$r->template_id.".php";
			if ( $return == true ) {
				return file_get_contents( $file );
			}else{
				return $file;
			}
		}
	}
	function loadModel( $model = "index" ) {
		$model = trim( str_replace( ".php", "", $model ) );
		$model = ( $model == "" ) ? "index" : $model;
		$file  = $this->abspath . $this->user_theme_path . "models" . $this->slash . $model . ".php";
		if ( is_file( $file ) ) {
			if ( !class_exists( "MODEL" ) ) {
				include_once( $this->sketchPath . "model" . $this->slash . "index.php" );
			} //!class_exists( "MODEL" )
			$model = strtoupper( trim( $model ) );
			if ( !isset( $this->models[ $model ] ) ) {
				include_once( $file );
				$this->models[ $model ] = new $model( $model );
			} //!isset( $this->models[ $model ] )
		} //is_file( $file )
		else {
			$this->loadError( "404" );
		}
	}
	function loadError( $error = "404" ) {
		@ob_end_clean();
		@ob_start( "ob_gzhandler" );
		switch ( $error ) {
			case "404":
				header( "HTTP/1.0 404 Not Found" );
				$file = $this->user_theme_path . "views" . $this->slash . "errors" . $this->slash . "404.php";
				if ( is_file( $file ) ) {
					include_once( $file );
				} //is_file( $file )
				else {
					$file = $this->sketchPath . "errors" . $this->slash . "404.php";
					if ( is_file( $file ) ) {
						include_once( $file );
					} //is_file( $file )
					else {
						exit( "Page not found" );
					}
				}
				break;
			case "db":
				$file = $this->user_theme_path . "views" . $this->slash . "errors" . $this->slash . "db.php";
				if ( is_file( $file ) ) {
					include_once( $file );
				} //is_file( $file )
				else {
					$file = $this->sketchPath . "errors" . $this->slash . "db.php";
					if ( is_file( $file ) ) {
						include_once( $file );
					} //is_file( $file )
					else {
						exit( "Page not found" );
					}
				}
				break;
			default:
				header( "HTTP/1.1 500 Internal Server Error" );
				$file = $this->user_theme_path . "views" . $this->slash . "errors" . $this->slash . $error . ".php";
				if ( is_file( $file ) ) {
					include_once( $file );
				} //is_file( $file )
				else {
					$file = $this->sketchPath . "errors" . $this->slash . $error . ".php";
					if ( is_file( $file ) ) {
						include_once( $file );
					} //is_file( $file )
					else {
						exit( "Page not found" );
					}
				}
				break;
		} //$error
	}
	function loadController( $controller = "index" ) {
		list( $controller,  ) = explode( "?", end( explode( "/", $controller ) ) );
		$controller = strtolower( trim( str_replace( ".php", "", $controller ) ) );
		$controller = strtolower( ( $controller == "" ) ? "index" : $controller );
		if ( !class_exists( "CONTROLLER" ) ) {
			include_once( $this->sketchPath . "controller" . $this->slash . "controller.php" );
		} //!class_exists( "CONTROLLER" )
		if ( !isset( $this->controller[ $controller ] ) ) {
			$file = $this->abspath . $this->user_theme_path . "controllers" . $this->slash . $controller . ".php";
			if ( !is_file( $file ) ) {
				$file = $this->sketchPath . "controller" . $this->slash . $controller . ".php";
			} //!is_file( $file )
			if ( is_file( $file ) ) {
				include_once( $file );
				$controller                      = strtoupper( trim( $controller ) );
				$this->controller[ $controller ] = new $controller( $this->page );
			} //is_file( $file )
			else {
				if ( strtolower( $controller ) != "index" ) {
					$this->loadController();
				} //strtolower( $controller ) != "index"
				else {
					$this->loadError( "404" );
				}
			}
		} //!isset( $this->controller[ $controller ] )
	}
	function getStylePath( ) {
		if ( $this->isAdminLoggedIn ) {
?>
<link href="<?php
			echo urlPath( "styles?v=" . $this->updateTime );
?>&a=admin<?php
			echo $this->mobile == false ? '' : '&m=';
?>" type="text/css" rel="stylesheet" />
<?php
		} //$this->isAdminLoggedIn
		else {
?>
<link href="<?php
			echo urlPath( "styles?v=" . $this->updateTime );
			echo $this->mobile == false ? '' : '&m=';
?>" type="text/css" rel="stylesheet" />
<?php
		}
	}
	function getScriptPath( ) {
		if ( isset( $this->settings[ 'jquery' ] ) && $this->settings[ 'jquery' ] ) {
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<?php
		} //isset( $this->settings[ 'jquery' ] ) && $this->settings[ 'jquery' ]
		if ( $this->settings[ 'googleapi' ] ) {
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
<?php
		} //$this->settings[ 'googleapi' ]
		if ( $this->isAdminLoggedIn ) {
?>
<script type="text/javascript" src="<?php
			echo urlPath( "scripts" );
?>?a=admin&v=<?php
			echo $this->updateTime;
?>"></script>
<script type="text/javascript" src="<?php
			echo str_replace( "index.php/", "", urlPath( "tiny_mce/tiny_mce_gzip.js" ) );
?>"></script>
<script type="text/javascript">
        tinyMCE_GZ.init({
            plugins : 'safari,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,'+
            'searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
            themes : 'simple,advanced',
            languages : 'en',
            disk_cache : true,
            debug : false
        });
        </script>
<?php
		} //$this->isAdminLoggedIn
		else {
?>
<script type="text/javascript" src="<?php
			echo urlPath( "scripts" );
?>?v=<?php
			echo $this->updateTime;
			echo $this->mobile == false ? '' : '&m=';
?>"></script>
<?php
		}
	}
	function getSiteSettings( $item ) {
		return isset( $this->settings[ 'dbsettings' ][ $item ] ) ? $this->settings[ 'dbsettings' ][ $item ] : false;
	}
	function getImages( $directory = "" ) {
		if ( isset( $this->alldirImages ) && is_array( $this->alldirImages ) ) {
			return $this->alldirImages;
		} //isset( $this->alldirImages ) && is_array( $this->alldirImages )
		else {
			$this->alldirImages = $this->gettheImages( $directory );
			return $this->alldirImages;
		}
	}
	function gettheImages( $directory = '' ) {
		$allImages = array( );
		$directory = str_replace( array(
			 $this->slash,
			$this->slash . $this->slash 
		), $this->slash, $directory );
		if ( $directory == '' || !is_dir( $directory ) ) {
			$directory = "sketch-images" . $this->slash;
		} //$directory == '' || !is_dir( $directory )
		$directory = str_replace( array(
			 $this->slash,
			$this->slash . $this->slash 
		), $this->slash, $directory );
		if ( is_dir( $directory ) && stripos( $directory, "_notes" ) === false ) {
			$direc = scandir( $directory );
			if ( $direc ) {
				natcasesort( $direc );
				foreach ( $direc as $key => $file ) {
					if ( !preg_match( '~^\.~', $file ) ) {
						if ( stripos( $file, ".jpg" ) !== false || stripos( $file, ".gif" ) !== false || stripos( $file, ".png" ) !== false ) {
							$ikey                             = end( explode( "sketch-images" . $this->slash, $directory ) );
							$ikey                             = str_replace( $this->slash . $this->slash, $this->slash, "sketch-images" . $this->slash . $ikey . $this->slash . ltrim( $file, "/" ) );
							$ikey                             = str_replace( $this->slash, "/", $ikey );
							$allImages[ $directory ][ $ikey ] = array( );
							list( $width, $height, $type, $attr ) = getimagesize( $directory . $this->slash . $file );
							$allImages[ $directory ][ $ikey ][ 'file' ]    = $file;
							$allImages[ $directory ][ $ikey ][ 'details' ] = array(
								 "width" => $width,
								"height" => $height,
								"type" => $type,
								"attr" => $attr 
							);
							if(!isset($_GET['outside'])){
								$allImages[ $directory ][ $ikey ][ 'usedon' ]  = array( );
								$SQL                                           = "SELECT menu_name FROM " . $this->settings[ 'prefix' ] . "sketch_page," . $this->settings[ 'prefix' ] . "sketch_menu WHERE " . $this->settings[ 'prefix' ] . "sketch_menu.page_id=" . $this->settings[ 'prefix' ] . "sketch_page.page_id AND (" . $this->settings[ 'prefix' ] . "sketch_page.content LIKE :content OR " . $this->settings[ 'prefix' ] . "sketch_page.edit LIKE :edit)";
								$r                                             = ACTIVERECORD::keeprecord( $SQL, array(
									 ":content" => "%" . $ikey . "%",
									":edit" => "%" . $ikey . "%" 
								) );
								while ( $r->advance() ) {
									$allImages[ $directory ][ $ikey ][ 'usedon' ][ ] = $r->menu_name;
								} //$r->advance()
								$r->free();
							}
						} //stripos( $file, ".jpg" ) !== false || stripos( $file, ".gif" ) !== false || stripos( $file, ".png" ) !== false
						else {
							if ( is_dir( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) ) ) {
								$allImages = array_merge( $allImages, $this->gettheImages( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) ) );
							} //is_dir( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) )
						}
					} //!preg_match( '~^\.~', $file )
				} //$direc as $key => $file
			} //$direc
			if ( count( $allImages ) < 1 ) {
				$ikey                                         = end( explode( "sketch-files" . $this->slash, $directory ) );
				$ikey                                         = str_replace( $this->slash . $this->slash, $this->slash, "sketch-files" . $this->slash . $ikey . $this->slash . ltrim( $file, $this->slash ) );
				$allImages[ $directory ][ $ikey ]             = array( );
				$allImages[ $directory ][ $ikey ][ 'file' ]   = "";
				$allImages[ $directory ][ $ikey ][ 'usedon' ] = array( );
			} //count( $allImages ) < 1
		} //is_dir( $directory ) && stripos( $directory, "_notes" ) === false
		return $allImages;
	}
	function getFiles( $directory = "" ) {
		if ( isset( $this->alldirfiles ) && is_array( $this->alldirfiles ) ) {
			return $this->alldirfiles;
		} //isset( $this->alldirfiles ) && is_array( $this->alldirfiles )
		else {
			$this->alldirfiles = $this->gethetFiles( $directory );
			return $this->alldirfiles;
		}
	}
	function gethetFiles( $directory = '' ) {
		$allImages = array( );
		if ( $directory == '' || !is_dir( $directory ) ) {
			$directory = "sketch-files" . $this->slash;
		} //$directory == '' || !is_dir( $directory )
		if ( is_dir( $directory ) ) {
			$direc = @scandir( $directory );
			if ( $direc ) {
				natcasesort( $direc );
				foreach ( $direc as $key => $file ) {
					if ( !preg_match( '~^\.~', $file ) ) {
						if ( is_file( $directory . $this->slash . $file ) && stripos( $directory . $file, "_notes" ) === false ) {
							$ikey                                         = end( explode( "sketch-files" . $this->slash, $directory ) );
							$ikey                                         = str_replace( $this->slash . $this->slash, $this->slash, "sketch-files" . $this->slash . $ikey . $this->slash . ltrim( $file, $this->slash ) );
							$ikey                                         = str_replace( $this->slash, "/", $ikey );
							$allImages[ $directory ][ $ikey ]             = array( );
							$allImages[ $directory ][ $ikey ][ 'file' ]   = $file;
							$allImages[ $directory ][ $ikey ][ 'usedon' ] = array( );
							$SQL                                          = "SELECT menu_name FROM " . $this->settings[ 'prefix' ] . "sketch_page," . $this->settings[ 'prefix' ] . "sketch_menu WHERE " . $this->settings[ 'prefix' ] . "sketch_menu.page_id=" . $this->settings[ 'prefix' ] . "sketch_page.page_id AND (" . $this->settings[ 'prefix' ] . "sketch_page.content LIKE :content OR " . $this->settings[ 'prefix' ] . "sketch_page.edit LIKE :edit)";
							$r                                            = ACTIVERECORD::keeprecord( $SQL, array(
								 ":content" => "%" . $ikey . "%",
								":edit" => "%" . $ikey . "%" 
							) );
							while ( $r->advance() ) {
								$allImages[ $directory ][ $ikey ][ 'usedon' ][ ] = $r->menu_name;
							} //$r->advance()
							$r->free();
						} //is_file( $directory . $this->slash . $file ) && stripos( $directory . $file, "_notes" ) === false
						else {
							if ( is_dir( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) ) ) {
								$allImages = array_merge( $allImages, $this->gethetFiles( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) ) );
							} //is_dir( rtrim( $directory, $this->slash ) . $this->slash . ltrim( $file, $this->slash ) )
						}
					} //!preg_match( '~^\.~', $file )
				} //$direc as $key => $file
				@closedir( $direc );
			} //$direc
			if ( count( $allImages ) < 1 ) {
				$ikey                                         = end( explode( "sketch-files" . $this->slash, $directory ) );
				$ikey                                         = str_replace( $this->slash . $this->slash, $this->slash, "sketch-files" . $this->slash . $ikey . $this->slash . ltrim( $file, "/" ) );
				$allImages[ $directory ][ $ikey ]             = array( );
				$allImages[ $directory ][ $ikey ][ 'file' ]   = $file;
				$allImages[ $directory ][ $ikey ][ 'usedon' ] = array( );
			} //count( $allImages ) < 1
		} //is_dir( $directory )
		return $allImages;
	}
	function getDirPlugins( ) {
		$allImages = array( );
		$directory = $this->sketchPath . "plugins" . $this->slash;
		$direc     = scandir( $directory );
		if ( $direc && is_array( $direc ) ) {
			foreach ( $direc as $key => $value ) {
				if ( !preg_match( '~^\.~', $value ) && is_dir( $directory . $this->slash . $value ) ) {
					$allImages[ $value ] = $value;
				} //!preg_match( '~^\.~', $value ) && is_dir( $directory . $this->slash . $value )
			} //$direc as $key => $value
		} //$direc && is_array( $direc )
		$directory = $this->abspath . $this->themepath . "plugins" . $this->slash;
		$direc     = scandir( $directory );
		if ( $direc && is_array( $direc ) ) {
			foreach ( $direc as $key => $value ) {
				if ( !preg_match( '~^\.~', $value ) && is_dir( $directory . $this->slash . $value ) ) {
					$allImages[ $value ] = $value;
				} //!preg_match( '~^\.~', $value ) && is_dir( $directory . $this->slash . $value )
			} //$direc as $key => $value
		} //$direc && is_array( $direc )
		return $allImages;
	}
	function loadHelper( $name ) {
		if ( !in_array( $name, $this->helpers ) && is_file( $this->abspath . $this->themepath . $this->slash . "helpers" . $this->slash . $name . ".php" ) ) {
			include_once( $this->abspath . $this->themepath . $this->slash . "helpers" . $this->slash . $name . ".php" );
			$this->helpers[ $name ] = $name;
		} //!in_array( $name, $this->helpers ) && is_file( $this->abspath . $this->themepath . $this->slash . "helpers" . $this->slash . $name . ".php" )
		else {
			if ( !in_array( $name, $this->helpers ) && is_file( $this->sketchPath . "helpers" . $this->slash . $name . $this->slash . $name . ".php" ) ) {
				include_once( $this->sketchPath . "helpers" . $this->slash . $name . $this->slash . $name . ".php" );
				$this->helpers[ $name ] = $name;
			} //!in_array( $name, $this->helpers ) && is_file( $this->sketchPath . "helpers" . $this->slash . $name . $this->slash . $name . ".php" )
			else {
				return false;
			}
		}
	}
	function thisPagePlugins( $plugin ) {
		$this->pageHasLoaded[ $plugin ] = $plugin;
	}
	function checkIfLoaded( $pluginname ) {
		return ( isset( $this->pageHasLoaded[ $pluginname ] ) ) ? true : false;
	}
	function getPlugins( $area ) {
		$this->pageAreas[ $area ] = $area;
		foreach ( $this->pluginArgs as $key => $value ) {
			if ( $value[ 'global_location' ] == $area ) {
				$this->registerPlugin( $value );
				$this->thisPagePlugins( $key );
				$this->plugins[ $key ]->showDisplay( $area );
			} //$value[ 'global_location' ] == $area
		} //$this->pluginArgs as $key => $value
	}
	function getPluginForm( $name ) {
		foreach ( $this->pluginArgs as $key => $value ) {
			if ( $key == strtolower( $name ) ) {
				$this->registerPlugin( $value );
				$this->plugins[ $name ]->showForm();
			} //$key == strtolower( $name )
		} //$this->pluginArgs as $key => $value
	}
	function registerPlugin( $args ) {
		if ( !isset( $this->plugins[ $args[ 'name' ] ] ) ) {
			$plugin = strtoupper( $args[ 'name' ] );
			if ( is_file( $this->abspath . $this->themepath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" ) ) {
				include_once( $this->abspath . $this->themepath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" );
				$this->plugins[ $args[ 'name' ] ] = new $plugin( $args );
			} //is_file( $this->abspath . $this->themepath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" )
			else {
				if ( is_file( $this->sketchPath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" ) ) {
					include_once( $this->sketchPath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" );
					$this->plugins[ $args[ 'name' ] ] = new $plugin( $args );
				} //is_file( $this->sketchPath . "plugins" . $this->slash . $args[ 'name' ] . $this->slash . $args[ 'name' ] . ".php" )
				else {
					return false;
				}
			}
		} //!isset( $this->plugins[ $args[ 'name' ] ] )
	}
	function callPlugin( $name ) {
		foreach ( $this->pluginArgs as $key => $value ) {
			if ( $key == strtolower( $name ) ) {
				$this->registerPlugin( $value );
				$this->thisPagePlugins( $key );
				$this->plugins[ $name ]->showDisplay();
			} //$key == strtolower( $name )
		} //$this->pluginArgs as $key => $value
	}
	function callAdminFilter( $name, $args ) {
		$this->registerPlugin( array(
			 "name" => $name 
		) );
		$this->thisPagePlugins( $name );
		$this->plugins[ $name ]->filter( $args );
	}
	function callPluginFilter( $name, $args ) {
		$name = strtolower( $name );
		foreach ( $this->pluginArgs as $key => $value ) {
			if ( $key == strtolower( $name ) ) {
				$this->registerPlugin( $value );
				$this->thisPagePlugins( $key );
				$this->plugins[ $name ]->filter( $args );
			} //$key == strtolower( $name )
		} //$this->pluginArgs as $key => $value
	}
	function callPluginAdmin( $name ) {
		$this->getPluginForm( $name );
	}
} // END SKETCH CLASS