<?php if(isset($_POST['error'])){
	echo $_POST['error'];
      } 
global $_COOKIE;
$data = array(
	 0 => @$_COOKIE[ 'lemail' ],
	1 => @$_COOKIE[ 'llock' ],
	2 => @$_COOKIE[ 'lpw' ]
);
if ( $data[ 1 ] !== md5( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
	$data = array(
		 "",
		"",
		""
	);
}  
	$pass = ( $data[ 2 ] != "" ) ? secureit( @$data[ 2 ], true ) : "";  
?>
<form class="required" method="post" action="<?php echo urlPath(sketch("menu_guid")); ?>">
    <h2>Login</h2>
    <input name="login" value="yes" type="hidden" />
    <input name="token" type="hidden" value="<?php $tok = md5(rand()); sessionAdd('token',$tok,false); echo sessionGet('token'); ?>"/>
  <ul class="forms">
    <li>
        <label>Email</label><input type="text" name="email" class="required email" value="<?php echo isset($_POST['email'])? $_POST['email'] :  @$data[ 0 ]; ?>">
    </li>
     <li>
        <label>Password</label><input type="password" class="required password" name="password" value="<?php echo isset($_POST['password'])? $_POST['password'] : $pass; ?>">
    </li>
    <li><label>Remember me</label>
    	<input type="checkbox" value="yes" name="remember" <?php if ( trim( $data[ 1 ] ) != '' ) {?> checked="checked" <?php } ?>/>
    </li>
    <li>
        <button type="submit">Log-in</button>
   </li>
  </ul>
</form>
<h2>Lost your password?</h2>
<p><a href="<?php echo urlPath(sketch("menu_guid")); ?>?recover" class="button">Start the recovery process</a></p>
<h2>Not signed up?</h2>
<p><a href="<?php echo urlPath(sketch("menu_guid")); ?>?register" class="button">Sign up today</a></p>
