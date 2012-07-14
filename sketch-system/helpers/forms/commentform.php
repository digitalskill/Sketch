<form class="required clear" action="<?php echo urlPath(sketch("menu_guid")); ?>?blogcat=<?php echo $i; ?>" method="post">
	<ul class="forms">
    <li>
    <div style="position:relative;">
    <label>Add your comment here...</label>
    <textarea name="edit" class="required rich:true" cols="50" rows="3"></textarea>
    </div>
    </li>
    <li>
    <input type="hidden" name="addcomment" value="addcomment">
    <input type="hidden" value="<?php echo $i; ?>" class="required integer" name="blog">
    <input type="hidden" name="token" value="<?php helper("session"); $tok = md5(rand()); sessionSet("token",$tok,false); echo sessionGet("token"); ?>" class="required"/>
    <input type="hidden" name="blogcat" value="<?php echo $i; ?>" />
    <input type="hidden" name="under" value="<?php echo $under; ?>"/>
    <button type="submit" class="clear positive"><span class="icons check"></span>Add Comment</button>
    </li>
    </ul>
</form>