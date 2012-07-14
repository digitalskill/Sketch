<ul style="float:left;width:100%">
    <li><div class="content-column">
	    <div class="title">Member Forms</div>
	    <div class="big-font">Select Forms for members</div>
	</div>
    </li>
</ul>
<ul style="float:left;width:70%;">
    <li><label>Email Registrations from</label>
	<input type="text" class="required email" name="emailto" value="<?php echo $this->e('emailto'); ?>" />
    </li>
    <li><label>Login form</label>
	<select name="loginform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('loginform') == "") { ?>selected="selected"<?php } ?>>None</option><?php
foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
?><option value="<?php echo $value; ?>" <?php if ($this->e('loginform') == $value) {
 ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	}
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("loginform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?></select>
    </li>
    <li><label>Details form</label>
	<select name="detailform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('detailform') == "") { ?>selected="selected"<?php } ?>>None</option><?php
	    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
			?><option value="<?php echo $value; ?>" <?php if ($this->e('detailform') == $value) { ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	    }
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("detailform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?></select>
    </li>
    <li><label>Reset Password form</label>
	<select name="resetform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('resetform') == "") {
 ?>selected="selected"<?php } ?>>None</option><?php
	    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
?><option value="<?php echo $value; ?>" <?php if ($this->e('resetform') == $value) { ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	    }
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("resetform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?>
</select>
    </li>
    <li><label>On Register Redirect</label>
	<select name="successreg" class="bgClass:'select_bg'">
	    <option value="">None</option>
			<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("successreg"),"type"=>"menu_guid")); ?>
	</select>
    </li>
    <li><label>On Login Redirect</label>
	<select name="redirect" class="bgClass:'select_bg'">
	    <option value="">None</option>
		<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("redirect"),"type"=>"menu_guid")); ?>
	</select>
    </li>
    <li><label>Member page (Select the page to show the login,details and register forms)</label>
	<select name="memberpage" class="required bgClass:'select_bg'">
	    <option value="">None</option>
		<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("memberpage"),"type"=>"menu_guid")); ?>
	</select>
    </li>
</ul>
<ul style="float:right;width:28%;margin-top:-10px">
    <li>
	<div class="instruction-box">
	    <h4>Members plugin instructions</h4>
	    <p><strong style="font-size:12px;">Email registrations from : </strong><br />This is the email address that is used to contact members that register.</p>
	    <p><strong style="font-size:12px;">Select the login form :  </strong><br />This is the form that is used to login. Leave blank to use sketch's login form.</p>
	    <p><strong style="font-size:12px;">Select the details form : </strong><br />This is the form that is used to Register new members.<br/>Leave blank to use sketch's login form.<br/>sketch members also use this form to update their details.</p>
	    <p><strong style="font-size:12px;">Select the password reset form : </strong><br />This is the form that is used to reset member passwords for members.</p>
	    <p><strong style="font-size:12px;">Select the register redirect form : </strong><br />This is where to take members once registered.<br/>Leave blank for them to be shown a thank you message and login form.</p>
	    <p><strong style="font-size:12px;">Select the Login redirect : </strong><br />You can select either a product page, blog or membership landing page. Leave blank to take members to the home page.</p>
	</div>
    </li>
</ul>