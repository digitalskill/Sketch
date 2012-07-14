<form method="post" select="true" title="Password Recovery" action="<?php echo urlPath(sketch("menu_guid")); ?>" class="required">
    <input type="hidden" name="reset"      value="reset" />
    <input type="hidden" class="required"  value="<?php sessionAdd("token", md5(rand()), false); echo sessionGet('token'); ?>" name="token"/>
    <fieldset>
	<div class="row">
            <label>Email</label><input type="text" name="email" />
	</div>
        <div class="row">
            <label>Nickname</label><input type="text" name="nickname" />
	</div>
    </fieldset>
    <a type="submit" class="whiteButton">Reset Password</a>
</form>