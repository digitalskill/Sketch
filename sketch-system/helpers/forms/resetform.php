<h3>Password Recovery form</h3>
<?php echo @$_POST['error']; ?>
<form method="post" action="<?php echo urlPath(sketch("menu_guid")); ?>" class="required">
    <input type="hidden" name="reset"      value="reset" />
    <input type="hidden" class="required"  value="<?php sessionAdd("token", md5(rand()), false); echo sessionGet('token'); ?>" name="token"/>
    <ul class="forms">
	<li>
            <label>Email</label><input type="text" name="email" value="" class="input email required" />
	</li>
        <li>
            <label>Nickname</label><input type="text" name="nickname" value="" class="input required" />
	</li>
	<li>
            <label>&nbsp;</label><button type="submit">Reset Password</button>
	</li>
    </ul>
</form>