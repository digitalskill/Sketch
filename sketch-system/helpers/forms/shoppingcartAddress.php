<?php
if(getProducts()){
    if(!isset($_POST['register']) && memberid()){
	    $_POST = memberGet();
    }
	if(is_array(getDeliveryInfo())){
		$_POST = getDeliveryInfo();
		if(!isset($_POST['firstname']) || trim($_POST['firstname'])==''){
			if(!isset($_POST['register']) && memberid()){
				$_POST = memberGet();
			}
		}
	}
    ?>
    <form method="post" action="<?php echo urlPath(sketch("menu_guid")); ?>" class="required" style="float:left;width:95%;padding:2%;border:1px solid #e2e2e2;background:#fff;">
 <?php if(!memberid() && $this->e("members")!= ""){?>
    <div style="float:right;width:35%">
    <h5>Already a member?</h5>
    <p><a href="<?php echo urlPath($this->e("members"));?>" class="button"><span class="icons user"></span>Login to your account</a></p>
      </div>
<?php }
	if($errorMessage != ''){
		echo "<div class='alert'><h3>Please fill in all fields</h3>";
		echo "<p>".$errorMessage."</p></div>";
	}
?>
    <h2 class="row">Enter contact Information</h2>
    <ul class="forms">
    <li>
    <label>First name</label><input type="text" name="firstname" value="<?php echo @$_POST['firstname']; ?>" class="input required <?php if(strpos($errorMessage,"firstname")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Last name</label><input type="text" name="lastname" value="<?php echo @$_POST['lastname']; ?>" class="input required <?php if(strpos($errorMessage,"lastname")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Email</label><input type="text" name="email" value="<?php echo @$_POST['email']; ?>" class="input required email <?php if(strpos($errorMessage,"email")!==false){?>validate-error<?php } ?>" /></li>
    <li><h2 class="row">Enter Delivery Information</h2></li>
    <li>
    <label>Address</label><input type="text" name="address" value="<?php echo @$_POST['address']; ?>" class="input required <?php if(strpos($errorMessage,"address")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Suburb</label><input type="text" name="suburb" value="<?php echo @$_POST['suburb']; ?>" class="input <?php if(strpos($errorMessage,"suburb")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>State</label><input type="text" name="state" value="<?php echo @$_POST['state']; ?>" class="input <?php if(strpos($errorMessage,"state")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Postcode</label><input type="text" name="postcode" value="<?php echo @$_POST['postcode']; ?>" class="input required integer <?php if(strpos($errorMessage,"postcode")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>City</label><input type="text" name="city" value="<?php echo @$_POST['city']; ?>" class="input required <?php if(strpos($errorMessage,"city")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Phone</label><input type="text" name="phone" value="<?php echo @$_POST['phone']; ?>" class="input required <?php if(strpos($errorMessage,"phone")!==false){?>validate-error<?php } ?>" /></li>
    <li>
    <label>Mobile</label><input type="text" name="mobile" value="<?php echo @$_POST['mobile']; ?>" class="input" /></li>
    <li>
    <label>Country</label>
    <select name="country">
    <?php $country = explode(",",$this->e("countries"));
	foreach($country as $key => $value){ ?>
	    <option value="<?php echo trim($value); ?>" <?php if(isset($_POST['country']) && trim($_POST['country'])==trim($value)){?>selected="selected"<?php } ?>><?php $subval = explode(":",trim($value)); echo $subval[0]; ?></option>
    <?php } ?>
    </select>
    </li>
    <li>
    	<?php if($this->e("terms")!=""){?><p>Please read and accept our <a href="<?php echo urlPath($this->e("terms")); ?>" target="_blank">terms and conditions.</a></p>
        <label style="width:220px;">Accept terms and conditions:</label>
	<?php $re = "required"; ?>
    	<input type="checkbox" class="<?php echo $re; if(strpos($errorMessage,"accept")!==false){?>validate-error<?php } ?>" title="You must accept our terms and conditions" name="terms"  value="accept" style="margin-top:7px;"/>
	<?php } ?>
    </li>
    <li>
	<label>&nbsp;</label>
    <button onclick="window.location='<?php echo urlPath(sketch("menu_guid")); ?>';"	class="button pill bleft" type="button"><span class="icons leftarrow"></span>Back to Cart</button>
    <button type="submit" class="button pill bright positive primary" name="continue" style="margin-left:0;"><span class="icons rightarrow"></span>Continue</button>
    </li>
    </ul>
    <div style="clear:both"></div>
     <div>
           <p style="text-align:right;">Progress: You are at step 2 of 3</p>
     </div>
</form>
<?php
}else{ ?>
	<div style="clear:both"></div>
	<h2>Your Shopping Cart is empty</h2>
	<a href="#" onclick="history.back();" class="button">Back to Shopping</a>
<?php } ?>
<div style="clear:both"></div>