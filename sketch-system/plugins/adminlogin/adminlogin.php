<?php
class ADMINLOGIN extends PLUGIN {
	function ADMINLOGIN( $args ) {
		$settings = array(
			 "location" => "script",
			"global" => 1,
			"php" => 1,
			"noform" => 0
		);
		$this->start( $settings, $args );
	}
	function update( $old, $new ) {
		return $new;
	}
	function display( ) {
		global $_GET;
		if ( isset( $_GET[ 'adminlogin' ] ) || isset( $_GET[ 'admin' ] ) ) {
?>

			<style type='text/css'>
				   #master-container {
				    	background:transparent !important;
				    	border:none !important;;
				    }
				    #master-container .myinput{
					border:1px solid #ccc !important;
					color:#999;
					height: 30px;
					padding-left: 5px;
					margin-left:5px;
				    }
			</style>

			<script type="text/javascript">
				window.addEvent("load",function(){
					var elm = new Element("a",{'id':'adminloginlink','href':'<?php
			echo urlPath( 'adminlogin.php' );
?>','html':'click me','styles':{'display':'none'}}).inject($(document.body),"bottom");
					adminPop = new Popup($(elm),{'id':3000,'width':'265','height':'290','maskWidth':'255','maskHeight':'200','closeOffsetT':34,classes:''});
					$(elm).fireEvent("click");
				});
			</script>
        <?php
		}
	}
	function form( ) {
	}
}