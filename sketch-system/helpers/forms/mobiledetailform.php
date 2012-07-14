<form selected="true" method="post" title="<?php if(memberid()){ ?>Update your details<?php }else{ ?>Register<?php } ?>" action="<?php echo urlPath($this->e('memberpage')); ?>?m">
    <?php if(isset($_POST['error'])){
     echo $_POST['error'];
    } ?>
  <?php if(memberid()){ ?>
    <input name="update" value="yes" type="hidden" />
  <?php }else{ ?>
     <input name="register" value="yes" type="hidden" />
  <?php } ?>
  <input name="token" type="hidden" value="<?php $tok = md5(rand()); sessionAdd('token',$tok,false); echo sessionGet('token'); ?>"/>
  <fieldset>
    <div class="row">
        <label>First name</label>
        <input type="text" class="required" value="<?php echo $_POST['firstname'];?>" name="firstname">
    </div>
    <div class="row">
        <label>Last name</label>
        <input type="text" class="required" value="<?php echo $_POST['lastname'];?>" name="lastname">
    </div>
    <div class="row">
        <label>Nickname</label>
        <input type="text" class="required" value="<?php echo $_POST['nickname'];?>" name="nickname">
   </div>
   <?php if(!memberid()){ ?>
    <div class="row">
        <label>Password</label>
	<?php $req = "required"; ?>
        <input type="password" class="<?php echo $req; ?> password" value="" name="password">
   </div>
   <?php } ?>
    <div class="row">
        <label>Email</label>
        <input type="text" class="required email" value="<?php echo $_POST['email'];?>" name="email">
   </div>
    <div class="row">
        <label>Address</label>
        <input type="text" class="required" value="<?php echo $_POST['address'];?>" name="address">
    </div>
    <div class="row">
        <label>Postcode</label>
        <input type="text" class="required integer" value="<?php echo $_POST['postcode']; ?>" name="postcode">
   </div>
    <div class="row">
        <label>City</label>
        <input type="text" class="required" value="<?php echo $_POST['city'];?>" name="city">
    </div>
    <div class="row">
        <label>Country</label>
        <input type="text" class="required" value="<?php echo $_POST['country']; ?>" name="country">
   </div>
      </fieldset>
       <?php if(!memberid()){ ?>
        <a class="whiteButton" type="submit">Sign Up</a>
	<?php }else{ ?>
	<a class="whiteButton" type="submit">Update Details</a>
	<?php } ?>
</form>