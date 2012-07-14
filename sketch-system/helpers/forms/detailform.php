<?php if(isset($_POST['error'])){?>
<?php 		echo $_POST['error']; ?>
<?php } 
	$memPage = getData("sketch_menu","menu_guid","sketch_menu_id=".intval($this->e('memberpage')));
	$memPage->advance();
?>
<form class="required" method="post" action="<?php echo urlPath($memPage->menu_guid); ?>">
  <?php if(memberid()){ ?>
  <h2>Update your details</h2>
    <input name="update" value="yes" type="hidden" />
  <?php }else{ ?>
    <h2>Register</h2>
     <input name="register" value="yes" type="hidden" />
  <?php } ?>
  <input name="token" type="hidden" value="<?php $tok = md5(rand()); sessionAdd('token',$tok,false); echo sessionGet('token'); ?>"/>
  <ul class="forms">
    <li>
        <label>First name</label>
        <input type="text" class="required" value="<?php echo $_POST['firstname'];?>" name="firstname">
    </li>
    <li>
        <label>Last name</label>
        <input type="text" class="required" value="<?php echo $_POST['lastname'];?>" name="lastname">
    </li>
    <li>
        <label>Nickname</label>
        <input type="text" class="required" value="<?php echo $_POST['nickname'];?>" name="nickname">
   </li>
   <?php if(!memberid()) {?>
    <li>
        <label>Password</label>
	<?php $req = "required"; ?>
        <input type="password" class="<?php echo $req; ?> password" name="password">
   </li>
   <?php } ?>
    <li>
        <label>Email</label>
        <input type="text" class="required email" value="<?php echo $_POST['email'];?>" name="email">
   </li>
    <li>
        <label>Address</label>
        <input type="text" class="required" value="<?php echo $_POST['address'];?>" name="address">
    </li>
    <li>
        <label>Postcode</label>
        <input type="text" class="required integer" value="<?php echo $_POST['postcode']; ?>" name="postcode">
   </li>
    <li>
        <label>City</label>
        <input type="text" class="required" value="<?php echo $_POST['city'];?>" name="city">
    </li>
    <li>
        <label>Country</label>
        <input type="text" class="required" value="<?php echo $_POST['country']; ?>" name="country">
   </li>
   <li>
       <?php if(!memberid()){ ?>
        <button type="submit">Sign Up</button>
	<?php }else{ ?>
	<button type="submit">Update Details</button>
	<?php } ?>
   </li>
  </ul>
</form>