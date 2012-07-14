<?php
class ADMINLOGIN extends CONTROLLER {
	function ADMINLOGIN( $page ) {
		global $_SESSION, $_REQUEST;
		if ( !isset( $_REQUEST[ 'ajax' ] ) ) {
			header( "location: " . urlPath() . "?adminlogin" );
			die();
		}
		header( 'Vary: Accept-Encoding' );
        header( 'Content-Type: text/html; charset=utf-8' );
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
		header("Pragma: no-cache");
        header( "Cache-Control: max-age=0, no-store, no-cache, private, must-revalidate" );
		if ( isset( $_REQUEST[ 'user_login' ] ) && isset( $_REQUEST[ 'user_pass' ] ) ) {
			if ( isset( $_SESSION[ 'logintoken' ] ) && isset( $_REQUEST[ 'token' ] ) && $_SESSION[ 'logintoken' ] == $_REQUEST[ 'token' ] ) {
				$this->processLogin();
			}
		} else {
			$this->showForm();
		}
	}
	function processLogin( ) {
		global $_POST, $_COOKIE;
		$SQL          = "SELECT count(user_login) as numberrecords FROM " . getSettings( 'prefix' ) . "users WHERE user_login=" . sketch( "db" )->quote( $_POST[ 'user_login' ] ) . " AND user_password=" . sketch( "db" )->quote( secureit( trim( $_POST[ 'user_pass' ] ) ) ) . " LIMIT 1";
		$countRecords = ACTIVERECORD::keeprecord( $SQL );
		$countRecords->advance();
		if ( $countRecords->numberrecords > 0 ) {
			$SQL = "SELECT * FROM " . getSettings( 'prefix' ) . "users WHERE user_login=" . sketch( "db" )->quote( $_POST[ 'user_login' ] ) . " AND user_password=" . sketch( "db" )->quote( secureit( $_POST[ 'user_pass' ] ) ) . " LIMIT 1";
			$r   = ACTIVERECORD::keeprecord( $SQL );
			$r->advance();
			$_SESSION[ 'admin' ] = $r->result;
			$_SESSION[ 'lock' ]  = md5( md5( $_SERVER[ 'HTTP_USER_AGENT' ] ) . md5( $_SERVER[ 'REMOTE_ADDR' ] ) );
			if ( isset( $_POST[ 'remember' ] ) ) {
				setcookie( "lock", md5( $_SERVER[ 'HTTP_USER_AGENT' ] ), time() + 60 * 60 * 24 * 30 * 3, "/" );
				setcookie( "name", $r->user_login, time() + 60 * 60 * 24 * 30 * 3, "/" );
				setcookie( "pw", $r->user_password, time() + 60 * 60 * 24 * 30 * 3, "/" );
			}
?>
	        <script type="text/javascript">
                                $$(".mask").setStyle("display","none");
                                var m = new Spinner($(document.body),{id:'loadspin',style:{"z-index":999999,"background-color":"#fff","color":"#778899"},"message":"Loading sketch - Please Wait"}).show();
                                $(m).fade(0.9);
				new Asset.images([
				   <?php
			$icount = 0;
			$f      = scandir( sketch( 'sketchPath' ) . 'plugins/admin/images/' );
			foreach ( $f as $key => $value ) {
				if ( strpos( $value, ".png" ) !== false || strpos( $value, ".jpg" ) !== false || strpos( $value, ".gif" ) !== false ) {
					if ( $icount > 0 ) {
						echo ",";
					}
					echo '"' . urlPath( "sketch-system/plugins/admin/images/" . $value ) . '"';
					$icount++;
				}
			}
?>
				],{onProgress:showProgress,onComplete:function(){
					 $('loadspin').getElement('.spinner-msg').set("html","Loading sketch - Please Wait<br />Images Loaded:100.00<br />Redirecting to page...");
					hold.delay(500);},onError:holdup});
                                function hold(){
                                  var local =  window.location.toString();
                                  local = local.substring(0,local.indexOf("admin")-1);
                                  window.location = local;
                                }
				function showProgress(counter,index,source){
				    $('loadspin').getElement('.spinner-msg').set("html","Loading sketch - Please Wait<br />Images Loaded:" + ((counter/<?php
			echo $icount;
?>) * 100).round(2) + "%");
				}
				function holdup(){
				    hold();
				}
			</script>
	        <?php
			$r->free();
		} else {
			$msg = "Please try again";
			helper( "email" );
			$SQL = "SELECT * FROM " . getSettings( 'prefix' ) . "users WHERE user_login=" . sketch( "db" )->quote( $_POST[ 'user_login' ] );
			$r   = ACTIVERECORD::keeprecord( $SQL );
			if ( $r->advance() ) {
				$result = email( "info@" . $_SERVER[ 'HTTP_HOST' ], $r->user_email, "Password Recovery", array(
					 "Password" => secureit( $r->user_password, true )
				) );
				if ( $result == "success" ) {
					$msg .= "<br />Your Password has been emailed to you.";
				}
			}
			$r->free();
?>
	        <script type="text/javascript">
			try{
			$("login").set("title","Invalid Login:<?php
			echo $msg;
?>");
			new sketchAlert($("login"));
			$("login").fireEvent("doAlert");
			}catch(e){alert(e);}
		</script>
	        <?php
		}
		$countRecords->free();
		exit( );
	}
	function showForm( ) {
		global $_REQUEST, $_COOKIE;
		helper( "session" );
		if ( !isset( $_REQUEST[ 'ajax' ] ) ) {
?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
			<head>
			<title>sketch &rsaquo; Log In</title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name='robots' content='noindex,nofollow' />
			<?php
		}
		if ( !isset( $_REQUEST[ 'ajax' ] ) ) {
?>
			</head>
			<body>
			<?php
		}
?>
<div id="login">
<div id="mehide" style="display:none;"></div>
<div id="version">Developed by <a href="email:husko2006@gmail.com">Husko</a></div>
<?php
		$data = array(
			 0 => @$_COOKIE[ 'name' ],
			1 => @$_COOKIE[ 'lock' ],
			2 => @$_COOKIE[ 'pw' ]
		);
		if ( $data[ 1 ] !== md5( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
			$data = array(
				 "",
				"",
				""
			);
		}
?>
  <form name="loginform" id="loginform" action="<?php
		echo urlPath( "adminlogin" );
?>" method="post" class="required ajax:true update:'mehide'">
  	<input type="hidden" name="token" value="<?php
		sessionAdd( "logintoken", md5( rand() ), false );
		echo sessionGet( 'logintoken' );
?>" />
    <div class="loginForm">
      <div class="sitename">sketch CMS</div>
      <div class="inputrow">
        <label id="loginlbl1">Login</label>
        <input type="text" name="user_login" id="user_login" title="Please enter your user name" class="myinput required label:'loginlbl1'" value="<?php
		echo @$data[ 0 ];
?>"/>
      </div>
      <div class="inputrow">
        <label id="loginlbl2">Password</label>
        <?php
		$pass = ( $data[ 2 ] != "" ) ? secureit( @$data[ 2 ], true ) : "";
?>
        <input type="password" name="user_pass" id="user_pass" title="Please enter your password" class="myinput required password label:'loginlbl2'" value="<?php
		echo $pass;
?>"/>
      </div>
      <div class="inputrow" style="margin-top:26px;">
        <input type="checkbox" name="remember" <?php
		if ( trim( $data[ 1 ] ) != '' ) {
?> checked="checked"<?php
		}
?> style="float: right;margin-right:26px;"/>
	<label style="clear:none;float: left;padding-left:8px;padding-top: 0;position: static;width: auto;">Remember Password</label>
      </div>
    </div>
    <input type="submit" value="Login" class="butn-login"/>
  </form>
</div>
<script type="text/javascript">
	function setupLoginform(){
		new Validate($('loginform'));
	}
	setupLoginform.delay(500);
</script>
	<?php
		if ( !isset( $_REQUEST[ 'ajax' ] ) ) {
?>
	<div class="greyline">
	</div>
	</body>
	</html>
	<?php
		}
	}
}