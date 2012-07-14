<form selected="true" method="post" title="Login" class="panel" action="<?php echo urlPath($this->e('memberpage')); ?>?m">
    <?php if(isset($_POST['error'])){
	echo $_POST['error'];
      } ?>
    <input name="login" value="yes" type="hidden" />
    <input name="token" type="hidden" value="<?php $tok = md5(rand()); sessionAdd('token',$tok,false); echo sessionGet('token'); ?>"/>
  <fieldset>
      <div class="row">
        <label>Email</label><input type="text" name="email" value="<?php echo stripslashes(trim($_POST['email'])); ?>" />
      </div>
     <div class="row">
        <label>Password</label><input type="password" name="password" value="" />
     </div>
  </fieldset>
    <a class="whiteButton" type="submit">Login</a><br /><br />
    <a class="whiteButton" href="<?php echo urlPath($this->e('memberpage')); ?>?recover">Recover password</a><br />
    <a class="whiteButton" href="<?php echo urlPath($this->e('memberpage')); ?>?register">Become a Member</a>
</form>