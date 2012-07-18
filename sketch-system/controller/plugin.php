<?php
class PLUGIN extends CONTROLLER {
	function start( $setup, $rowdata ) {
		global $sketch;
		$this->settings = array_merge( (array) $setup, (array) $rowdata );
		$this->page_id  = sketch( 'page_id' );
		$this->prefix   = $sketch->settings[ 'prefix' ];
		$this->canAdmin = $sketch->isAdminLoggedIn;
		$this->getData();
	}
	function PLUGIN( $name ) {
		parent::__construct( "plugin" );
	}
	public function getAdminclass( ) {
		return @$this->settings[ 'adminclass' ];
	}
	public function get_plugin_id( ) {
		return "";
	}
	public function filter( $args = '' ) {
		$this->display( $args );
	}
	public function getPageDetails( ) {
?>
	<div id="page-details">
	    <div class="content-column">
		<div class="title">Page Name</div>
		<div class="big-font"><?php
		echo sketch( "menu_name" );
?></div>
	    </div>
	    <div class="content-column">
		<div class="title">Page url</div>
		<div class="big-font lgrey"><?php
		echo urlPath( sketch( "menu_guid" ) );
?>/</div>
    </div>
    <div class="content-column">
	<div class="title">Last Updated By</div>
	<div class="big-font lgrey"><?php
		echo sketch( "updated_by" );
?></div>
    </div>
    <div class="content-column">
	<div class="title">Date Updated</div>
	<?php
		list( $y, $m, $d ) = explode( "-", sketch( "page_updated" ) );
		$d = explode( " ", $d );
		if ( isset( $d[ 1 ] ) ) {
			list( $h, $m, $s ) = explode( ":", $d[ 1 ] );
		} else {
			list( $h, $m, $s ) = explode( ":", "0:0:0" );
		}
		$d = $d[ 0 ];
?>
		<div class="big-font lgrey"><?php
		echo date( "D j M Y, h:i:s", mktime( $h, $m, $s, $m, $d, $y ) );
?></div>
	    </div>
	    <div class="clb"></div>
	</div>
<?php
	}
	public function checkAdmin( ) {
		return $this->doAdmin;
	}
	public function getSection( ) {
		return ( isset( $this->settings[ 'pluginsection' ] ) ) ? $this->settings[ 'pluginsection' ] : $this->settings[ 'name' ];
	}
	public function menuName( $n = '' ) {
		return ( isset( $this->settings[ 'menu_name' ] ) && trim( $this->settings[ 'menu_name' ] ) != "" ) ? $this->settings[ 'menu_name' ] : ( ( isset( $this->settings[ 'menuName' ] ) && trim( $this->settings[ 'menuName' ] ) != "" ) ? $this->settings[ 'menuName' ] : $n );
	}
	public function isArea( $area ) {
		if ( isset( $this->settings[ 'global_location' ] ) && trim( $this->settings[ 'global_location' ] ) != '' ) {
			$this->settings[ 'area' ]     = $this->settings[ 'global_location' ];
			$this->settings[ 'location' ] = $this->settings[ 'global_location' ];
		} else {
			$this->settings[ 'global_location' ] = $this->settings[ 'area' ];
			$this->settings[ 'location' ]        = $this->settings[ 'area' ];
		}
		return ( ( isset( $this->settings[ 'global_location' ] ) && $this->settings[ 'global_location' ] == $area ) ) ? true : false;
	}
	public function getOrder( ) {
		return intval( @$this->settings[ 'admin_order' ] );
	}
	public function showIfAdmin( ) {
		return ( isset( $this->settings[ 'admin' ] ) || ( isset( $this->settings[ 'is_admin' ] ) && $this->settings[ 'is_admin' ] == 1 ) ) ? true : false;
	}
	public function isSuper( ) {
		return ( ( isset( $this->settings[ 'isSuper' ] ) && $this->settings[ 'isSuper' ] == true ) || ( isset( $this->settings[ 'is_super_admin' ] ) && $this->settings[ 'is_super_admin' ] == 1 ) ) ? true : false;
	}
	public function superUser( ) {
		global $_SESSION;
		if ( isset( $_SESSION[ 'admin' ][ 'is_super' ] ) && $_SESSION[ 'admin' ][ 'is_super' ] == 1 ) {
			return false;
		} else {
			return $this->isSuper();
		}
	}
	private function getData( ) {
		global $sketch, $_REQUEST;
		$holdname = $this->settings[ 'name' ];
		$SQL      = "SELECT * FROM " . $this->prefix . "plugin WHERE `name`=" . sketch( "db" )->quote( $this->settings[ 'name' ] ) . " LIMIT 1";
		$r        = ACTIVERECORD::keeprecord( $SQL );
		$r->advance();
		if ( $r->rowCount() > 0 ) {
			$r->content = contentToArray( $r->content );
			$r->edit    = contentToArray( $r->edit );
		}
		$this->settings           = array_merge( $this->settings, array_merge( (array) $r->result, (array) @$this->settings[ 'content' ] ) );
		$this->settings[ 'name' ] = $holdname;
		$r->free();
	}
	public function e( $i, $def = '' ) {
		if ( isset( $this->settings[ 'content' ][ $i ] ) ) {
			if ( is_array( $this->settings[ 'content' ][ $i ] ) ) {
				return $this->settings[ 'content' ][ $i ];
			} else {
				return str_replace( ";#;", '"', stripslashes( trim( (string) $this->settings[ 'content' ][ $i ] ) ) );
			}
		} else {
			if ( isset( $this->settings[ $i ] ) ) {
				return stripslashes( trim( (string) $this->settings[ $i ] ) );
			} else {
				global $sketch;
				$t = "test_" . $i;
				if ( $sketch->$t ) {
					return $sketch->$i;
				} else {
					return $def;
				}
			}
		}
	}
	public function showDisplay( $area = '' ) {
		global $_REQUEST;
		$this->isArea( $area );
		$display = true;
		if ( method_exists( $this->settings[ 'name' ], 'displayCheck' ) ) {
			if ( $this->displayCheck() == false || $this->displayCheck() == 0 ) {
				$display = false;
			}
		}
		$container = ( @$this->settings[ 'area' ] == "pre" || @$this->settings[ 'area' ] == "meta" || @$this->settings[ 'area' ] == "end" || @$this->settings[ 'area' ] == "start" || @$this->settings[ 'area' ] == "script" ) ? false : true;
		if ( isset( $_REQUEST[ 'checking' ] ) ) {
			$this->settings[ 'content' ] = $this->settings[ 'edit' ];
		}
		if ( $container && !sketch( "mobile" ) ) { ?><div id="<?php
			echo $this->settings[ 'name' ]; ?>"><?php
		}
		if ( $display ) {
			if ( is_file( sketch( "abspath" ) . sketch( "user_theme_path" ) . "views" . sketch( "slash" ) . strtolower( trim( $this->settings[ 'name' ]."view" ) ) . ".php" ) ) {
				include( sketch( "abspath" ) . sketch( "user_theme_path" ) . "views" . sketch( "slash" ) . strtolower( trim( $this->settings[ 'name' ]."view" ) ) . ".php" );
			} else {
				$this->display();
			}
		}
		if ( $container && !sketch( "mobile" ) ) {
?></div><?php
		}
	}
	public function doUpdate( ) {
		global $_POST, $sketch;
		helper( "cache" );
		$cache = CACHECLASS::cache( 'unset' );
		$cache->resetCache();
		$new = array( );
		foreach ( $_POST as $k => $v ) {
			if ( $k != "page_id" && $k != "plugin_id" && $k != "name" ) {
				if ( !is_array( $v ) ) {
					$new[ $k ] = str_replace( array(
						 '"'
					), array(
						 ';#;'
					), stripslashes( $v ) );
				} else {
					$new[ $k ] = $v;
				}
			}
		}
		$values                      = $this->update( $this->settings[ 'edit' ], $new );
		$this->settings[ 'content' ] = $values;
		$this->settings[ 'edit' ]    = $values;
		if ( isset( $this->settings[ "adminclass" ] ) && strpos( $this->settings[ "adminclass" ], "showPreview:false" ) !== false || isset( $_POST[ 'order' ] ) ) {
			$this->approve();
		} else {
			$SQL   = "UPDATE " . $this->prefix . "plugin SET edit=" . sketch( "db" )->quote( serialize( $values ) ) . " WHERE plugin_id='" . intval( $this->settings[ 'plugin_id' ] ) . "'";
			$start = microtime( true );
			$r     = ACTIVERECORD::keeprecord( $SQL );
			$sketch->setQueryData( $start, $SQL );
			if ( !isset( $this->settings[ "adminclass" ] ) || strpos( $this->settings[ "adminclass" ], "updateForm:false" ) !== true ) {
				$this->showForm();
			}
?>
	    <script type="text/javascript">
	        $(document.body).removeEvents("updatepreview");
	        $(document.body).addEvent("updatepreview",function(){
	    	$('<?php
			echo $this->settings[ 'name' ];
?>').load("<?php
			echo urlPath( "admin/ajax_plugin_" ) . $this->settings[ 'name' ];
?>?page_id=<?php
			echo $sketch->page_id;
?>&preview=edit");
	        });
	    </script>
<?php
		}
	}
	public function updateSettings( $data ) {
		$r = ACTIVERECORD::keeprecord( updateDB( "plugin", $data, "WHERE `name`=" . sketch( "db" )->quote( $data[ 'name' ] ) ) );
		$this->getData();
	}
	public function candoform( ) {
		return !isset( $this->settings[ 'noform' ] );
	}
	public function topNav( ) {
		return isset( $this->settings[ 'topnav' ] );
	}
	public function previewCurrent( ) {
		$this->display();
	}
	public function previewEdit( ) {
		global $sketch;
		$this->settings[ 'content' ] = @$this->settings[ 'edit' ];
		$this->preview();
?>
	<script type="text/javascript">
	    $(document.body).removeEvents("publishnew");
	    $(document.body).removeEvents("getlive");
	    $('<?php echo $this->settings[ 'name' ]; ?>').set("load",{
		onComplete: function(){
		    $('<?php echo $this->settings[ 'name' ]; ?>').unspin();
		    $(document.body).fireEvent("reload"); }});
		    $(document.body).addEvent("publishnew",function(){
			$('<?php echo $this->settings[ 'name' ]; ?>').unspin();
			$('<?php echo $this->settings[ 'name' ]; ?>').load("<?php
			echo urlPath( "admin/ajax_plugin_" ) . $this->settings[ 'name' ]; ?>?page_id=<?php
			echo $sketch->page_id; ?>&preview=approve");
	    });
	    $(document.body).addEvent("getlive",function(){
		$('<?php echo $this->settings[ 'name' ]; ?>').unspin();
		$('<?php
		echo $this->settings[ 'name' ]; ?>').load("<?php
		echo urlPath( "admin/ajax_plugin_" ) . $this->settings[ 'name' ]; ?>?page_id=<?php
		echo $sketch->page_id; ?>&preview=preview");
	    });
	</script>
<?php
	}
	public function approve( ) {
		global $sketch;
		$this->settings[ 'content' ] = $this->settings[ 'edit' ];
		$SQL                         = "UPDATE " . $this->prefix . "plugin SET content=" . sketch( "db" )->quote( serialize( $this->settings[ 'content' ] ) ) . ",edit=" . sketch( "db" )->quote( serialize( $this->settings[ 'content' ] ) ) . " WHERE plugin_id='" . intval( $this->settings[ 'plugin_id' ] ) . "'";
		$r                           = ACTIVERECORD::keeprecord( $SQL );
		if ( $r ) {
			if ( isset( $this->settings[ "adminclass" ] ) && strpos( $this->settings[ "adminclass" ], "updateForm:true" ) !== false ) {
				$this->showForm();
			} else {
				if ( @strpos( $this->settings[ "adminclass" ], "showPublish:false" ) === false ) {
					$this->display();
				}
			}
		}
	}
	public function install( ) {
		$global_location             = ( @$this->settings[ 'global' ] == 1 ) ? @$this->settings[ 'location' ] : '';
		$this->settings[ 'content' ] = str_replace( '"', ";#;", @$this->settings[ 'content' ] );
		$values                      = array_merge( (array) @$this->settings[ 'content' ] );
		$SQL                         = "INSERT INTO " . $this->prefix . "plugin (php,activated,`name`,is_admin,global_location,menu_name,content,edit) " . "values(" . intval( @$this->settings[ 'php' ] ) . ",1" . "," . sketch( "db" )->quote( $this->settings[ 'name' ] ) . " " . "," . intval( @$this->settings[ 'admin' ] ) . "," . sketch( "db" )->quote( $global_location ) . " " . "," . sketch( "db" )->quote( $this->e( 'menuName', $this->settings[ 'name' ] ) ) . " " . "," . sketch( "db" )->quote( serialize( $values ) ) . " " . "," . sketch( "db" )->quote( serialize( $values ) ) . " " . ")";
		$r                           = ACTIVERECORD::keeprecord( $SQL );
	}
	public function showForm( ) {
		global $sketch;
		$this->settings[ 'content' ] = @$this->settings[ 'edit' ];
		if(!isset($_GET['noform'])){
?>
	<form class="required ajax:true <?php
		if ( @strpos( $this->settings[ "adminclass" ], "updateForm:false" ) === false ) { ?>output:'load-box'<?php } ?>" method="post" action="<?php echo urlPath( "admin" ); ?>/admin_plugin_<?php echo $this->settings[ 'name' ]; ?>" id="form_<?php echo $this->settings[ 'name' ]; ?>">
	    <input type="hidden" name="page_id" value="<?php echo $sketch->page_id; ?>" />
	    <input type="hidden" name="plugin_id" value="<?php echo $this->settings[ 'plugin_id' ]; ?>" />
	    <input type="hidden" name="preview" value="edit" />
<?php
		}
		if ( is_file( sketch( "abspath" ) . sketch( "user_theme_path" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) . strtolower( trim( $this->settings[ 'name' ] ) ) . ".php" ) ) {
			include( sketch( "abspath" ) . sketch( "user_theme_path" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) . strtolower( trim( $this->settings[ 'name' ] ) ) . ".php" );
		} else {
			$this->form();
		}
		if(!isset($_GET['noform'])){
?>
	    <div style="clear:both;">&nbsp;</div>
	</form><?php
		}
	}
}